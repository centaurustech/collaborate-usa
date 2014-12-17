<?php
/** Notification Cron
 * Run once a week
 * Removed Old Notifications that are over 80 days old and have been Read
*/

#/ Redirect if not in our IP
if(in_array($_SERVER['REMOTE_ADDR'], array('110.93.203.122', '110.93.203.14', '127.0.0.1', '166.62.35.176'))==false){
exit;
}

require_once('../includes/config.php');
include_once('../includes/db_lib.php');

/////////////////////////////////////////////////////////////////////////

//$allowed = array('localhost', 'www.collaborateusa.com', 'collaborateusa.com', 'new.collaborateusa.com', 'cusa-local');
//if(!in_array($_SERVER['SERVER_NAME'], $allowed)) {exit;}

$_GET = $_POST = array();

/////////////////////////////////////////////////////////////////////////

#/ Delete Old Notifications
$sql_notifi = "DELETE FROM user_notifications
WHERE is_read='1'
AND created_on < (NOW() - INTERVAL 61 DAY)";
@mysql_exec($sql_notifi, 'save');
?>