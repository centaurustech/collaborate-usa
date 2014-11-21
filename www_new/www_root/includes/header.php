<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=0" />


    <?php if(!isset($no_header_ajax)){ ?>

    <?php if(isset($pg_meta) && is_array($pg_meta)) { ?>
    <title><?php if(array_key_exists('page_title', $pg_meta)){echo $pg_meta['page_title']." | ";} ?>CollaborateUSA.com</title>
    <?php if(array_key_exists('meta_keywords', $pg_meta)) { echo '<meta name="keywords" content="'.$pg_meta['meta_keywords'].'" />'."\n"; } ?>
    <?php if(array_key_exists('meta_descr', $pg_meta)) { echo '<meta name="description" content="'.$pg_meta['meta_descr'].'" />'."\n"; } ?>
    <?php } ?>

    <link rel="shortcut icon" href="<?=DOC_ROOT?>favicon.ico" />

    <link href='http://fonts.googleapis.com/css?family=Lato:400,300,700' rel='stylesheet' type='text/css' />
    <!--<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/font.css" />-->
    <link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/style.css" />

    <script type="text/javascript" src="<?=DOC_ROOT?>assets/js/jquery-2.1.1.min.js"></script>


    <!-- fancybox -->
    <link rel="stylesheet" href="<?=DOC_ROOT?>assets/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script src="<?=DOC_ROOT?>assets/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript"></script>
    <script>
    $(document).ready(function() {
    	$(".fbox").fancybox({
    	    minWidth    : 230,
    	    minHeight   : 300,
    		maxWidth	: 950,
    		maxHeight	: 600,
    		autoSize	: true,
            fitToView	: true,
            openEffect	: 'elastic',
    		closeEffect	: 'elastic',
    	});

        $(".fbox_a").fancybox({
    	    minWidth    : 230,
    	    minHeight   : 300,
    		maxWidth	: 950,
    		maxHeight	: 600,
    		autoSize	: true,
            fitToView	: true,
            openEffect	: 'elastic',
    		closeEffect	: 'elastic',

            prevEffect	: 'none',
    		nextEffect	: 'none',
            helpers	: {
    			buttons	: {}
    		}
    	});
    });
    </script>


    <?php if(isset($load_bx_slider)) { ?>
    <!-- bx slider -->
    <link href="<?=DOC_ROOT?>assets/css/jquery.bxslider.css" rel="stylesheet" />
    <script src="<?=DOC_ROOT?>assets/js/jquery.bxslider.js"></script>
    <?php } ?>


    <?php if(isset($load_owl_carousel)) { ?>
    <!-- Owl Carousel -->
    <link href="<?=DOC_ROOT?>assets/css/owl.carousel.css" rel="stylesheet" />
    <link href="<?=DOC_ROOT?>assets/css/owl.theme.css" rel="stylesheet" />
    <script src="<?=DOC_ROOT?>assets/js/owl.carousel.js"></script>
    <?php } ?>


    <?php if(isset($load_validation)) { ?>
    <link rel="stylesheet" href="<?=DOC_ROOT?>assets/js/validator_2/css/validationEngineDarkRed.jquery.css" type="text/css" media="screen" charset="utf-8" />
    <script src="<?=DOC_ROOT?>assets/js/validator_2/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
    <script src="<?=DOC_ROOT?>assets/js/validator_2/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <?php } ?>



    <script>
    var DOC_ROOT = '<?=DOC_ROOT?>';
    </script>

    <script type="text/javascript" src="<?=DOC_ROOT?>assets/js/var_dump.js"></script>

    <?php if(isset($insert_head)) { echo $insert_head; } ?>

    <?php } //end no_header_ajax.... ?>
</head>

<body>
<?php
$user_idc = (int)@$_SESSION['CUSA_Main_usr_id'];
////////////--------------------------------
if(!isset($no_header)){ ?>

<div id="mask_1" style="display:none; text-align:center; position:absolute; z-index:10000;
width:100%; height:100%; background:#000000;
filter:alpha(opacity=15);
-moz-opacity:0.15;
opacity: 0.15;">&nbsp;&nbsp;</div>

<script>
$(document).ready(function(){
   var body_ht = parseInt($('.section-main').outerHeight())+parseInt($('.mar-page').outerHeight());
   $('#mask_1').css('height', (body_ht+90)+'px');
});
</script>


<?php
#/ get needed site_media
$sm_array = array('main_logo');
$site_media_main = get_site_media($sm_array);
//var_dump("<pre>", $site_media_main); die();

#/ get other needed info
$site_contact_info = @cb89(@mysql_exec("SELECT * FROM site_contact_info"), 'c_key');
//var_dump("<pre>", $site_contact_info); die();
?>

<div class="header">
    <div class="container">
        <?php $site_top_logo = @$site_media_main['main_logo']; ?>
        <div class="logo"><a href="<?=DOC_ROOT?>"><img src="<?=DOC_ROOT?>assets/images_2/media/<?php echo @$site_top_logo[0]['m_file']; ?>"
        alt="<?php echo @$site_top_logo[0]['alt_text']; ?>" title="<?php echo @$site_top_logo[0]['alt_text']; ?>" /></a></div>


        <?php if($user_idc<=0) { ?>
        <link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/top_login.css" />
        <div class="loginform">
        <form action="<?=DOC_ROOT?>signin" method="POST">
            <div class="login_left" style="vertical-align: top;">
                <input type="text" name="email_add" id="email_add" placeholder="Enter Email Address" /><br />
				<?php /*<input type="checkbox" name='remember_me' id="remember_me" value='1' /><span>Remember Me</span>*/ ?>
            </div>

            <div class="login_right">
                <input type="password" name="p_wd" id="p_wd" placeholder="Your Password" />
                <input type="submit" name="submit" value="Sign In"/><br />
                <a href="<?=DOC_ROOT?>recover-access">Forgot Password?</a>
            </div>
            <div class="clear"></div>
        </form>
        </div>

        <?php } else { ?>

        <?php } ?>

        <div class="clear"></div>

    </div>
</div>

<?php if(isset($current_pgx) && ($current_pgx=='home')){ ?>
<!-- Home -->

    <div class="slider">
    <div style="max-width:1270px; margin:0 auto;">
        <div class="slider_jpg_100">
        <?php
        if(isset($site_misc_data) && is_array($site_misc_data) && array_key_exists('home_sliders', $site_misc_data))
        {
            echo '<ul class="bxslider">';
            if(is_array(['home_sliders'])){
            foreach($site_misc_data['home_sliders'] as $hms_v)
            {
                echo '<li>';
                echo "<img src=\"{$consts['DOC_ROOT']}assets/images_2/misc/{$hms_v['m_image']}\" />
                <div class=\"slider_area {$hms_v['content_settings']}\">
                    <h2>{$hms_v['title']}</h2>
                    <p>{$hms_v['m_value']}</p>";


                if($user_idc<=0){
                echo "<a class=\"yellow_sbtn\" href=\"{$consts['DOC_ROOT']}join\">Join The Movement</a>";}

                echo "</div>";
                echo '</li>';
            }
            }
            echo '</ul>';
        }
        ?>
        </div>
    </div>
    </div>

<?php } else { ?>
<!-- non-Home -->

    <div class="header2">
    <div class="container">
        <div class="head_ttles">
        <?php if(isset($page_heading) && !empty($page_heading)) {echo "<h3>{$page_heading}</h3>";} ?>
        </div>
    </div>
    </div>

<?php } ?>


<?php }//end if no header..
////////////--------------------------------
?>

<?php
////////////////////// Display Notifications
//$_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to proceed with your search request at this moment!<br />Please try again later.');
if (@array_key_exists("CUSA_MSG_GLOBAL", $_SESSION))
{
    echo '<br /><div class="container">';
    if ($_SESSION["CUSA_MSG_GLOBAL"][0]==false){ echo '<div class="error" id="err_gd2"><b class="red-txt">ERROR:&nbsp;&nbsp;</b> '.$_SESSION['CUSA_MSG_GLOBAL'][1].'</div>'; }
    if ($_SESSION["CUSA_MSG_GLOBAL"][0]==true) { echo '<div class="infor" id="err_gd2">'.$_SESSION['CUSA_MSG_GLOBAL'][1].'</div>'; }
    //echo '<script type="text/javascript">time_id("err_gd2", "div", 60000);</script>';
    unset($_SESSION["CUSA_MSG_GLOBAL"]);
    echo '</div>';
}
#-
?>