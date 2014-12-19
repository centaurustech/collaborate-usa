<?php
define('CONTEXT_DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT']); //$_SERVER['CONTEXT_DOCUMENT_ROOT']
if(in_array($_SERVER['SERVER_NAME'], array('cusa-local', 'localhost'))==false){
define('MAIN_ROOT', str_replace('/www', '', CONTEXT_DOCUMENT_ROOT));
} else {
define('MAIN_ROOT', str_replace('/www_root', '', CONTEXT_DOCUMENT_ROOT));
}

require_once(MAIN_ROOT.'/includes/config.php');

include_once(MAIN_ROOT.'/includes/format_str.php');
include_once(MAIN_ROOT.'/includes/func_1.php');
include_once(MAIN_ROOT.'/includes/db_lib.php');
include_once(MAIN_ROOT.'/includes/model_main.php');

include_once(MAIN_ROOT.'/includes/email_templates.php');
include_once(MAIN_ROOT.'/includes/send_mail.php');
include_once(MAIN_ROOT.'/includes/func_time.php');
include_once(MAIN_ROOT.'/includes/notif_func.php');
include_once(MAIN_ROOT.'/includes/patronage_points_func.php');

if($_SERVER['REQUEST_URI'] != '/ecosystem/logout')
require_once(CONTEXT_DOCUMENT_ROOT.'/includes/session.php');

$_SESSION['engine'] = 'CI';