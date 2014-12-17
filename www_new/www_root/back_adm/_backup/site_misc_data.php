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

$ignore = array_flip(array('pageindex', 'search_it', 'title', 'm_value', 'm_cat')); //for reset type func

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


    #/ Get files to delete
    $sq = sprintf("SELECT m_image from site_misc_data WHERE id IN (%s)", $rid_csv);
    $c_info = mysql_exec($sq);


    #/ Delete all Records and Child Records
    $query = sprintf("DELETE FROM site_misc_data WHERE id IN (%s)", $rid_csv);
    mysql_query($query);


    #/ Delete files for these records
    if(($c_info) && count($c_info)>0)
    foreach($c_info as $v)
    {
        if($v['m_image']!=''){
        @unlink('../assets/images_2/misc/'.$v['m_image']);
        }
    }


    $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Record(s) were successfully DELETED.');
    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_title = getgpcvar("title", "G");
$sr_m_value = getgpcvar("m_value", "G");
$sr_m_cat = getgpcvar("m_cat", "G");

$operation_page = 'site_misc_opp.php';
/////////////////////////////////////////////////////////////////////////

$load_fancy = true;
$pg_title = "Misc. Site Data";
include_once("includes/header.php");
?>

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


<!-- Custom Functions -->
<script>
function filter_x(fld)
{
    var key = fld.name;
    var val = escape(fld.value);

    var url_x = '';
    if((key!='title') && (document.getElementById('title').value!='')){url_x+="&title="+escape(document.getElementById('title').value);}
    if((key!='m_value') && (document.getElementById('m_value').value!='')){url_x+="&m_value="+escape(document.getElementById('m_value').value);}
    if((key!='m_cat') && (document.getElementById('m_cat').value!='')){url_x+="&m_cat="+escape(document.getElementById('m_cat').value);}

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
    location.href='<?=$operation_page?><?=$param3?>&misc_id='+rec_id;

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


    <table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">

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

       if(!empty($sr_title)) $get_where .= $src->where_it($sr_title, 'title', 'title');
       if(!empty($sr_m_value)) $get_where .= $src->where_it($sr_m_value, 'm_value', 'm_value');
       if(!empty($sr_m_cat)) $get_where .= $src->where_it($sr_m_cat, 'm_cat', 'm_cat');

       $where .= $get_where;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2, 3)) == false) {
	$orderby = 3;
	}

	switch($orderby)
    {
		case '1': $orderby = 'title'; break;
		case '2': $orderby = 'm_value'; break;
		case '3': $orderby = 'm_cat'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "ASC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT * FROM site_misc_data
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
		case 'title':  $orderby = '1'; break;
		case 'm_value': $orderby = '2'; break;
		case 'm_cat': $orderby = '3'; break;
	}

    ##/ Fill select-options
    $cat_opts = '';
    $cats = @format_str(@mysql_exec("SELECT DISTINCT m_cat FROM site_misc_data ORDER BY m_cat"));
    if(is_array($cats) && count($cats)>0){
    foreach($cats as $cat_v){$cat_opts.= "<option value='{$cat_v['m_cat']}'>{$cat_v['m_cat']}</option>";}
    }
    #-
	?>

    <tr>
        <th width="4%" nowrap>&nbsp;</th>

        <th valign="top" width="26%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Name / Title
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="40%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Value
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="20%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 3, "orderdi" => $orderby == 3 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Category / Group
            <?php if ($orderby == 3 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 3 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="10%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="title" name="title" value="<?php echo $sr_title; ?>" style="width:170px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="m_value" name="m_value" value="<?php echo $sr_m_value; ?>" style="width:210px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="m_cat" name="m_cat" style="width:120px;" onchange="filter_x(this);"><option value=""></option><?php echo $cat_opts; ?></select><?php echo "<script>document.getElementById('m_cat').value='{$sr_m_cat}';</script>"; ?></th>
        <th class="inps"></th>
    </tr>


    <?php
	if($recrd == false)
	{
		?>
        <tr>
        <td colspan="5" align="center">
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
            $recrd["m_value"] = strip_tags($recrd["m_value"]);

            $recrd = format_str($recrd);

            $c++;
            $skip_flag = false;

            $title = ($recrd["title"]);
            if(($sr_title !== false) && ($sr_title!='')){
            $title = $src->get_highlighted($title, 'title');
            if($title=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $m_value = ($recrd["m_value"]);
            if(($sr_m_value !== false) && ($sr_m_value!='')){
            $m_value = $src->get_highlighted($m_value, 'm_value');
            if($m_value=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $m_cat = ($recrd["m_cat"]);
            if(($sr_m_cat !== false) && ($sr_m_cat!='')){
            $m_cat = $src->get_highlighted($m_cat, 'm_cat');
            if($m_cat=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }


            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"><input type="checkbox" name="RecordID[]" value="<?php echo $recrd["id"]; ?>" /></td>

                <?php if($recrd['m_image']!=''){ ?>
                <td valign="top"><a href="../assets/images_2/misc/<?php echo $recrd['m_image']; ?>" class="fbox" title="Image"><?php echo $title; ?></a></td>
                <?php } else { ?>
                <td valign="top"><?php echo $title; ?></td>
                <?php } ?>

                <td valign="top"><?php echo cut_str($m_value, 120); ?></td>
                <td valign="top"><?php echo $m_cat; ?></td>

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
        <td colspan="5"><br /><input type="submit" class="button" value="Delete Selected Record(s)"/></td>
    </tr>

    <tr style="background: none !important;">
        <td colspan="5"><br />
        <?php
		#### Build Paging
		$query = sprintf("
        SELECT count(*) AS C
        FROM site_misc_data
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
<div style="float:left; font-style:italic;">For entries with Images, click on their Titles to review them.</div>

<div style="float: right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" /></div>
<div style="clear:both;"></div>

<?php
include_once("includes/footer.php");
?>