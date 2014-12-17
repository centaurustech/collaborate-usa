<?php
require_once('../includes/config.php');

//if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start('ob_gzhandler'); else ob_start();

/////////////////////////////////////////////////
#/*
echo "<pre>";

//var_dump("<pre>", apache_get_modules());echo '<br /><br />';
var_dump(get_current_user()); echo '<br />';

$ax = exec('ls -a', $ar);
var_dump("<pre>", $ax, $ar, "</pre>"); echo '<br />';

var_dump(ini_get('memory_limit')); echo '<br />';
var_dump(php_sapi_name());

phpinfo();
die();
#*/

/////////////////////////////////////////////////
/*
include_once('../includes/send_mail.php');

$msg = "Dear Xr,<br /><br />
Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.
Ut wisi enim ad minim veniam, quis nostrud exerci tation <b>ullamcorper</b> suscipit lobortis nisl ut aliquip ex ea commodo consequat.<br /><br />
Ut wisi enim ad minim veniam, quis nostrud <b>exerci tation</b> ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.";

send_mail('raheelhsn.dev@gmail.com', 'Hello World Subject', 'Hello World Heading', $msg);
#*/
?>