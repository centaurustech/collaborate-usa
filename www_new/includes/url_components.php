<?php
##/ Break URL Components into identifiers for controllers

if(DOC_ROOT=='/')
$REQUEST_URI = @str_ireplace(array(SITE_URL_WWW, SITE_URL, '.html'), '', $_SERVER['REQUEST_URI']);
else
$REQUEST_URI = @str_ireplace(array(SITE_URL_WWW, SITE_URL, DOC_ROOT, '.html'), '', $_SERVER['REQUEST_URI']);


function get_url_comp($REQUEST_URI)
{
    $url_path = @parse_url($REQUEST_URI, PHP_URL_PATH);
    $url_path = @ltrim($url_path, '/');
    $url_path = @rtrim($url_path, '/');
    return $url_path;
}
$url_path = get_url_comp($REQUEST_URI);
$url_comp = @array_diff(@explode('/', $url_path), array(''));
?>