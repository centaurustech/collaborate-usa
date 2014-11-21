<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

#/ Setup variables & check empty
$my_user_id = (int)@$_SESSION["cusa_admin_usr_id"];
if($my_user_id<=0){exit;}

////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
@header(' ', true, 404);

$pg_title = "Error 404 - Content Not Found";
include_once("includes/header.php");
?>

<h1 style='color:#B7151A;'><?=$pg_title?></h1>

<div style='color:#B7151A; padding:0 20px;'>
    <br /><br /><br />
    <b style='font-size:15px; font-weight:bold;'>ERROR 404</b><br /><br />
    The Content you are looking for could not be found at this moment.
</div><br /><br />

<br />
<div class="logo" style='padding:0 20px;'>
<img src="<?=DOC_ROOT?>assets/images/lgo_y.png" title="collaborateusa.com" /></div>
<br />


<?php
include_once("includes/footer.php");
?>