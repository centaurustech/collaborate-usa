<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <?php if(defined('ADMIN_SESSION_TIMEOUT')){ echo '<meta http-equiv="refresh" content="'.(ADMIN_SESSION_TIMEOUT+5).'" />'; } ?>

	<title><?php if(isset($pg_title) && !empty($pg_title)) echo $pg_title.' | '; ?>CUSA Admin</title>
    <link rel="shortcut icon" href="<?=DOC_ROOT?>favicon_6.ico" />

    <link type="text/css" rel="stylesheet" href="<?=DOC_ROOT_ADMIN?>assets/styles/template_cusa.css" />

    <?php if(!isset($no_header)){ ?>
    <script src="<?=DOC_ROOT?>assets/js/custom.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?=DOC_ROOT_ADMIN?>assets/js/main.js"></script>

    <script type="text/javascript" src="<?=DOC_ROOT_ADMIN?>assets/js/local_storage.js"></script>
    <script type="text/javascript" src="<?=DOC_ROOT_ADMIN?>assets/js/var_dump.js"></script>
    <script type="text/javascript" src="<?=DOC_ROOT?>assets/js/jquery-1.11.1.min.js"></script>

    <?php if(isset($load_fancy)) { ?>
    <link rel="stylesheet" href="<?=DOC_ROOT?>assets/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script src="<?=DOC_ROOT?>assets/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
    <?php } ?>

    <?php } ?>

    <style>
    <?php if(stristr($browser, 'msie')!=false){
    echo "
    input.button{
    line-height: 16px;
    }
    ";
    } ?>
    </style>

    <script>
    var DOC_ROOT_ADMIN = '<?=DOC_ROOT_ADMIN?>';
    </script>
</head>

<body>
<center>
<div id="main_par_div" style="width:97%; text-align:left;">
<br /><br />

<?php if(!isset($no_header)){ ?>
<div style="text-align:center; margin-top:-10px;">
    <div style="padding:0 0 16px 0; margin-left:-10px;"><img src="<?=DOC_ROOT?>assets/images/lgo_fish21.png" title="CUSA Admin" /></div>
</div>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="92%" id="HEADER" class="no_print">
<tr>
    <td>

    <div style="float:left;">
        <div style="padding:0;">CUSA - Administration Area</div>
    </div>

    <div style="float:right; padding:0; margin-top:18px;">
        <?php
        if(usercheck())
        {
            ?>
            <span>
            Logged in as <b><?php echo rtext($_SESSION["adm_usr_info"]['first_name'].' '.$_SESSION["adm_usr_info"]['last_name']); ?></b>
            [<a href="<?=DOC_ROOT_ADMIN?>logout">Logout</a>]
            </span>
            <?php
        }
        ?>
    </div>
    <div style="clear:both; padding:0; height:0px; font-size:0px;"></div>
    </td>
</tr>
</table>
<?php } ?>

<?php if(!isset($no_header) && (usercheck())){ ?>
<style>
.tog_on{width:10px; cursor:pointer; border:solid 1px #C2CCD6; background:url(<?=DOC_ROOT_ADMIN?>images/close_tog_2.png) #FFFFFF repeat-y 100% center;}
.tog_off{width:10px; cursor:pointer; background:url(<?=DOC_ROOT_ADMIN?>images/open_tog.png) #FFFFFF no-repeat 100% center;}
</style>

<script type="text/javascript" src="<?=DOC_ROOT_ADMIN?>assets/js/left_menu.js"></script>
<?php } ?>


<div class="table_dv">
<table align="center" border="0" cellpadding="0" cellspacing="0" width="98%" id="CONTENT">


<tr><td colspan="4">
<div style="height:20px; background:#ffffff;">
</div></td></tr>


<tr valign="top">


<?php if(!isset($no_header)){ ?>
<?php
$cur_page = cur_page();
if(usercheck())
{
    $cusa_adm_perm = $_SESSION['cusa_adm_perm'];
    ?>
    <td id="COL1" class="no_print" style="padding-right:1px;">
	   <ul class="leftmenu">


        <?php if(is_array($cusa_adm_perm) && in_array(1, $cusa_adm_perm)) { ?>
        <li style="width:180px;">
            <span id="m_1"><b>Admin Users Mgt</b></span>
			<ul class="leftmenusub">
            <li><a href="<?=DOC_ROOT_ADMIN?>admin_users.php" <?php if($cur_page=='admin_users.php' || $cur_page=='admin_users_opp.php') echo "class='selected'"; ?>>Admin Users</a></li>
			</ul>
		</li>
        <?php } ?>


        <?php if(is_array($cusa_adm_perm) && in_array(4, $cusa_adm_perm)) { ?>
        <li style="width:180px;">
            <span id="m_3"><b>Business Management</b></span>
			<ul class="leftmenusub">
            <li><a href="<?=DOC_ROOT_ADMIN?>packages.php" <?php if($cur_page=='packages.php' || $cur_page=='packages_opp.php') echo "class='selected'"; ?>>Membership Packages</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>system_config.php" <?php if($cur_page=='system_config.php' || $cur_page=='system_config_opp.php') echo "class='selected'"; ?>>System Configurations</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>p_points_config.php" <?php if($cur_page=='p_points_config.php' || $cur_page=='p_points_config_opp.php') echo "class='selected'"; ?>>Patronage Points Config</a></li>
            </ul>
		</li>
        <?php } ?>


        <?php if(is_array($cusa_adm_perm) && in_array(2, $cusa_adm_perm)) { ?>
        <li style="width:180px;">
            <span id="m_4"><b>Site Management</b></span>
			<ul class="leftmenusub">
            <li><a href="<?=DOC_ROOT_ADMIN?>site_contacts.php" <?php if($cur_page=='site_contacts.php' || $cur_page=='site_contact_opp.php') echo "class='selected'"; ?>>Site Contact Info</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>pages_categories.php" <?php if($cur_page=='pages_categories.php' || $cur_page=='pages_categories_opp.php') echo "class='selected'"; ?>>Page Categories</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>site_pages.php" <?php if($cur_page=='site_pages.php' || $cur_page=='site_pages_opp.php') echo "class='selected'"; ?>>Site Pages</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>site_media.php" <?php if($cur_page=='site_media.php' || $cur_page=='site_media_opp.php') echo "class='selected'"; ?>>Site Media</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>site_misc_data.php" <?php if($cur_page=='site_misc_data.php' || $cur_page=='site_misc_opp.php') echo "class='selected'"; ?>>Misc. Site Data</a></li>
            </ul>
		</li>
        <?php } ?>


        <?php /* if(is_array($cusa_adm_perm) && in_array(3, $cusa_adm_perm)) { ?>
        <li style="width:180px;">
            <span id="m_6"><b>Newsletter Management</b></span>
			<ul class="leftmenusub">
            <li><a href="<?=DOC_ROOT_ADMIN?>newsltr_subsc.php" <?php if($cur_page=='newsltr_subsc.php' || $cur_page=='newsltr_subsc_opp.php') echo "class='selected'"; ?>>Newsletter Subscribers</a></li>
            </ul>
		</li>
        <?php }*/ ?>


        <?php if(is_array($cusa_adm_perm) && in_array(7, $cusa_adm_perm)) { ?>
        <li style="width:180px;">
            <span id="m_7"><b>Members Management</b></span>
			<ul class="leftmenusub">
            <li><a href="<?=DOC_ROOT_ADMIN?>users.php" <?php if($cur_page=='users.php' || $cur_page=='users_opp.php') echo "class='selected'"; ?>>Members</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>user_payments.php" <?php if($cur_page=='user_payments.php' || $cur_page=='user_payment_opp.php') echo "class='selected'"; ?>>Member Payments</a></li>
            </ul>
		</li>
        <?php } ?>


        <?php if(is_array($cusa_adm_perm) && in_array(8, $cusa_adm_perm)) { ?>
        <li style="width:180px;">
            <span id="m_8"><b>Voices Management</b></span>
			<ul class="leftmenusub">
            <li><a href="<?=DOC_ROOT_ADMIN?>voice_cats.php" <?php if($cur_page=='voice_cats.php' || $cur_page=='voice_cats_opp.php') echo "class='selected'"; ?>>Voice Categories</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>voice_tags.php" <?php if($cur_page=='voice_tags.php' || $cur_page=='voice_tags_opp.php') echo "class='selected'"; ?>>Voice Tags</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>voices.php" <?php if($cur_page=='voices.php' || $cur_page=='voices_opp.php') echo "class='selected'"; ?>>Voices (active)</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>voices_dump.php" <?php if($cur_page=='voices_dump.php' || $cur_page=='voices_dump_opp.php') echo "class='selected'"; ?>>Voices (archive)</a></li>
            </ul>
		</li>
        <?php } ?>


        <li style="width:180px;">
            <span id="m_9"><b>Administration&nbsp;Area</b></span>
			<ul class="leftmenusub">

            <?php /* if(is_array($cusa_adm_perm) && in_array(9, $cusa_adm_perm)) { ?>
            <li><a href="<?=DOC_ROOT_ADMIN?>stats.php" <?php if($cur_page=='stats.php') echo "class='selected'"; ?>>Statistical Overview</a></li>
            <?php } */ ?>

            <li><a href="<?=DOC_ROOT_ADMIN?>my_settings_opp.php" <?php if($cur_page=='my_settings_opp.php') echo "class='selected'"; ?>>My Settings</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>home" <?php if($cur_page=='index.php') echo "class='selected'"; ?>>Admin Home</a></li>
            <li><a href="<?=DOC_ROOT?>home" target="_blank">CUSA Frontend</a></li>
            <li><a href="<?=DOC_ROOT_ADMIN?>logout">Log Out</a></li>
            </ul>
		</li>

	   </ul>
	   <br />
    </td>

    <td style="background:#ffffff;"></td>

    <td id="tog_it" valign="center" class="tog_on" title="click to show/hide the Left Menu Panel" onclick="toggle_left();">
    <div style="width:10px;"></div>
    </td>

    <?php
}//end if....
?>
<?php } ?>



<td id="COL2" style="width:100%">
<?php
if(usercheck())
{
    if(!isset($no_header)){
	echo '<div style="min-height:450px;">';
    }else{
    echo '<div>';
    }

    if($cur_page=='index.php'){
    ?>
	<div class="no_print" id="USERMENU">Welcome <b><?php echo rtext($_SESSION["adm_usr_info"]['first_name'].' '.$_SESSION["adm_usr_info"]['last_name']); ?></b>
    &nbsp;<span style="color: #003366;">[<a href="<?=DOC_ROOT_ADMIN?>logout">Logout</a>]</span></div>
    <br /><br />

	<?php
    }
}
else
{
    ?>
    <div>
    <?php
}

////////////////////// Display Notifications
#/ Global Msg for the CUSAAdmin
//$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'Unable to proceed with your search request at this moment!<br />Please try again later.');
if (@array_key_exists("CUSA_ADMIN_MSG_GLOBAL", $_SESSION))
{
    if ($_SESSION["CUSA_ADMIN_MSG_GLOBAL"][0]==false){ echo '<div class="error" id="err_gd2" style=""><strong class="red-txt">ERROR:&nbsp;&nbsp;</strong> '.$_SESSION['CUSA_ADMIN_MSG_GLOBAL'][1].'</div>'; }
    if ($_SESSION["CUSA_ADMIN_MSG_GLOBAL"][0]==true) { echo '<div class="infor" id="err_gd2" style="">'.$_SESSION['CUSA_ADMIN_MSG_GLOBAL'][1].'</div>'; }
    //echo '<script type="text/javascript">time_id("err_gd2", "div", 60000);</script>';
    unset($_SESSION["CUSA_ADMIN_MSG_GLOBAL"]);
    echo '<br />';
}
#-
?>