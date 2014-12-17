<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Create_Ocean extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTANTS
    /////////////////////////////////////////////////
    
    private $_config;
    
    /////////////////////////////////////////////////
    // CONSTANTS
    /////////////////////////////////////////////////
    
    // set debug levels    
    const ERROR   = "error";
    const SUCCESS = "success";
    const INFO    = "info";
    const WARNING = "warning";
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        
        // get config and store to private var
        $this->_config = c_get_config();
        
        // write log
        $this->log("Model Mod_Create_Ocean loaded.");
        
        // load file system model
        $this->load->model("Mod_File_System");
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _upload_eco_system_image(){
        
        // check valid user
        $user_id = $this->get_logged_uid();
        
        if(! $user_id){
            
            // invalid user login
            $this->_set_message(self::ERROR, "You are not valid user.");
            redirect(base_url() . $this->_config["ocean_create_url"] . '/0');
            exit("Unathorized user.");
        }
        
        // set path
        $path = '../user_files/prof';
        
        // create user id folder inside folder folder
        $user_folder = $this->Mod_File_System->create_folder($path, $user_id);
        
        if($user_folder["status"] == true){
            
            // overwrite path
            $path = $path . '/' . $user_id;
            
            // create voices folder inside user id folder
            $user_eco_folder = $this->Mod_File_System->create_folder($path, 'ecosystem');
            
            // check whether folder is created
            if($user_eco_folder["status"] == true){
                
                // set upload file config        
                $config['upload_path'] = $path . '/' . 'ecosystem';
        		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
        		$config['max_size']	= '2048';
                $config['encrypt_name'] = true;
                
                return $this->Mod_File_System->create_file($config, true, 820, 820);
            }
            
            // folder not created
            else{
                
                // write log
                $this->log("ecosystem folder not be created in $user_id folder; function _upload_eco_system_image; file mod_create_ocean", "error");
                
                // set message
                $this->_set_message(self::ERROR, "Ocean not created kindly contact the support. with this error code " . USER_ECOSYSTEM_FOLDER_NOT_CREATED);
                
                // redirect imediately to my voices page
                redirect(base_url() . $this->_config["ocean_create_url"] . '/0');
                exit;  
            }
        }
        else{
            
            // write log
            $this->log("$user_id folder not be created in $path folder; function _upload_eco_system_image; file mod_create_ocean", "error");
            
            // set message
            $this->_set_message(self::ERROR, "Ocean not created kinly contact support. with this error code " . USER_FOLDER_NOT_CREATED);
            
            // redirect imediately to my voices page
            redirect(base_url() . $this->_config["ocean_create_url"] . '/0');
            exit; 
        }
    }
    
    private function _set_message($level = self::INFO, $msg = ""){
        $this->session->set_userdata('create_voice_message', array("level" => $level, "message" => $msg));
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function create_ocean(){
        
        // get var in post
        $title  = rtrim(ltrim($this->input->post('voc_title', true)));
        $desc   = $this->input->post('voc_desc', true);
        $tags   = $this->input->post('voc_tags', true);
        $cat_id = $this->input->post('voc_cat', true);
        $oc_key = $this->input->post('oc_key', true);
        
        $user_id = $this->get_logged_uid();
        
        if($this->is_valid_ocean_key($user_id, $oc_key)){
            // check title is not empty
            if(! empty($title)){
                
                // check voice is valid            
                if($this->is_valid_voice_cat($cat_id)){
                    
                    // decode to array
                    $tags = @json_decode($tags);
                    
                    // check tags is array 
                    if(is_array($tags)){
                        
                        $filter_tags_data = $this->filter_tags($tags);
                        
                        if($filter_tags_data['status'] == true){
                            
                            $tags_id = json_encode($filter_tags_data['data']);

                            // upload file and return data where error or success
                            $upload_image = $this->_upload_eco_system_image();
                            
                            if($upload_image["status"] == true){

                                // check valid user
                                if($user_id){
                                    
                                    $image_data = $upload_image["upload_data"];
                                    
                                    // set inserting data
                                    $ocean_data = array(
                                        "level"         => 3,
                                        "moderator_id"  => $user_id,
                                        "voice_cat_id"  => $cat_id,
                                        "voice_tag_ids" => $tags_id,
                                        "title"         => $title,
                                        "description"   => $desc,
                                        "eco_pic"       => $image_data->file_name,
                                        "created_on"    => c_now()
                                    );
                                    
                                    // insert voice to voice table
                                    if($this->db->insert('eco_system', $ocean_data)){
                                        
                                        $last_ocean_data = $this->get_last_ocean($user_id);
                                        $get_merged_data = $this->get_merged_request($user_id, $oc_key);
                                        
                                        $data = array("parent_id" => $last_ocean_data["id"]);
                                        
                                        // update caller eco system
                                        $this->db->where('id', $get_merged_data["caller_eco_sys_id"]);
                                        $this->db->update('eco_system', $data);
                                        
                                        // update receiver eco system
                                        $this->db->where('id', $get_merged_data["receiver_eco_sys_id"]);
                                        $this->db->update('eco_system', $data);
                                        
                                        // delete caller  merge request
                                        $this->db->where("(caller_eco_sys_id='{$get_merged_data[caller_eco_sys_id]}' OR caller_eco_sys_id='{$get_merged_data[receiver_eco_sys_id]}')");
                                        $this->db->delete('eco_merge_requests');
                                        
                                        // delete receiver merge request
                                        $this->db->where("(receiver_eco_sys_id='{$get_merged_data[caller_eco_sys_id]}' OR receiver_eco_sys_id='{$get_merged_data[receiver_eco_sys_id]}')");
                                        $this->db->delete('eco_merge_requests');
                                        
                                        // ocean successfully created
                                        $this->_set_message(self::SUCCESS, "Your Ocean successfully created.");
                                        redirect(base_url() . $this->_config["my_oceans_url"]);
                                        
                                    }
                                    else{                                        
                                        $this->_set_message(self::ERROR, "Ocean can't created.");
                                    }
                                }
                                
                                // invalid user
                                else{
                                    
                                    // invalid user login
                                    $this->_set_message(self::ERROR, "You are not valid user.");                            
                                }
                            }
                            else{
                                $this->_set_message(self::ERROR, $upload_image["error"]);
                            }
                        }
                        else{
                            $this->_set_message(self::ERROR, $filter_tags_data['message']);
                            redirect(base_url() . $this->_config["ocean_create_url"] . '/0');
                            exit("Unathorized user.");
                        }                                        
                    }
                    
                    // opp's tags is not array
                    else{
                        $this->_set_message(self::ERROR, "Ocean Tags invalid format.");
                    } 
                }
                
                // category id is invalid
                else{
                    $this->_set_message(self::ERROR, "Ocean Category invalid.");
                }
            }
            else{
                $this->_set_message(self::ERROR, "Ocean Title can't be blank.");
            }
        }
        
        // ocean key invalid
        else{
            $this->_set_message(self::ERROR, "Invalid ocean creation.");            
        }
        
        redirect(base_url() . $this->_config["ocean_create_url"] . '/0');
    }
    
}
