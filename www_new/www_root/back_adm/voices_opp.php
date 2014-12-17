<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 8; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST_ori = $_POST;
$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('vc_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$vc_id = (int) getgpcvar("vc_id", "G");

$back_page = "voices.php";
$cur_page = cur_page();

if($vc_id<=0){
redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true); //prevent add
}
/////////////////////////////////////////////////////////////////

if(isset($_POST['question_text']))
{
    $vc_id = (int) getgpcvar("vc_id", "P");
    $user_id = (int) getgpcvar("user_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['question_text'], ['voice_cat_id'], ['user_id']],
    'lengthMax' => [['question_text', 170]],
    ];

    $form_v->labels(array(
    'question_text' => 'Voice Question',
    'voice_cat_id' => 'Voice Category',
    'user_id' => 'User Info',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $voice_details = rem_risky_tags($_POST_ori['voice_details']);
        $_POST['is_blocked'] = (int)@$_POST['is_blocked'];


        ##/ Set Voice Tags
        $voice_tag_ids = '';
        if(array_key_exists('voice_tags', $_POST) && is_array($_POST['voice_tags']))
        {
            $voice_tag_ids_ar = array();
            foreach($_POST['voice_tags'] as $voice_tags)
            {
                $sql_1 = "INSERT INTO voice_tags (tag, added_on) VALUES ('{$voice_tags}', NOW())
                ON DUPLICATE KEY UPDATE tag='{$voice_tags}', id=LAST_INSERT_ID(id)";
                @mysql_exec($sql_1, 'save');
                $voice_tag_ids_ar[]= (string)@mysql_insert_id();
            }
            //var_dump("<pre>", $_POST, $voice_tag_ids_ar); die();

            if(!empty($voice_tag_ids_ar)){
            $voice_tag_ids = @json_encode($voice_tag_ids_ar);}
        }
        #-


        ##/ Image processing & savings
        include_once('../../includes/resize_images.php');
        $up_path = "../user_files/prof/{$user_id}/voices/";
        if(!is_dir($up_path)){mkdir($up_path, 0705, true);}

        $sql_prt = $new_voice_pic = '';
        if(is_uploaded_file(@$_FILES['voice_pic']['tmp_name'])){
        $new_voice_pic = upload_img_rs('voice_pic', 0, 0, $up_path, 'Voice Pic', '', '', 'CUSA_ADMIN_MSG_GLOBAL');
        if($new_voice_pic!='') {
        $sql_prt.=" voice_pic='{$new_voice_pic}', ";
        }
        }
        #-

        if($vc_id>0)  //Edit Mode
        {
            #/ update
    		$sql_tb1 = "UPDATE user_voices SET
            voice_cat_id='{$_POST['voice_cat_id']}', voice_tag_ids='{$voice_tag_ids}', question_text='{$_POST['question_text']}',
            voice_details='{$voice_details}', {$sql_prt} is_blocked='{$_POST['is_blocked']}'
            WHERE id='{$vc_id}'";
    		mysql_exec($sql_tb1, 'save');


            #/ Delete Old images
            $cur_voice_pic = @$_POST["cur_voice_pic"];
            if(($new_voice_pic!='') && ($new_voice_pic != $cur_voice_pic)){
            @unlink($up_path.$cur_voice_pic);
            }

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Voice data has been successfully Updated');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&vc_id={$vc_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {

        }//end Add....

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
$user_id = 0;
$empt_tags = '';
$empt = $empt_2 = array();
if (($vc_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT vc.*, u.email_add
    FROM user_voices vc
    LEFT JOIN users u ON u.id=vc.user_id
    WHERE vc.id='%d'", $vc_id);

	$token  = mysql_query($query, $cn1);// or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}

    $user_id = (int)@$empt['user_id'];

    #/ Get Tags
    $tag_ids = @json_decode($empt['voice_tag_ids']);
    //var_dump($empt['voice_tag_ids'], $tag_ids);

    if(is_array($tag_ids) && count($tag_ids)>0)
    {
        $tag_ids_s = implode(',', $tag_ids);
        $sql_2 = "SELECT id, tag FROM voice_tags WHERE id IN ({$tag_ids_s})";
        $empt_tags = @mysql_exec($sql_2);
    }
}

if(isset($_POST['title']))
{
    $empt = $_POST;
}
///////////////////////////////////////////////////////////////////
$load_fancy = true;
$pg_title = "Member Voices";
include_once("includes/header.php");

include_once('../../includes/upload_btn.php');
include_once('includes/editor/show_editor.php');
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($vc_id>0)? "Edit ": "Add "; ?> Record</h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<!-- Tagit -->
<link rel="stylesheet" media="screen" type="text/css" href="<?=DOC_ROOT?>assets/js/jquery-ui-1.11.2.blue/jquery-ui.min.css" />
<link rel="stylesheet" href="<?=DOC_ROOT?>assets/js/jag-it/css/jquery.tagit.css" type="text/css" media="screen" />
<link rel="stylesheet" href="<?=DOC_ROOT?>assets/js/jag-it/css/tagit.ui-zendesk.css" type="text/css" media="screen" />

<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
<script src="<?=DOC_ROOT?>assets/js/jag-it/js/tag-it.min.js" type="text/javascript"></script>

<script>var tags_url = '<?=DOC_ROOT_ADMIN?>voice_tags_ajax.php?search_it=1&ro=1';</script>
<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/voice_tags.js"></script>

<script>
$(document).ready(function() {
	$(".fbox").fancybox({
	    minWidth    : 550,
	    minHeight   : 5,
		maxWidth	: 950,
		maxHeight	: 600,
		autoSize	: true,
        fitToView	: false,
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


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('question_text').value=='')
    {
        err += 'Please enter the Voice Question!\n';
    }

    document.getElementById('voice_details').value=oEdit1_voice_details.getHTMLBody();
    if((document.getElementById('voice_details').value=='') || (document.getElementById('voice_details').value=='<br>'))
    {
        //err += 'Please enter some Voice Details!\n';
    }

    if(document.getElementById('voice_pic').value!='')
    if(!/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i.test(document.getElementById('voice_pic').value))
    {
        err += 'Please select the Image in JPG, GIF or PNG format!\n';
    }

    if(document.getElementById('voice_cat_id').value=='')
    {
        err += 'Please select a Voice Category!\n';
    }


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
<!-- //////////////////// -->


<form action="" method="post" name="f2" id="f2" onsubmit="return check_this();" enctype="multipart/form-data">

<?php if($vc_id){ ?>
<input type="hidden" name="vc_id" id="vc_id" value="<?php echo $vc_id; ?>" />
<input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
<input type="hidden" name="cur_voice_pic" value="<?=(@$empt['voice_pic'])?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>VOICE INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:140px; float:left;">Posted By (Member):</div>
        <div style="float:left;"><a <?php if($user_id>0){?> class="fbox fancybox.ajax"
        href="<?php echo "{$consts['DOC_ROOT_ADMIN']}users_opp.php?u_id={$user_id}&ro=1"; ?>" <?php } ?> title="<?=format_str(@$empt['email_add'])?>"><?=format_str(@$empt['email_add'])?></a>
        </div>

        <div style="clear:both; height:10px;"></div>
        <div style="width:140px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:10px;"></div>

        <div style="width:140px; float:left;">Voice Question:</div>
        <div style="float:left;"><input type="text" id="question_text" name="question_text" maxlength="170" value="<?=format_str(@$empt['question_text'])?>" style="width:350px; border:1px solid #000261;" />
        <span style="color:#CC0000;">&nbsp;*</span>
        </div>

        <div style="clear:both; height:20px;"></div>

        <div style="width:140px; float:left;">Picture:</div>
        <div style="float:left;">
        <?php echo upload_btn('voice_pic', '', 'width:230px; border:1px solid #000261;', 'button', 'float:left;', 'margin-top:-1px;', 'width:300px !important; font-size:19px !important; cursor:pointer;', 'browse', 'image/*'); ?>
        <span class="submsg">&nbsp;&nbsp;(recommended minimum width = 820px)</span>

        <div style="clear:both; height:3px;"></div>
        <?php
        if(@$empt['voice_pic']!='')
        {
            list($g_width, $g_height) = @getimagesize('../user_files/prof/'.$empt['user_id'].'/voices/'.$empt['voice_pic']);
            ?>
            <div style="float:left;"><a href="../user_files/prof/<?php echo $empt['user_id'].'/voices/'.$empt['voice_pic']; ?>" class="fbox" rel="<?php echo "{$g_width}/{$g_height}"; ?>" title="Voice Picture">Click here to review the current Image</a></div>
            <div style="clear:both; height:8px;"></div>
            <?php
        }
        ?>
        </div>


        <div style="clear:both; height:10px;"></div>
        <div style="width:140px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:15px;"></div>


        <div style="width:140px; float:left;">Voice Details:</div>
        <div style="float:left;">
        <?php
        $edit_obj = new make_editor($browser);
        echo $edit_obj->show_editor('voice_details', '300', '500', format_str(@$empt['voice_details']));
        ?>
        <?php /*<span style="color:#CC0000;">*</span>*/ ?>
        </div>



    <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<th>VOICE SETTING</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:140px; float:left;">Voice Category:</div>
        <div style="float:left;">
            <?php
            $sql_cats = "SELECT DISTINCT category, id FROM voice_categories GROUP BY category ORDER BY category";
            $m_cats = mysql_exec($sql_cats);
            ?>
            <select id="voice_cat_id" name="voice_cat_id" style="width:260px; border:1px solid #000261;">
            <option value="">-Please Select-</option>
            <?php if(@count($m_cats)>0)foreach($m_cats as $m_catv){
            echo "<option value=\"{$m_catv['id']}\">{$m_catv['category']}</option>";
            }
            ?>
            </select>
            <span style="color:#CC0000;">&nbsp;*</span>
            <?php if(isset($empt['voice_cat_id'])) echo "<script>document.getElementById('voice_cat_id').value='{$empt['voice_cat_id']}';</script>"; ?>
        </div>

        <div style="clear:both; height:15px;"></div>
        <div style="width:140px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:10px;"></div>


        <div style="width:140px; float:left;">Tags:</div>

        <div style="float:left; width:495px;">
            <style>
            #myTags{
                border-radius:0 !important;
                border:1px solid #000261;
                margin: 0 !important;
            }
            </style>

            <ul id="myTags">
                <?php
                if(!empty($empt_tags))
                foreach ($empt_tags as $etv){
                    echo "<li>{$etv['tag']}</li>";
                } ?>
            </ul>

            <div style="clear:both; height:5px;"></div>
            <span class="submsg">&nbsp;(max 5 allowed)</span>
        </div>
        <div id="load_x" style="display:none; margin-left:10px !important;"><img src="<?=DOC_ROOT?>assets/images/load_blue.gif" /></div>

        <div style="clear:both; height:10px;"></div>
        <div style="width:140px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:10px;"></div>

        <div style="width:140px; float:left;">Is Blocked?</div>
        <div style="float:left;"><input type="checkbox" name="is_blocked" id="is_blocked" value="1" <?php if(@$empt['is_blocked']=='1') echo "checked='checked'"; ?> />
        <span class="submsg">&nbsp;(Admin based blocking)</span>
        </div>

    <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;" />&nbsp;
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;">
		</td>
	</tr>
    </table>

</form>

<br /><br />
<div style="float:left; font-style:italic;">
Click on the <b>Posted By (Email Address)</b> to review the User Details.
</div>

<?php
include_once("includes/footer.php");
?>