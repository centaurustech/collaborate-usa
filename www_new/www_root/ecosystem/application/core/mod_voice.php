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
    
    public function get_voices($uid = 0){
        
    }
    
}