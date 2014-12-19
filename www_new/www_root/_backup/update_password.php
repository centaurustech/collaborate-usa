<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}

include_once('includes/session.php');

$user_id = (int)@$_SESSION["CUSA_Main_usr_id"];
if($user_id<=0){exit;}
$user_info = @$_SESSION["CUSA_Main_usr_info"];

$success = false;
if(@in_array('success', $url_comp)!=false){
$success = true;
}

/////////////////////////////////////////////////////////////////////

#/ Process Post
if(isset($_POST['pass_curr']))
{
    include_once('../includes/func_enc.php');

    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    #/*
    if(check_attempts(2)==false)
    {
        if($_SESSION["au_wrongtry"]>=4) redirect_me('logout'); //additional security
        update_attempt_counts(); redirect_me($seo_tag);
    }#*/

    ##/ Validate Fields
    include_once('../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['pass_curr'], ['pass_w'], ['c_pass_w'], ['secret_question_id'], ['secret_answer'], ['vercode']],
    'lengthMin' => [['pass_curr', 5], ['pass_w', 7], ['c_pass_w', 7]],
    'lengthMax' => [['pass_curr', 20], ['pass_w', 20], ['c_pass_w', 20], ['secret_answer', 190], ['vercode', 10]],
    'equals' => [['c_pass_w', 'pass_w']],
    ];

    $form_v->labels(array(
    'pass_curr' => 'Current Password',
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

    #/ Check & match current password
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $_POST['secret_question_id'] = (int)@$_POST['secret_question_id'];

        $pass_curr = @$_POST['pass_curr'];
        $pass_curr_w = @md5_encrypt($pass_curr);

        $sql_p1 = "SELECT u.*
        FROM users u
        LEFT JOIN user_info ui ON u.id = ui.user_id

        WHERE u.id='{$user_id}'
        AND u.pass_w='{$pass_curr_w}'
        AND u.email_add='{$user_info['email_add']}'
        AND ui.secret_question_id='{$_POST['secret_question_id']}' AND ui.secret_answer='{$_POST['secret_answer']}'
        ";
        $current_info = @mysql_exec($sql_p1, 'single');

        if(!is_array($current_info) || empty($current_info) || !array_key_exists('email_add', $current_info)){
        $fv_errors[] = array('Your provided info (<b>Current Password</b> or the <b>Secret Question</b> info) does not match your current records! Please try again.');
        }
    }


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        #/ encrypt password
        $new_pass = @$_POST['pass_w'];
        $pass_w = @md5_encrypt($new_pass);

        #/save users
        $sql_users = "UPDATE users SET pass_w='{$pass_w}'
        WHERE email_add='{$user_info['email_add']}' AND id='{$user_id}'";
        @mysql_exec($sql_users, 'save');


        ##/ Send Emails to User
        include_once('../includes/email_templates.php');
        include_once('../includes/send_mail.php');

        $heading = $subject = "Password Updated at collaborateUSA.com";
        $body_in = password_updated($user_info, $new_pass);
        send_mail($user_info['email_add'], $subject, $heading, $body_in);
        #-


        #/ Redirect
        reset_attempt_counts();
        $_SESSION["CUSA_MSG_GLOBAL"] = array(true, "Your Account Password has been successfully Updated.");
        redirect_me($seo_tag.'/success');

        exit;
    }
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
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

$category_msg = "Please use the form below to <b>Update</b> your Account Password at collaborateUSA.com.";


#/ Fill pg_meta
$pg_meta = array(
    'page_title'=> "Password Update",
);
$page_heading = $pg_meta['page_title'];
$head_msg = "Update Account Password at collaborateUSA.com";

$load_validation = true;
include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>
<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/form.css" />

<script>
$(document).ready(function() {
    $("#form_pass").validationEngine({
    promptPosition: "topRight"
    });
});
</script>


<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">
    <h1><strong><?=$page_heading?> Form</strong></h1><br />

    <?php if($success!=false) { ?>

    <style>
    .row-fluid .right_menu_e{
        display:none;
    }
    </style>

    <br />
    <div>Dear User,<br /><br />
    Your Password has been <b>successfully Updated</b>.<br /><br />
    Furthermore, an Email containing the details have been sent to your registered email address.<br /><br />

    <br />
    <b style="font-size: 1.2em !important%;">
    Best Regards,<br />
    <span style="color: #2CA1F4;">collaborateUSA.com</span></b>
    </div>
    <br />

    <?php }else { ?>


    <?php if(!empty($category_msg)) { ?><div class="content"><?php echo $category_msg; ?></div><?php } ?><br />

    <br />
    <?php $tabindex=1; ?>
    <div class="" style="margin-left:0;">
    <div class="contact-main fields-big">
    <form id="form_pass" name="form_pass" action="" method="POST" autocomplete="off">

        <h4 class="heading" style="text-decoration: underline;">Password Info</h4><br />

        <label>Current Password:</label>
        <div class="right_fl">
            <input type="password" name="pass_curr" id="pass_curr" class="validate[required]" autocomplete="off"
            placeholder="Confirm Password" maxlength="20" style="width:320px;" tabindex="<?=$tabindex++?>"
            /><span class="required">*</span>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>New Password:</label>
        <div class="right_fl">
            <input type="password" name="pass_w" id="pass_w" class="validate[required, minSize[7]]" autocomplete="off"
            placeholder="Password" maxlength="20" style="width:320px;" tabindex="<?=$tabindex++?>"
            /><span class="required">*</span>
        </div>
        <div style="clear: both;"></div>


        <label>&nbsp;</label>
        <div class="right_fl">
            <input type="password" name="c_pass_w" id="c_pass_w" class="validate[required, equals[pass_w]]" autocomplete="off"
            placeholder="Confirm Password" maxlength="20" style="width:320px;" tabindex="<?=$tabindex++?>"
            /><span class="required">*</span>
        </div>
        <div style="clear: both;"></div>
        <br />


        <hr style="width:67%; display:inline-block;" />
        <div style="clear: both;"></div>
        <br />


        <h4 class="heading" style="text-decoration: underline;">Verification</h4><br />

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
        <br />


        <label></label>
        <div class="right_fl" style="">
            <input type="submit" class="blue_btn" value="SUBMIT" style="width: 120px;" />
        </div>
        <div style="clear: both;"></div>

    </form>
    </div>
    </div>

    <?php } //else success.. ?>

</div>

</div>
<div class="clear"></div>
</div>
</div>

<?php
include_once("includes/footer.php");
?>