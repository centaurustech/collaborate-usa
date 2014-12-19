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

$member_id = $user_id;

/////////////////////////////////////////////////////////////////////

#/ Process Post
if(isset($_POST['email_add']))
{
    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    if(check_attempts(7)==false){
    update_attempt_counts(); redirect_me($seo_tag);
    }

    ##/ Validate Fields
    include_once('../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['email_add'], ['screen_name'], ['first_name'], ['last_name'], ['identify_by'], ['country_code'], ['state'], ['zip'], ['city'], ['address_ln_1']],
    'lengthMin' => [['screen_name', 5]],
    'lengthMax' => [['email_add', 150], ['screen_name', 50], ['first_name', 65], ['middle_name', 20], ['last_name', 50], ['company_name', 100], ['identify_by', 50], ['country_code', 2], ['state', 50], ['zip', 20], ['city', 200], ['address_ln_1', 200], ['address_ln_2', 150], ['phone_number', 20]],
    'email' => [['email_add']],
    'slug' => [['screen_name']],
    ];

    $form_v->labels(array(
    'email_add' => 'Email Address',
    'identify_by' => 'Identification',
    'country_code' => 'Country',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();

    //var_dump("<pre>", $member_id, $_POST, $fv_errors); die();
    #-


    #/ Check if Email Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT email_add FROM users WHERE email_add='{$_POST['email_add']}' and id!='{$user_id}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Email Address is already used, please try a different one!');
        }
    }

    #/ Check if screen_name Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT screen_name FROM users WHERE screen_name='{$_POST['screen_name']}' and id!='{$user_id}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Screen Name is already used, please try a different one!');
        }
    }


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        ###/ Process Profile Pic
        include_once('../includes/resize_images.php');
        $up_path = "user_files/prof/{$user_id}/";
        if(!is_dir($up_path)){mkdir($up_path, 0705, true);}

        $sql_prt = $profile_pic = '';
        if(is_uploaded_file(@$_FILES['profile_pic']['tmp_name']))
        {
            $copy_data = array(0=>array('i_part'=>'_th', 'size_w'=>60, 'size_h'=>60));

            $profile_pic = @upload_img_rs('profile_pic', 250, 250, $up_path, 'Profile Pic', '', 250, 'CUSA_MSG_GLOBAL', false, $copy_data);
            if($profile_pic!='') {
            $sql_prt.=", profile_pic='{$profile_pic}'";
            $_POST['profile_pic'] = $profile_pic;
            }
        }
        #-

        #/ update users
		$sql_users = "UPDATE users SET email_add='{$_POST['email_add']}',
        screen_name='{$_POST['screen_name']}', first_name='{$_POST['first_name']}', middle_name='{$_POST['middle_name']}', last_name='{$_POST['last_name']}',
        company_name='{$_POST['company_name']}', identify_by='{$_POST['identify_by']}' {$sql_prt}
        WHERE id='{$user_id}'";
		@mysql_exec($sql_users, 'save');


        #/ Delete Old image
        $cur_profile_pic = @$_POST["cur_profile_pic"];
        if(($cur_profile_pic!='') && ($profile_pic!='') && ($profile_pic != $cur_profile_pic)){
        @unlink($up_path.$cur_profile_pic);
        @unlink($up_path.@substr_replace($cur_profile_pic, '_th.', @strrpos($cur_profile_pic, '.'), 1)); //_th
        }


        #/ update user_info
		$sql_user_info = "UPDATE user_info SET country_code='{$_POST['country_code']}', state='{$_POST['state']}',
        city='{$_POST['city']}', address_ln_1='{$_POST['address_ln_1']}', address_ln_2='{$_POST['address_ln_2']}',
    	zip='{$_POST['zip']}', phone_number='{$_POST['phone_number']}'
        WHERE user_id='{$user_id}'";
		@mysql_exec($sql_user_info, 'save');


        #/ update user_permissions
        $fields_perm = implode(',', $_POST['user_perm']);
        $sql_user_permissions = "UPDATE user_permissions SET fields_perm='{$fields_perm}'
        WHERE user_id='{$user_id}'";
		@mysql_exec($sql_user_permissions, 'save');


        #/ Update Session
        foreach($_POST as $POSTk=>$POSTv)
        {
            if(array_key_exists($POSTk, $_SESSION['CUSA_Main_usr_info']))
            {
                $_SESSION['CUSA_Main_usr_info'][$POSTk] = $POSTv;
            }
        }
        //var_dump("<pre>", $_SESSION['CUSA_Main_usr_info']); die();


        #/ Redirect
        reset_attempt_counts();
        $_SESSION["CUSA_MSG_GLOBAL"] = array(true, "Your Profile data has been successfully Updated..");
        redirect_me($seo_tag);
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

#/ get Members Profile Info
include_once('../includes/profile_func.php');

$member_info_ar = get_member_info($member_id, $user_id);
$member_info = @$member_info_ar[0];
//var_dump("<pre>", $member_id, $member_info, mysql_error()); die();
if(!is_array($member_info) || !array_key_exists('user_ident', $member_info)){redirect_me('404');}

#/ User Permission
$user_permissions = @$member_info_ar[1];
//var_dump("<pre>", $user_permissions); die();

#/ Permission Images
$public = "{$consts['DOC_ROOT']}assets/images/secure_public.png";
$private = "{$consts['DOC_ROOT']}assets/images/secure_private.png";


#/ Current Profile Pic
$prof_pic = DOC_ROOT."assets/images/ep.png";
if(array_key_exists('profile_pic', $member_info))
{
    if(!@empty($member_info['profile_pic'])){
    $prof_pic = DOC_ROOT."user_files/prof/{$member_id}/{$member_info['profile_pic']}";
    }
}
$prof_pic_th = @substr_replace($prof_pic, '_th.', @strrpos($prof_pic, '.'), 1);

#/ Country & State
$countries = @format_str(@mysql_exec("SELECT * FROM countries ORDER BY country_name"));
$states = @format_str(@mysql_exec("SELECT * FROM states WHERE country_code='US' ORDER BY state_name"));

#/ Data to be placed
$empt = $member_info;
$empt_p = $user_permissions;
if(isset($_POST['email_add']))
{
    $empt = $_POST;
    $empt_p = $_POST['user_perm'];
}
//var_dump("<pre>", $empt_p); die();
/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=> "Profile Update",
);
$page_heading = $pg_meta['page_title'];

$load_validation = true;
include_once("includes/header.php");
include_once('../includes/upload_btn_front.php');

/////////////////////////////////////////////////////////////////////
?>
<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/form.css" />
<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/country_state.js"></script>
<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/custom.js"></script>

<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/jquery.ddslick.js"></script>
<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/ddslick.css" />

<script>
$(document).ready(function() {
    $("#form_pass").validationEngine({
    promptPosition: "topRight"
    });

    $('.perm_settings').each(function(){
        $(this).ddslick({
            width: 195,
            truncateDescription: true,
            onSelected: function(selectedData){
            }
        });
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

<style>
.userDp {
    width: 60px !important;
    height: 60px !important;
}
</style>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">
    <h1><strong><?=$page_heading?> Form</strong></h1><br />

    <div>Please use this form to <b>Update</b> your Profile. It also allows you to update the
    <b>Privacy Settings</b> of important fields directly from here.</div><br />

    <br />
    <?php $tabindex=1; ?>
    <div class="" style="margin-left:0;">
    <div class="contact-main fields-big">
    <form id="form_pass" name="form_pass" action="" method="POST" autocomplete="off" enctype="multipart/form-data">

        <input type="hidden" name="cur_profile_pic" value="<?=(@$empt['profile_pic'])?>" />

        <?php /*<h4 class="heading" style="text-decoration: underline;">Profile Info</h4>*/ ?>
        <br />

        <label><b>Profile Pic</b></label>
        <div class="right_fl">
        <div class="in_put" style="display: inline-block; width:338px;">
            <?php echo upload_btn('profile_pic', 'validate[funcCall[chck_img]]', 'width:94%;', 'blue_btn', 'display: inline-block; width:100% !important;', 'margin-top:1px; padding:0; height:31px; border:none;', 'width:340px; font-size:19px !important; cursor:pointer; max-width:100%;', 'browse', 'image/*'); ?>
            <span class="submsg">(max size 250x250 allowed)</span>
            <div style="clear: both;"></div>

            <img id="profile_thumb" style="margin-top:3px;" width="60" height="60"
            src="<?=$prof_pic_th?>" alt="Profile Image" class="userDp round_borders" />
            <div style="clear: both;"></div>
            <br /><hr />

        </div>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>Email Address</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" name="email_add" id="email_add" class="validate[required, custom[email]]"
                value="<?php if(isset($empt['email_add'])){echo $empt['email_add'];} ?>"
                placeholder="Email" maxlength="150" style="width:320px" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
                <span class="submsg">(will also be used as your Signin Id)</span>
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('email_add', $empt_p)){echo "selected=\"\"";} ?> value="email_add" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('email_add', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>Screen Name</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" id="screen_name" name="screen_name" class="validate[required, custom[Alpha_Numeric]]"
                value="<?php if(isset($empt['screen_name'])){echo $empt['screen_name'];} ?>"
                placeholder="Screen" maxlength="50" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
                <span class="submsg">(Your default identification)</span>
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('screen_name', $empt_p)){echo "selected=\"\"";} ?> value="screen_name" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('screen_name', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>First Name</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" id="first_name" name="first_name" class="validate[required]"
                value="<?php if(isset($empt['first_name'])){echo $empt['first_name'];} ?>"
                placeholder="First" maxlength="50" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('first_name', $empt_p)){echo "selected=\"\"";} ?> value="first_name" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('first_name', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br class="mbl_br" />

        <label>Middle Name</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" id="middle_name" name="middle_name" class=""
                value="<?php if(isset($empt['middle_name'])){echo $empt['middle_name'];} ?>"
                placeholder="Middle" maxlength="20" style="width:90px;" tabindex="<?=$tabindex++?>" />
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('middle_name', $empt_p)){echo "selected=\"\"";} ?> value="middle_name" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('middle_name', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br class="mbl_br" />

        <label>Last Name</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" name="last_name" id="last_name" class="validate[required]"
                value="<?php if(isset($empt['last_name'])){echo $empt['last_name'];} ?>"
                placeholder="Last" maxlength="50" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('last_name', $empt_p)){echo "selected=\"\"";} ?> value="last_name" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('last_name', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>Company / Organization</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" name="company_name" id="company_name" class=""
                value="<?php if(isset($empt['company_name'])){echo $empt['company_name'];} ?>"
                placeholder="Organization" maxlength="100" style="width:320px;" tabindex="<?=$tabindex++?>" />
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('company_name', $empt_p)){echo "selected=\"\"";} ?> value="company_name" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('company_name', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label><b>Identification</b></label>
        <div class="right_fl">
            <select name="identify_by" id="identify_by" class="validate[required]" style="width:337px;" tabindex="<?=$tabindex++?>">
                <option value="">Select Identification</option>
                <option value="screen_name">Screen Name</option>
                <option value="full_name">Full Name</option>
                <option value="company_name">Company Name</option>
            </select><span class="required">*</span>
            <span class="submsg">(Your preferred identification. Selected item will always <br /> be displayed irrespective of its Permission)</span>
            <?php if(isset($empt['identify_by'])){echo "<script>document.getElementById('identify_by').value='{$empt['identify_by']}';</script>";}
            else {echo "<script>document.getElementById('identify_by').value='screen_name';</script>";}  ?>
        </div>
        <div style="clear: both;"></div>
        <br />


        <hr style="width:67%; display:inline-block;" />
        <div style="clear: both;"></div>
        <br />

        <h4 class="heading" style="text-decoration: underline;">Location</h4><br />

        <label>Country</label>
        <div class="right_fl">
            <select name="country_code" id="country_code" class="validate[required]" style="width:337px;"
            tabindex="<?=$tabindex++?>" onchange="change_state(this.value);">
                <option value="">Select Country</option>
                <?php foreach ($countries as $country) { ?>
                <option value='<?php echo $country['country_code']; ?>'><?php echo $country['country_name']; ?></option>
                <?php } ?>
            </select><span class="required">*</span>
            <?php if(isset($empt['country_code'])){echo "<script>document.getElementById('country_code').value='{$empt['country_code']}';</script>";}
            else {echo "<script>document.getElementById('country_code').value='US';</script>";} ?>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>State</label>
        <div class="right_fl">
            <div class="right_fl" id="us_states_div">
                <select name="us_state" id="us_state" class="validate[required]" style="width:337px;"
                tabindex="<?=$tabindex++?>" onchange="set_state(this.value);">
                    <option value="">Select State</option>
                    <?php foreach ($states as $state) { ?>
                    <option value='<?php echo $state['state_code']; ?>'><?php echo $state['state_name']; ?></option>
                    <?php } ?>
                </select><span class="required">*</span>
                <?php if(isset($empt['state'])){echo "<script>document.getElementById('us_state').value='{$empt['state']}';</script>";} ?>
            </div>

            <div class="right_fl" id="intr_states_div" style="display:none;">
                <input type="text" name="state" id="state" class=""
                value="<?php if(isset($empt['state'])){echo $empt['state'];} ?>"
                placeholder="State" maxlength="50" style="width:130px;" tabindex="<?=$tabindex++?>" />
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('state', $empt_p)){echo "selected=\"\"";} ?> value="state" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('state', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>Zip</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" name="zip" id="zip" class="validate[required]"
                value="<?php if(isset($empt['zip'])){echo $empt['zip'];} ?>"
                placeholder="Zip" maxlength="20" style="width:130px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('zip', $empt_p)){echo "selected=\"\"";} ?> value="zip" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('zip', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br /><br />


        <label>City</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" name="city" id="city" class="validate[required]"
                value="<?php if(isset($empt['city'])){echo $empt['city'];} ?>"
                placeholder="City" maxlength="190" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('city', $empt_p)){echo "selected=\"\"";} ?> value="city" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('city', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>Address Line 1</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" name="address_ln_1" id="address_ln_1" class="validate[required]"
                value="<?php if(isset($empt['address_ln_1'])){echo $empt['address_ln_1'];} ?>"
                placeholder="Address 1" maxlength="150" style="width:320px;" tabindex="<?=$tabindex++?>"
                /><span class="required">*</span>
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('address_ln_1', $empt_p)){echo "selected=\"\"";} ?> value="address_ln_1" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('address_ln_1', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br class="mbl_br" />

        <label>Address Line 2</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" name="address_ln_2" id="address_ln_2" class=""
                value="<?php if(isset($empt['address_ln_2'])){echo $empt['address_ln_2'];} ?>"
                placeholder="Address 2" maxlength="150" style="width:320px;" tabindex="<?=$tabindex++?>" />
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('address_ln_2', $empt_p)){echo "selected=\"\"";} ?> value="address_ln_2" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('address_ln_2', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <br />


        <label>Phone Number</label>
        <div class="right_fl">
            <div class="right_fl">
                <input type="text" name="phone_number" id="phone_number" class=""
                value="<?php if(isset($empt['phone_number'])){echo $empt['phone_number'];} ?>"
                placeholder="Phone" maxlength="20" style="width:130px;" tabindex="<?=$tabindex++?>" />
            </div>

            <div class="right_fl dd_main_container" style="margin-top:6px;">
                <select id="user_perm[]" name="user_perm[]" class="perm_settings">
                    <option <?php if(is_array($empt_p) && in_array('phone_number', $empt_p)){echo "selected=\"\"";} ?> value="phone_number" data-imagesrc="<?=$private?>" data-description="Visible to Me & My Connections">&nbsp;</option>
                    <option <?php if(is_array($empt_p) && !in_array('phone_number', $empt_p)){echo "selected=\"\"";} ?> value="" data-imagesrc="<?=$public?>" data-description="Visible to Public">&nbsp;</option>
                </select>
            </div>
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

</div>

</div>
<div class="clear"></div>
</div>
</div>

<?php
include_once("includes/footer.php");
?>