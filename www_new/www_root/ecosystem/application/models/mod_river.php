<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_River extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_Stream loaded.");
        
        // load models
        $this->_load_models();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_User');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function merge_to_ocean(){
        
        $result = array("status" => false, "message" => "");
        
        if(is_logged_in()){
            $mr_id = $this->input->post('m_strm', true);
            $or_id = $this->input->post('o_strm', true);
            
            $logged_id = $this->get_logged_uid();            
                    
            $mr_data = $this->get_single_river($mr_id);
            $or_data = $this->get_single_river($or_id);
            
            if($mr_data['status'] == true){
                
                $mr = $mr_data['data'];                        
                #$ms_sys_id = $ms['eco_sys_id'];
                
                if($or_data['status'] == true){
                
                    $or = $or_data['data'];                        
                    #$os_sys_id = $os['eco_sys_id'];
                    
                    if(! $this->is_already_merged($mr['id'])){
                        if(! $this->is_already_merged($or['id'])){
                            if(! $this->is_already_requested($logged_id, $mr['id'], $or['id'])){
                                if(! $this->is_ready_for_ocean($logged_id, $mr['id'], $or['id'])){
                                    
                                    $merge_request = $this->add_request_to_merge($logged_id, $mr['id'], $or['id']);                                                                                
                                
                                    if($merge_request['status'] == true){
                                        
                                        $config = c_get_config();
                                        
                                        $user = $this->is_valid_user($this->get_logged_uid());
                                        $other_user = $this->Mod_User->get_user($or['moderator_id']);
                                        
                                        $mr_link = "<a href='" . base_url() . $config['single_river_url'] . "/{$mr[id]}'>{$mr[question_text]}</a>";
                                        $or_link = "<a href='" . base_url() . $config['single_river_url'] . "/{$or[id]}'>{$or[question_text]}</a>";
                                        
                                        $notif_details = "{$user[name]}<br />
                                                          Invite Request for Creating Ocean<br />
                                                          Hi {$other_user[name]}, I would like to merge my river 
                                                          {$mr_link} with your river {$or_link} 
                                                          due to our cause and awareness message and I would like to<br /> 
                                                          <br />
                                                          promote it mutually.<br />
                                                          Approval required, thanks.<br />";
                                        
                                        $notif_data = array(
                                            'template_id' => "6",
                                            'user_id' => "$or[moderator_id]", //receiver
                                            'from_user_id' => "{$logged_id}",
                                            'objects' => "River:River",
                                            'object_id' => "{$merge_request[data][id]}",
                                            'object_location' => 'eco_merge_requests',
                                            'visit_url' => "ecosystem/invitation", //to visit after click from top menu and from emails
                                            'notif_details' => $notif_details, //details for emails
                                        );
                                                                                    
                                        generate_notification($notif_data, true);
                                        
                                        $result['status'] = true;
                                        $result['message'] = "Request sent.";
                                    }
                                    else{
                                        $result['message'] = $merge_request['message'];
                                    }   
                                }
                                else{
                                    $result['message'] = "River already ready for creating ocean.";
                                }
                            }
                            else{
                                $result['message'] = "Request already sent.";    
                            }
                        }
                        else{
                            $result['message'] = "River already merged.";    
                        }    
                    }
                    else{
                        $result['message'] = "River already merged.";    
                    }
                }
                else{
                    $result['message'] = "Invalid river.";
                }
            }
            else{
                $result['message'] = "Invalid your river.";
            }                
        }
        else{
            $result['message'] = "Login first.";
        }
        
        return $result;
    }
    
}
