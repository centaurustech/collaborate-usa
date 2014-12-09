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
                "profile_pic" => "v.jpg",
                "fname" => "Syed",
                "mname" => "Owais",
                "lname" => "Ali",
                "company" => "Meritocracy"
            );
            
            $CI->session->set_userdata('user_data', $dummy_logged_in_data);
            return true;
        }
        else{
            $dummy_logged_in_data = array(
                "uid" => 2,
                "uemail" => "dp.owaisali@gmail.com", 
                "screen_name" => "Syed Owais Ali",
                "profile_pic" => "v.jpg",
                "fname" => "Syed",
                "mname" => "Owais",
                "lname" => "Ali",
                "company" => "Meritocracy"
            );
            
            $CI->session->set_userdata('user_data', $dummy_logged_in_data);
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

// pick param
if( ! function_exists('c_pick_param')){
    
    function c_pick_param($bundle = array(), $param, $default){
        return (isset($bundle[$param])) ? $bundle[$param] : $default;
    }
}

// find plane url and make to link
if( ! function_exists('make_url_to_link')){
    
    function make_url_to_link($text = ""){
        $text = preg_replace('$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', $text." ");                    
        $text = preg_replace('$(\s|^)(www\.[a-z0-9_./?=&-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$2"  target="_blank">$2</a> ', $text." ");
        return $text;
    }
}

// since time
if(! function_exists('c_get_time_elapsed')){
    function c_get_time_elapsed($time){

        $time = time() - $time; // to get the time since that moment
    
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
    
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'') . ' ago';
        }
    }
}

// manage name
if(! function_exists('manage_name')){
    function manage_name($user){
        
        $name = $user['screen_name'];
        
        switch($user['identify_by']){
            case "full_name":
                $name = $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name'];
                break;
                
            case "screen_name":
                $name = $user['screen_name'];
                break;
                
            case "company_name":
                $name = $user['company_name'];
                break;
        }
        
        return $name;
    }
}