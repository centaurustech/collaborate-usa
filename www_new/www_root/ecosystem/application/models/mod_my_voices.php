<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_My_Voices extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_My_Voices loaded.");
    }
}
