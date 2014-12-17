<?php
@session_start();

if(in_array($_SERVER['SERVER_NAME'], array('cusa-local', 'localhost'))==false) //SERVER
{
    //turn off all errors when its on Production
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', "On");
    ini_set('display_startup_errors', true);

    #/*##/ ini force as php.ini is not working on godaddy
    ini_set('short_open_tag', 'On');
    ini_set('register_globals', 'Off');
    ini_set('magic_quotes_gpc', 'Off');
    ini_set('memory_limit', '512M');
    ini_set('upload_max_filesize', '5M');
    ini_set('post_max_size', '8M');
    ini_set('max_execution_time', '2400');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    #*/-


    $host = 'localhost';
	$user = 'collabus_cUSA01w';
	$pass = 'WsAh5OrXB}s}';
    $dbname = 'collabus_cusa_new';
	$cn1 = $cn = @mysql_connect($host, $user, $pass);
	$db = @mysql_select_db($dbname, $cn);

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {define('URL_PREFIX', 'https');}
    else {define('URL_PREFIX', 'http');}

    //define('SITE_URL', URL_PREFIX.'://new.collaborateusa.com/'); //www.collaborateusa.com/www_new/www_root/
    //define('SITE_URL', URL_PREFIX.'://166.62.35.176/~collabusanew/www_new/www_root/');
    define('SITE_URL', URL_PREFIX.'://www.collaborateusa.com/');

    //define('SITE_URL_WWW', URL_PREFIX.'://collaborateusa.com/'); //collaborateusa.com/www_new/www_root
    //define('SITE_URL_WWW', URL_PREFIX.'://166.62.35.176/~collabusanew/www_new/www_root/');
    define('SITE_URL_WWW', URL_PREFIX.'://collaborateusa.com/');

    define('TOP_ROOT', '/www/');
    define('DOC_ROOT', '/');//www_new/www_root/ or /home/rancan1102/www_new/www_root/
    //define('DOC_ROOT', '/~collabusanew/www_new/www_root/');
    #define('DOC_ROOT', '/www_new/www_root/');

    define('DOC_ROOT_ADMIN', '/back_adm/');//www_new/www_root/back_adm/
    //define('DOC_ROOT_ADMIN', '/~collabusanew/www_new/www_root/back_adm/');
    #define('DOC_ROOT_ADMIN', '/www_new/www_root/back_adm/');


    define('AUTO_COMPLETE', 'autocomplete="off"');
    define('support_em', 'support@collaborateusa.com');
    define('mem_support_em', 'membersupport@collaborateusa.com');
    define('SERVER_TYPE', 'LIVE');


    ### Special Code to stop get_magic_quotes_gpc
    #/ You will have to manually do it for parse_str function as it may insert magic quote there automatically
    function stop_magic_quotes($in)
    {
        $out = $in;

    	if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
        {
            if(is_array($out))
            {
                foreach($out as $k=>$v)
                {
                    $v = stop_magic_quotes($v);
                    $out[$k] = $v;
                }
            }
            else
            {
                $out = stripslashes($out);
            }
        }

    	return $out;
    }//end func................

    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
    {
        $_GET = array_map('stop_magic_quotes', $_GET);
        $_POST = array_map('stop_magic_quotes', $_POST);
    }//end if....
    #-

}
else //LOCALHOST
{
	error_reporting(E_ALL);

    $host = 'localhost';
	$user = 'root';
	$pass = '';
	$dbname = 'cusa';

	$cn1 = $cn = @mysql_connect($host, $user, $pass);
	$db = @mysql_select_db($dbname, $cn);

    define('SITE_URL', 'http://cusa-local/');
    define('SITE_URL_WWW', 'http://cusa-local/');

    define('TOP_ROOT', '/www_root/');
    define('DOC_ROOT', '/');
    define('DOC_ROOT_ADMIN', '/back_adm/');

    define('AUTO_COMPLETE', '');
    define('support_em', 'support@collaborateusa.com');
    define('mem_support_em', 'membersupport@collaborateusa.com');
    define('SERVER_TYPE', 'LOCAL');
}

///////////////////////////////////////////////////////////////

if(!$db){
//echo mysql_error();
die("<br /><strong>DATABSE CONNECTION ERROR !!</strong>");
} else {
@mysql_query("SET CHARACTER SET utf8;");
}

///////////////////////////////////////////////////////////////

static $browser;
$browser = $_SERVER['HTTP_USER_AGENT'];
if((stristr($browser, 'msie')!=false) || (stristr($browser, 'trident')!=false)) {$browser = 'msie';}
if(stristr($browser, 'chrome')!=false) {$browser = str_ireplace('safari', '', $browser);}
define('BROWSER', $browser);

$consts = get_defined_constants();

//////////////////////////////////////////////////////////////

@$_SESSION['global_vars'] = array();
$_SESSION['engine'] = 'generic';
?>