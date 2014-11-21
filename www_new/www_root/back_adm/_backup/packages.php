<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 4; include_once('includes/check_permission.php');

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

$ignore = array_flip(array('pageindex', 'search_it', 'title', 'cost', 'cost_dir', 'is_basic', 'is_recursive', 'is_active')); //for reset type func

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
    $query = sprintf("DELETE FROM membership_packages WHERE id IN (%s)", $rid_csv);
    mysql_query($query);

    #/ Delete service_types
    $query = sprintf("DELETE FROM package_benefits WHERE package_id IN (%s)", $rid_csv);
    mysql_query($query);


    $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Record(s) were successfully DELETED.');
    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_title = getgpcvar("title", "G");
$sr_cost = getgpcvar("cost", "G");
$sr_cost_dir = getgpcvar("cost_dir", "G");
$sr_is_basic = getgpcvar("is_basic", "G");
$sr_is_recursive = getgpcvar("is_recursive", "G");
$sr_is_active = getgpcvar("is_active", "G");

$operation_page = 'packages_opp.php';
/////////////////////////////////////////////////////////////////////////

$load_fancy = true;

$pg_title = "Membership Packages";
include_once("includes/header.php");
?>

<script>
$(document).ready(function() {
	$(".fbox").fancybox({
	    minWidth    : 200,
	    minHeight   : 5,
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
    if((key!='cost') && (document.getElementById('cost').value!='')){url_x+="&cost="+escape(document.getElementById('cost').value);}
    if((key!='cost_dir') && (document.getElementById('cost_dir').value!='')){url_x+="&cost_dir="+escape(document.getElementById('cost_dir').value);}
    if((key!='is_basic') && (document.getElementById('is_basic').value!='')){url_x+="&is_basic="+escape(document.getElementById('is_basic').value);}
    if((key!='is_recursive') && (document.getElementById('is_recursive').value!='')){url_x+="&is_recursive="+escape(document.getElementById('is_recursive').value);}
    if((key!='is_active') && (document.getElementById('is_active').value!='')){url_x+="&is_active="+escape(document.getElementById('is_active').value);}

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
    location.href='<?=$operation_page?><?=$param3?>&pkg_id='+rec_id;

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
        return confirm("Are you sure you want to delete the selected record(s)?\nThis may have some impact on Users\' data who already purchased this package.");
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

       if(!empty($sr_title)) $get_where .= $src->where_it($sr_title, 'lp.title', 'title');

       $cdir = 'equals';
       if($sr_cost_dir=='lte')
       $cdir = 'less-than-equals';
       if($sr_cost_dir=='gte')
       $cdir = 'greater-than-equals';

       if($sr_cost!='') $get_where .= $src->where_it($sr_cost, 'cost', 'cost', $cdir);

       if($sr_is_basic!='') $get_where .= $src->where_it($sr_is_basic, 'is_basic', 'is_basic', 'equals');
       if($sr_is_recursive!='') $get_where .= $src->where_it($sr_is_recursive, 'is_recursive', 'is_recursive', 'equals');
       if($sr_is_active!='') $get_where .= $src->where_it($sr_is_active, 'is_active', 'is_active', 'equals');

       $where .= $get_where;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2, 3, 4, 5, 6)) == false) {
	$orderby = 6;
	}

	switch($orderby)
    {
		case '1': $orderby = 'lp.title'; break;
		case '2': $orderby = 'cost'; break;
        case '3': $orderby = 'is_basic'; break;
		case '4': $orderby = 'is_recursive'; break;
		case '5': $orderby = 'is_active'; break;
		case '6': $orderby = 'added_on'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "DESC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT lp.*, lp.id as lp_id
    FROM membership_packages lp
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
		case 'lp.title':  $orderby = '1'; break;
		case 'cost': $orderby = '2'; break;
        case 'is_basic': $orderby = '3'; break;
		case 'is_recursive': $orderby = '4'; break;
		case 'is_active': $orderby = '5'; break;
		case 'added_on': $orderby = '6'; break;
	}
	?>

    <tr>
        <th width="4%" nowrap>&nbsp;</th>

        <th valign="top" width="26%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Title
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="17%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Cost ($)
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="14%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 3, "orderdi" => $orderby == 3 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Is Basic
            <?php if ($orderby == 3 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 3 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="14%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 4, "orderdi" => $orderby == 4 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Dues Enabled
            <?php if ($orderby == 4 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 4 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="14%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 5, "orderdi" => $orderby == 5 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Is Active
            <?php if ($orderby == 5 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 5 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="11%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="title" name="title" value="<?php echo $sr_title; ?>" style="width:130px;" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br />
        <select id="cost_dir" name="cost_dir" style="width:50px;" nchange="filter_x(this);"><option value=""></option><option value="lte">&lt;=</option><option value="gte">&gt;=</option></select><?php echo "<script>document.getElementById('cost_dir').value='{$sr_cost_dir}';</script>"; ?>
        <input type="text" id="cost" name="cost" value="<?php echo $sr_cost; ?>" style="width:40px;" maxlength="3" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="is_basic" name="is_basic" style="width:80px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('is_basic').value='{$sr_is_basic}';</script>"; ?></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="is_recursive" name="is_recursive" style="width:80px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('is_recursive').value='{$sr_is_recursive}';</script>"; ?></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="is_active" name="is_active" style="width:80px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('is_active').value='{$sr_is_active}';</script>"; ?></th>
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

            $is_active = ($recrd["is_active"]);

            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            if($is_active=='0')
            $tr_bg = "#FFF0F0";

            #/ get Benefits
            $query_2 = sprintf("SELECT title FROM package_benefits WHERE package_id='%d' ORDER BY id", $recrd["lp_id"]);
        	$empt_2 = mysql_exec($query_2);
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"><input type="checkbox" name="RecordID[]" value="<?php echo $recrd["lp_id"]; ?>" /></td>

                <td valign="top">
                    <?php if(is_array($empt_2) && count($empt_2)>0)
                    {
                        echo "<a class=\"fbox\" href=\"#stypes_{$recrd["lp_id"]}\" title=\"List of Benefits\">{$title}</a>";

                        echo "<div id='stypes_{$recrd["lp_id"]}' style='display:none;'><br /><center>";
                        echo "<b style='color:#53A9E9; text-decoration:underline;'>{$recrd["title"]}</b><br /><br /><div style='height:5px;'></div>";
                        foreach($empt_2 as $empt_2v)
                        {
                            echo str_replace(array('[b]', '[/b]'), array('<b>', '</b>'), $empt_2v['title'])."<div style='height:5px;'></div>";
                        }
                        echo "</center></div>";
                    }
                    else {
                    echo cut_str(($title), 130);
                    }
                    ?>
                </td>

                <td valign="top"><?php echo number_format((float)$recrd["cost"], 1); ?></td>

                <td valign="top"><?php echo ($recrd["is_basic"])?'Yes':'No'; ?></td>
                <td valign="top"><?php echo ($recrd["is_recursive"])?'Yes':'No'; ?></td>
                <td valign="top"><?php echo ($is_active)?'Yes':'No'; ?></td>

                <td valign="top" align="right">
                    <?php if($is_active=='0') {?><img src="<?=DOC_ROOT_ADMIN?>/images/red.png" width="10" border="none" title="in-active" /><?php } ?>
                    <input type="button" class="button" value="Edit" onclick="update_rec('<?php echo $recrd['lp_id']; ?>', '<?php echo $pageindex; ?>');" />
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
        FROM membership_packages lp
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
<div style="float:left; font-style:italic;">Click on the Titles to review the List of Benefits within</div>

<div style="float: right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" /></div>
<div style="clear:both;"></div>

<?php
include_once("includes/footer.php");
?>