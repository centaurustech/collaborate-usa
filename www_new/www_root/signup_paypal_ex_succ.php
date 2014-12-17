<?php
//STEP 3

if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}
//var_dump($url_comp, $seo_tag_id); die();

////////////////////////////## Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}home';</script>";
    exit;
}

#/ check & redirect - if not coming from step1
if(!isset($_SESSION['signup_stage']) || empty($_SESSION['signup_stage']))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}signup';</script>";
    exit;
}

#/ check & redirect - if already crossed this step
#/*
if(($_SESSION['signup_stage']!='signup') && ($_SESSION['signup_stage']!='signup-ppe-success'))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}{$_SESSION['signup_stage']}';</script>";
    exit;
}
#*/
////////////////////##--

function error_1()
{
    $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to proceed with your request at this moment! Please try again later.');
    redirect_me('signup');
    exit;
}

#/ match pay_chk
if(!@array_key_exists('pay_chk', $_GET) || ($_GET['pay_chk']!=$_SESSION['pay_chk'])){error_1();}

#/ match Token
if(!isset($_GET['PayerID']) ||
!isset($_GET['token']) ||
($_GET['token']!=$_SESSION['TOKEN']) ||
!array_key_exists('signup_cart', $_SESSION))
{error_1();}

if(!array_key_exists($_GET['token'], $_SESSION['signup_cart']))
{error_1();}

$_SESSION['payer_id'] = $_GET['PayerID'];


#/ Get POSTED data
$signup_cart = @$_SESSION['signup_cart'][$_GET['token']];
$user_POST = @$signup_cart['user_post'];
if(!is_array($signup_cart) || count($signup_cart)<=0){error_1();}

//var_dump("<pre>", $signup_cart); die('x');

/////////////////////////////////////////////////////////////////////

$success_1 = false;
if(isset($_SESSION['signup_success']) && ($_SESSION['signup_success']=='1')){
$success_1 = true;
}
//var_dump($success_1); die();


$category_msg = "Please wait while we Confirm your Payment Process via Paypal.";

/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=> "Signup / Membership Registration",
);
$page_heading = "Payment Confirmation via Paypal";

include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>

<script src="<?=DOC_ROOT?>assets/js/ajax.js" type="text/javascript"></script>
<script src="<?=DOC_ROOT?>assets/js/ajax_work.js" type="text/javascript"></script>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">

    <h1><strong><?=format_str($pg_meta['page_title'])?></strong></h1>
    <br />

    <div>
        <?php if($success_1==false) { ?>

        <script>
        $(document).ready(function() {

            //$('#mask_1').css('display', '');
            var href = '<?=DOC_ROOT?>signup-ppe-confirm/?pay_chk=<?=$_GET['pay_chk']?>&token=<?=$_GET['token']?>&PayerID=<?=$_GET['PayerID']?>';

            var jsString = {runIt:function(dta)
            {
                //$('#mask_1').css('display', 'none');
                if(dta.search(/go\|\|\|\|/im)>=0)
                {
                    var dtx = dta.split('||||TKU-');
                    //alert(dtx[1]); return false;

                    if((typeof(dtx[1])!='undefined'))
                    {
                        //location.href="<?=DOC_ROOT?>signup-details/?pay_chk=<?=$_GET['pay_chk']?>&token="+dtx[1];
                        location.href="<?=DOC_ROOT?>signup-details";

                        return false;
                    }
                }
                else
                {
                    if(document.getElementById('err_gd2') != null)
                    {
                        $('#err_gd2').html('<b class="red-txt">ERROR:&nbsp;&nbsp;</b> '+dta);
                        $('html,body').animate({scrollTop:$('.header2').offset().top}, {duration: 800, easing: 'swing'});
                    }
                    else
                    {
                        var error_html = '<div class="container" style="background:#FFF"><div class="content_holder"><br><div class="error" id="err_gd2"><b class="red-txt">ERROR:&nbsp;&nbsp;</b> '+dta+'</div></div></div>';
                        //alert(error_html);
                        $(error_html).insertAfter($('.header2'));
                        $('html,body').animate({scrollTop:$('.header2').offset().top}, {duration: 800, easing: 'swing'});
                    }
                }
            }};

            ajax_work(href, '', 'load_x', 'mask_1', jsString);

        });
        </script>

        <div>Dear <?php echo $user_POST['first_name'] ?>,<br /><br />
        Thank you for taking your time to complete the SignUp. We are now <b>verifying your Payment Details</b> with Paypal.<br /><br />

        Once this step is completed, you will be automatically redirected to
        the <b>Thank You</b> page where you will also be able to complete your <b>Profile Info</b>.
        Please be patient while we complete this step...<br /><br />

        <img id="load_x" src="<?=DOC_ROOT?>assets/images/load_blue.gif" />
        </div>

        <?php } else { ?>

        <script>

        </script>

        <?php } ?>
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