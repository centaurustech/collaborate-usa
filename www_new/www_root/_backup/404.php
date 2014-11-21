<?php
require_once('../includes/config.php');

@header(' ', true, 404);
/////////////////////////////////////////////////////////////////////

include_once('../includes/format_str.php');
include_once('../includes/func_1.php');
include_once('../includes/db_lib.php');
include_once('../includes/model_main.php');

////////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=>format_str('404 - Not Found'),
);

$page_heading = "404 - Content Not Found";

$current_menus = array();
include_once("includes/header.php");
?>

<style>
.l1 {
    margin-left:20px;
    text-align: left;
}

@media (max-width: 655px) {
.l1 {
    text-align: center !important;
    margin:20px 0 0;
}
}
</style>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="text-align: center;">
    <br />
    <div style="display:inline-block;"><img src="<?=SITE_URL?>assets/images/err.jpg" style="width:100px;" /></div>

    <div class="l1" style="display:inline-block; vertical-align: middle;">
        <h3 class="heading_red">Error 404 - Content Not Found</h3>
        <p style="color:#B7151A;">The Content you are looking for could not be found at this moment.</p>
    </div>
    <div style="clear: both;"></div>
</div>

</div>
<div class="clear"></div>
</div>
</div>

<?php
include_once("includes/footer.php");
?>