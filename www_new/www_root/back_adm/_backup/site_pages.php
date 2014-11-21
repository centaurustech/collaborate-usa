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

$my_user_id = (int)@$_SESSION["cusa_admin_usr_id"];
if($my_user_id<=0){exit;}

/////////////////////////////////////////////////////////////////////////

$ignore = array_flip(array('pageindex', 'search_it', 'cat_id', 'title', 'seo_tag', 'is_active', 'popup_only')); //for reset type func

$param = http_build_query(array_diff_key($_GET, $ignore));
$param3 = http_build_query($_GET);

if(!empty($param)) $param = '?'.$param.'&'; else $param = '?';
if(!empty($param3)) $param3 = '?'.$param3.'&'; else $param3 = '?';

/////////////////////////////////////////////////////////////////////////

if(isset($_POST['command'])) //delete
{
    $cur_page = cur_page();

    $rid = getgpcvar("RecordID", "P");
    $rid_csv = csvfromintarray($rid, "-1");

    #/ Get seo_tags + delete pdf files
    $sq = sprintf("SELECT seo_tag_id, pdf_content from site_pages WHERE id IN (%s) AND delete_locked='0' AND self_managed='0'", $rid_csv);
    $c_info = mysql_exec($sq);
    $seo_tags_csv = '';
    if(is_array($c_info) && count($c_info)>0)
    {
        //$c_info = @array_keys(cb89($c_info, 'seo_tag_id'));
        $seo_tags_ar = array();
        foreach($c_info as $v)
        {
            $seo_tags_ar[] = $v['seo_tag_id'];

            if($v['pdf_content']!=''){
            @unlink('../assets/media/docs/'.$v['pdf_content']);
            }
        }
    }
    $seo_tags_csv = @implode(',', $seo_tags_ar);
    //var_dump("<pre>", $c_info, $seo_tags_csv); die();


    #/ Delete all Records and Child Records
    $query = sprintf("DELETE FROM site_pages WHERE id IN (%s) AND delete_locked='0' AND self_managed='0'", $rid_csv);
    mysql_query($query);


    #/ Delete seo_tags
    if(!empty($seo_tags_csv)){
    $query = sprintf("DELETE FROM seo_tags WHERE id IN (%s)", $seo_tags_csv);
    mysql_query($query);
    }


    $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Record(s) were successfully DELETED.');
    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_cat_id = getgpcvar("cat_id", "G");
$sr_title = getgpcvar("title", "G");
$sr_seo_tag = getgpcvar("seo_tag", "G");
$sr_is_active = getgpcvar("is_active", "G");
$sr_popup_only = getgpcvar("popup_only", "G");

$operation_page = 'site_pages_opp.php';
/////////////////////////////////////////////////////////////////////////

$pg_title = "Site Pages";
include_once("includes/header.php");
?>

<!-- Custom Functions -->
<script>
function filter_x(fld)
{
    var key = fld.name;
    var val = escape(fld.value);

    var url_x = '';
    if((key!='title') && (document.getElementById('title').value!='')){url_x+="&title="+escape(document.getElementById('title').value);}
    if((key!='seo_tag') && (document.getElementById('seo_tag').value!='')){url_x+="&seo_tag="+escape(document.getElementById('seo_tag').value);}
    if((key!='cat_id') && (document.getElementById('cat_id').value!='')){url_x+="&cat_id="+escape(document.getElementById('cat_id').value);}
    if((key!='is_active') && (document.getElementById('is_active').value!='')){url_x+="&is_active="+escape(document.getElementById('is_active').value);}
    if((key!='popup_only') && (document.getElementById('popup_only').value!='')){url_x+="&popup_only="+escape(document.getElementById('popup_only').value);}

    //if((url_x=='') && (val=='')){
    if(val==''){
    }
    else
    {
        var url = '<?php echo $cur_page.$param; ?>search_it=1&'+key+'='+val+url_x;
        location.href=url;
    }
}//end func.....

function update_rec(rec_id, pageindex)
{
    location.href='<?=$operation_page?><?=$param3?>&sp_id='+rec_id;

}//end func......

function clear_all()
{
    location.href='<?php echo $cur_page.$param; ?>';
}
</script>


<?php
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
?>

<div style="float:left;"><h1><?=$pg_title?></h1></div>
<div style="clear:both; height:1px;">&nbsp;</div>

<div style="float:left; width:100%">
<div style="float:right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" />
<a class="button" href="<?=$operation_page?><?=$param3?>">&nbsp;Add New&nbsp;</a></div>
<div style="clear:both;"></div>
</div>

<div style="clear:both; height:20px;">&nbsp;</div>

<div>
    <script>
    function validate_f1()
    {
        if (IsChecked(document.f1["RecordID[]"], "Please select atleast 1 record to delete !") == false) return false;
        return confirm("Are you sure you want to delete the selected record(s)?");
    }
    </script>
    <form action="" method="post" name="f1" onsubmit="return validate_f1();">
    <input type="hidden" name="command" value="del" />


    <table border="0" cellpadding="0" cellspacing="0" class="datagrid dg_middle" width="100%">

    <?php
    $recrd = false;

	#### Build SQL
	$where="";

	##### Search String
    if($search_it)
	{
       include_once("../../includes/srch_lib.php");
       $src = new srch_h();
       $get_where = '';

       if(!empty($sr_title)) $get_where .= $src->where_it($sr_title, 'sp.title', 'title');
       if(!empty($sr_seo_tag)) $get_where .= $src->where_it($sr_seo_tag, 'st.seo_tag', 'seo_tag');
       if($sr_cat_id!='') $get_where .= $src->where_it($sr_cat_id, 'cat_id', 'cat_id', 'equals');
       if($sr_is_active!='') $get_where .= $src->where_it($sr_is_active, 'sp.is_active', 'is_active', 'equals');
       if($sr_popup_only!='') $get_where .= $src->where_it($sr_popup_only, 'sp.popup_only', 'popup_only', 'equals');

       $where .= $get_where;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2, 3, 4, 5, 6)) == false) {
	$orderby = 6;
	}

	switch($orderby)
    {
		case '1': $orderby = 'sp.title'; break;
		case '2': $orderby = 'st.seo_tag'; break;
        case '3': $orderby = 'cat_id'; break;
		case '4': $orderby = 'sp.is_active'; break;
		case '5': $orderby = 'popup_only'; break;
		case '6': $orderby = 'sp.id'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "DESC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT sp.*, sp.id as sp_id, st.seo_tag, pc.title as cat_title

    FROM site_pages sp
    LEFT JOIN seo_tags st ON st.id = sp.seo_tag_id
    LEFT JOIN page_categories pc ON sp.cat_id=pc.id

    WHERE 1 = 1 %s
	ORDER BY %s %s
    LIMIT %d, %d
	", $where, $orderby, $orderdi, $pageindex * $pagesize, $pagesize
	);
    //echo '<pre>'.$query; die();
    #### ---


	$token = mysql_query($query) or die(mysql_error());
    $recrd = mysql_fetch_assoc($token);

	switch($orderby)
    {
		case 'sp.title':  $orderby = '1'; break;
		case 'st.seo_tag': $orderby = '2'; break;
        case 'cat_id': $orderby = '3'; break;
		case 'sp.is_active': $orderby = '4'; break;
        case 'popup_only': $orderby = '5'; break;
		case 'sp.id': $orderby = '6'; break;
	}


    ###/ Fill select-options
    $cat_opts = '';

    $cats = @format_str(@mysql_exec("SELECT * FROM page_categories ORDER BY title"));
    if(is_array($cats) && count($cats)>0){
    foreach($cats as $cat_v){$cat_opts.= "<option value='{$cat_v['id']}'>{$cat_v['title']}</option>";}
    }
    #-
	?>

    <tr>
        <th width="3%" nowrap>&nbsp;</th>

        <th valign="top" width="23%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Title
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="18%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            SEO Tag
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="18%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 3, "orderdi" => $orderby == 3 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Category
            <?php if ($orderby == 3 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 3 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="14%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 4, "orderdi" => $orderby == 4 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Is Active
            <?php if ($orderby == 4 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 4 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="14%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 5, "orderdi" => $orderby == 5 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Independent Page
            <?php if ($orderby == 5 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 5 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="10%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="title" name="title" value="<?php echo $sr_title; ?>" style="width:130px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="seo_tag" name="seo_tag" value="<?php echo $sr_seo_tag; ?>" style="width:120px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="cat_id" name="cat_id" style="width:120px;" onchange="filter_x(this);"><option value=""></option><?php echo $cat_opts; ?></select><?php echo "<script>document.getElementById('cat_id').value='{$sr_cat_id}';</script>"; ?></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="is_active" name="is_active" style="width:80px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('is_active').value='{$sr_is_active}';</script>"; ?></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="popup_only" name="popup_only" style="width:80px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('popup_only').value='{$sr_popup_only}';</script>"; ?></th>
        <th class="inps"></th>
    </tr>


    <?php
	if($recrd == false)
	{
		?>
        <tr>
        <td colspan="7" align="center">
        <br />
        <?php
        echo "No Results / Records Found !";
        if($search_it)
        {
            echo "&nbsp;Please clear the Filter and try again !";
        }
        ?>
        <br /><br />
        </td>
	    </tr>
        <?php
	}
	else
	{
        $c = 0;

        ## Customer Orders table start from here
		while($recrd)
		{
            $recrd = format_str($recrd);

            $c++;
            $skip_flag = false;

            $title = ($recrd["title"]);
            if(($sr_title !== false) && ($sr_title!='')){
            $title = $src->get_highlighted($title, 'title');
            if($title=='--continue--continue--continue--continue--') {$skip_flag = true;} //fail safe
            }

            $seo_tag = ($recrd["seo_tag"]);
            if(($sr_seo_tag !== false) && ($sr_seo_tag!='')){
            $seo_tag = $src->get_highlighted($seo_tag, 'seo_tag');
            if($seo_tag=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $is_active = ($recrd["is_active"]);
            $popup_only = ($recrd["popup_only"]);


            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            if($is_active=='0')
            $tr_bg = "#FFF0F0";
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"><input type="checkbox" name="RecordID[]" value="<?php echo $recrd["sp_id"]; ?>" /></td>
                <td valign="top"><?php echo cut_str(($title), 130); ?></td>
                <td valign="top"><?php echo $seo_tag; ?></td>
                <td valign="top"><?php echo empty($recrd["cat_title"])? '-':$recrd["cat_title"]; ?></td>

                <td valign="top"><?php echo ($is_active)?'Yes':'No'; ?></td>
                <td valign="top"><?php echo ($popup_only)?'Yes':'No'; ?></td>

                <td valign="top" align="right">
                    <?php if($is_active=='0') {?><img src="<?=DOC_ROOT_ADMIN?>/images/red.png" width="10" border="none" title="in-active" /><?php } ?>
                    <input type="button" class="button" value="Edit" onclick="update_rec('<?php echo $recrd['sp_id']; ?>', '<?php echo $pageindex; ?>');" />
                </td>
            </tr>
            <?php
            }//end if skip.....

            $recrd = mysql_fetch_assoc($token);
		}//end while...
    ?>

    <tr>
        <td colspan="7"><br /><input type="submit" class="button" value="Delete Selected Record(s)"/></td>
    </tr>

    <tr style="background: none !important;">
        <td colspan="7"><br />
        <?php
		#### Build Paging
		$query = sprintf("
        SELECT count(*) AS C
        FROM site_pages
        WHERE 1 = 1 %s
        ", $where
        );

        $token = mysql_query($query) or die(mysql_error());
		$recrd = mysql_fetch_assoc($token);
		$count = $recrd["C"];

        include_once('../../includes/admin/pagination_admin.php');
        echo "</div>";
        ### ---
		?>

        </td>
    </tr>

    <?php
	}//end else........
	?>
    </table>
    </form>
</div>
<br />

<div style="float: right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" /></div>
<div style="clear:both;"></div>

<?php
include_once("includes/footer.php");
?>