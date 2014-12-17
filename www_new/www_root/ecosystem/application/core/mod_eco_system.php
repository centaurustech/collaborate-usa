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
        
        $sql = "SELECT * FROM users WHERE id=? AND is_blocked=0";
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
    
    public function get_eco_system_by_user($sid = 0, $uid = 0){
        
        $sql = "SELECT * FROM eco_system WHERE moderator_id=? AND id=?";
        $query = $this->db->query($sql, array($uid, $sid));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function get_eco_system($sid = 0){
        
        $sql = "SELECT * FROM eco_system WHERE id=?";
        $query = $this->db->query($sql, array($sid));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function has_river($sid = 0){
        
        $sql = "SELECT * 
                FROM eco_system AS sys1
                INNER JOIN eco_system AS sys2
                ON sys2.`id` = sys1.`parent_id`
                WHERE sys1.`id`=?";
        $query = $this->db->query($sql, array($sid));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function has_ocean($sid = 0){
        
        $sql = "SELECT * 
                FROM eco_system AS sys1
                INNER JOIN eco_system AS sys2
                ON sys2.`id` = sys1.`parent_id`
                WHERE sys1.`id`=?";
        $query = $this->db->query($sql, array($sid));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function is_possible_to_create_river($sid = 0, $uid = 0){
        
        $sql = "SELECT * FROM eco_merge_requests WHERE receiver_eco_sys_id=? AND requested_by=? AND verification_str!=''";
        $query = $this->db->query($sql, array($sid, $uid));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function is_possible_to_create_ocean($sid = 0, $uid = 0){
        
        $sql = "SELECT * FROM eco_merge_requests WHERE receiver_eco_sys_id=? AND requested_by=? AND verification_str!=''";
        $query = $this->db->query($sql, array($sid, $uid));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function is_already_merged($sys_id = 0){
        
        $sql = "SELECT * FROM eco_system WHERE id=? AND parent_id!=0 AND is_admin_blocked=0";
        $rsl = $this->db->query($sql, array($sys_id));
        
        if($rsl->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function is_already_requested($req_id = 0, $caller_sys_id = 0, $receiver_sys_id = 0){
        
        $sql = "SELECT * FROM eco_merge_requests WHERE requested_by=? AND caller_eco_sys_id=? AND receiver_eco_sys_id=?";
        $query = $this->db->query($sql, array($req_id, $caller_sys_id, $receiver_sys_id));
        
        if($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function is_ready_for_river($req_id = 0, $caller_sys_id = 0, $receiver_sys_id = 0){
        $sql = "SELECT * FROM eco_merge_requests WHERE ((caller_eco_sys_id=? OR receiver_eco_sys_id=?) OR (caller_eco_sys_id=? OR receiver_eco_sys_id=?)) AND verification_str!=''";
        $query = $this->db->query($sql, array($caller_sys_id, $receiver_sys_id, $receiver_sys_id, $caller_sys_id));
        
        if($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function is_possible_to_join($req_id = 0, $receiver_sys_id = 0){
        
        $sql = "SELECT emr.*
                FROM eco_merge_requests AS emr
                LEFT JOIN streams_voice AS str
                ON str.`eco_sys_id` = emr.`receiver_eco_sys_id`
                AND emr.`receiver_eco_sys_id`=?
                WHERE emr.`requested_by`=?
                AND str.is_blocked=0
                ORDER BY str.id DESC";
        $query = $this->db->query($sql, array($receiver_sys_id, $req_id));
        
        if($query->num_rows() > 0){
            return false;
        }
        else{
            return true;
        }
    }
    
    public function is_possible_to_join_ocean($req_id = 0, $receiver_sys_id = 0){
        
        $sql = "SELECT emr.*
                FROM eco_merge_requests AS emr
                LEFT JOIN eco_system AS sys
                ON sys.`id` = emr.`receiver_eco_sys_id`
                AND emr.`receiver_eco_sys_id`=?
                WHERE emr.`requested_by`=?
                AND sys.is_admin_blocked=0
                ORDER BY sys.id DESC";
        $query = $this->db->query($sql, array($receiver_sys_id, $req_id));
        
        if($query->num_rows() > 0){
            return false;
        }
        else{
            return true;
        }
    }
    
    public function add_request_to_merge($req_id = 0, $caller_sys_id = 0, $receiver_sys_id = 0){
        
        $result = array("status" => false, "message" => "", "data" => array());
        
        if($this->is_valid_user($req_id)){
            if($this->is_valid_eco_system_id($caller_sys_id) && $this->is_valid_eco_system_id($receiver_sys_id)){
                $data = array(
                    'requested_by' => $req_id,
                    'caller_eco_sys_id' => $caller_sys_id,
                    'receiver_eco_sys_id' => $receiver_sys_id,
                    'added_on' => c_now()
                );
                
                if($this->db->insert('eco_merge_requests', $data)){
                    
                    $sql = "SELECT * FROM eco_merge_requests ORDER BY id DESC LIMIT 1";
                    $rsl = $this->db->query($sql);
                    
                    $result['status'] = true;
                    $result['message'] = "Request added.";
                    $result['data'] = $rsl->row_array();
                }
                else{
                    $result['message'] = "System error.";
                }
            }
        }
        else{
            $result['message'] = "Invalid user.";
        }
        
        return $result;
    }
    
    public function get_merged_request($uid = 0, $key = ""){
        
        $sql = "SELECT * FROM eco_merge_requests WHERE requested_by=? AND verification_str=?";
        $query = $this->db->query($sql, array($uid, $key));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    // get merge request by caller receiver
    public function get_merged_request_by_cr($caller_id = 0, $receiver_id = 0){
        
        $sql = "SELECT * FROM eco_merge_requests WHERE (caller_eco_sys_id=? AND receiver_eco_sys_id=?) OR (caller_eco_sys_id=? AND receiver_eco_sys_id=?)";
        $query = $this->db->query($sql, array($caller_id, $receiver_id, $receiver_id, $caller_id));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
            
    public function is_valid_river_key($uid = 0, $key = ""){
        $sql = "SELECT * FROM eco_merge_requests WHERE requested_by=? AND verification_str=?";
        $query = $this->db->query($sql, array($uid, $key));
        
        if($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function is_valid_ocean_key($uid = 0, $key = ""){
        $sql = "SELECT * FROM eco_merge_requests WHERE requested_by=? AND verification_str=?";
        $query = $this->db->query($sql, array($uid, $key));
        
        if($query->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function get_last_river($uid = 0){
        $sql = "SELECT * FROM eco_system WHERE `moderator_id`=? AND `parent_id`=0 AND `level`=2 AND `is_admin_blocked`=0 ORDER BY id DESC LIMIT 1";
        $query = $this->db->query($sql, array($uid));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function get_last_ocean($uid = 0){
        $sql = "SELECT * FROM eco_system WHERE `moderator_id`=? AND `parent_id`=0 AND `level`=3 AND `is_admin_blocked`=0 ORDER BY id DESC LIMIT 1";
        $query = $this->db->query($sql, array($uid));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function get_eco_merge_request($id = 0){
        $sql = "SELECT * FROM eco_merge_requests WHERE id=?";
        $query = $this->db->query($sql, array($id));
        
        if($query->num_rows() > 0){
            return $query->row_array();
        }
        else{
            return false;
        }
    }
    
    public function get_request_receiver_stream($req_id = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");                
        
        $sql = "SELECT str.*
                FROM streams_voice AS str
                LEFT JOIN eco_merge_requests AS emr
                ON str.`eco_sys_id` = emr.`receiver_eco_sys_id`
                WHERE emr.`id`=?
                AND str.is_blocked=0
                ORDER BY str.id DESC";
                
        $rsl = $this->db->query($sql, array($req_id));
        
        if($rsl->num_rows() > 0){
            $result["status"] = true;
            $result["message"] = "Stream found.";
            $result["data"] = $rsl->row_array();
        }
        else{
            $result["message"] = "Voice stream id {$req_id} invalid or stream not exist's associated this id {$req_id}.";
        }
                
        return $result;
    }
    
    public function get_request_caller_stream($req_id = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");                
            
        $sql = "SELECT str.*
                FROM streams_voice AS str
                LEFT JOIN eco_merge_requests AS emr
                ON str.`eco_sys_id` = emr.`caller_eco_sys_id`
                WHERE emr.`id`=?
                AND str.is_blocked=0
                ORDER BY str.id DESC";
                
        $rsl = $this->db->query($sql, array($req_id));
        
        if($rsl->num_rows() > 0){
            $result["status"] = true;
            $result["message"] = "Stream found.";
            $result["data"] = $rsl->row_array();
        }
        else{
            $result["message"] = "Voice stream id {$req_id} invalid or stream not exist's associated this id {$req_id}.";
        }
                
        return $result;
    }
    
    public function get_stream_by_eco_sys_id($sid = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        if(is_logged_in()){
            
            $sql = "SELECT * FROM streams_voice WHERE eco_sys_id=? AND is_blocked=0";
            $rsl = $this->db->query($sql, array($sid));
            
            if($rsl->num_rows() > 0){
                $result["status"] = true;
                $result["message"] = "Stream found.";
                $result["data"] = $rsl->row_array();
            }
            else{
                $result["message"] = "Stream id {$id} invalid or stream not exist's associated this id {$id}.";
            }    
        }
        else{
            $result["message"] = "Login first.";
        }
                
        return $result;
    }
    
    public function get_single_river($id = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        if(is_logged_in()){
            
            $sql = "SELECT * FROM eco_system WHERE id=? AND is_admin_blocked=0";
            $rsl = $this->db->query($sql, array($id));
            
            if($rsl->num_rows() > 0){
                $result["status"] = true;
                $result["message"] = "River found.";
                $result["data"] = $rsl->row_array();
            }
            else{
                $result["message"] = "River id {$id} invalid or river not exist's associated this id {$id}.";
            }    
        }
        else{
            $result["message"] = "Login first.";
        }
                
        return $result;
    }
    
    public function get_single_ocean($id = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        if(is_logged_in()){
            
            $sql = "SELECT * FROM eco_system WHERE id=? AND is_admin_blocked=0";
            $rsl = $this->db->query($sql, array($id));
            
            if($rsl->num_rows() > 0){
                $result["status"] = true;
                $result["message"] = "ocean found.";
                $result["data"] = $rsl->row_array();
            }
            else{
                $result["message"] = "Ocean id {$id} invalid or ocean not exist's associated this id {$id}.";
            }    
        }
        else{
            $result["message"] = "Login first.";
        }
                
        return $result;
    }
    
    public function is_valid_river($id = 0){
        
        $sql = "SELECT * FROM eco_system WHERE id=? AND is_admin_blocked=0";
        $rsl = $this->db->query($sql, array($id));
        
        if($rsl->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function get_child_streams($id = 0){
        
        $sql = "SELECT *
                FROM eco_system AS sys
                INNER JOIN streams_voice AS str
                ON str.`eco_sys_id`=sys.`id`
                WHERE sys.`parent_id`=?
                ORDER BY sys.`id` DESC";
        $rsl = $this->db->query($sql, array($id));
        
        if($rsl->num_rows() > 0){
            return $rsl->result_array();
        }
        else{
            return false;
        }
    }
    
    public function is_valid_ocean($id = 0){
        
        $sql = "SELECT * FROM eco_system WHERE id=? AND is_admin_blocked=0";
        $rsl = $this->db->query($sql, array($id));
        
        if($rsl->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function get_child_rivers($id = 0){
        
        $sql = "SELECT * FROM eco_system WHERE `parent_id`=? AND `level`=2 ORDER BY id DESC";
        $rsl = $this->db->query($sql, array($id));
        
        if($rsl->num_rows() > 0){
            return $rsl->result_array();
        }
        else{
            return false;
        }
    }
}
