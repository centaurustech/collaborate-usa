<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Eco_Member extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // PRIVATE VAR
    /////////////////////////////////////////////////
    
    private $_config;

    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_Eco_Member loaded.");
        
        $this->_config = c_get_config();
        // load models
        $this->_load_models();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){        
        
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function is_eco_member($eco_sys_id = 0, $user_id = 0){        
        
        $sql = "SELECT * FROM eco_members WHERE eco_sys_id=? AND user_id=?";
        $rsl = $this->db->query($sql, array($eco_sys_id, $user_id));
        
        if($rsl->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function get_eco_member($eco_sys_id = 0, $user_id = 0){
        // set return result
        $result = array("status" => false, "message" => "", "data" => array());
        
        $sql = "SELECT * FROM eco_members WHERE eco_sys_id=? AND user_id=?";
        $rsl = $this->db->query($sql, array($eco_sys_id, $user_id));
        
        if($rsl->num_rows() > 0){
            $result['status'] = true;
            $result['data'] = $rsl->row_array();
            $result['message'] = 'Data found.';
        }
        else{
            $result['message'] = 'No data found.';
        }
        
        return $result;
    }
    
    public function mark_fuf($sid = 0, $uid = 0){
        
        $sql = "SELECT * FROM eco_members WHERE eco_sys_id=? AND user_id=?";
        $rsl = $this->db->query($sql, array($sid, $uid));
        
        if($rsl->num_rows() > 0){
            $sql = "DELETE FROM eco_members WHERE eco_sys_id=? AND user_id=?";
            $this->db->query($sql, array($sid, $uid));
            return true;
        }
        else{
            $data = array(
                "eco_sys_id" => $sid,
                "user_id" => $uid,
                "joined_on" => c_now()
            );
            
            $this->db->insert('eco_members', $data);
            return true;
        }
        
        return false;
    }
}