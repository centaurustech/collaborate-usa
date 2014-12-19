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
    
    public function get_recent_members($limit = 10){
        
        $result = array("status" => false, "message" => "", "data" => array());
        
        if(is_logged_in()){
            
            $sql = "SELECT user.*, 

                    u_info.`id` AS ui_id, 
                    u_info.`secret_question_id` AS secret_question_id, 
                    u_info.`secret_answer` AS secret_answer, 
                    u_info.`country_code` AS country_code,
                    u_info.`state` AS state,
                    u_info.`city` AS city,
                    u_info.`address_ln_1` AS address_ln_1,
                    u_info.`address_ln_2` AS address_ln_2,
                    u_info.`zip` AS zip,
                    u_info.`phone_number` AS phone_number,
                    u_info.`total_patronage_points` AS total_patronage_points
                    
                    FROM `users` AS `user`
                    LEFT JOIN `user_info` AS u_info
                    ON u_info.`user_id` = user.`id`
                    ORDER BY user.`id` DESC
                    LIMIT ?";
                    
            $rsl = $this->db->query($sql, array($limit));
            
            if($rsl->num_rows() > 0){
                $result['status'] = true;
                $result['message'] = "User found.";
                $result['data'] = $rsl->result_array();
            }
            else{
                $result['message'] = "No user found.";
            }
        }
        else{
            $result['message'] = "Login first.";    
        }
        
        return $result;
    }
    
}
