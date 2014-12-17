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
            $ms_id = $this->input->post('m_strm', true);
            $os_id = $this->input->post('o_strm', true);
            
            $logged_id = $this->get_logged_uid();
            
            if($this->is_my_stream($ms_id, $logged_id)){
                
                if(! $this->is_my_stream($os_id, $logged_id)){
                    
                    $ms_data = $this->get_single_stream($ms_id);
                    $os_data = $this->get_single_stream($os_id);
                    
                    if($ms_data['status'] == true){
                        
                        $ms = $ms_data['data'];                        
                        $ms_sys_id = $ms['eco_sys_id'];
                        
                        if($os_data['status'] == true){
                        
                            $os = $os_data['data'];                        
                            $os_sys_id = $os['eco_sys_id'];
                            
                            if(! $this->is_already_merged($ms_sys_id)){
                                if(! $this->is_already_merged($os_sys_id)){
                                    if(! $this->is_already_requested($logged_id, $ms_sys_id, $os_sys_id)){
                                        if(! $this->is_ready_for_river($logged_id, $ms_sys_id, $os_sys_id)){
                                            
                                            $merge_request = $this->add_request_to_merge($logged_id, $ms_sys_id, $os_sys_id);                                                                                
                                        
                                            if($merge_request['status'] == true){
                                                
                                                $config = c_get_config();
                                                
                                                $user = $this->is_valid_user($this->get_logged_uid());
                                                $other_user = $this->Mod_User->get_user($os['user_id']);
                                                
                                                $ms_link = "<a href='" . base_url() . $config['single_stream_url'] . "/{$ms[id]}'>{$ms[question_text]}</a>";
                                                $os_link = "<a href='" . base_url() . $config['single_stream_url'] . "/{$os[id]}'>{$os[question_text]}</a>";
                                                
                                                $notif_details = "{$user[name]}<br />
                                                                  Invite Request for Creating River<br />
                                                                  Hi {$other_user[name]}, I would like to merge my stream 
                                                                  {$ms_link} with your stream {$os_link} 
                                                                  due to our cause and awareness message and I would like to<br /> 
                                                                  <br />
                                                                  promote it mutually.<br />
                                                                  Approval required, thanks.<br />";
                                                
                                                $notif_data = array(
                                                    'template_id' => "6",
                                                    'user_id' => "{$os[user_id]}", //receiver
                                                    'from_user_id' => "{$logged_id}",
                                                    'objects' => "Stream:Stream",
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
                                            $result['message'] = "Stream already ready for creating river.";
                                        }
                                    }
                                    else{
                                        $result['message'] = "Request already sent.";    
                                    }
                                }
                                else{
                                    $result['message'] = "Stream already merged.";    
                                }    
                            }
                            else{
                                $result['message'] = "Stream already merged.";    
                            }
                        }
                        else{
                            $result['message'] = "Invalid stream.";
                        }
                    }
                    else{
                        $result['message'] = "Invalid your stream.";
                    }
                }
                else{
                    $result['message'] = "This is not valid stream.";
                }                
            }
            else{
                $result['message'] = "This is not your stream.";
            }
        }
        else{
            $result['message'] = "Login first.";
        }
        
        return $result;
    }
    
}
