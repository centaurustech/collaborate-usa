<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 2; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('med_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$med_id = (int) getgpcvar("med_id", "G");

$back_page = "site_media.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if(isset($_POST['m_cat']))
{
    $med_id = (int) getgpcvar("med_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['m_cat']], //, ['m_type']
    'lengthMax' => [['m_cat', 25], ['alt_text', 100]],
    ];

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        ###/ Image processing & savings
        include_once('../../includes/resize_images.php');
        $up_path = "../assets/images_2/media/";
        //if(!is_dir($up_path)){mkdir($up_path, 0705, true);}

        #/ Determine $m_type
        $m_type = '';
        if(is_uploaded_file(@$_FILES['m_file']['tmp_name']))
        {
            $up_type = $_FILES['m_file']['type'];
            //var_dump($up_type); die();

            if(stristr($up_type,'video')!=false){
            $m_type = 'video';
            } else if(stristr($up_type,'image')!=false){
            $m_type = 'image';
            }
        }

        $placement_tag = $sql_prt = $sql_prt_2 = $new_m_file = '';
        if($m_type=='video')
        $new_m_file = upload_vdo('m_file', $up_path, 'Media File', '', 'CUSA_ADMIN_MSG_GLOBAL');
        else if($m_type=='image')
        $new_m_file = upload_img_rs('m_file', 0, 0, $up_path, 'Media File', '', '', 'CUSA_ADMIN_MSG_GLOBAL');

        if($new_m_file!='')
        {
            $sql_prt.=" m_file='{$new_m_file}', ";
            $sql_prt_2.=" m_type='{$m_type}', ";

            $placement_tag = current(explode(".", $new_m_file));
            $sql_prt.=" placement_tag='{$placement_tag}', ";
        }
        #-


        if($med_id>0)  //Edit Mode
        {
            ###/ Updating Database
            #/ site_media
    		$sql_tb1 = "UPDATE site_media SET {$sql_prt} {$sql_prt_2}
            alt_text='{$_POST['alt_text']}', m_cat='{$_POST['m_cat']}'
            WHERE id='{$med_id}'";
    		mysql_exec($sql_tb1, 'save');
            #-

            ##/ Delete Old images
            $cur_m_file = @$_POST["cur_m_file"];
            if(($new_m_file!='') && ($new_m_file != $cur_m_file)){
            @unlink($up_path.$cur_m_file);
            }
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Media data has been successfully Updated');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&med_id={$med_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {
            ////////////////-------

            ###/ Insert into DB

            #/ site_media
            $sql_tb1 = "INSERT INTO site_media
        	(placement_tag, m_file, m_type, m_cat, alt_text, added_on)
        	VALUES('{$placement_tag}', '{$new_m_file}', '{$m_type}', '{$_POST['m_cat']}', '{$_POST['alt_text']}', now())";
            mysql_exec($sql_tb1, 'save');
            $med_id = mysql_insert_id();
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Media data has been successfully Added');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&med_id={$med_id}", true);

        }//end Add ..


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
if (($med_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT * FROM site_media WHERE id='%d'", $med_id);
	$token  = mysql_query($query, $cn1); // or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}
}

if(isset($_POST['title']))
{
    $empt = $_POST;
}
///////////////////////////////////////////////////////////////////
$load_fancy = true;

$pg_title = "Site Media";
include_once("includes/header.php");

include_once('../../includes/upload_btn.php');
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($med_id>0)? "Edit ": "Add "; ?> Record</h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<script>
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
</script>


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('m_cat_sel').value=='-')
    {
        err += 'Please select a Category!\n';
    }
    else if(document.getElementById('m_cat_sel').value=='other')
    {
        if(document.getElementById('m_cat_field').value==''){
        err += 'Please enter a Category!\n';
        } else if(document.getElementById('m_cat_field').value.search(/^[a-z0-9\-_]{1,}$/i)<0) {
        err += 'The Category can only contain AlphaNumeric values (with -dash or _underscore as separators)!\n';
        }
    }

<?php if($med_id>0){ ?>
    if(document.getElementById('m_file').value!='')
<?php } ?>
    if(!/(\.bmp|\.gif|\.jpg|\.jpeg|\.png|\.mp4)$/i.test(document.getElementById('m_file').value))
    {
        err += 'Please select a File in JPG, GIF, PNG or MP4 format!\n';
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


function toggle_cat(caller, div_id)
{
    if(caller.value=='other')
    {
        document.getElementById(div_id+'_div').style.display='';
        document.getElementById(div_id).disabled=false;
    }
    else
    {
        document.getElementById(div_id+'_div').style.display='none';
        document.getElementById(div_id).disabled=true;
    }

}//end func...

$(document).ready(function(){
    $('#m_cat_sel').change();
});
</script>
<!-- //////////////////// -->


<form action="" method="post" name="f2" id="f2" onsubmit="return check_this();" enctype="multipart/form-data">

<?php if($med_id){ ?>
<input type="hidden" name="med_id" id="med_id" value="<?php echo $med_id; ?>" />
<input type="hidden" name="cur_m_file" value="<?=(@$empt['m_file'])?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>MEDIA INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:130px; float:left;">Placement Tag:</div>
        <div style="float:left;"><input type="text" id="placement_tag" name="placement_tag" maxlength="50" value="<?=format_str(@$empt['placement_tag'])?>" style="width:250px; border:1px solid #000261;" disabled="" />
        <span class="submsg">&nbsp;&nbsp;auto generated</span>
        </div>

        <div style="clear:both; height:15px;"></div>
        <div style="width:130px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:15px;"></div>


        <div style="width:130px; float:left;">Category:</div>
        <div style="float:left;">
            <?php
            $sql_cats = "SELECT DISTINCT m_cat FROM site_media ORDER BY m_cat";
            $categories = mysql_exec($sql_cats);
            ?>
            <span class="submsg">for reference and grouping only / will not be displayed</span><br /><br />
            <div id="m_cat_select">
                <select id="m_cat_sel" name="m_cat" onchange="toggle_cat(this, 'm_cat_field');">
                <option value="-">-Please Select-</option>
                <option value="other">{New Value}</option>
                <?php if(@count($categories)>0)foreach($categories as $cat_v){
                echo "<option value=\"{$cat_v['m_cat']}\">{$cat_v['m_cat']}</option>";
                }
                ?>
                </select>
                <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
                <?php if(isset($empt['m_cat'])) echo "<script>document.getElementById('m_cat_sel').value='{$empt['m_cat']}';</script>"; ?>
            </div>

            <div id="m_cat_field_div" style="display:none;"><br /><input type="text" id="m_cat_field" name="m_cat" maxlength="25" value="" style="width:150px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
        </div>


        <div style="clear:both; height:15px;"></div>
        <div style="width:130px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:15px;"></div>


        <div style="width:130px; float:left;">ALT Text:</div>
        <div style="float:left;"><input type="text" id="alt_text" name="alt_text" maxlength="100" value="<?=format_str(@$empt['alt_text'])?>" style="width:250px; border:1px solid #000261;" />
        </div>

        <div style="clear:both; height:10px;"></div>


        <div style="width:130px; float:left;">Media Type:</div>
        <div style="float:left;">
            <select id="m_type" name="m_type" disabled="">
                <option value="-">-Please Select-</option>
                <option value="image">Image</option>
                <option value="video">Video</option>
                </select>
                <span class="submsg">&nbsp;&nbsp;auto generated</span>
                <?php if(isset($empt['m_type'])) echo "<script>document.getElementById('m_type').value='{$empt['m_type']}';</script>"; ?>
        </div>

        <div style="clear:both; height:10px;"></div>


        <div style="width:130px; float:left;">Media File:</div>
        <div style="float:left;">
        <?php echo upload_btn('m_file', '', 'width:230px; border:1px solid #000261;', 'button', 'float:left;', 'margin-top:-1px;', 'width:300px !important; font-size:19px !important; cursor:pointer;', 'browse'); ?>
        <span style="color:#CC0000;">&nbsp;&nbsp;*</span>

        <div style="clear:both; height:3px;"></div>
        <?php
        if(@$empt['m_file']!='')
        {
            if($empt['m_type']=='video')
            {
                ?>
                <script>
                $(".fbox_video").fancybox({
                    'beforeShow': function(){
                        $(window).on({
                            'resize.fancybox' : function(){
                                $.fancybox.update();
                            }
                        });
                     },
                     'afterClose': function(){
                          $(window).off('resize.fancybox');
                     },
                     //width      : '640',
                     //height     : '360',
                     fitToView  : true,
                     closeClick : false,
                     openEffect : 'none',
                     closeEffect: 'none',
                     closeBtn   : 'true',
                     scrolling  : 'no',
                });
                </script>

                <div style="float:left;"><a href="#video_plx"
                class="fbox_video" title="Video">Click here to review the current Video</a></div>

                <div style="display: none;">
                <div id="video_plx">
                    <video preload='metadata' controls width="auto" height="auto" style="width:100% !important; height:auto !important">
                    <source src="<?php echo "../assets/images_2/media/{$empt['m_file']}"; ?>" type="video/mp4">
                    Your browser does not support the video tag.
                    </video>
                </div>
                </div>
                <?php
            }
            else
            {
                list($g_width, $g_height) = @getimagesize('../assets/images_2/media/'.$empt['m_file']);
                ?>
                <div style="float:left;"><a href="../assets/images_2/media/<?php echo $empt['m_file']; ?>" class="fbox" rel="<?php echo "{$g_width}/{$g_height}"; ?>" title="Image">Click here to review the current Image</a></div>
                <?php
            }
            ?>
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
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;">&nbsp;&nbsp;
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;">
		</td>
	</tr>
    </table>
    <br />

</form>

<?php
include_once("includes/footer.php");
?>