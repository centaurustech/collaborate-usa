<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Vote_Voices extends Voice {
    
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
        
        $this->log("Controller MY_Vote_Voices loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
        
        $data = array(
            "heading" => "MY VOTES",
            "title" => "My Votes"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        $this->load_header($data);
        $this->load->view('my_vote_voices', $this->page_data);
        $this->load_footer();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_My_Vote_Voices'); 
        $this->load->model('Mod_Sidebar');       
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function my_vote_voices_ajax(){
        
        $start = $this->input->post('s', true);
        $limit = (isset($this->_config['my_voice_per_page_limit'])) ? $this->_config['my_voice_per_page_limit'] : 10;
        
        if( ! is_numeric($start)){
            $start = 0;
        }
        
        $bundle = array("start" => $start, "limit" => $limit);
        
        $voices = $this->Mod_My_Vote_Voices->get_my_vote_voices($bundle);
        
        if($voices["is_data"]){
            
            $voice_to_html = $this->Mod_My_Vote_Voices->mvv_to_html($voices["data"]);
            
            if($voice_to_html["status"] == true){
                $voices["data"] = $voice_to_html["data"];
            }
            
            $voices["message"] = $voice_to_html["message"];
        }
            
        echo @json_encode($voices);               
    }
}
