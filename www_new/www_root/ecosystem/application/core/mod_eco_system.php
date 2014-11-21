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
        $this->log("Model Mod_Eco_System loaded.");
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
    // PROTECTED FUNCTIONS
    /////////////////////////////////////////////////
    
    /**
     * Mod_Eco_System::log()
     * 
     * @param mixed $msg
     * @param string $level
     * @param bool $php_error
     * @return void
     */
    protected function log($msg, $level = 'info', $php_error = FALSE){
        $this->cusa_log->log($level, $msg, $php_error);
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
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
                    return $user_id;                    
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
            return $uid;
        }
        else{
            return false;
        }
    }
    
    
    /**
     * Mod_Eco_System::get_voice_categories()
     * 
     * @param mixed $id
     * @return
     */
    public function get_voice_categories($id = null){
        
        // store all categories data
        $result = array("is_data" => false, "data" => array());
        
        // set query to null
        $query = null;
        
        // check is null or not set
        if($id == null){
            
            // query for select all voice catgories
            $sql = "SELECT * FROM voice_categories";
            
            // exec sql and store res to query
            $query = $this->db->query($sql);
        }
        
        // else select category by category id
        else{
            
            if($this->is_valid_voice_cat($id)){
                // query for select voice category by id
                $sql = "SELECT * FROM voice_categories WHERE id=?";
                
                // exec sql and store res to query
                $query = $this->db->query($sql, array($id));
            }
            else{
                return $result;
            }
        }
        
        // check first query is not null
        if($query != null){
            
            // check category count if count is greater than 0
            if($query->num_rows() > 0){
                
                // set found data flag to true
                $result["is_data"] = true;
                
                // fetch and store data to result
                $result["data"] = $query->result_array();
            }
        }
        
        // query is error return
        else{
            
            // write log
            $this->log('function get_voice_categories; var $query is null return file name mod_voice.php in application/core directory', "debug");                    
        }
        
        // return result
        return $result;
    }
    
    /**
     * Mod_Eco_System::is_valid_voice_cat()
     * 
     * @param integer $id
     * @return
     */
    public function is_valid_voice_cat($id = 0){
        
        // query for select voice category by id
        $sql = "SELECT * FROM voice_categories WHERE id=?";
        
        // exec sql and store res to query
        $query = $this->db->query($sql, array($id));
        
        // check valid category id
        if($query->num_rows() > 0){
            return true;
        }
        
        // invalid category id
        else{
            return false;
        }
    }
    
    public function get_folder_by_uid($uid = 0){
        
        // user is valid
        if($this->is_valid_user($uid)){
            
        }
        
        // user not valid
        else{
            return false;
        }
    }
}
