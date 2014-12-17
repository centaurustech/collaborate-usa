<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Oceans extends Voice {
    
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
        
        $this->log("Controller MY_Oceans loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
        
        $data = array(
            "heading" => "MY Oceans",
            "title" => "My Oceans"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        $this->load_header($data);
        $this->load->view('my_oceans', $this->page_data);
        $this->load_footer();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_My_Oceans');
        $this->load->model('Mod_Sidebar');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function my_oceans_ajax(){
        
        $start = $this->input->post('s', true);
        $limit = (isset($this->_config['my_ocean_per_page_limit'])) ? $this->_config['my_ocean_per_page_limit'] : 10;
        
        if( ! is_numeric($start)){
            $start = 0;
        }
        
        $bundle = array("start" => $start, "limit" => $limit);
        
        $oceans = $this->Mod_My_Oceans->get_my_oceans($bundle);
        
        if($oceans["is_data"]){
            
            $voice_to_html = $this->Mod_My_Oceans->mo_to_html($oceans["data"]);
            
            if($voice_to_html["status"] == true){
                $oceans["data"] = $voice_to_html["data"];
            }
            
            $oceans["message"] = $voice_to_html["message"];
        }
            
        echo @json_encode($oceans);
    }
}
