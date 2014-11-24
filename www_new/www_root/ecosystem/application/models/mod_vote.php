<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Vote extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_Vote loaded.");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function vote($vocid = 0, $votval = ''){
        
        $result = array("status" => false, "message" => "Login first.", "already_casted" => false, "data" => array());
        
        // check login
        if(is_logged_in()){
            
            // check valid login user
            $uid  = $this->get_logged_uid();
            $user = $this->is_valid_user($uid);            
            if($user){
                
                // check valid voice
                if($this->is_valid_voice($vocid)){
                    
                    // check already vote casted with no error
                    $rlt = $this->is_vote_casted($uid, $vocid);
                    if($rlt["status"] == true){
                        
                        // check no cast
                        if($rlt["already_casted"] == false){
                            
                            $result["message"] = $rlt["message"];
                            $result["data"]["user"] = $rlt["data"]["user"];
                            
                            $vrlt = $this->vote_cast($uid, $vocid, $votval);
                            
                            $result["status"] = $vrlt["status"];
                            $result["message"] = $vrlt["message"];
                            
                            // check successfull vote cast
                            if($vrlt["status"] == true){
                                $result["data"]["vote"] = $vrlt["data"]["vote"];
                                $result["data"]["voice"] = $vrlt["data"]["voice"];
                            }
                            
                        }
                        
                        // else already casted
                        else{
                            $result["message"] = $rlt["message"];
                            $result["data"]["user"] = $rlt["data"]["user"];
                        }
                    }
                    
                    else{
                        $result["message"] = $rlt["message"];
                        $result["data"]["user"] = $rlt["data"]["user"];
                    }
                }
                
                // invalid voice
                else{
                    $result["message"] = "Invalid voice.";
                }
            }
            
            // invalid user login
            else{
                $result["message"] = "Unathorized login.";
            }
        }
        
        return $result;
    }
    
    public function vote_cast($uid = 0, $vid = 0, $vval = ""){
        $result = array("status" => false, "message" => "", "data" => array());
        
        // check valid user
        $user = $this->is_valid_user($uid);
        if($user){
            
            // check valid voice
            if($this->is_valid_voice($vid)){
                
                // check vote value
                if($this->is_valid_vote_value($vval)){
                    
                    $sql = "SELECT * FROM voices_votes WHERE user_id=? AND voice_id=?";
                    $rsl = $this->db->query($sql, array($uid, $vid));
                    
                    // check already vote casted
                    if($rsl->num_rows() < 1){
                        $data = array(
                            "voice_id" => $vid, 
                            "user_id" => $uid, 
                            "vote_value" => $vval, 
                            "voted_on" => c_now()
                        );
                        
                        if($this->db->insert("voices_votes", $data)){
                            $voice = $this->get_single_voice($vid);
                            $result["message"] = "Vote successfully cast.";
                            $result["status"] = true;
                            $result["data"]["vote"] = $this->get_last_vote();
                            $result["data"]["voice"] = $voice["data"];
                        }
                        else{
                            $result["message"] = "vote insertion failed.";
                        }
                    }
                    else{
                        $result["message"] = "Already vote casted.";
                    }    
                }
                else{
                    $result["message"] = "Invalid vote value.";
                }                
            }
            else{
                $result["message"] = "{$vid} id can't exists in voices";
            }
        }
        else{
            $result["message"] = "Invalid user login.";
        }
        
        return $result;
    }
    
    public function get_last_vote(){
        $sql = "SELECT * FROM voices_votes ORDER BY id DESC LIMIT 1";
        $rsl = $this->db->query($sql);
        
        return $rsl->result_array();
    }
    
    public function is_vote_casted($uid = 0, $vid = 0){
        
        $result = array("status" => false, "message" => "", "data" => array(), "already_casted" => false);
        
        // check valid user
        $user = $this->is_valid_user($uid);
        if($user){
            
            // check valid voice
            if($this->is_valid_voice($vid)){
                $sql = "SELECT * FROM voices_votes WHERE user_id=? AND voice_id=?";
                $rsl = $this->db->query($sql, array($uid, $vid));
                
                // check already vote casted
                if($rsl->num_rows() > 0){
                    $result["message"] = "Already vote casted.";
                    $result["data"]["user"] = $user;  
                    $result["status"] = false;
                    $result["already_casted"] = true;
                }
                else{
                    $result["status"] = true;                    
                    $result["message"] = "Yes u can.";
                    $result["data"]["user"] = $user; 
                }
            }
            else{
                $result["message"] = "{$vid} id can't exists in voices";
            }
        }
        else{
            $result["message"] = "Invalid user login.";
        }
        
        return $result;
    }
    
    public function is_valid_vote_value($val = ""){
        return ($val == "i_see" || $val == "i_dont_see") ? true : false;
    }
}
