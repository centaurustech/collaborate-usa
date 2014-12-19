<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}

include_once('includes/session.php');

$user_id = (int)@$_SESSION["CUSA_Main_usr_id"];
if($user_id<=0){exit;}
$user_info = @$_SESSION["CUSA_Main_usr_info"];

/////////////////////////////////////////////////////////////////////

#/ Identify Profile-user to fetch
$member_id = 0;
if(isset($url_comp[1]))
{
    $member_id = (int)@$url_comp[1];
    if($member_id<=0) redirect_me('404');
}
if($member_id<=0) $member_id = $user_id;
//var_dump("<pre>", $url_comp, $member_id); die();


#/ get Members Profile Info
include_once('../includes/profile_func.php');
$member_info_ar = get_member_info($member_id, $user_id);
$member_info = @$member_info_ar[0];
//var_dump("<pre>", $member_id, $member_info, mysql_error()); die();
if(!is_array($member_info) || !array_key_exists('user_ident', $member_info)){redirect_me('404');}


#/ Misc..
$user_permissions = @$member_info_ar[1];
//var_dump("<pre>", $user_permissions); die();

$public = $private = '';
if($member_id == $user_id){
$public = "<img class=\"dimg\" src=\"{$consts['DOC_ROOT']}assets/images/secure_public.png\" title=\"Visiblity is Public\" />";
$private = "<img class=\"dimg\" src=\"{$consts['DOC_ROOT']}assets/images/secure_private.png\" title=\"Visiblity is Private & Connections only\" />";
}

$prof_pic = DOC_ROOT."assets/images/ep.png";
if(array_key_exists('profile_pic', $member_info))
{
    if(!@empty($member_info['profile_pic'])){
    $prof_pic = DOC_ROOT."user_files/prof/{$member_id}/{$member_info['profile_pic']}";
    }
}
//$prof_pic_th = @substr_replace($prof_pic, '_th.', @strrpos($prof_pic, '.'), 1);
/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
if($member_id == $user_id){
$pg_meta = array(
    'page_title'=> "My Profile",
);
}
else {
$pg_meta = array(
    'page_title'=> "{$member_info['user_ident']} :: Profile",
);
}
$page_heading = $pg_meta['page_title'];

include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>

<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/form.css" />
<style>
.profile_pic {
    display:inline-block;
    vertical-align:top;
    max-width:250px;
    padding:5px 20px 30px 0;
}

.profile_pic img {
    border: solid 1px #ddd;
    max-width:100%;
}

.profile_name {
    display:inline-block;
    vertical-align:top;
}

.profile_name h1{
    padding-top:0;
}

.profile_name label {
    color:#555;
}

.profile_name .right_fl {
}

@media (max-width: 650px) {
.profile_pic{
    padding-right:0 !important;
    max-width:100%;
    width:100%;
}

.profile_pic img{
    text-align:center;
    width:250px;
    max-width:90%;
}
}
</style>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">

    <div class="profile_pic">
        <img src="<?=$prof_pic?>" class="round_borders" />
    </div>

    <div class="profile_name">
        <h1><strong><?=$member_info['user_ident']?></strong></h1><br class="dsk_br" />

        <?php if(array_key_exists('joined_on', $member_info)) {
        include_once('../includes/func_time.php');
        ?>
        <label>Member Since:</label>
        <div class="right_fl"><?=@time_elapsed_string(@strtotime($member_info['joined_on']))?></div>
        <div style="clear: both;"></div>
        <br />
        <br class="mbl_br" />
        <?php } ?>
    </div>

    <div class="" style="margin-left:0;">
    <div class="contact-main fields-big display-big">

        <?php if(array_key_exists('email_add', $member_info)) { ?>
        <label>Email Address: <?php if(!in_array('email_add', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['email_add'])? '-':$member_info['email_add'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>


        <?php if(array_key_exists('screen_name', $member_info)) { ?>
        <label>Screen: <?php if(!in_array('screen_name', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl flbld"><?=empty($member_info['screen_name'])? '-':$member_info['screen_name'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>


        <?php if(array_key_exists('first_name', $member_info)) { ?>
        <label>First Name: <?php if(!in_array('first_name', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl flbld"><?=empty($member_info['first_name'])? '-':$member_info['first_name'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>

        <?php if(array_key_exists('middle_name', $member_info)) { ?>
        <label>Middle Name: <?php if(!in_array('middle_name', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['middle_name'])? '-':$member_info['middle_name'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>

        <?php if(array_key_exists('last_name', $member_info)) { ?>
        <label>Last Name: <?php if(!in_array('last_name', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['last_name'])? '-':$member_info['last_name'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>


        <?php if(array_key_exists('company_name', $member_info)) { ?>
        <label>Company / Organization: <?php if(!in_array('company_name', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['company_name'])? '-':$member_info['company_name'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>


        <hr style="width:65%; display:inline-block;" />
        <div style="clear: both;"></div>
        <br />


        <h4 class="heading" style="text-decoration: underline;">Location</h4><br />


        <?php if(array_key_exists('country_name', $member_info)) { ?>
        <label>Country:</label>
        <div class="right_fl flbld"><?=empty($member_info['country_name'])? '-':$member_info['country_name'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>

        <?php if(array_key_exists('state_name', $member_info) && !empty($member_info['state_name'])) { ?>
        <label>State: <?php if(!in_array('state', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=$member_info['state_name']?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } else if(array_key_exists('state', $member_info)) { ?>
        <label>State: <?php if(!in_array('state', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['state'])? '-':$member_info['state'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>


        <?php if(array_key_exists('zip', $member_info)) { ?>
        <label>Zip: <?php if(!in_array('zip', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['zip'])? '-':$member_info['zip'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>

        <?php if(array_key_exists('city', $member_info)) { ?>
        <label>City: <?php if(!in_array('city', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['city'])? '-':$member_info['city'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>


        <?php if(array_key_exists('address_ln_1', $member_info)) { ?>
        <label>Address Line 1: <?php if(!in_array('address_ln_1', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['address_ln_1'])? '-':$member_info['address_ln_1'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>

        <?php if(array_key_exists('address_ln_2', $member_info)) { ?>
        <label>Address Line 2: <?php if(!in_array('address_ln_2', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['address_ln_2'])? '-':$member_info['address_ln_2'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>

        <?php if(array_key_exists('phone_number', $member_info)) { ?>
        <label>Phone Number: <?php if(!in_array('phone_number', $user_permissions)){echo $public;} else {echo $private;} ?></label>
        <div class="right_fl"><?=empty($member_info['phone_number'])? '-':$member_info['phone_number'];?></div>
        <div style="clear: both;"></div>
        <br />
        <?php } ?>


        <?php if($member_id == $user_id) { ?>
        <br />
        <label></label>
        <div class="right_fl" style="">
            <?php /* <input type="button" class="blue_btn" value="UPDATE" style="width: 120px;" onclick="" />*/ ?>
            <a href="<?=DOC_ROOT."update-profile"?>" class="blue_btn" style="padding-top:4px;">UPDATE</a>
        </div>
        <div style="clear: both;"></div>
        <?php } ?>

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