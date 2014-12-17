<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 8; include_once('includes/check_permission.php');

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

$ignore = array_flip(array('pageindex', 'search_it', 'tag', 'uc_dir', 'usage_count')); //for reset type func

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

    #/ Delete all Records and Child Records
    $query = sprintf("DELETE FROM voice_tags WHERE id IN (%s)", $rid_csv);
    mysql_query($query);


    $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Record(s) were successfully DELETED.');
    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_tag = getgpcvar("tag", "G");
$sr_uc_dir = getgpcvar("uc_dir", "G");
$sr_usage_count = getgpcvar("usage_count", "G");

$operation_page = 'voice_tags_opp.php';

/////////////////////////////////////////////////////////////////////////

$pg_title = "Voice Tags";
include_once("includes/header.php");
?>


<!-- Custom Functions -->
<script>
function filter_x(fld)
{
    var key = fld.name;
    var val = escape(fld.value);

    var url_x = '';
    if((key!='tag') && (document.getElementById('tag').value!='')){url_x+="&tag="+escape(document.getElementById('tag').value);}
    if((key!='uc_dir') && (document.getElementById('uc_dir').value!='')){url_x+="&uc_dir="+escape(document.getElementById('uc_dir').value);}
    if((key!='usage_count') && (document.getElementById('usage_count').value!='')){url_x+="&usage_count="+escape(document.getElementById('usage_count').value);}

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
    location.href='<?=$operation_page?><?=$param3?>&vt_id='+rec_id;

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
<div style="float:right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" />&nbsp;
<a class="button" href="<?=$operation_page?><?=$param3?>">&nbsp;Add New&nbsp;</a></div>
<div style="clear:both;"></div>
</div>

<div style="clear:both; height:20px;">&nbsp;</div>

<div>
    <script>
    function validate_f1()
    {
        if (IsChecked(document.f1["RecordID[]"], "Please select atleast 1 record to delete !") == false) return false;
        return confirm("Are you sure you want to delete the selected record(s)?\nThey could have Voices & Eco-System data associated with them.");
    }
    </script>
    <form action="" method="post" name="f1" onsubmit="return validate_f1();">
    <input type="hidden" name="command" value="del" />


    <table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">

    <?php
    $recrd = false;

	#### Build SQL
	$where="";
    $having = '';

	##### Search String
    if($search_it)
	{
       include_once("../../includes/srch_lib.php");
       $src = new srch_h();
       $get_where = '';
       $get_having = '';

       if(!empty($sr_tag)) $get_where.= $src->where_it($sr_tag, 'tag', 'tag');

       $cdir = 'equals';
       if($sr_uc_dir=='lte')
       $cdir = 'less-than-equals';
       if($sr_uc_dir=='gte')
       $cdir = 'greater-than-equals';

       if($sr_usage_count!='') $get_having.= $src->where_it($sr_usage_count, 'usage_count', 'usage_count', $cdir);

       $where.= $get_where;
       $having.= $get_having;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2)) == false) {
	$orderby = 2;
	}

	switch($orderby)
    {
		case '1': $orderby = 'tag'; break;
		case '2': $orderby = 'usage_count'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "DESC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT vt.id, vt.tag,
    uv.cx, sv.cx, es.cx,
    ( (IF(ISNULL(uv.cx), 0, uv.cx)) + (IF(ISNULL(sv.cx), 0, sv.cx)) + (IF(ISNULL(es.cx), 0, es.cx)) ) AS usage_count

    FROM voice_tags vt

    LEFT JOIN (SELECT vt.id AS voice_tag_id, COUNT(ax) cx FROM voice_tags vt LEFT JOIN (SELECT REPLACE(REPLACE(REPLACE(uv.voice_tag_ids, '\"', ''), '[', ''), ']', '') ax FROM user_voices uv WHERE uv.voice_tag_ids!='') uv_view ON FIND_IN_SET(vt.id, uv_view.ax) GROUP BY vt.id) uv ON uv.voice_tag_id=vt.id
    LEFT JOIN (SELECT vt.id AS voice_tag_id, COUNT(ax) cx FROM voice_tags vt LEFT JOIN (SELECT REPLACE(REPLACE(REPLACE(sv.voice_tag_ids, '\"', ''), '[', ''), ']', '') ax FROM streams_voice sv WHERE sv.voice_tag_ids!='') uv_view ON FIND_IN_SET(vt.id, uv_view.ax) GROUP BY vt.id) sv ON sv.voice_tag_id=vt.id
    LEFT JOIN (SELECT vt.id AS voice_tag_id, COUNT(ax) cx FROM voice_tags vt LEFT JOIN (SELECT REPLACE(REPLACE(REPLACE(es.voice_tag_ids, '\"', ''), '[', ''), ']', '') ax FROM eco_system es WHERE es.voice_tag_ids!='') uv_view ON FIND_IN_SET(vt.id, uv_view.ax) GROUP BY vt.id) es ON es.voice_tag_id=vt.id

    WHERE 1 = 1 %s
    GROUP BY vt.id
    HAVING 1=1 %s
	ORDER BY %s %s
    LIMIT %d, %d
	", $where, $having, $orderby, $orderdi, $pageindex * $pagesize, $pagesize
	);
    //echo '<pre>'.$query; die();
    #### ---


	$token = mysql_query($query) or die(mysql_error());
    $recrd = mysql_fetch_assoc($token);

	switch($orderby)
    {
		case 'tag':  $orderby = '1'; break;
		case 'usage_count': $orderby = '2'; break;
	}
	?>

    <tr>
        <th width="2%" nowrap>&nbsp;</th>

        <th valign="top" width="48%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Tag Title
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="40%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Usage Count
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="10%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="tag" name="tag" value="<?php echo $sr_tag; ?>" style="width:200px;" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br />
        <select id="uc_dir" name="uc_dir" style="width:60px;" nchange="filter_x(this);"><option value=""></option><option value="lte">&lt;=</option><option value="gte">&gt;=</option></select><?php echo "<script>document.getElementById('uc_dir').value='{$sr_uc_dir}';</script>"; ?>
        <input type="text" id="usage_count" name="usage_count" value="<?php echo $sr_usage_count; ?>" style="width:70px;" maxlength="3" onblur="filter_x(this);" /></th>

        <th class="inps"></th>
    </tr>


    <?php
	if($recrd == false)
	{
		?>
        <tr>
        <td colspan="4" align="center">
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

            $tag = ($recrd["tag"]);
            if(($sr_tag !== false) && ($sr_tag!='')){
            $tag = $src->get_highlighted($tag, 'tag');
            if($tag=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }


            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"><input type="checkbox" name="RecordID[]" value="<?php echo $recrd["id"]; ?>" /></td>

                <td valign="top"><?php echo $tag; ?></td>
                <td valign="top"><?php echo number_format((int)$recrd["usage_count"], 0); ?></td>

                <td valign="top" align="right">
                    <input type="button" class="button" value="Edit" onclick="update_rec('<?php echo $recrd['id']; ?>', '<?php echo $pageindex; ?>');" />
                </td>
            </tr>
            <?php
            }//end if skip.....

            $recrd = mysql_fetch_assoc($token);
		}//end while...
    ?>

    <tr>
        <td colspan="4"><br /><input type="submit" class="button" value="Delete Selected Record(s)"/></td>
    </tr>

    <tr style="background: none !important;">
        <td colspan="4"><br />
        <?php
		#### Build Paging
        $query = sprintf("SELECT count(*) AS C FROM
        (
        SELECT vt.id,
        ( (IF(ISNULL(uv.cx), 0, uv.cx)) + (IF(ISNULL(sv.cx), 0, sv.cx)) + (IF(ISNULL(es.cx), 0, es.cx)) ) AS usage_count

        FROM voice_tags vt

        LEFT JOIN (SELECT vt.id AS voice_tag_id, COUNT(ax) cx FROM voice_tags vt LEFT JOIN (SELECT REPLACE(REPLACE(REPLACE(uv.voice_tag_ids, '\"', ''), '[', ''), ']', '') ax FROM user_voices uv WHERE uv.voice_tag_ids!='') uv_view ON FIND_IN_SET(vt.id, uv_view.ax) GROUP BY vt.id) uv ON uv.voice_tag_id=vt.id
        LEFT JOIN (SELECT vt.id AS voice_tag_id, COUNT(ax) cx FROM voice_tags vt LEFT JOIN (SELECT REPLACE(REPLACE(REPLACE(sv.voice_tag_ids, '\"', ''), '[', ''), ']', '') ax FROM streams_voice sv WHERE sv.voice_tag_ids!='') uv_view ON FIND_IN_SET(vt.id, uv_view.ax) GROUP BY vt.id) sv ON sv.voice_tag_id=vt.id
        LEFT JOIN (SELECT vt.id AS voice_tag_id, COUNT(ax) cx FROM voice_tags vt LEFT JOIN (SELECT REPLACE(REPLACE(REPLACE(es.voice_tag_ids, '\"', ''), '[', ''), ']', '') ax FROM eco_system es WHERE es.voice_tag_ids!='') uv_view ON FIND_IN_SET(vt.id, uv_view.ax) GROUP BY vt.id) es ON es.voice_tag_id=vt.id

        WHERE 1 = 1 %s
        GROUP BY vt.id
        HAVING 1=1 %s
        ) as t1", $where, $having
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