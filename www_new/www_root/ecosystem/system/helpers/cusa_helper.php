<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// check is any user logged in
if( ! function_exists('is_logged_in')){
    
    function is_logged_in(){
        
        $CI =& get_instance();
    
        $is_logged_in = $CI->session->userdata('user_data');
        
        if( !isset($is_logged_in) || $is_logged_in != true){
            $dummy_logged_in_data = array(
                "uid" => 2, 
                "uemail" => "dp.owaisali@gmail.com", 
                "screen_name" => "Syed Owais Ali",
                "profile_pic" => "apple_1.png",
                "fname" => "Syed",
                "mname" => "Owais",
                "lname" => "Ali",
                "company" => "Meritocracy"
            );
            
            $CI->session->set_userdata('user_data', $dummy_logged_in_data);            
            return true;
        }
        else{            
            return true;
        }
    }
}

// get assets url
if( ! function_exists('c_get_assets_url')){
    
    function c_get_assets_url(){
        return base_url() . 'assets/';
    }
}

// get now date time
if( ! function_exists('c_now')){
    function c_now(){
        return date("Y-m-d H:i:s");
    }
}

// get configs
if( ! function_exists('c_get_config')){
    function c_get_config(){
        $CI =& get_instance();
        return $CI->config->config;
    }
}