<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mod_Eco_System
 * 
 * @package 
 * @author Syed Owais Ali
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Mod_Eco_System extends CI_Model {
    
    /////////////////////////////////////////////////
    // VARIABLES
    /////////////////////////////////////////////////
    
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    /**
     * Mod_Eco_System::__construct()
     * 
     * @return void
     */
    public function __construct(){
        parent::__construct();
        $this->_load_library();
        $this->_load_models();
        
        $this->log("Model Mod_Eco_System loaded.");
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        //$this->load->model('Mod_Vote');
    }
    
    /////////////////////////////////////////////////
    // LOAD LIBRARIES
    /////////////////////////////////////////////////
    
    /**
     * Mod_Eco_System::_load_library()
     * 
     * @return void
     */
    private function _load_library(){
        $params = array('is_cusa_log' => $this->config->config["cusa_log"]);
        $this->load->library('cusa_log', $params);
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    /**
     * Mod_Eco_System::log()
     * 
     * @param mixed $msg
     * @param string $level
     * @param bool $php_error
     * @return void
     */
    public function log($msg, $level = 'info', $php_error = FALSE){
        $this->cusa_log->log($level, $msg, $php_error);
    }
    
    /**
     * Mod_Eco_System::get_logged_uid()
     * 
     * @return
     */
    public function get_logged_uid(){
        
        // check whether user is logged on
        if(is_logged_in()){
            
            $userdata = $this->session->userdata('user_data');
            
            // confirm user data is set to session
            if($userdata != false){
                $user_id = $this->is_valid_user($userdata["uid"]);
                
                // check is valid user id
                if($user_id){
                    return $userdata["uid"];                    
                }
                
                // user is not valid this is invalid or unknown user
                else{
                    // write log
                    $this->log('User Id is not valid or unknown kindly check this; function get_logged_uid; var $userdata; file application/core/mod_eco_sysetm.php', "debug");
                    return false;
                }
            }
            
            // user data not store in session
            else{
                
                // write log
                $this->log('User Data not set in user data session; function get_logged_uid; var $userdata; file application/core/mod_eco_sysetm.php', "debug");
                return false;
            }
        }
        
        // opp's user is not login
        else{
            
            // write log
            $this->log('User is not logged in; function get_logged_uid; file application/core/mod_eco_sysetm.php', "debug");
            return false;
        }
    }
    
    /**
     * Mod_Eco_System::is_valid_user()
     * 
     * @param integer $uid
     * @return
     */
    public function is_valid_user($uid = 0){
        
        $sql = "SELECT * FROM users WHERE id=?";
        $query = $this->db->query($sql, array($uid));
        
        if($query->num_rows() > 0){
            $user = $query->row_array();
            $user['name'] = manage_name($user);
            return $user;
        }
        else{
            return false;
        }
    }
    
    public function is_valid_eco_system_id($sid = 0){
        
        $sql = "SELECT * FROM eco_system WHERE id=?";
        $query = $this->db->query($sql, array($sid));
        
        if($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    /*
    public function get_folder_by_uid($uid = 0){
        
        // user is valid
        if($this->is_valid_user($uid)){
            
        }
        
        // user not valid
        else{
            return false;
        }
    }
    */
}
