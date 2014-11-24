<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voices extends Voice {
    
    /////////////////////////////////////////////////
    // PRIVATE VAR
    /////////////////////////////////////////////////
    
    private $page_data = array();
    private $_config;
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        
        // get config and store to private var
        $this->_config = c_get_config();
        
        // load all my voice models
        $this->_load_models();
        
        $this->log("Controller Voices loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    //public function index(){
        
        
    //}
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_Voices');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function ajax_vote_action(){
        
        $voc_id = $this->input->post('vocid', true);
        $vot_va = $this->input->post('votva', true);
                
        if($vot_va == 1){
            $vot_va = $this->_config['vote_up'];
        }
        else{
            $vot_va = $this->_config['vote_down'];
        }
        
        if(! is_numeric($voc_id)){
            $voc_id = 0;
        }
        
        $result = $this->Mod_Vote->vote($voc_id, $vot_va);        
        echo @json_encode($result);                      
    }
}
