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

$ignore = array_flip(array('sp_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$sp_id = (int) getgpcvar("sp_id", "G");

$back_page = "site_pages.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if(isset($_POST['seo_tag']))
{
    $sp_id = (int) getgpcvar("sp_id", "P");
    $seo_tag_id = (int)@$_POST['seo_tag_id'];
    $content_type = @$_POST['content_type'];
    $self_managed = (int)@$_POST['self_managed'];


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    if($content_type=='pdf')
    {
        $rules = [
        'required' => [['title'], ['seo_tag']],
        'lengthMax' => [['title', 60], ['seo_tag', 30]],
        ];
    }
    else if($content_type=='html')
    {
        if($self_managed=='1')
        {
            $rules = [
            'required' => [['title'], ['seo_tag'], ['page_heading']],
            'lengthMax' => [['title', 60], ['seo_tag', 30], ['page_heading', 150], ['head_msg', 200]],
            ];
        }
        else
        {
            $rules = [
            'required' => [['title'], ['seo_tag'], ['page_heading'], ['pg_content']],
            'lengthMax' => [['title', 60], ['seo_tag', 30], ['page_heading', 150], ['head_msg', 200]],
            ];
        }
    }

    $form_v->labels(array(
    'page_heading' => 'Top Heading',
    'head_msg' => 'Sub Heading',
    'pg_content' => 'HTML Content',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $_FILES, $fv_errors); die();
    #-


    ##/ Check if seo_tag is unique
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        //$chk_user = mysql_exec("SELECT seo_tag FROM site_pages WHERE seo_tag='{$_POST['seo_tag']}' and id!='{$sp_id}'", 'single');
        $chk_user = mysql_exec("SELECT seo_tag FROM seo_tags WHERE seo_tag='{$_POST['seo_tag']}' and id!='{$seo_tag_id}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('The SEO TAG you entered already exists! Please try a different one.');
        }
    }
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        #/ Setup variables to save
        $_POST['cat_id'] = (int)@$_POST['cat_id'];
        $_POST['popup_only'] = (int)@$_POST['popup_only'];
        $_POST['is_active'] = (int)@$_POST['is_active'];

        $m_type = $pg_content = '';
        $sql_prt = $new_pdf_content = '';
        $up_path = "../assets/media/docs/";

        if($content_type=='html')
        {
            if($self_managed=='0')
            {
                $pg_content = rem_risky_tags($_POST_ori['pg_content']);

                $sql_prt.=" pdf_content='', ";
                if($sp_id>0) //Delete Old files
                {
                    $cur_pdf_content = @$_POST["cur_pdf_content"];
                    if(!empty($cur_pdf_content)){
                    @unlink($up_path.$cur_pdf_content);
                    }
                }
            }
        }
        else if($content_type=='pdf')
        {
            $pg_content = $_POST['page_heading'] = $_POST['head_msg'] = $_POST['pg_content'] = '';
            $_POST['meta_keywords'] = $_POST['meta_descr'] = '';

            ##/ Setup PDF file
            if(is_uploaded_file(@$_FILES['pdf_content']['tmp_name']))
            {
                $up_type = $_FILES['pdf_content']['type'];
                if($up_type=='application/pdf'){
                $m_type = 'pdf';
                }
                //var_dump($up_type); die();

                #/ Upload file
                if($m_type == 'pdf')
                {
                    $new_pdf_content = format_filename(@$_FILES['pdf_content']['name']);

                    if($sp_id>0) //Delete Old files
                    {
                        $cur_pdf_content = @$_POST["cur_pdf_content"];
                        if(($new_pdf_content!='') && ($new_pdf_content != $cur_pdf_content)){
                        @unlink($up_path.$cur_pdf_content);
                        }
                    }

                    $ret = move_uploaded_file($_FILES['pdf_content']['tmp_name'], $up_path.$new_pdf_content);
                    if($ret===false)
                    {
                        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, 'Unable to upload the PDF file! Please try again...');
                        if($sp_id>0)
                        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&sp_id={$sp_id}", true);
                        else
                        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}", true);
                    }
                    else
                    {
                        $sql_prt.=" pdf_content='{$new_pdf_content}', ";
                    }
                }
            }
            #-
        }//end if pdf...


        if($sp_id>0)  //Edit Mode
        {
            #/ seo_tags
        	$sql_seo_tags = "UPDATE seo_tags SET seo_tag='{$_POST['seo_tag']}'
            WHERE id='{$seo_tag_id}'";
        	mysql_exec($sql_seo_tags, 'save');

            #/ update site_pages
    		$sql_tb1 = "UPDATE site_pages SET
            cat_id='{$_POST['cat_id']}', title='{$_POST['title']}',
            page_heading='{$_POST['page_heading']}', head_msg='{$_POST['head_msg']}', pg_content='{$pg_content}',
            {$sql_prt}
            seo_tag_id='{$seo_tag_id}',
            meta_keywords='{$_POST['meta_keywords']}', meta_descr='{$_POST['meta_descr']}',
            popup_only='{$_POST['popup_only']}', is_active='{$_POST['is_active']}'
            WHERE id='{$sp_id}'";
    		mysql_exec($sql_tb1, 'save');


            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Site Page data has been successfully Updated');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&sp_id={$sp_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {
            ////////////////-------

            #/ insert seo_tags
            $sql_tb2 = "INSERT INTO seo_tags (seo_tag)
        	VALUES('{$_POST['seo_tag']}')";
            mysql_exec($sql_tb2, 'save');
            $seo_tag_id = (int)@mysql_insert_id();

            #/ insert site_pages
            $sql_tb1 = "INSERT INTO site_pages
            (cat_id, title, page_heading, head_msg, pg_content, pdf_content, seo_tag_id, meta_keywords, meta_descr, popup_only, is_active)
        	VALUES('{$_POST['cat_id']}', '{$_POST['title']}', '{$_POST['page_heading']}', '{$_POST['head_msg']}', '{$pg_content}', '{$new_pdf_content}',
            '{$seo_tag_id}', '{$_POST['meta_keywords']}', '{$_POST['meta_descr']}', '{$_POST['popup_only']}', '{$_POST['is_active']}')";
            mysql_exec($sql_tb1, 'save');
            $sp_id = (int)@mysql_insert_id();
            //var_dump($sp_id); echo mysql_error(); die();


            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Site Page data has been successfully Added');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&sp_id={$sp_id}", true);

        }//end Add....
        #-


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
$empt = $empt_2 = array();
if (($sp_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT sp.*, sp.id as sp_id, st.seo_tag

    FROM site_pages sp
    LEFT JOIN seo_tags st ON st.id = sp.seo_tag_id

    WHERE sp.id='%d'", $sp_id);
    //die($query);

	$token  = mysql_query($query, $cn1); //or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}

    $empt['content_type'] = 'html';
    if(isset($empt['pdf_content']) && !empty($empt['pdf_content']))
    $empt['content_type'] = 'pdf';
}

if(isset($_POST['seo_tag']))
{
    $empt = $_POST;
    $empt['is_active'] = (int)@$empt['is_active'];
}
///////////////////////////////////////////////////////////////////

$pg_title = "Site Pages";
include_once("includes/header.php");

include_once('includes/editor/show_editor.php');
include_once('../../includes/upload_btn.php');
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($sp_id>0)? "Edit ": "Add "; ?> Record</h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>

<style>
ul{margin:0; padding:0; list-style-type: none;}
.pfs {clear:both; margin-bottom:6px;}
</style>

<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('title').value=='')
    {
        err += 'Please enter the Page Title!\n';
    }

    if(document.getElementById('seo_tag').value=='')
    {
        err += 'Please enter the SEO Tag!\n';
    }
    else if(document.getElementById('seo_tag').value.search(/^[a-z0-9\-_]{1,}$/i)<0)
    {
        err += 'The Seo Tag can only contain Alphanumeric values (with Dash or Underscore as separators)!\n';
    }


    //check which type of content is needed
    $(document).ready(function(){
    var content_typ = $('input[name=content_type]:checked', '#f2').val();

    if(content_typ=='pdf')
    {
        if(document.getElementById('pdf_content').value=='')
        {
            <?php if($sp_id<=0){ ?>err += 'Please select a PDF file!\n';<?php } ?>
        }
        else if(!/(\.pdf)$/i.test(document.getElementById('pdf_content').value))
        {
            err += 'Please select a file in PDF format only!\n';
        }
    }
    else if(content_typ=='html')
    {
        if(document.getElementById('page_heading').value=='')
        {
            err += 'Please enter the Top Heading!\n';
        }

        document.getElementById('pg_content').value=oEdit1_pg_content.getHTMLBody();
        if((document.getElementById('pg_content').value=='') || (document.getElementById('pg_content').value=='<br>'))
        {
            <?php if(@$empt['self_managed']=='0') { ?>
            err += 'Please enter the Page Content!\n';
            <?php } ?>
        }
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

</script>
<!-- //////////////////// -->


<form action="" method="post" name="f2" id="f2" onsubmit="return check_this();" enctype="multipart/form-data">

<?php if($sp_id){ ?>
<input type="hidden" name="sp_id" id="sp_id" value="<?php echo $sp_id; ?>" />
<input type="hidden" name="seo_tag_id" id="seo_tag_id" value="<?php echo (int)@$empt['seo_tag_id']; ?>" />
<input type="hidden" name="cur_pdf_content" value="<?=(@$empt['pdf_content'])?>" />
<input type="hidden" name="self_managed" value="<?php echo (int)@$empt['self_managed']; ?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">

    <tr>
	<th>BASIC INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:130px; float:left;">Page Title:</div>
        <div style="float:left;"><input type="text" id="title" name="title" maxlength="60" value="<?=format_str(@$empt['title'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">SEO Tag / URL Part:</div>
        <div style="float:left;"><input type="text" id="seo_tag" name="seo_tag" maxlength="30" value="<?=format_str(@$empt['seo_tag'])?>" style="width:150px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        <span class="submsg">must be unique and in alpha_numberic only</span>
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<th>SETTINGS</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:130px; float:left;">Page Category:</div>
        <div style="float:left;">
            <?php
            $cats = @format_str(@mysql_exec("SELECT * FROM page_categories ORDER BY title"));
            ?>
            <select id="cat_id" name="cat_id" style="width:250px; border:1px solid #000261;">
            <option value=""></option>
            <?php
            if(is_array($cats) && count($cats)>0){
            foreach($cats as $cat_ck=>$cat_cv){
            echo "<option value=\"{$cat_cv['id']}\">{$cat_cv['title']}</option>";
            }
            }
            ?>
            </select>
            <?php if(isset($empt['cat_id'])) echo "<script>document.getElementById('cat_id').value='{$empt['cat_id']}';</script>"; ?>
            <span class="submsg">&nbsp;&nbsp;i.e. Category MENU within which this Page falls (mainly for display)</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Is Active?</div>
        <div style="float:left;"><input type="checkbox" name="is_active" id="is_active" value="1" <?php if (@$empt['is_active']!='0') echo "checked='checked'"; ?> />
        </div>


        <div style="<?php if(@$empt['self_managed']=='1') { ?>display:none;<?php } ?>">
        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Content Type</div>
        <div style="float:left;"><input type="radio" name="content_type" class="content_type" value="html" onclick="toggle_div(this, 'html_cont_div', 'pdf_cont_div');" />HTML&nbsp;&nbsp;
            <input type="radio" name="content_type" class="content_type" value="pdf" onclick="toggle_div(this, 'pdf_cont_div', 'html_cont_div');" />PDF
            <?php $p_ctv=0; $p_ctv2='html'; if(isset($empt['content_type'])){if($empt['content_type']=='pdf'){$p_ctv=1; $p_ctv2='pdf';}else if($empt['content_type']=='html'){$p_ctv=0;}}
            echo "<script>$(document).ready(function(){document.getElementById('f2').elements['content_type'][{$p_ctv}].checked='checked'; $('.content_type')[{$p_ctv}].click();});</script>"; ?>
        </div>
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>
</table>


<div id="pdf_cont_div" style="display: none;">
<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
    <tr>
	<th>PAGE CONTENT</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:130px; float:left;">PDF File:</div>
        <div style="float:left;">
            <?php echo upload_btn('pdf_content', '', 'width:230px; border:1px solid #000261;', 'button', 'float:left;', 'margin-top:-1px;', 'width:300px !important; font-size:19px !important; cursor:pointer;', 'browse'); ?>
            <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
            <span class="submsg">&nbsp;&nbsp;will OVERRIDE previously uploaded same-name file</span>

            <?php if(@$empt['pdf_content']!='') { ?>
            <div style="clear:both; height:3px;"></div>
            <div style="float:left;"><a href="../assets/media/docs/<?php echo $empt['pdf_content']; ?>" title="PDF Content" target="_blank">Click here to review the current file</a></div>
            <?php } ?>
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

</table>
</div>


<div id="html_cont_div" style="display: none;">
<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">

    <tr>
	<th>HEADING SECTION</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:130px; float:left;">Top Heading:</div>
        <div style="float:left;"><input type="text" id="page_heading" name="page_heading" maxlength="150" value="<?=format_str(@$empt['page_heading'])?>" style="width:350px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Sub Heading:</div>
        <div style="float:left;"><textarea id="head_msg" name="head_msg" rows="2" style="width:355px; border:1px solid #000261; float:left;"><?=format_str(@$empt['head_msg'])?></textarea>
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<th>PAGE INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:130px; float:left;">Meta Keywords:</div>
        <div style="float:left;"><input type="text" id="meta_keywords" name="meta_keywords" maxlength="250" value="<?=format_str(@$empt['meta_keywords'])?>" style="width:350px; border:1px solid #000261;" />
        <span class="submsg">&nbsp;&nbsp;Ignore for popup contents</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Meta Description:</div>
        <div style="float:left;"><input type="text" id="meta_descr" name="meta_descr" maxlength="250" value="<?=format_str(@$empt['meta_descr'])?>" style="width:350px; border:1px solid #000261;" />
        <span class="submsg">&nbsp;&nbsp;Ignore for popup contents</span>
        </div>

        <div style="<?php if(@$empt['self_managed']=='1') { ?>display:none;<?php } ?>">
        <div style="clear:both; height:20px;"></div>

        <div style="width:130px; float:left;">Show in Popup only?</div>
        <div style="float:left;"><input type="checkbox" name="popup_only" id="popup_only" value="1" <?php if(@$empt['popup_only']=='1') echo "checked='checked'"; ?> />
        <span class="submsg">Content will be shown only in a PopUp, instead of a separate Independent Page</span>
        </div>

        <div style="clear:both; height:20px;"></div>


        <div style="width:130px; float:left;">HTML Content:</div>
        <div style="float:left;">
            <?php
            $edit_obj = new make_editor($browser);
            echo $edit_obj->show_editor('pg_content', '480', '650', format_str(@$empt['pg_content']));
            ?>
            <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>
        </div>

    <div style="clear:both;"></div>
    </td>
    </tr>
</table>
</div>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">

    <tr><td>&nbsp;</td></tr>

	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;" />&nbsp;&nbsp;
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;">
		</td>
	</tr>

</table>

</form>

<?php
include_once("includes/footer.php");
?>