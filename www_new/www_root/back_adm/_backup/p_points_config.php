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

$ignore = array_flip(array('pageindex', 'search_it', 'category', 'action_title', 'action_key', 'cost_dir', 'points', 'percentage_points', 'cost2_dir', 'limits_per_day', 'is_active')); //for reset type func

$param = http_build_query(array_diff_key($_GET, $ignore));
$param3 = http_build_query($_GET);

if(!empty($param)) $param = '?'.$param.'&'; else $param = '?';
if(!empty($param3)) $param3 = '?'.$param3.'&'; else $param3 = '?';

/////////////////////////////////////////////////////////////////////////

if(isset($_POST['command'])) //delete
{
//no delete allowed
}

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_action_title = getgpcvar("action_title", "G");
$sr_category = getgpcvar("category", "G");
$sr_action_key = getgpcvar("action_key", "G");
$sr_cost_dir = getgpcvar("cost_dir", "G");
$sr_cost2_dir = getgpcvar("cost2_dir", "G");
$sr_points = getgpcvar("points", "G");
$sr_percentage_points = getgpcvar("percentage_points", "G");
$sr_limits_per_day = getgpcvar("limits_per_day", "G");
$sr_is_active = getgpcvar("is_active", "G");

$operation_page = 'p_points_config_opp.php';
/////////////////////////////////////////////////////////////////////////

$pg_action_title = "Patronage Points Config";
include_once("includes/header.php");
?>

<!-- Custom Functions -->
<script>
function filter_x(fld)
{
    var key = fld.name;
    var val = escape(fld.value);

    var url_x = '';
    if((key!='action_title') && (document.getElementById('action_title').value!='')){url_x+="&action_title="+escape(document.getElementById('action_title').value);}
    if((key!='category') && (document.getElementById('category').value!='')){url_x+="&category="+escape(document.getElementById('category').value);}
    if((key!='action_key') && (document.getElementById('action_key').value!='')){url_x+="&action_key="+escape(document.getElementById('action_key').value);}
    if((key!='cost_dir') && (document.getElementById('cost_dir').value!='')){url_x+="&cost_dir="+escape(document.getElementById('cost_dir').value);}
    if((key!='cost2_dir') && (document.getElementById('cost2_dir').value!='')){url_x+="&cost2_dir="+escape(document.getElementById('cost2_dir').value);}
    if((key!='points') && (document.getElementById('points').value!='')){url_x+="&points="+escape(document.getElementById('points').value);}
    if((key!='percentage_points') && (document.getElementById('percentage_points').value!='')){url_x+="&percentage_points="+escape(document.getElementById('percentage_points').value);}
    if((key!='limits_per_day') && (document.getElementById('limits_per_day').value!='')){url_x+="&limits_per_day="+escape(document.getElementById('limits_per_day').value);}
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
    location.href='<?=$operation_page?><?=$param3?>&conf_id='+rec_id;

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

<div style="float:left;"><h1><?=$pg_action_title?></h1></div>
<div style="clear:both; height:1px;">&nbsp;</div>

<div style="float:left; width:100%">
<div style="float:right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" />
<?php /*<a class="button" href="<?=$operation_page?><?=$param3?>">&nbsp;Add New&nbsp;</a></div>*/ ?>
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
    <input type="hidden" name="xcommand" value="del" />


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

       if(!empty($sr_action_title)) $get_where .= $src->where_it($sr_action_title, 'action_title', 'action_title');
       if(!empty($sr_category)) $get_where .= $src->where_it($sr_category, 'category', 'category');
       if(!empty($sr_action_key)) $get_where .= $src->where_it($sr_action_key, 'action_key', 'action_key');

       $cdir = 'equals';
       if($sr_cost_dir=='lte')
       $cdir = 'less-than-equals';
       if($sr_cost_dir=='gte')
       $cdir = 'greater-than-equals';
       if($sr_points!='') $get_where .= $src->where_it($sr_points, 'points', 'points', $cdir);

       $c2dir = 'equals';
       if($sr_cost2_dir=='lte')
       $c2dir = 'less-than-equals';
       if($sr_cost2_dir=='gte')
       $c2dir = 'greater-than-equals';
       if($sr_percentage_points!='') $get_where .= $src->where_it($sr_percentage_points, 'percentage_points', 'percentage_points', $c2dir);

       if($sr_limits_per_day!='') $get_where .= $src->where_it($sr_limits_per_day, 'limits_per_day', 'limits_per_day', 'equals');
       if($sr_is_active!='') $get_where .= $src->where_it($sr_is_active, 'is_active', 'is_active', 'equals');

       $where .= $get_where;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2, 3, 4, 5, 6, 7)) == false) {
	$orderby = 2;
	}

	switch($orderby)
    {
		case '1': $orderby = 'action_title'; break;
        case '2': $orderby = 'category'; break;
		case '3': $orderby = 'action_key'; break;
        case '4': $orderby = 'points'; break;
        case '5': $orderby = 'percentage_points'; break;
        case '6': $orderby = 'limits_per_day'; break;
        case '7': $orderby = 'is_active'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "ASC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT * FROM patronage_points_config
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
		case 'action_title':  $orderby = '1'; break;
        case 'category':  $orderby = '2'; break;
		case 'action_key': $orderby = '3'; break;
        case 'points': $orderby = '4'; break;
        case 'percentage_points': $orderby = '5'; break;
        case 'limits_per_day': $orderby = '6'; break;
        case 'is_active': $orderby = '7'; break;
	}
	?>

    <tr>
        <th width="2%" nowrap>&nbsp;</th>

        <th valign="top" width="20%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Action
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="13%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Category
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="14%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 3, "orderdi" => $orderby == 3 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Key
            <?php if ($orderby == 3 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 3 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="12%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 4, "orderdi" => $orderby == 4 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Direct Points
            <?php if ($orderby == 4 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 4 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="13%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 5, "orderdi" => $orderby == 5 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Percentage Points
            <?php if ($orderby == 5 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 5 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>


        <th valign="top" width="11%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 6, "orderdi" => $orderby == 6 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Limits Per Day
            <?php if ($orderby == 6 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 6 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="8%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 7, "orderdi" => $orderby == 7 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Is Active
            <?php if ($orderby == 7 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 7 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="7%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="action_title" name="action_title" value="<?php echo $sr_action_title; ?>" style="width:170px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="category" name="category" value="<?php echo $sr_category; ?>" style="width:100px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="action_key" name="action_key" value="<?php echo $sr_action_key; ?>" style="width:100px;" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br />
        <select id="cost_dir" name="cost_dir" style="width:45px;" nchange="filter_x(this);"><option value=""></option><option value="lte">&lt;=</option><option value="gte">&gt;=</option></select><?php echo "<script>document.getElementById('cost_dir').value='{$sr_cost_dir}';</script>"; ?>
        <input type="text" id="points" name="points" value="<?php echo $sr_points; ?>" style="width:35px;" maxlength="3" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br />
        <select id="cost2_dir" name="cost2_dir" style="width:45px;" nchange="filter_x(this);"><option value=""></option><option value="lte">&lt;=</option><option value="gte">&gt;=</option></select><?php echo "<script>document.getElementById('cost2_dir').value='{$sr_cost2_dir}';</script>"; ?>
        <input type="text" id="percentage_points" name="percentage_points" value="<?php echo $sr_percentage_points; ?>" style="width:35px;" maxlength="3" onblur="filter_x(this);" /></th>


        <th class="inps"><i style="font-size: 10px;">=</i><br /><input type="text" id="limits_per_day" name="limits_per_day" value="<?php echo $sr_limits_per_day; ?>" style="width:40px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="is_active" name="is_active" style="width:50px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('is_active').value='{$sr_is_active}';</script>"; ?></th>
        <th class="inps"></th>
    </tr>


    <?php
	if($recrd == false)
	{
		?>
        <tr>
        <td colspan="9" align="center">
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

            $action_title = ($recrd["action_title"]);
            if(($sr_action_title !== false) && ($sr_action_title!='')){
            $action_title = $src->get_highlighted($action_title, 'action_title');
            if($action_title=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $category = ($recrd["category"]);
            if(($sr_category !== false) && ($sr_category!='')){
            $category = $src->get_highlighted($category, 'category');
            if($category=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $action_key = ($recrd["action_key"]);
            if(($sr_action_key !== false) && ($sr_action_key!='')){
            $action_key = $src->get_highlighted($action_key, 'action_key');
            if($action_key=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $is_active = ($recrd["is_active"]);


            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            if($is_active=='0')
            $tr_bg = "#FFF0F0";
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"></td>

                <td valign="top"><?php echo $action_title; ?></td>

                <td valign="top"><?php echo $category; ?></td>
                <td valign="top"><?php echo $action_key; ?></td>

                <td valign="top"><?php echo ((int)$recrd["points"]); ?></td>
                <td valign="top"><?php echo ((int)$recrd["percentage_points"]); ?>%</td>
                <td valign="top"><?php echo ((int)$recrd["limits_per_day"]); ?></td>
                <td valign="top"><?php echo ($is_active)?'Yes':'No'; ?></td>

                <td valign="top" align="right">
                    <?php if($is_active=='0') {?><img src="<?=DOC_ROOT_ADMIN?>/images/red.png" width="10" border="none" title="in-active" /><?php } ?>
                    <input type="button" class="button" value="Edit" onclick="update_rec('<?php echo $recrd['id']; ?>', '<?php echo $pageindex; ?>');" />
                </td>
            </tr>
            <?php
            }//end if skip.....

            $recrd = mysql_fetch_assoc($token);
		}//end while...
    ?>

    <?php /*<tr>
        <td colspan="8"><br /><input type="submit" class="button" value="Delete Selected Record(s)"/></td>
    </tr>*/ ?>

    <tr style="background: none !important;">
        <td colspan="9"><br />
        <?php
		#### Build Paging
		$query = sprintf("
        SELECT count(*) AS C
        FROM patronage_points_config
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
<div style="float:left; font-style:italic;">This Section is used to set <b>Patronage Points</b> allocation configurations.<br />
Direct "<b>Points</b>" will take precedence over "<b>Percentage Points</b>", hence they must be '0' for Percentage to take effect.
</div>

<div style="float: right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" /></div>
<div style="clear:both;"></div>

<?php
include_once("includes/footer.php");
?>