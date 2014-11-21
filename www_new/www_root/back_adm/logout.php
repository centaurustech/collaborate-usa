<?php
include_once('../../includes/config.php');

#### Destroy Sessions
unset($_SESSION['cusa_admin_usr_id']);
unset($_SESSION['adm_usr_info']);
unset($_SESSION['cusa_adm_perm']);

unset($_SESSION['CUSA_ADMIN_MSG_GLOBAL']);
unset($_SESSION['LAST_CUSA_Admin_ACTIVITY']);

session_unset(); // unset $_SESSION variable for the run-time
session_destroy(); // destroy session data in storage
##--

@header("Location: {$consts['DOC_ROOT_ADMIN']}login");
echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT_ADMIN']}login';</script>";
exit;
?>