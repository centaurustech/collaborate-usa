<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voice extends Eco_System {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    public function __construct(){
        parent::__construct();
        $this->log("Controller Voice Loaded.");
    }
}