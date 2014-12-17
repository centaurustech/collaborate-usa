<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Merger extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_Merger loaded.");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function stream_to_river(){
        
        $result = array("status" => false, "message" => "");
        
        if(is_logged_in()){                                    
            $caller_sys_id = $this->input->post('csid', true);
            $receiver_sys_id = $this->input->post('rsid', true);
            
            $merged_request = $this->get_merged_request_by_cr($caller_sys_id, $receiver_sys_id);            
            if($merged_request){
                
                if($merged_request['verification_str'] == ''){
                    
                    $caller_eco_system = $this->get_eco_system_by_user($caller_sys_id, $merged_request['requested_by']);                
                    if($caller_eco_system){
                        
                        $receiver_eco_system = $this->get_eco_system_by_user($receiver_sys_id, $this->get_logged_uid());                                        
                        if($receiver_eco_system){
                            
                            $sql = "SELECT * FROM eco_merge_requests WHERE (caller_eco_sys_id=? OR caller_eco_sys_id=? OR receiver_eco_sys_id=? OR receiver_eco_sys_id=?)";
                            $rsl = $this->db->query($sql, array($caller_sys_id, $receiver_sys_id, $receiver_sys_id, $caller_sys_id));
                            $rows = $rsl->result_array();
                            foreach($rows as $mr){
                                // delete notification
                                $this->db->where('object_id', $mr['id']);
                                $this->db->delete('user_notifications');   
                            }
                            
                            $rc_key = md5(uniqid(rand())).md5(uniqid(rand())).md5(uniqid(rand()));
                            
                            $data = array("verification_str" => $rc_key);
                            
                            $this->db->where('id', $merged_request['id']);
                            $this->db->update('eco_merge_requests', $data);
                            
                            // delete other request to merge request table
                            $this->db->where("((caller_eco_sys_id='{$caller_sys_id}' OR caller_eco_sys_id='{$receiver_sys_id}' OR receiver_eco_sys_id='{$caller_sys_id}' OR receiver_eco_sys_id='{$receiver_sys_id}') AND verification_str='')");
                            $this->db->delete('eco_merge_requests');
                                                                                                                                                
                            $rec_stream = $this->get_stream_by_eco_sys_id($receiver_sys_id);
                            if($rec_stream['status'] == true){
                                
                                $config = c_get_config();
                                
                                $notif_details = "Your request to create a River is accepted successfully by the moderator of \"Stream ".$rec_stream['data']['question_text']."\". Please follow the steps below:<br />
                                                 1. Go to your invited stream: \"Stream <a href='".base_url().$config['single_stream_url'].'/'.$rec_stream['data']['id']."'>".$rec_stream['data']['question_text']."</a>\"<br />
                                                 2. Click on Create River button<br />
                                                 3. Fill the form and submit.<br />";
                                
                                $notif_data = array(
                                    'template_id' => "8",
                                    'user_id' => "{$caller_eco_system[moderator_id]}", //receiver
                                    'from_user_id' => "{$receiver_eco_system[moderator_id]}",
                                    'objects' => "Stream:Stream",
                                    'object_id' => "{$merged_request[id]}",
                                    'object_location' => 'eco_merge_requests',
                                    'visit_url' => "ecosystem/notification", //to visit after click from top menu and from emails
                                    'notif_details' => $notif_details, //details for emails
                                );
                                                                            
                                generate_notification($notif_data, true);       
                            }
                                                                                                            
                            $result['status'] = true;
                            $result['message'] = "Request accepted.";
                        }
                        else{
                            $result['message'] = 'Receiver invalid.';    
                        }
                    }
                    else{
                        $result['message'] = 'Caller invalid.';
                    }   
                }
                else{
                    $result['message'] = "Request already accepted.";
                }
            }
            else{
                $result['message'] = "Invalid caller and receiver.";
            }
        }
        
        else{
            $result['message'] = "Login first.";
        }
        
        return $result;
    }
    
    public function reject_river_invite(){
        $result = array("status" => false, "message" => "");
        
        if(is_logged_in()){
            $caller_sys_id = $this->input->post('csid', true);
            $receiver_sys_id = $this->input->post('rsid', true);
            
            $merged_request = $this->get_merged_request_by_cr($caller_sys_id, $receiver_sys_id);            
            if($merged_request){
                
                if($merged_request['verification_str'] == ''){
                    
                    $caller_eco_system = $this->get_eco_system_by_user($caller_sys_id, $merged_request['requested_by']);                
                    if($caller_eco_system){
                        
                        $receiver_eco_system = $this->get_eco_system_by_user($receiver_sys_id, $this->get_logged_uid());                                        
                        if($receiver_eco_system){
                            
                            $notif_details = "Your request to create a River is rejected unfortunately by the moderator of \"Stream\".";
                            
                            $notif_data = array(
                                'template_id' => "11",
                                'user_id' => "{$caller_eco_system[moderator_id]}", //receiver
                                'from_user_id' => "{$receiver_eco_system[moderator_id]}",
                                'objects' => "Stream:Stream",
                                'object_id' => "{$merged_request[id]}",
                                'object_location' => 'eco_merge_requests',
                                'visit_url' => "ecosystem/notification", //to visit after click from top menu and from emails
                                'notif_details' => $notif_details, //details for emails
                            );
                                                                        
                            generate_notification($notif_data, true);
                            
                            
                            // delete other request to merge request table
                            $this->db->where("(caller_eco_sys_id='{$caller_sys_id}' AND receiver_eco_sys_id='{$receiver_sys_id}')");
                            $this->db->delete('eco_merge_requests');
                            
                            $sql = "SELECT * FROM user_notifications WHERE object_id=? ORDER BY id ASC LIMIT 1";
                            $rsl = $this->db->query($sql, array($merged_request['id']));
                            
                            if($rsl->num_rows() > 0){
                                $row = $rsl->row_array();
                                
                                // delete notification
                                $this->db->where('id', $row['id']);
                                $this->db->delete('user_notifications');
                            }
                                                                                
                            $result['status'] = true;
                            $result['message'] = "Request rejected successfully.";
                        }
                        else{
                            $result['message'] = 'Receiver invalid.';    
                        }
                    }
                    else{
                        $result['message'] = 'Caller invalid.';
                    }   
                }
                else{
                    $result['message'] = "Request already accepted.";
                }
            }
            else{
                $result['message'] = "Invalid caller and receiver.";
            }
        }
        
        else{
            $result['message'] = "Login first.";
        }
        
        return $result;
    }
}
