<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mod_Voice
 * 
 * @package 
 * @author Syed Owais Ali
 * @copyright 2014
 * @version $Id$
 * @access public
 */
class Mod_Voice extends Mod_Eco_System {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_Voice loaded.");
    }
    
    /////////////////////////////////////////////////
    // PROTECTED FUNCTIONS
    /////////////////////////////////////////////////
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function get_voices($bundle = array()){
        
        // set return result
        $result = array("status" => true, "message" => "", "is_data" => false, "data" => array());
        
        $sql = c_pick_param($bundle, "sql", "SELECT * FROM user_voices");
        $query = $this->db->query($sql);
        
        // check query have data
        if($query->num_rows() > 0){
            
            $result["message"] = "Data successfully load.";
            $result["is_data"] = true;
            $result["data"] = $query->result_array();   
        }
        else{
            $result["message"] = "There is no data available.";
        }
        
        // return result
        return $result;
    }
    
    public function is_valid_voice($id = 0){
        
        $sql = "SELECT * FROM user_voices WHERE id=?";
        $rsl = $this->db->query($sql, array($id));
        
        if($rsl->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function get_single_voice($id = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        if(is_logged_in()){
            
            $sql = "SELECT * FROM user_voices WHERE id=?";
            $rsl = $this->db->query($sql, array($id));
            
            if($rsl->num_rows() > 0){
                $result["status"] = true;
                $result["message"] = "Voice found.";
                $result["data"] = $rsl->result_array();
            }
            else{
                $result["message"] = "Voice id {$id} invalid or voice not exist's associated this id {$id}.";
            }    
        }
        else{
            $result["message"] = "Login first.";
        }
                
        return $result;
    }
}