<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 2; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST_ori = $_POST;
$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('misc_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$misc_id = (int) getgpcvar("misc_id", "G");

$back_page = "site_misc_data.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if(isset($_POST['title']))
{
    $misc_id = (int) getgpcvar("misc_id", "P");
    //die('x');

    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['title'], ['m_value'], ['m_cat']],
    'lengthMax' => [['title', 250], ['m_cat', 70]],
    ];

    $form_v->labels(array(
    'title' => 'Name / Title',
    'm_value' => 'Value',
    'm_cat' => 'Category',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $content_type = @$_POST['content_type'];

        if($content_type=='html') {
        $m_value = rem_risky_tags($_POST_ori['m_value']);
        } else if($content_type=='plain') {
        $m_value = $_POST['m_value'];
        }
        //var_dump($content_type, $m_value); die();


        ##/ Image processing & savings
        include_once('../../includes/resize_images.php');
        $up_path = "../assets/images_2/misc/";
        //if(!is_dir($up_path)){mkdir($up_path, 0705, true);}

        $sql_prt = $new_m_image = '';
        if(is_uploaded_file(@$_FILES['m_image']['tmp_name'])){
        $new_m_image = upload_img_rs('m_image', 0, 0, $up_path, 'Image', '', '', 'CUSA_ADMIN_MSG_GLOBAL');
        if($new_m_image!='') {
        $sql_prt.=" m_image='{$new_m_image}', ";
        }
        }
        #-


        if($misc_id>0)  //Edit Mode
        {
            ###/ Updating Database
            #/ site_misc_data
    		$sql_tb1 = "UPDATE site_misc_data SET title='{$_POST['title']}', m_value='{$m_value}',
            content_type='{$content_type}', content_settings='{$_POST['content_settings']}',
            {$sql_prt} m_cat='{$_POST['m_cat']}'
            WHERE id='{$misc_id}'";
            //die($sql_tb1);
    		mysql_exec($sql_tb1, 'save');
            #-

            ##/ Delete Old images
            $cur_m_image = @$_POST["cur_m_image"];
            if(($new_m_image!='') && ($new_m_image != $cur_m_image)){
            @unlink($up_path.$cur_m_image);
            }
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Site data has been successfully Updated');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&misc_id={$misc_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {
            ////////////////-------

            ###/ Updating Database

            #/ site_misc_data
            $sql_tb1 = "insert into site_misc_data
        	(title, m_value, m_image, m_cat, content_type, content_settings)
        	values('{$_POST['title']}', '{$m_value}', '{$new_m_image}', '{$_POST['m_cat']}', '{$content_type}', '{$_POST['content_settings']}')";
            mysql_exec($sql_tb1, 'save');
            $misc_id = mysql_insert_id();
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Site data has been successfully Added');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&misc_id={$misc_id}", true);

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
if (($misc_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT * FROM site_misc_data WHERE id='%d'", $misc_id);
	$token  = mysql_query($query, $cn1); // or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}

    if($empt['content_type']=='html'){
    $empt['m_value_html'] = $empt['m_value'];
    }else if($empt['content_type']=='plain'){
    $empt['m_value_plain'] = $empt['m_value'];
    }
}

if(isset($_POST['title']))
{
    $empt = $_POST;
}
///////////////////////////////////////////////////////////////////
$load_fancy = true;

$pg_title = "Misc. Site Data";
include_once("includes/header.php");

include_once('../../includes/upload_btn.php');
include_once('includes/editor/show_editor.php');
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($misc_id>0)? "Edit ": "Add "; ?> Record</h1></div>
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

    if(document.getElementById('title').value=='')
    {
        err += 'Please enter the Name / Title!\n';
    }

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

    if(document.getElementById('m_image').value!='')
    if(!/(\.bmp|\.gif|\.jpg|\.jpeg|\.png)$/i.test(document.getElementById('m_image').value))
    {
        err += 'Please select the Image in JPG, GIF or PNG format\n';
    }


    //check which type of content is needed
    $(document).ready(function(){
    var content_typ = $('input[name=content_type]:checked', '#f2').val();

    document.getElementById('m_value').value = '';
    if(content_typ=='plain')
    {
        document.getElementById('m_value').value=document.getElementById('m_value_plain').value;
    }
    else if(content_typ=='html')
    {
        document.getElementById('m_value').value=oEdit1_m_value_html.getHTMLBody();
    }
    //alert(content_typ);
    //alert(document.getElementById('m_value').value);

    if((document.getElementById('m_value').value=='') || (document.getElementById('m_value').value=='<br>'))
    {
        err += 'Please enter the Value!\n';
    }
    });


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

<?php if($misc_id){ ?>
<input type="hidden" name="misc_id" id="misc_id" value="<?php echo $misc_id; ?>" />
<input type="hidden" name="cur_m_image" value="<?=(@$empt['m_image'])?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>MISC. INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:130px; float:left;">Name / Title:</div>
        <div style="float:left;"><input type="text" id="title" name="title" maxlength="250" value="<?=format_str(@$empt['title'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both; height:20px;"></div>

        <div style="width:130px; float:left;">Value:</div>
        <div style="float:left;">
            <input type="radio" name="content_type" class="content_type" value="plain" onclick="toggle_div(this, 'plain_cont_div', 'html_cont_div');" />Plain Text&nbsp;&nbsp;&nbsp;
            <input type="radio" name="content_type" class="content_type" value="html" onclick="toggle_div(this, 'html_cont_div', 'plain_cont_div');" />HTML
            <?php $p_ctv=0; $p_ctv2='plain'; if(isset($empt['content_type'])){if($empt['content_type']=='plain'){$p_ctv=0; $p_ctv2='plain';}else if($empt['content_type']=='html'){$p_ctv=1; $p_ctv2='html';}}
            echo "<script>$(document).ready(function(){document.getElementById('f2').elements['content_type'][{$p_ctv}].checked='checked'; $('.content_type')[{$p_ctv}].click();});</script>"; ?>

            <div style="clear:both; height:10px;"></div>

            <div id="plain_cont_div" style="display: none;">
                <textarea id="m_value_plain" name="m_value_plain" rows="4" style="width:400px; float:left; border:1px solid #000261;"><?=format_str(@$empt['m_value_plain'])?></textarea>
                <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
            </div>

            <div id="html_cont_div" style="display: none;">
                <?php
                $edit_obj = new make_editor($browser);
                echo $edit_obj->show_editor('m_value_html', '300', '500', format_str(@$empt['m_value_html']));
                ?>
                <span style="color:#CC0000;">&nbsp;&nbsp;*</span><br />
                <span class="submsg">&nbsp;&nbsp;Firefox: first do Italic, then Bold. Chrome: Bold, then Italic</span><br /><br />
            </div>

            <!--<input type="hidden" name="m_value" id="m_value" value="" />-->
            <input type="text" name="m_value" id="m_value" style="font-size:1px; border:none; background:none;" />
        </div>


        <div style="clear:both; height:15px;"></div>
        <div style="width:130px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:15px;"></div>


        <div style="width:130px; float:left;">Setting / Value2:</div>
        <div style="float:left;"><input type="text" id="content_settings" name="content_settings" maxlength="150" value="<?=format_str(@$empt['content_settings'])?>" style="width:250px; border:1px solid #000261;" />
        <span class="submsg">&nbsp;&nbsp;for technical values only (will not be displayed) / please dont alter this</span><br /><br />
        </div>

        <div style="clear:both; height:15px;"></div>

        <div style="width:130px; float:left;">Category:</div>
        <div style="float:left;">
            <?php
            $sql_cats = "SELECT DISTINCT m_cat FROM site_misc_data ORDER BY m_cat";
            $m_cats = mysql_exec($sql_cats);
            ?>
            <span class="submsg">for reference and grouping only / will not be displayed</span><br /><br />
            <div id="m_cat_select">
                <select id="m_cat_sel" name="m_cat" onchange="toggle_cat(this, 'm_cat_field');">
                <option value="-">-Please Select-</option>
                <option value="other">{New Value}</option>
                <?php if(@count($m_cats)>0)foreach($m_cats as $m_catv){
                echo "<option value=\"{$m_catv['m_cat']}\">{$m_catv['m_cat']}</option>";
                }
                ?>
                </select>
                <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
                <?php if(isset($empt['m_cat'])) echo "<script>document.getElementById('m_cat_sel').value='{$empt['m_cat']}';</script>"; ?>
            </div>

            <div id="m_cat_field_div" style="display:none;"><br /><input type="text" id="m_cat_field" name="m_cat" maxlength="70" value="" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
        </div>


        <div style="clear:both; height:15px;"></div>
        <div style="width:130px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:15px;"></div>


        <div style="width:130px; float:left;">Image:</div>
        <div style="float:left;">
        <?php echo upload_btn('m_image', '', 'width:230px; border:1px solid #000261;', 'button', 'float:left;', 'margin-top:-1px;', 'width:300px !important; font-size:19px !important; cursor:pointer;', 'browse', 'image/*'); ?>

        <div style="clear:both; height:3px;"></div>
        <?php
        if(@$empt['m_image']!='')
        {
            list($g_width, $g_height) = @getimagesize('../assets/images_2/misc/'.$empt['m_image']);
            ?>
            <div style="float:left;"><a href="../assets/images_2/misc/<?php echo $empt['m_image']; ?>" class="fbox" rel="<?php echo "{$g_width}/{$g_height}"; ?>" title="Image">Click here to review the current Image</a></div>
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

</form>

<?php
include_once("includes/footer.php");
?>