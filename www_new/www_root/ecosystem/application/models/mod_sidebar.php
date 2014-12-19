<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Sidebar extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // PRIVATE VAR
    /////////////////////////////////////////////////
    
    private $_config;
    private $page_data = array();

    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_Sidebar loaded.");
        
        $this->_config = c_get_config();
        // load models
        $this->_load_models();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){        
        $this->load->model("Mod_User");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function get_default_sidebar(){
        
        // set return result
        $result = array("status" => true, "message" => "", "data" => "");
        
        $recent_users = $this->Mod_User->get_recent_members(8);
        $this->page_data['recent_users'] = $recent_users['data'];
        
        $other_random_stream = $this->get_other_random_stream();
        $this->page_data['is_or_stream'] = $other_random_stream['status'];
        $this->page_data['or_stream'] = $other_random_stream['data'];
        
        $other_random_river = $this->get_other_random_river();
        $this->page_data['is_or_river'] = $other_random_river['status'];
        $this->page_data['or_river'] = $other_random_river['data'];
        
        $result['data'] = $this->load->view("default_sidebar", $this->page_data, true);
        
        return $result;
    }    
}