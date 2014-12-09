<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Create_Voices extends Mod_Voice {
    
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
        $this->log("Model Mod_Create_Voices loaded.");
        
        // load file system model
        $this->load->model("Mod_File_System");
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _upload_voice_image(){
        
        // check valid user
        $user_id = $this->get_logged_uid();
        
        if(! $user_id){
            
            // invalid user login
            $this->_set_message(self::ERROR, "You are not valid user.");
            redirect(base_url() . $this->_config["my_voice_url"]);
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
            $user_voice_folder = $this->Mod_File_System->create_folder($path, 'voices');
            
            // check whether folder is created
            if($user_voice_folder["status"] == true){
                
                // set upload file config        
                $config['upload_path'] = $path . '/' . 'voices';
        		$config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
        		$config['max_size']	= '2048';
                $config['encrypt_name'] = true;
                
                return $this->Mod_File_System->create_file($config, true, 820, 820);
            }
            
            // folder not created
            else{
                
                // write log
                $this->log("voices folder not be created in $user_id folder; function _upload_voice_image; file mod_create_voice", "error");
                
                // set message
                $this->_set_message(self::ERROR, "Voice not created kindly contact the support. with this error code " . USER_VOICE_FOLDER_NOT_CREATED);
                
                // redirect imediately to my voices page
                redirect(base_url() . $this->_config["my_voice_url"]);
                exit;  
            }
        }
        else{
            
            // write log
            $this->log("$user_id folder not be created in $path folder; function _upload_voice_image; file mod_create_voice", "error");
            
            // set message
            $this->_set_message(self::ERROR, "Voice not created kinly contact support. with this error code " . USER_FOLDER_NOT_CREATED);
            
            // redirect imediately to my voices page
            redirect(base_url() . $this->_config["my_voice_url"]);
            exit; 
        }
    }
    
    private function _filter_tags($tags = array()){
        
        // store return tags id
        $ret_tags_id = array();
        
        foreach($tags as $tag){
            
            $sql = "SELECT * FROM voice_tags WHERE tag=?";
            $query = $this->db->query($sql, array(strtolower($tag)));
            
            // if tag is already found simple get this id
            if($query->num_rows() > 0){
                
                $result = (object)$query->row_array();
                
                // push tag id to return tags id array
                array_push($ret_tags_id, $result->id);
            }
            
            // else tag is not found entry this tag
            else{
                
                $user_id = $this->get_logged_uid();
                if($user_id){
                    
                    $data = array('user_id' => $user_id, 'tag' => $tag, 'added_on' => c_now());
                    $result = $this->db->insert('voice_tags', $data);
                    
                    $sql = "SELECT * FROM voice_tags ORDER BY id DESC LIMIT 1";
                    $query = $this->db->query($sql);
                    
                    if($query->num_rows() > 0){
                        $row = (object)$query->row_array();
                    
                        // push tag id to return tags id array                    
                        array_push($ret_tags_id, $row->id);    
                    }                    
                }
                else{
                    
                    // invalid user login
                    $this->_set_message(self::ERROR, "You are not valid user.");
                    redirect(base_url() . $this->_config["my_voice_url"]);
                    exit("Unathorized user.");
                }
            }
        }
        
        // return tags id
        return $ret_tags_id;
    }
    
    private function _set_message($level = self::INFO, $msg = ""){
        $this->session->set_userdata('create_voice_message', array("level" => $level, "message" => $msg));
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function create_voice(){                
        
        // get var in post
        $title  = rtrim(ltrim($this->input->post('voc_title', true)));
        $desc   = $this->input->post('voc_desc', true);
        $tags   = $this->input->post('voc_tags', true);
        $cat_id = $this->input->post('voc_cat', true);
        
        // check title is not empty
        if(! empty($title)){
            
            // check voice is valid            
            if($this->is_valid_voice_cat($cat_id)){
                
                // decode to array
                $tags = @json_decode($tags);
                
                // check tags is array 
                if(is_array($tags)){
                    
                    $tags_id = json_encode($this->_filter_tags($tags));
                    
                    // upload file and return data where error or success
                    $upload_image = $this->_upload_voice_image();
                    
                    if($upload_image["status"] == true){
                                                                                    
                        $user_id = $this->get_logged_uid();
                        
                        // check valid user
                        if($user_id){
                            
                            $image_data = $upload_image["upload_data"];
                            
                            // set inserting data
                            $voice_data = array(
                                "user_id"       => $user_id,
                                "voice_cat_id"  => $cat_id,
                                "voice_tag_ids" => $tags_id,
                                "question_text" => $title,
                                "voice_details" => $desc,
                                "voice_pic"     => $image_data->file_name,
                                "added_on"      => c_now()
                            );
                            
                            // insert voice to voice table
                            $this->db->insert('user_voices', $voice_data);
                            
                            // voice successfully created
                            $this->_set_message(self::SUCCESS, "Your voice successfully created.");
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
                
                // opp's tags is not array
                else{
                    $this->_set_message(self::ERROR, "Voice Tags invalid format.");
                } 
            }
            
            // category id is invalid
            else{
                $this->_set_message(self::ERROR, "Voice Category invalid.");
            }
            
            
        }
        else{
            $this->_set_message(self::ERROR, "Voice Title can't be blank.");
        }
        
        redirect(base_url() . $this->_config["my_voice_url"]);
    }
}
