<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_My_Voices extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_My_Voices loaded.");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function get_my_voices($bundle = array()){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        // get valid logging id
        $uid = $this->get_logged_uid();
        
        // first user is valid
        if($uid){
            
            // get query param
            $sql = c_pick_param($bundle, "sql", "SELECT * FROM user_voices");
            $result["message"] = $sql;  
        }
        
        // user is not valid
        else{
            $result["message"] = "User ID is not valid";
        }
        
        // return result with info
        return $result;
    }
}
