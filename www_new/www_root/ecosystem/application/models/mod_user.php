<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_User extends Mod_Eco_System {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_User loaded.");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function get_user($id = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => array());                
        
        if(is_logged_in()){
            
            $sql = "SELECT * FROM users WHERE id=?";
            $query = $this->db->query($sql, array($id));
            
            if($query->num_rows() > 0){
                $result['status'] = true;
                $result['message'] = 'User found.';
                $result['data'] = $query->row_array();
                
                $result['data']['name'] = manage_name($result['data']);
            }
            else{
                $result['message'] = "Invalid user id";
            }
        }
        else{
            $result['message'] = "Invalid user login.";
        }
        
        return $result;
    }
    
}
