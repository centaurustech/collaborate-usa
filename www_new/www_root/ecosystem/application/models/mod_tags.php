<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Tags extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_Tag loaded.");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function get_tags($t_ids = ""){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => array());                
        
        $id = @json_decode($t_ids);
        
        if(count($id) > 0){
            
            for($i = 0; $i < count($id); $i++){
                
                $sql = "SELECT * FROM voice_tags WHERE id=?";
                $rsl = $this->db->query($sql, array($id[$i]));
                
                if($rsl->num_rows() > 0){
                    $result["status"] = true;                    
                    $result["message"] = "Tags found.";
                    $rlt = $rsl->row_array();
                    array_push($result["data"], $rlt["tag"]);
                }      
            }                        
        }        
        else{
            $result["message"] = "tags id invalid or tags not exist's associated this id.";
        }
        return $result;
    }
    
}
