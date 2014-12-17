<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Rivers extends Voice {
    
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
        
        $this->log("Controller MY_Rivers loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
        
        $data = array(
            "heading" => "MY RIVERS",
            "title" => "My Rivers"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        $this->load_header($data);
        $this->load->view('my_rivers', $this->page_data);
        $this->load_footer();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_My_Rivers');
        $this->load->model('Mod_Sidebar');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function my_rivers_ajax(){
        
        $start = $this->input->post('s', true);
        $limit = (isset($this->_config['my_river_per_page_limit'])) ? $this->_config['my_river_per_page_limit'] : 10;
        
        if( ! is_numeric($start)){
            $start = 0;
        }
        
        $bundle = array("start" => $start, "limit" => $limit);
        
        $rivers = $this->Mod_My_Rivers->get_my_rivers($bundle);
        
        if($rivers["is_data"]){
            
            $voice_to_html = $this->Mod_My_Rivers->mr_to_html($rivers["data"]);
            
            if($voice_to_html["status"] == true){
                $rivers["data"] = $voice_to_html["data"];
            }
            
            $rivers["message"] = $voice_to_html["message"];
        }
            
        echo @json_encode($rivers);
    }
}
