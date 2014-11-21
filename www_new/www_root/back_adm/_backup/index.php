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

$pg_title = "Home";
include_once("includes/header.php");
?>

<br />
    <div class="logo" style="margin-left:-5px;">
    <img src="<?=DOC_ROOT?>assets/images/lgo_y.png" title="collaborateusa.com" /></div>
<br /><br />

<div>
    Please use the Left Menu Panel to navigate through the CUSA <b>Admin Section</b>.
    You can click on the arrow-bar (next to the left panel) in order to hide the Panel.
</div><br />

<?php
include_once("includes/footer.php");
?>