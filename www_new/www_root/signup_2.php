<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}
//var_dump($url_comp, $seo_tag_id); die();

////////////////////////////## Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}home';</script>";
    exit;
}

#/ check & redirect - if not coming from step1
#/*
if(!isset($_SESSION['signup_stage']) || empty($_SESSION['signup_stage']))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}signup';</script>";
    exit;
}

#/ Paypal Management


#/ check & redirect - if already crossed step2 (will use when we have step3)
if(isset($_SESSION['signup_stage']) && !empty($_SESSION['signup_stage']) && ($_SESSION['signup_stage']!='signup-details'))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}{$_SESSION['signup_stage']}';</script>";
    exit;
}
#*/
////////////////////##--

$success = false;
if(is_array($url_comp))
{
    if(@in_array('success', $url_comp)!=false){
    $success = true;
    }
}

$success_1 = false;
if(isset($_SESSION['signup_success']) && ($_SESSION['signup_success']=='1')){
$success_1 = true;
}

$success_2 = false;
if(isset($_SESSION['signup_success']) && ($_SESSION['signup_success']=='2')){
$success_2 = true;
$success = true;
}
//var_dump($_SESSION['signup_success'], $success_1, $success_2); die();

/////////////////////////////////////////////////////////////////////

#/ Process Post
if(isset($_POST['screen_name']) && ($success_2 == false))
{
    $_SESSION['signup_stage'] = 'signup-details';

    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    if(check_attempts(3)==false){
    update_attempt_counts(); redirect_me($seo_tag);
    }

    $fv_errors = '';

    #/ get old page posted data
    $POST_1 = @$_SESSION['signup_filled']['1'];
    if(!is_array($POST_1) || count($POST_1)<=0 || !array_key_exists('email_add', $POST_1))
    {
        $fv_errors[] = array('Unable to process your request at this moment! Please try again later.');
    }

    #/ Check & match User Info in DB
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT email_add, id FROM users WHERE email_add='{$POST_1['email_add']}'", 'single');
        if(empty($chk_user) || !isset($chk_user['id']))
        {
            $fv_errors[] = array('Unable to process your request at this moment! Please try again later.');
        }
    }


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0)){

    ##/ Validate Fields
    include_once('../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['screen_name'], ['identify_by'], ['address_ln_1'], ['city'], ['country_code'], ['state'], ['zip']],
    'lengthMin' => [['screen_name', 5]],
    'lengthMax' => [['screen_name', 50], ['identify_by', 50], ['address_ln_1', 200], ['address_ln_2', 150], ['phone_number', 20], ['city', 200], ['country_code', 2], ['state', 50], ['zip', 20]],
    'slug' => [['screen_name']],
    ];

    $form_v->labels(array(
    'identify_by' => 'Identification',
    'country_code' => 'Country',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();

    //var_dump("<pre>", $_POST, $fv_errors); die();
    }
    #-


    #/ Check if Screen Name Already exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_scr = mysql_exec("SELECT screen_name FROM users WHERE screen_name='{$_POST['screen_name']}'", 'single');
        if(!empty($chk_scr))
        {
            $fv_errors[] = array('This Screen Name is already used, please try a different one!');
        }
    }


    ##/ Process
    //var_dump("<pre>", $fv_errors, $POST_1, $chk_user); die();
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $user_id = $chk_user['id'];

        #/ Free Package Processing
        include_once('../includes/process_signup.php');
        process_signup_3($_POST, $_FILES, $user_id);
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
    $countries = @format_str(@mysql_exec("SELECT * FROM countries ORDER BY country_name"));
    $states = @format_str(@mysql_exec("SELECT * FROM states WHERE country_code='US' ORDER BY state_name"));
}

$category_msg2 = "If you have already completed this step,
please <b><a href=\"{$consts['DOC_ROOT']}signin\">Click Here to Signin</a></b> instead.";

$category_msg = "Please fill the form below to complete your <b>Profile Info</b> at collaborateUSA.com. ".$category_msg2;

/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=> "Signup: Profile Info",
);
$page_heading = "Membership at collaborateUSA.com";

$load_validation = true;
include_once("includes/header.php");
include_once('../includes/upload_btn_front.php');
/////////////////////////////////////////////////////////////////////
?>
<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/form.css" />
<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/country_state.js"></script>
<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/custom.js"></script>

<script>
$(document).ready(function() {
    $("#form_signup").validationEngine({
    promptPosition: loc, //"topRight"
    });
});


function chck_img(field)
{
    var err = '';
    //$(document).ready(function(e){
    var field_v = field.val();

    if(field_v==''){
    }
    else if(!/(\.gif|\.jpg|\.jpeg|\.png)$/i.test(field_v))
    {
       err = 'The Profile Pic must be in JPG, GIF or PNG format!';
       return err;
    }
    //});

}//end func....

$(document).ready(function(e){

    $("#profile_pic").change(function(){
        if($('#pImage_profile_pic').validationEngine('validate')) {return false;}
        preview_img(this, 'profile_thumb'); //preview image
    });
});
</script>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">

    <h1><strong><?=format_str($pg_meta['page_title'])?></strong></h1>
    <br />

    <div>
        <?php if($success!=false) { ?>

        <div>Thank you for completing your <b>Profile</b> at collaborateUSA.com,
        please <a href="<?=$consts['DOC_ROOT']?>signin"><b>Signin</b></a> to your Account.<br /><br />

        Do note, in order to use your Account you are required to Activate it. For this, an email was sent previously to your provided
        Email Address with an <b>Account Activation Link</b>.
        If you have not Activated your account yet, please follow that email in order to complete the <b>Activation</b> step.
        <br /><br />

        Once again, we would like to thank you for joining us.<br /><br /><br />

        <b style="font-size:1.1em !important;">
        Best Regards,<br />
        <span style="color: #2CA1F4;">CollaborateUSA.com</span></b>
        </div>

        <?php } else { ?>


        <?php if($success_1!=false) { ?>

        <div>Thank you for <b>Signing Up</b> at collaborateUSA.com. Your Account has been successfully created.<br /><br />
        An email has been sent to your provided Email Address with an <b>Account Activation Link</b>.
        Please follow that email in order to <b>Activate</b> your account.</div>

        <?php } //else { ?>

        <br />
        <hr style="width:68%; display:inline-block;" />
        <br /><br />

        <?php if(!empty($category_msg)) { ?><div class="content"><?php echo $category_msg; ?></div><br /><?php } ?><br />

        <?php $tabindex=1; ?>
        <div class="fields-big">
        <form id="form_signup" name="form_signup" action="" method="POST" <?=AUTO_COMPLETE?> enctype="multipart/form-data">

            <h4 class="heading">Profile Info:</h4><br />

            <label>Screen Name</label>
            <div class="right_fl">
                <input type="text" id="screen_name" name="screen_name" class="validate[required, custom[Alpha_Numeric]]"
                value="<?php if(isset($_POST['screen_name'])){echo $_POST['screen_name'];} ?>"
                placeholder="Screen" maxlength="50" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
                <span class="submsg">(Your default identification)</span>
            </div>
            <div style="clear: both;"></div>


            <label>Identification</label>
            <div class="right_fl">
                <select name="identify_by" id="identify_by" class="validate[required]" style="width:337px;" tabindex="<?=$tabindex++?>">
                    <option value="">Select Identification</option>
                    <option value="screen_name">Screen Name</option>
                    <option value="full_name">Full Name</option>
                    <option value="company_name">Company Name</option>
                </select><span class="required">*</span>
                <?php if(isset($_POST['identify_by'])){echo "<script>document.getElementById('identify_by').value='{$_POST['identify_by']}';</script>";}
                else {echo "<script>document.getElementById('identify_by').value='screen_name';</script>";}  ?>
            </div>
            <div style="clear: both;"></div>
            <br class="dsk_br" />


            <label>Profile Pic</label>
            <div class="right_fl">
            <div class="in_put" style="display: inline-block; width:338px;">
                <?php echo upload_btn('profile_pic', 'validate[funcCall[chck_img]]', 'width:94%;', 'blue_btn', 'display: inline-block; width:100% !important;', 'margin-top:1px; padding:0; height:31px; border:none;', 'width:340px; font-size:19px !important; cursor:pointer; max-width:100%;', 'browse', 'image/*'); ?>
                <span class="submsg">(max size 250x250 allowed)</span>
                <div style="clear: both;"></div>

                <img id="profile_thumb" style="margin-top:3px;" width="50" height="50"
                src="<?=DOC_ROOT?>assets/images/ep.png" alt="Profile Image" class="userDp round_borders" />
                <div style="clear: both;"></div>
                <br /><hr />

            </div>
            </div>
            <div style="clear: both;"></div>
            <br class="dsk_br" />



            <br />
            <h4 class="heading">Address Info:</h4><br />

            <label>Address Line 1</label>
            <div class="right_fl">
                <input type="text" name="address_ln_1" id="address_ln_1" class="validate[required]"
                value="<?php if(isset($_POST['address_ln_1'])){echo $_POST['address_ln_1'];} ?>"
                placeholder="Address 1" maxlength="150" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>
            <div style="clear: both;"></div>


            <label>Address Line 2</label>
            <div class="right_fl">
                <input type="text" name="address_ln_2" id="address_ln_2" class=""
                value="<?php if(isset($_POST['address_ln_2'])){echo $_POST['address_ln_2'];} ?>"
                placeholder="Address 2" maxlength="150" style="width:320px;" tabindex="<?=$tabindex++?>" />
            </div>
            <div style="clear: both;"></div>


            <label>Phone Number</label>
            <div class="right_fl">
                <input type="text" name="phone_number" id="phone_number" class=""
                value="<?php if(isset($_POST['phone_number'])){echo $_POST['phone_number'];} ?>"
                placeholder="Phone" maxlength="20" style="width:130px;" tabindex="<?=$tabindex++?>" />
            </div>
            <div style="clear: both;"></div>
            <br class="dsk_br" />


            <label>City</label>
            <div class="right_fl">
                <input type="text" name="city" id="city" class="validate[required]"
                value="<?php if(isset($_POST['city'])){echo $_POST['city'];} ?>"
                placeholder="City" maxlength="190" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>
            <div style="clear: both;"></div>
            <br class="dsk_br" />


            <label>Country</label>
            <div class="right_fl">
                <select name="country_code" id="country_code" class="validate[required]" style="width:337px;"
                tabindex="<?=$tabindex++?>" onchange="change_state(this.value);">
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $country) { ?>
                    <option value='<?php echo $country['country_code']; ?>'><?php echo $country['country_name']; ?></option>
                    <?php } ?>
                </select><span class="required">*</span>
                <?php if(isset($_POST['country_code'])){echo "<script>document.getElementById('country_code').value='{$_POST['country_code']}';</script>";}
                else {echo "<script>document.getElementById('country_code').value='US';</script>";} ?>
            </div>
            <div style="clear: both;"></div>


            <label>State</label>
            <div class="right_fl" id="us_states_div">
                <select name="us_state" id="us_state" class="validate[required]" style="width:337px;"
                tabindex="<?=$tabindex++?>" onchange="set_state(this.value);">
                    <option value="">Select State</option>
                    <?php foreach ($states as $state) { ?>
                    <option value='<?php echo $state['state_code']; ?>'><?php echo $state['state_name']; ?></option>
                    <?php } ?>
                </select><span class="required">*</span>
                <?php if(isset($_POST['state'])){echo "<script>document.getElementById('us_state').value='{$_POST['state']}';</script>";} ?>
            </div>

            <div class="right_fl" id="intr_states_div" style="display:none;">
                <input type="text" name="state" id="state" class=""
                value="<?php if(isset($_POST['state'])){echo $_POST['state'];} ?>"
                placeholder="State" maxlength="50" style="width:130px;" tabindex="<?=$tabindex++?>" />
            </div>
            <div style="clear: both;"></div>

            <label>Zip</label>
            <div class="right_fl">
                <input type="text" name="zip" id="zip" class="validate[required]"
                value="<?php if(isset($_POST['zip'])){echo $_POST['zip'];} ?>"
                placeholder="Zip" maxlength="20" style="width:130px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>
            <div style="clear: both;"></div>


            <br />
            <label></label>
            <hr style="width:37%; display:inline-block;" />
            <br /><br />


            <label></label>
            <div class="right_fl" style="">
                <input type="submit" class="blue_btn" value="SUBMIT" style="width: 120px;" />
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

<?php
include_once("includes/footer.php");
?>