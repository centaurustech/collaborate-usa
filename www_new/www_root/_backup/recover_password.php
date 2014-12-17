<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}

////////////////////////////## Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}home';</script>";
    exit;
}
////////////////////##--

$success = false;
if(@in_array('success', $url_comp)!=false){
$success = true;
}

$show_form = true;

/////////////////////////////////////////////////////////////////////

#/ Process Post
if(isset($_POST['email_add']))
{
    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    if(check_attempts(3)==false){
    update_attempt_counts(); redirect_me($seo_tag);
    }

    ##/ Validate Fields
    include_once('../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['email_add'], ['secret_question_id'], ['secret_answer'], ['vercode']],
    'lengthMax' => [['email_add', 150], ['secret_answer', 190], ['vercode', 10]],
    'email' => [['email_add']],
    ];

    $form_v->labels(array(
    'email_add' => 'Email Address',
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


    ##/ Find User Info
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $_POST['secret_question_id'] = (int)@$_POST['secret_question_id'];

        #/ Match User
        $sql_1 = "SELECT *, US.id as user_id
        FROM users US
        LEFT JOIN user_info UI ON US.id = UI.user_id
        WHERE US.email_add='{$_POST['email_add']}'
        AND UI.secret_question_id='{$_POST['secret_question_id']}' AND secret_answer='{$_POST['secret_answer']}'
        ";
        $qa_res = mysql_exec($sql_1, 'single');

        if(empty($qa_res) || !is_array($qa_res))
        {
            $fv_errors[] = array("Unable to match your given info in our Records! Please try again.");
        }
        else
        {
            #/ Check if account is not activated
            if($qa_res['account_activated'] == 0){
            $fv_errors[] = array("Your Account has <b>NOT been ACTIVATED</b> yet!<br />- Please follow the Account Activation email sent to you at the time of Signup in order to activate your Account first.");
            $show_form = false;
            }

            #/ Check if account is blocked
            if($qa_res['is_blocked'] == 1){
            $fv_errors[] = array("Your Account has <b>BLOCKED</b> by the Admin! Consequently, you will NOT be able to Access your Account.");
            $show_form = false;
            }
        }
    }
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $user_info = $qa_res;
        if( is_array($user_info) && array_key_exists('email_add', $user_info) )
        {
            #/ create & encrypt temp password
            include_once('../includes/func_enc.php');
            $new_pass = @createRandomPassword();
            $pass_w = @md5_encrypt($new_pass);


            #/save users
            $sql_users = "UPDATE users SET pass_w='{$pass_w}'
            WHERE email_add='{$user_info['email_add']}' AND id='{$user_info['user_id']}'";
            @mysql_exec($sql_users, 'save');


            #/ Send Emails to User
            include_once('../includes/email_templates.php');
            include_once('../includes/send_mail.php');

            $subject = $heading = "Account Access Recovery from collaborateUSA.com";
            $body_in = account_recover_access($user_info, $new_pass);

            send_mail($_POST['email_add'], $subject, $heading, $body_in);


            #/ Redirect
            reset_attempt_counts();
            redirect_me($seo_tag.'/success');
        }
        else
        {
            $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to process your request at this moment! Please try again later.');
            redirect_me($seo_tag);
        }

        exit;
    }
    else
    {
        $fv_msg = 'You have the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, $fv_msg);
        update_attempt_counts();
    }

}//end if form post..

/////////////////////////////////////////////////////////////////////

if($success==false){
$secret_questions = @format_str(@mysql_exec("SELECT * FROM secret_questions ORDER BY question"));
}

/////////////////////////////////////////////////////////////////////

$category_msg2 = "If you have recovered you Account already,
please <b><a href=\"{$consts['DOC_ROOT']}signin\">Click Here to Signin</a></b> instead.";

$category_msg = "Please fill out the form below to <b>Recover the Access</b> to your Account. ".$category_msg2;


#/ Fill pg_meta
$pg_meta = array(
    'page_title'=> "Account Access Recovery",
);
$page_heading = "Recover your User Account Access at collaborateUSA.com";

$load_validation = true;
include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>

<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/form.css" />

<script>
$(document).ready(function() {
    $("#form_recover").validationEngine({
    promptPosition: loc, //"topRight"
    });
});
</script>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">

<?php if(($show_form==true) || ($success==true)){ ?>
    <h1><strong><?=format_str($pg_meta['page_title'])?></strong></h1><br />

    <?php if($success!=false) { ?>

    <div>Dear User,<br /><br />An email has been sent to your provided Email Address with an <b>Account Recovery</b> message.
    Please follow that email to Recover your account access.<br /><br />

    <?php if(!empty($category_msg2)) { ?><?php echo $category_msg2; ?><br /><br /><?php } ?>

    <br />
    <b style="font-size: 1.2em !important%;">
    Best Regards,<br />
    <span style="color: #2CA1F4;">collaborateUSA.com</span></b>
    </div>

    <?php }else{ ?>

    <?php if(!empty($category_msg)) { ?><div class="content"><?php echo $category_msg; ?></div><br /><?php } ?><br />

    <?php $tabindex=1; ?>
    <div class="span10" style="margin-left:0;">
    <div class="contact-main fields-big">
    <form id="form_recover" name="form_recover" action="" method="POST" <?=AUTO_COMPLETE?>>

        <h4 class="heading" style="text-decoration: underline;">Account Info</h4><br />

        <label>Email Address</label>
        <div class="right_fl">
            <input type="text" name="email_add" id="email_add" class="validate[required, custom[email]]"
            value="<?php if(isset($_POST['email_add'])){echo $_POST['email_add'];} ?>"
            placeholder="Email Address" maxlength="150" style="width:320px;" tabindex="<?=$tabindex++?>"
            /><span class="required">*</span>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>Secret Question</label>
        <div class="right_fl">
            <select name="secret_question_id" id="secret_question_id" class="validate[required]" style="width:337px;" tabindex="<?=$tabindex++?>">
                <option value="">Select Secret Question</option>
                <?php foreach ($secret_questions as $secret_question) { ?>
                <option value='<?php echo $secret_question['id']; ?>'><?php echo $secret_question['question']; ?></option>
                <?php } ?>
            </select><span class="required">*</span>
            <?php if(isset($_POST['secret_question_id'])){echo "<script>document.getElementById('secret_question_id').value='{$_POST['secret_question_id']}';</script>";} ?>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>Answer</label>
        <div class="right_fl">
            <input type="text" name="secret_answer" id="secret_answer" class="validate[required]" value=""
            placeholder="Secret Answer" maxlength="190" style="width:320px;" tabindex="<?=$tabindex++?>"
            /><span class="required">*</span>
        </div>
        <div style="clear: both;"></div>
        <br />


        <hr style="width:68%; display:inline-block;" />
        <div style="clear: both;"></div>
        <br />

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
            <input type="submit" class="blue_btn" value="SUBMIT" style="width: 120px;" />
        </div>
        <div style="clear: both;"></div>
        <br />

        <hr style="width:67%; display:inline-block;" />
        <div style="clear: both;"></div>
        <br />


        <?php if(!empty($category_msg2)) { ?><div><?php echo $category_msg2; ?></div><?php } ?>

    </form>
    </div>
    </div>

    <?php
    } //else success..

}
else if(!empty($fv_msg) || !empty($fv_errors))
{
    ?>
    <br />
    <div style="text-align:center;">
    <div style="display:inline-block;"><img src="<?=SITE_URL?>assets/images/err.jpg" style="max-width:150px;" /></div>
    </div>
    <?php
}
?>
</div>

</div>
</div>
</div>

<?php
include_once("includes/footer.php");
?>