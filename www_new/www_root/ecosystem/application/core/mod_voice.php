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
        
        $sql = c_pick_param($bundle, "sql", "SELECT * FROM user_voices WHERE is_blocked=0");
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
        
        $sql = "SELECT * FROM user_voices WHERE id=? AND is_blocked=0";
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
            
            $sql = "SELECT * FROM user_voices WHERE id=? AND is_blocked=0";
            $rsl = $this->db->query($sql, array($id));
            
            if($rsl->num_rows() > 0){
                $result["status"] = true;
                $result["message"] = "Voice found.";
                $result["data"] = $rsl->row_array();
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
    
    public function get_voice_category($id = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");                
            
        $sql = "SELECT * FROM voice_categories WHERE id=?";
        $rsl = $this->db->query($sql, array($id));
        
        if($rsl->num_rows() > 0){
            $result["status"] = true;
            $result["message"] = "Category found.";
            $result["data"] = $rsl->row_array();
        }
        else{
            $result["message"] = "Voice category id {$id} invalid or category not exist's associated this id {$id}.";
        }
                
        return $result;
    }
    
    
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
    
    public function get_single_stream($id = 0){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        if(is_logged_in()){
            
            $sql = "SELECT * FROM streams_voice WHERE id=? AND is_blocked=0";
            $rsl = $this->db->query($sql, array($id));
            
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
    
    public function is_valid_stream($id = 0){
        
        $sql = "SELECT * FROM streams_voice WHERE id=? AND is_blocked=0";
        $rsl = $this->db->query($sql, array($id));
        
        if($rsl->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function get_comments_data($bundle = array()){
        
        // set return result
        $result = array("status" => true, "message" => "", "is_data" => false, "data" => array());
        
        $sql = c_pick_param($bundle, "sql", "SELECT * FROM eco_discussion_comments");
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
    
    public function is_more_data($sql = ""){
        
        $rsl = $this->db->query($sql);
        if($rsl->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function is_my_stream($sid = 0, $uid = 0){
        
        $sql = "SELECT * FROM streams_voice WHERE id=? AND user_id=? AND is_blocked=0";
        $rsl = $this->db->query($sql, array($sid, $uid));
        
        if($rsl->num_rows() > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function filter_tags($tags = array()){
        
        // set return result
        $result = array("status" => true, "message" => "", "data" => array());
        
        foreach($tags as $tag){
            
            $sql = "SELECT * FROM voice_tags WHERE tag=?";
            $query = $this->db->query($sql, array(strtolower($tag)));
            
            // if tag is already found simple get this id
            if($query->num_rows() > 0){
                
                $rsl = (object)$query->row_array();
                
                // push tag id to return tags id array
                array_push($result['data'], $rsl->id);
            }
            
            // else tag is not found entry this tag
            else{
                
                $user_id = $this->get_logged_uid();
                if($user_id){
                    
                    $data = array('user_id' => $user_id, 'tag' => $tag, 'added_on' => c_now());
                    $this->db->insert('voice_tags', $data);
                    
                    $sql = "SELECT * FROM voice_tags ORDER BY id DESC LIMIT 1";
                    $query = $this->db->query($sql);
                    
                    if($query->num_rows() > 0){
                        $row = (object)$query->row_array();
                    
                        // push tag id to return tags id array                    
                        array_push($result['data'], $row->id);    
                    }                    
                }
                else{
                    
                    // invalid user login
                    $result['status'] = false;
                    $result['message'] = 'You are not valid user.';
                }
            }
        }
        
        // return tags id
        return $result;
    }
}