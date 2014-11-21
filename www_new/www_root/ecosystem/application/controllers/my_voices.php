<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Voices extends Voice {
    
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
        
        $this->log("Controller MY_Voices loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
                
        $this->page_data["voice_categories"] = $this->Mod_My_Voices->get_voice_categories();
        
        $this->load_header();
        $this->load->view('my_voices', $this->page_data);
        $this->load_footer();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_My_Voices');        
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function my_voices_ajax(){
        
        $start = $this->input->post('s', true);
        $limit = (isset($this->_config['my_voice_per_page_limit'])) ? $this->_config['my_voice_per_page_limit'] : 10;
        
        if( ! is_numeric($start)){
            $start = 0;
        }
        
        $sql = "SELECT * FROM user_voice ORDER BY id DESC LIMIT {$start}, {$limit}";
        
        $bundle = array("sql" => $sql);
        
        $voices = $this->Mod_My_Voices->get_my_voices($bundle);
                                
        echo @json_encode($voices);
    }
}
