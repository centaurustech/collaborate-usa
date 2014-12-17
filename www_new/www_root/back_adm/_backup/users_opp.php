<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 7; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('u_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$u_id = (int) getgpcvar("u_id", "G");
$read_only = (int)getgpcvar("ro", "G");
//$read_only = 1; //testing

$back_page = "users.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if($read_only<=0)
if(isset($_POST['first_name']))
{
    $u_id = (int) getgpcvar("u_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);


    $rules = [
    'required' => [['package_id'], ['email_add'], ['first_name'], ['last_name'], ['screen_name']], //['address_ln_1'], ['city'], ['country_code'], ['state'], ['zip']
    'lengthMin' => [['screen_name', 5]],
    'lengthMax' => [['email_add', 120], ['first_name', 50], ['middle_name', 20], ['last_name', 50], ['company_name', 100], ['screen_name', 50], ['address_ln_1', 200], ['city', 180], ['country_code', 2], ['state', 50], ['zip', 20]],
    'email' => [['email_add']],
    'slug' => [['screen_name']],
    ];

    $form_v->labels(array(
    'package_id' => 'Membership Package',
    'email_add' => 'Email Address',
    'country_code' => 'Country',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();

    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    #/ Check if Email Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT email_add FROM users WHERE email_add='{$_POST['email_add']}' and id!='{$u_id}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Email Address is already used, please try a different one!');
        }
    }

    #/ Check if screen_name Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT screen_name FROM users WHERE screen_name='{$_POST['screen_name']}' and id!='{$u_id}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Screen Name is already used, please try a different one!');
        }
    }


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $account_activated = (int)@$_POST['account_activated'];
        $is_blocked = (int)@$_POST['is_blocked'];
        $_POST['package_id'] = (int)@$_POST['package_id'];


        if($u_id>0)  //Edit Mode
        {
            #/ users
    		$sql_users = "UPDATE users SET package_id='{$_POST['package_id']}', email_add='{$_POST['email_add']}',
            screen_name='{$_POST['screen_name']}', first_name='{$_POST['first_name']}', middle_name='{$_POST['middle_name']}', last_name='{$_POST['last_name']}',
            company_name='{$_POST['company_name']}', account_activated='{$account_activated}', is_blocked='{$is_blocked}'
            WHERE id='{$u_id}'";
    		mysql_exec($sql_users, 'save');


            #/ user_info
    		$sql_user_info = "UPDATE user_info SET country_code='{$_POST['country_code']}', state='{$_POST['state']}',
            city='{$_POST['city']}', address_ln_1='{$_POST['address_ln_1']}', address_ln_2='{$_POST['address_ln_2']}',
        	zip='{$_POST['zip']}', phone_number='{$_POST['phone_number']}'
            WHERE user_id='{$u_id}'";
    		mysql_exec($sql_user_info, 'save');


            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The User data was successfully Updated..');
            //redirect_me("{$consts['DOC_ROOT_ADMIN']}admin_users_opp.php{$param2}&u_id={$u_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {
            ////////////////-------

            #/ encrypt password
            include_once('../../includes/func_enc.php');
            $new_pass = (string)createRandomPassword();
    		$pass_w = md5_encrypt($new_pass);

            ###/ Updating Database

            #/ users
            $sql_users = "INSERT INTO users
        	(package_id, email_add, pass_w, screen_name, first_name, middle_name, last_name, company_name, account_activated, is_blocked, joined_on)
        	values('{$_POST['package_id']}', '{$_POST['email_add']}', '{$pass_w}', '{$_POST['screen_name']}', '{$_POST['first_name']}', '{$_POST['middle_name']}', '{$_POST['last_name']}', '{$_POST['company_name']}', '{$account_activated}', '{$is_blocked}', NOW())";
            mysql_exec($sql_users, 'save');
            $u_id = (int)@mysql_insert_id();
            #-

            if($u_id>0)
            {
                #/ user_info
                $sql_user_info = "INSERT INTO user_info
            	(user_id, country_code, state, city, address_ln_1, address_ln_2, zip, phone_number)
            	values('{$u_id}', '{$_POST['country_code']}', '{$_POST['state']}', '{$_POST['city']}', '{$_POST['address_ln_1']}', '{$_POST['address_ln_2']}', '{$_POST['zip']}', '{$_POST['phone_number']}')";
                mysql_exec($sql_user_info, 'save');


                #/acc_verifications
                $verification_str = mt_rand().md5(uniqid(rand())).mt_rand();
                $sql_veri = "INSERT INTO acc_verifications (user_id, verification_str)
                VALUES ('{$u_id}', '{$verification_str}')";
        		@mysql_exec($sql_veri, 'save');


                #/save user_permissions //private fields only
                $fields_perm = 'email_add,state,city,address_ln_1,address_ln_2,zip,phone_number';
                $sql_fields_perm = "INSERT INTO user_permissions (user_id, fields_perm)
                VALUES ('{$u_id}', '{$fields_perm}')";
        		@mysql_exec($sql_fields_perm, 'save');



                #/ Send Account Creation Email
                include_once('../../includes/email_templates.php');
                include_once('../../includes/send_mail.php');

                $chk_pkg = mysql_exec("SELECT * FROM membership_packages WHERE id='{$_POST['package_id']}'", 'single');
                $heading = $subject = "Account Setup Confirmation from collaborateUSA.com";

                $insert_msg = "Please find below your Temporary Password:<br /><br />";
                $insert_msg.= "<b>Email Address</b>: {$_POST['email_add']}<br />";
                $insert_msg.= "<b>Password</b>: {$new_pass}<br /><br />";
                $insert_msg.= "Please note that this is a temporary password and you should update it after your first login.";

                $body_in = signup_success($_POST['first_name'], $u_id, $verification_str, $insert_msg, $chk_pkg, true);
                send_mail($_POST['email_add'], $subject, $heading, $body_in, 'collaborateUSA.com', $consts['mem_support_em']);
                #-
            }


            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The User data has been Added successfully. A Temporary Password has been generated and an Email has been sent to their Email Address.');
            //redirect_me("{$consts['DOC_ROOT_ADMIN']}admin_users_opp.php{$param2}&u_id={$u_id}", true);

        }//end Add ..


        ##/ Image processing & savings
        if($u_id>0)
        {
            include_once('../../includes/resize_images.php');
            $up_path = "../user_files/prof/{$u_id}/";
            if(!is_dir($up_path)){mkdir($up_path, 0705, true);}

            $sql_upd_prt = $profile_pic = '';
            if(is_uploaded_file(@$_FILES['profile_pic']['tmp_name']))
            {
                $copy_data = array(0=>array('i_part'=>'_th', 'size_w'=>35, 'size_h'=>35));

                $profile_pic = upload_img_rs('profile_pic', 250, 250, $up_path, 'Profile Pic', '', 250, 'CUSA_MSG_GLOBAL', false, $copy_data);
                if($profile_pic!='') {
                $sql_upd_prt.=" profile_pic='{$profile_pic}' ";
                }
            }
            //var_dump("<pre>", $u_id, $sql_upd_prt); die();

            if(!empty($sql_upd_prt))
            {
                #/ Update DB
                $sql_users = "UPDATE users SET {$sql_upd_prt}
                WHERE id='{$u_id}'";
        		mysql_exec($sql_users, 'save');


                #/ Delete Old image
                $cur_profile_pic = @$_POST["cur_profile_pic"];
                if(($cur_profile_pic!='') && ($profile_pic!='') && ($profile_pic != $cur_profile_pic)){
                @unlink($up_path.$cur_profile_pic);
                @unlink($up_path.@substr_replace($cur_profile_pic, '_th.', @strrpos($cur_profile_pic, '.'), 1)); //_th
                }
            }
        }
        #-


        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&u_id={$u_id}", true);

    }//end if errors...
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, $fv_msg);
    }

}////end if post.................................
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


#### Get record if EDIT Mode
$empt = array();
if (($u_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT *, u.id AS u_id
    FROM users u
    LEFT JOIN user_info ui ON u.id=ui.user_id
    WHERE u.id='%d'", $u_id);

	$token  = mysql_query($query, $cn1);
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}
}

if(isset($_POST['first_name']))
{
    $empt = $_POST;
}
//var_dump("<pre>", $empt);
///////////////////////////////////////////////////////////////////

if($read_only>0)
$no_header = true;
else{
$load_fancy = true;
include_once('../../includes/upload_btn.php');
}

$pg_title = "Members / Users";
include_once("includes/header.php");
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($u_id>0)? "Review ": "Add "; ?> Record</h1></div>
<?php if($read_only<=0) { ?><div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div><?php } ?>
<div style="clear:both; height:15px;">&nbsp;</div>

<?php if($read_only<=0) { ?>
<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/country_state.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$(".fbox").fancybox({
		minHeight   : 5,
		minWidth    : 5,
        maxWidth	: 950,
		maxHeight	: 600,
		autoSize	: true,
        fitToView	: false,
        openEffect	: 'elastic',
		closeEffect	: 'elastic',
	});
});

function check_this()
{
    var err = '';

    if(document.getElementById('package_id').value=='')
    {
        err += 'Please select a Membership Package!\n';
    }

    if(document.getElementById('email_add').value=='')
    {
        err += 'Please enter the Email Address!\n';
    }

    if(document.getElementById('first_name').value=='')
    {
        err += 'Please enter the First Name!\n';
    }

    if(document.getElementById('last_name').value=='')
    {
        err += 'Please enter the Last Name!\n';
    }

    if(document.getElementById('screen_name').value=='')
    {
        err += 'Please enter the Screen Name!\n';
    }
    else if(document.getElementById('screen_name').value.search(/^[a-z0-9\-_]{1,}$/i)<0)
    {
        err += 'The Screen Name can only contain Alphanumeric values (with Dash or Underscore as separators)!\n';
    }


    if(document.getElementById('profile_pic').value!='')
    if(!/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i.test(document.getElementById('profile_pic').value))
    {
        err += 'Please select the Profile Pic in JPG, GIF or PNG format\n';
    }


    /*if(document.getElementById('address_ln_1').value=='')
    {
        err += 'Please enter the Address Line 1!\n';
    }

    if(document.getElementById('city').value=='')
    {
        err += 'Please enter the City!\n';
    }

    if(document.getElementById('country_code').value=='')
    {
        err += 'Please select a Country!\n';
    }


    if(document.getElementById('country_code').value=='US')
    {
        if(document.getElementById('us_state').value=='')
        {
            err += 'Please select a State!\n';
        }
    }
    else
    {
       if(document.getElementById('state').value=='')
        {
            err += 'Please enter the State!\n';
        }
    }


    if(document.getElementById('zip').value=='')
    {
        err += 'Please enter the Zip Code!\n';
    }
    */


    if(err!='')
    {
        alert("Please clear the following ERROR(s):\n\n"+err);
        return false;
    }
    else
    {
        return true;
    }

    return false;
}//end func....
</script>
<?php } ?>
<!-- //////////////////// -->


<?php if($read_only<=0) { ?>
<form action="" method="post" name="f2" id="f2" autocomplete="off" onsubmit="return check_this();" enctype="multipart/form-data">

<?php if($u_id){ ?>
<input type="hidden" name="u_id" id="u_id" value="<?php echo $u_id; ?>" />
<input type="hidden" name="cur_profile_pic" value="<?=(@$empt['profile_pic'])?>" />
<?php } ?>

<?php } ?>

<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>ACCOUNT INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:150px; float:left;">Membership Package:</div>
        <?php if($read_only<=0) { ?><div style="float:left;">
            <?php
            $membership_packages = @format_str(@mysql_exec("SELECT * FROM membership_packages ORDER BY title"));
            ?>
            <select id="package_id" name="package_id" style="width:260px; border:1px solid #000261;">
            <option value=""></option>
            <?php if(is_array($membership_packages) && count($membership_packages)>0)
            foreach($membership_packages as $pack_v)
            {
                $pack_v['cost'] = (float)$pack_v['cost'];
                $cost_dv = number_format($pack_v['cost'], 2);
                echo "<option value=\"{$pack_v['id']}\">{$pack_v['title']}".($pack_v['cost']>0? " (\${$cost_dv})":'')."</option>";
            }
            ?>
            </select>
            <?php if(isset($empt['package_id'])) echo "<script>document.getElementById('package_id').value='{$empt['package_id']}';</script>"; ?>
            <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
            <div style="clear:both; height:5px;"></div>
            <span class="submsg">(if you manually change the Package, you may have to manually enter the <b>Payment Data</b> from Payment's page if a Paid Package is being selected)</span>
        </div>
        <?php }else{ ?><div style="float:left;"><?php
        if(isset($empt['package_id']))
        {
            $pack_v = @format_str(@mysql_exec("SELECT * FROM membership_packages WHERE id='{$empt['package_id']}'", 'single'));

            $pack_v['cost'] = (float)$pack_v['cost'];
            $cost_dv = number_format($pack_v['cost'], 2);
            echo @($pack_v['title'].($pack_v['cost']>0? " (\${$cost_dv})":''));
        }
        ?></div>
        <?php } ?>



        <div style="clear:both; height:20px;"></div>

        <div style="width:150px; float:left;">Email/LoginID:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="email_add" name="email_add" autocomplete="off" maxlength="120" value="<?=format_str(@$empt['email_add'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
        <?php }else{ ?><div style="float:left; font-weight:bold;"><?=format_str(@$empt['email_add'])?></div>
        <?php } ?>

        <?php if($u_id){ ?>
        <div style="clear:both; height:20px;"></div>

        <div style="width:150px; float:left;">Account Activated?</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="checkbox" name="account_activated" id="account_activated" value="1" <?php if(@$empt['account_activated']=='1') echo "checked='checked'"; ?> />
        <span class="submsg">Automatically Actiavted when Users verify their Email Address</span></div>
        <?php }else{ ?><div style="float:left;"><?=(@$empt['account_activated']=='1')? 'Yes':'No'?></div>
        <?php } ?>

        <div style="clear:both; height:10px;"></div>

        <div style="width:150px; float:left;">Block Account?</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="checkbox" name="is_blocked" id="is_blocked" value="1" <?php if(@$empt['is_blocked']=='1') echo "checked='checked'"; ?> />
        <span class="submsg">For Admin based Account Access blockage</span></div>
        <?php }else{ ?><div style="float:left;"><?=(@$empt['is_blocked']=='1')? 'Yes':'No'?></div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>

        <div style="width:150px; float:left;">Joined On:</div>
        <div style="float:left;"><?=format_str(@$empt['joined_on'])?></div>

        <?php } ?>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>



    <tr>
	<th>PROFILE INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:150px; float:left;">First Name:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="first_name" name="first_name" maxlength="60" value="<?=format_str(@$empt['first_name'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['first_name'])?></div>
        <?php } ?>

        <div style="clear:both; height:10px;"></div>

        <div style="width:150px; float:left;">Middle Name:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="middle_name" name="middle_name" maxlength="20" value="<?=format_str(@$empt['middle_name'])?>" style="width:50px; border:1px solid #000261;" /></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['middle_name'])?></div>
        <?php } ?>

        <div style="clear:both; height:10px;"></div>

        <div style="width:150px; float:left;">Last Name:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="last_name" name="last_name" maxlength="60" value="<?=format_str(@$empt['last_name'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['last_name'])?></div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>

        <div style="width:150px; float:left;">Company / Org.:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="company_name" name="company_name" maxlength="100" value="<?=format_str(@$empt['company_name'])?>" style="width:250px; border:1px solid #000261;" /></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['company_name'])?></div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>

        <div style="width:150px; float:left;">Screen Name:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="screen_name" name="screen_name" maxlength="50" value="<?=format_str(@$empt['screen_name'])?>" style="width:150px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['screen_name'])?></div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>

        <div style="width:150px; float:left;">Profile Pic:</div>
        <div style="float:left;">
        <?php if($read_only<=0) { ?>
        <?php echo upload_btn('profile_pic', '', 'width:190px; border:1px solid #000261;', 'button', 'float:left;', 'margin-top:-1px;', 'width:260px !important; font-size:19px !important; cursor:pointer;', 'browse', 'image/*'); ?>
        <span class="submsg">&nbsp;&nbsp;(max size 250x250 allowed)</span>
        <div style="clear:both; height:3px;"></div>
        <?php
        }
        if(@$empt['profile_pic']!='')
        {
            list($g_width, $g_height) = @getimagesize("../user_files/prof/{$empt['user_id']}/{$empt['profile_pic']}");
            $profile_pic = "{$empt['user_id']}/".@substr_replace($empt['profile_pic'], '_th.', @strrpos($empt['profile_pic'], '.'), 1);
            ?>
            <div style="float:left;">
            <?php if($read_only<=0) { ?><a href="../user_files/prof/<?=$empt['user_id']?>/<?=$empt['profile_pic']?>" class="fbox" rel="<?php echo "{$g_width}/{$g_height}"; ?>" title="Image"><?php } ?>
            <img src="../user_files/prof/<?=$profile_pic?>" style="width:35px; height:35px;" />
            <?php if($read_only<=0) { ?></a><?php } ?>
            </div>
            <div style="clear:both; height:8px;"></div>
            <?php
        }
        ?>
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<th>ADDRESS INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:150px; float:left;">Address Line 1:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="address_ln_1" name="address_ln_1" maxlength="150" value="<?=format_str(@$empt['address_ln_1'])?>" style="width:250px; border:1px solid #000261;" /></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['address_ln_1'])?></div>
        <?php } ?>

        <div style="clear:both; height:10px;"></div>

        <div style="width:150px; float:left;">Address Line 2:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="address_ln_2" name="address_ln_2" maxlength="150" value="<?=format_str(@$empt['address_ln_2'])?>" style="width:250px; border:1px solid #000261;" /></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['address_ln_2'])?></div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>


        <div style="width:150px; float:left;">Phone:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="phone_number" name="phone_number" maxlength="20" value="<?=format_str(@$empt['phone_number'])?>" style="width:150px; border:1px solid #000261;" /></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['phone_number'])?></div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>

        <div style="width:150px; float:left;">City:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="city" name="city" maxlength="200" value="<?=format_str(@$empt['city'])?>" style="width:250px; border:1px solid #000261;" /></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['city'])?></div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>


        <div style="width:150px; float:left;">Country:</div>
        <?php if($read_only<=0) { ?><div style="float:left;">
            <?php
            $countries = @format_str(@mysql_exec("SELECT * FROM countries ORDER BY country_name"));
            ?>
            <select id="country_code" name="country_code"
            onchange="change_state(this.value);" style="width:260px; border:1px solid #000261;">
            <option value=""></option>
            <?php if(is_array($countries) && count($countries)>0)foreach($countries as $cat_v){
            echo "<option value=\"{$cat_v['country_code']}\">{$cat_v['country_name']}</option>";
            }
            ?>
            </select>
            <?php if(isset($empt['country_code'])) echo "<script>document.getElementById('country_code').value='{$empt['country_code']}';</script>"; ?>
            <?php /*<span style="color:#CC0000;">&nbsp;*</span>*/ ?>
        </div>
        <?php }else{ ?><div style="float:left;"><?php
        if(isset($empt['country_code']))
        {
            $countries = @format_str(@mysql_exec("SELECT * FROM countries WHERE country_code='{$empt['country_code']}'", 'single'));
            echo @$countries['country_name'];
        }
        ?></div>
        <?php } ?>

        <div style="clear:both; height:10px;"></div>


        <div style="width:150px; float:left;">State:</div>
        <?php if($read_only<=0) { ?><div style="float:left;">
            <?php
            $states = @format_str(@mysql_exec("SELECT * FROM states WHERE country_code='US' ORDER BY state_name"));
            ?>
            <div id="us_states_div">
                <select name="us_state" id="us_state"
                onchange="set_state(this.value);" style="width:260px; border:1px solid #000261;">
                    <option value="">Select State</option>
                    <?php if(is_array($states) && count($states)>0)foreach ($states as $state) { ?>
                    <option value='<?php echo $state['state_code']; ?>'><?php echo $state['state_name']; ?></option>
                    <?php } ?>
                </select>
                <?php if(isset($empt['state'])){echo "<script>document.getElementById('us_state').value='{$empt['state']}';</script>";} ?>
            </div>

            <div id="intr_states_div" style="display:none;">
                <input type="text" id="state" name="state" maxlength="50" value="<?=format_str(@$empt['state'])?>"
                style="width:150px; border:1px solid #000261;" />
            </div>
        </div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['state'])?></div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>


        <div style="width:150px; float:left;">Zip:</div>
        <?php if($read_only<=0) { ?><div style="float:left;"><input type="text" id="zip" name="zip" maxlength="20" value="<?=format_str(@$empt['zip'])?>" style="width:150px; border:1px solid #000261;" /></div>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['zip'])?></div>
        <?php } ?>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <?php if($u_id){ ?>
    <tr><td>&nbsp;</td></tr>

    <tr>
	<th>ACTIVITY COUNT</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:150px; float:left;">Total Voices:</div>
        <div style="float:left;"><?=(int)format_str(@$empt['voice_count'])?></div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:150px; float:left;">Total Patronage Points:</div>
        <div style="float:left;"><?=number_format((int)format_str(@$empt['total_patronage_points']), 0)?></div>

        <div style="clear:both;"></div>
    </td>
    </tr>
    <?php } ?>


    <?php if($read_only<=0) { ?>
    <tr><td>&nbsp;</td></tr>

	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;" />&nbsp;
            <input type="button" class="button" value="Cancel / Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;">
		</td>
	</tr>
    <?php } ?>

    </table>

<?php if($read_only<=0) { ?>
</form>
<?php } ?>

<?php if($u_id<=0){ ?>
<br />
<div style="float:left; font-style:italic;">Note:<br /><br />
1) New User will receive an <b>Activation Email</b> on their Email Address. Only when the Account is activated, it can be used.<br />
2) A random Password will be auto-generated and Emailed to the User upon successful Activation.</div>
<?php } ?>

<?php
include_once("includes/footer.php");
?>