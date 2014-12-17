<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}
//var_dump($url_comp, $seo_tag_id); die();

////////////////////////////## Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}home';</script>";
    exit;
}

#/ check & redirect - if already crossed Step1
if(isset($_SESSION['signup_stage']) && !empty($_SESSION['signup_stage']) && ($_SESSION['signup_stage']!='signup'))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}{$_SESSION['signup_stage']}';</script>";
    exit;
}
////////////////////##--

$success = false;
$sel_pack = 0;
if(is_array($url_comp))
{
    if(@in_array('success', $url_comp)!=false){
    $success = true;
    }
    else if(isset($url_comp['1']))
    {
        $sel_pack = (int)$url_comp['1'];
    }
}
//var_dump($sel_pack); die();

/////////////////////////////////////////////////////////////////////

#/ Process Post
if(isset($_POST['first_name']))
{
    $_SESSION['signup_stage'] = 'signup';

    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    if(check_attempts(3)==false){
    update_attempt_counts(); redirect_me($seo_tag);
    }


    ##/ Validate Fields
    include_once('../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['first_name'], ['last_name'], ['package_id'], ['email_add'], ['pass_w'], ['c_pass_w'], ['secret_question_id'], ['secret_answer'], ['vercode']],
    'lengthMin' => [['pass_w', 7], ['c_pass_w', 7]],
    'lengthMax' => [['first_name', 65], ['middle_name', 20], ['last_name', 50], ['company_name', 100], ['email_add', 150], ['pass_w', 20], ['c_pass_w', 20], ['secret_answer', 190], ['vercode', 10]],
    'email' => [['email_add']],
    'equals' => [['c_pass_w', 'pass_w']],
    ];

    $form_v->labels(array(
    'package_id' => 'Membership Package',
    'email_add' => 'Email Address',
    'pass_w' => 'Password',
    'c_pass_w' => 'Confirm Password',
    'secret_question_id' => 'Secret Question',
    'vercode' => 'Verification Code',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();

    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    #/ Check Captcha Code
    if( (empty($_SESSION['cap_code'])) || (empty($_POST['vercode'])) || ($_SESSION['cap_code']!=$_POST['vercode']) )
    {
        $fv_errors[] = array('The Verification Code you entered does not match the one given in the image! Please try again.');
    }

    #/ Check if Email Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT email_add FROM users WHERE email_add='{$_POST['email_add']}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Email Address is already used, please try a different one!');
        }
    }

    #/ Check if Package
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_pkg = mysql_exec("SELECT * FROM membership_packages WHERE id='{$_POST['package_id']}' AND is_active='1'", 'single');
        if(empty($chk_pkg))
        {
            $fv_errors[] = array('The Membership Package you selected cannot be purchased at this moment, please try a different one!');
        }
    }


    ##/ Process
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        #/ Determine Direction to move
        $chk_pkg = @format_str($chk_pkg);
        $chk_pkg['cost'] = (float)$chk_pkg['cost'];
        $package_type = 'free';
        if($chk_pkg['cost']>0){
        $package_type = 'paid';
        }
        //var_dump($chk_pkg['cost'], $package_type); die();


        #/ make a copy of submission
        $_SESSION['signup_filled']['1'] = $_POST;
        include_once('../includes/process_signup.php');

        if($package_type=='paid')
        {
            #/ Paid Package Processing
            //process_signup_1($_POST, $chk_pkg);
        }
        else
        {
            #/ Free Package Processing
            process_signup_2($_POST, $chk_pkg);
        }//end if free...
    }
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, $fv_msg);
        update_attempt_counts();
    }
    #-

}//end if form post..

/////////////////////////////////////////////////////////////////////

if($success==false)
{
    include_once('../includes/model_package.php');
    $all_packages = get_all_packages(false);

    $secret_questions = @format_str(@mysql_exec("SELECT * FROM secret_questions ORDER BY question"));
    //var_dump("<pre>", $all_packages); die();
}

$category_msg2 = "If you already have an Account previously setup at collaborateUSA.com,
please <b><a href=\"{$consts['DOC_ROOT']}signin\">Click Here to Signin</a></b> instead.";

$category_msg = "Please fill the form below to Signup for a <b>Membership Account</b> at collaborateUSA.com. ".$category_msg2;


/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=> "Signup / Membership Registration",
);
$page_heading = "Membership at collaborateUSA.com";

$load_validation = true;
include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>
<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/form.css" />
<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/signup.css" />

<?php if($success==false) { ?>
<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/custom.js"></script>

<script src="<?=DOC_ROOT?>assets/js/ajax.js" type="text/javascript"></script>
<script src="<?=DOC_ROOT?>assets/js/ajax_work.js" type="text/javascript"></script>

<?php /*<script src='https://www.paypalobjects.com/js/external/dg.js' type='text/javascript'></script>*/ ?>
<?php
//require_once('../includes/paypal/Common/Constants.php');
require_once('../includes/paypal/classic/express/paypalfunctions.php');
}//end if success....
?>


<script>
<?php if($success==false) { ?>
<?php
echo "var all_packs = [];\n";
if(is_array($all_packages))
foreach ($all_packages as $pack_v){
echo "all_packs[{$pack_v['id']}]='".(int)($pack_v['cost']>0)."';\n";
}
?>
//alert(dump(all_packs));

function change_pack(val)
{
    if(val=='') return false;
    if(typeof(all_packs)=='undefined') return false;
    if(typeof(all_packs[val])=='undefined') return false;

    if(all_packs[val]=='1')
    {
        document.getElementById('paypl_lgo').style.display='';
    }
    else
    {
        document.getElementById('paypl_lgo').style.display='none';
    }
}//end func....


$(document).ready(function(){

$.chk_signup = function(status)
{
    //alert(status);
    if(status==false) return false;

    var valx = document.getElementById('package_id').value;
    if(valx=='') return false;

    if(typeof(all_packs)=='undefined') return false;
    if(typeof(all_packs[valx])=='undefined') return false;

    if(all_packs[valx]!='1'){
    return true; //free package = no need for payment
    }
    else
    {
        <?php //Ajax call for first step of Paypal processing ?>
        $('#submit_btn').addClass('loading_bg');
        var href = "<?=DOC_ROOT?>signup-ppe";
        //return false;

        var jsString = {runIt:function(dta)
        {
            $('#mask_1').css('display', 'none'); //due to jquery.ajax used instead
            $('#submit_btn').removeClass('loading_bg');

            if(dta.search(/go\|\|\|\|/im)>=0)
            {
                var dtx = dta.split('||||D2CO-');
                //alert(dtx[1]); return false;

                if((typeof(dtx[1])!='undefined') && ("<?=@$PAYPAL_URL?>".search(/http/im)>=0)) <?php //@$PAYPAL_URL_DIRECT?>
                {
                    $('#mask_1').css('display', '');
                    $('#submit_btn').addClass('loading_bg');

                    var pkey = dtx[1];

                    <?php /*
                    var p_form_field = $('#adp_form form #paykey');
                    p_form_field.val(pkey);
                    $('#adp_form form #submitBtn').click();
                    */ ?>

                    location.href="<?=@$PAYPAL_URL?>"+pkey;

                    return false; <?php //always false due to location redirect and NOT actual form submission ?>
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
        //ajax_work(href, '', '', 'mask_1', jsString);

        <?php //Using JQuery Ajax instead ?>
        $('#mask_1').css('display', '');
        var post_data = $("#form_signup").serializeArray();
        //alert(dump(post_data)); return false;

        $.ajax({
            type: "POST",
            url: href,
            data: post_data,
            cache: false,
            dataType: 'text',
        }).done(function(msg){
        jsString.runIt(msg);
        });

        return false;

    }//end else....
};//end func....


$("#form_signup").validationEngine({
    promptPosition: loc,//"topRight"
    onValidationComplete: function(form, status){
        return $.chk_signup(status);
    }
});

});

<?php }//end if success.... ?>
</script>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">

    <h1><strong><?=format_str($pg_meta['page_title'])?></strong></h1>
    <br />

    <div>
        <?php if($success!=false) { ?>

        <div>Thank you for <b>Signing Up</b> at collaborateUSA.com. Your Account has been successfully created.<br /><br />
        An email has been sent to your provided Email Address with an <b>Account Activation Link</b>.
        Please follow that email in order to <b>Activate</b> your account.</div>

        <?php }else { ?>

        <?php if(!empty($category_msg)) { ?><div class="content"><?php echo $category_msg; ?></div><br /><?php } ?><br />

        <?php $tabindex=1; ?>
        <div class="fields-big">
        <form id="form_signup" name="form_signup" action="" method="POST" <?=AUTO_COMPLETE?>
        nsubmit="$(document).ready(function(){return $.chk_signup();});">

            <h4 class="heading">Personal Info:</h4><br />

            <label>First Name</label>
            <div class="right_fl">
                <input type="text" id="first_name" name="first_name" class="validate[required]"
                value="<?php if(isset($_POST['first_name'])){echo $_POST['first_name'];} ?>"
                placeholder="First" maxlength="50" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>
            <div style="clear: both;"></div>

            <label>Middle Name</label>
            <div class="right_fl">
                <input type="text" id="middle_name" name="middle_name" class=""
                value="<?php if(isset($_POST['middle_name'])){echo $_POST['middle_name'];} ?>"
                placeholder="Middle" maxlength="20" style="width:90px;" tabindex="<?=$tabindex++?>" />
            </div>
            <div style="clear: both;"></div>

            <label>Last Name</label>
            <div class="right_fl">
                <input type="text" name="last_name" id="last_name" class="validate[required]"
                value="<?php if(isset($_POST['last_name'])){echo $_POST['last_name'];} ?>"
                placeholder="Last" maxlength="50" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>
            <div style="clear: both;"></div>
            <br class="dsk_br" />


            <label>Organization</label>
            <div class="right_fl">
                <input type="text" name="company_name" id="company_name" class=""
                value="<?php if(isset($_POST['company_name'])){echo $_POST['company_name'];} ?>"
                placeholder="Organization" maxlength="100" style="width:320px;" tabindex="<?=$tabindex++?>" />
            </div>
            <div style="clear: both;"></div>
            <br class="dsk_br" />


            <br />
            <h4 class="heading">Account Info:</h4><br />


            <label>Membership Package</label>
            <div class="right_fl">
                <?php
                $all_packs = array();
                if(is_array($all_packages))
                foreach ($all_packages as $pack_v)
                {
                    $pack_v['cost'] = (float)$pack_v['cost'];
                    $cost_dv = number_format($pack_v['cost'], 2);
                    $all_packs[] = "<option value='{$pack_v['id']}'>{$pack_v['title']}".($pack_v['cost']>0? " (\${$cost_dv})":'')."</option>";
                }
                ?>
                <select name="package_id" id="package_id" class="validate[required]" style="width:337px;"
                tabindex="<?=$tabindex++?>" nchange="change_pack(this.value);">
                    <option value="">Select Package -</option>
                    <?php if(isset($all_packs) && is_array($all_packs)) echo implode(' ', $all_packs); ?>
                </select><span class="required">*</span>
                <?php if(isset($_POST['package_id'])){echo "<script>document.getElementById('package_id').value='{$_POST['package_id']}';</script>";} //change_pack('{$_POST['package_id']}');
                else {echo "<script>document.getElementById('package_id').value='{$sel_pack}';</script>";} //change_pack('{$sel_pack}'); ?>

                <?php if(stristr($browser, 'chrome')==false) { ?>
                <style>
                .paypal_img table,
                .paypal_img table tbody,
                .paypal_img table tbody tr,
                .paypal_img table tbody tr td {
                    display:block;
                }
                </style>
                <?php } ?>
                <div id="paypl_lgo" tyle="display: none;">
                    <div class="paypal_img" style="display: inline-block;">
                    <!-- PayPal Logo --><table border="0" cellpadding="10" cellspacing="0" align="center">
                    <tr><td align="center"><a href="https://www.paypal.com/webapps/mpp/paypal-popup" title="How PayPal Works"
                    onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;">
                    <img class="round_borders" src="https://www.paypalobjects.com/webstatic/mktg/logo-center/Security_Banner_234x60_4a.gif" border="0" alt="PayPal Logo"></a></td></tr></table><!-- PayPal Logo -->
                    </div>
                    <br class="mbl_br" style="margin-bottom:20px;" />
                </div>

            </div>
            <div style="clear: both;"></div>
            <br class="" />


            <label>Email Address</label>
            <div class="right_fl">
                <input type="text" name="email_add" id="email_add" class="validate[required, custom[email]]"
                value="<?php if(isset($_POST['email_add'])){echo $_POST['email_add'];} ?>"
                placeholder="Email" maxlength="150" style="width:320px" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
                <span class="submsg">(will be used as your Signin Id)</span>
            </div>
            <div style="clear: both;"></div>
            <br class="dsk_br" />


            <label>Password</label>
            <div class="right_fl">
                <input type="password" name="pass_w" id="pass_w" class="validate[required, minSize[6]]" autocomplete="off"
                placeholder="Password" maxlength="20" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>
            <div style="clear: both;"></div>


            <label>Confirm Password</label>
            <div class="right_fl">
                <input type="password" name="c_pass_w" id="c_pass_w" class="validate[required, equals[pass_w]]" autocomplete="off"
                placeholder="Confirm" maxlength="20" style="width:320px" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>
            <div style="clear: both;"></div>
            <br class="dsk_br" />


            <br />
            <h4 class="heading">Account Recovery:</h4><br />

            <label>Secret Question</label>
            <div class="right_fl">
                <select name="secret_question_id" id="secret_question_id" class="validate[required]" style="width:337px;" tabindex="<?=$tabindex++?>">
                    <option value="">Select Question -</option>
                    <?php if(is_array($secret_questions)) foreach ($secret_questions as $secret_question) { ?>
                    <option value='<?php echo $secret_question['id']; ?>'><?php echo $secret_question['question']; ?></option>
                    <?php } ?>
                </select><span class="required">*</span>
                <?php if(isset($_POST['secret_question_id'])){echo "<script>document.getElementById('secret_question_id').value='{$_POST['secret_question_id']}';</script>";} ?>
            </div>
            <div style="clear: both;"></div>


            <label>Answer</label>
            <div class="right_fl">
                <input type="text" name="secret_answer" id="secret_answer" class="validate[required]"
                value="<?php if(isset($_POST['secret_answer'])){echo $_POST['secret_answer'];} ?>"
                placeholder="Answer" maxlength="190" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>
            <div style="clear: both;"></div>


            <br />
            <hr style="width:67.5%; display:inline-block;" />
            <br /><br />

            <label>Verification Code</label>
            <div class="right_fl veri_block">
                <div style="font-size:12px; margin-bottom:10px;">Please enter the Code you see in the box into this field:</div>
                <?php $title = 'Please enter this Verification Code you see in the box into the security code field. If you have trouble reading the code, click on REFRESH to re-generate it.'; ?>
                <div style="display:inline-block; padding-right:5px; vertical-align:top;"><input name="vercode" id="vercode" class="validate[required]" type="text" maxlength="10" placeholder="Code" style="width:150px;" tabindex="<?=$tabindex++?>" /><span class="required no_pos">*</span></div>
                <div style="display:inline-block; padding:0 5px; vertical-align:top;"><img src='<?=DOC_ROOT?>secure-captcha' id='secure_image' border='0' class="round_borders" style="" /></div>
                <div style="display:inline-block; padding:0 5px 0 0; vertical-align:middle;"><a href="javascript:void(0)" style="font-size:12px; " onclick="document.getElementById('secure_image').src=document.getElementById('secure_image').src+'?<?php echo time(); ?>';">Refresh</a></div>
                <div style="display:inline-block; vertical-align:middle;"><img src='<?=DOC_ROOT?>assets/images/tip2.gif' border='0' class="toolIT" title='<?php echo $title; ?>' style="cursor:help;" /></div>
            </div>
            <div style="clear: both;"></div>
            <br />

            <label></label>
            <div class="right_fl" style="">
                <input type="submit" id="submit_btn" class="blue_btn" value="SIGNUP" style="width: 120px;" />
            </div>
            <div style="clear: both;"></div>

            <br />
            <hr style="width:67%; display:inline-block;" />
            <br /><br />

            <?php if(!empty($category_msg2)) { ?><div><?php echo $category_msg2; ?></div><?php } ?>

        </form>
        </div>

        <?php } ?>
    </div>
    <div style="clear: both;"></div>

</div>

</div>
<div class="clear"></div>
</div>
</div>

<?php /* // for paypal adaptive -- /*
<div id="adp_form" style="display:none;">
    <form action="https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay" target="PPDGFrame" class="standard">
    <input id="type" type="hidden" name="expType" value="light" />
    <input id="paykey" type="hidden" name="paykey" value="" />
    <input type="submit" id="submitBtn" value="Pay with PayPal" />
    </form>
</div>

<script type="text/javascript" charset="utf-8">
var embeddedPPFlow = new PAYPAL.apps.DGFlow({trigger: 'submitBtn'});
</script>
*/ ?>

<?php
include_once("includes/footer.php");
?>