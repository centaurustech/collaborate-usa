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

$btr = (int)getgpcvar("btr", "G");
$bkr = (string)getgpcvar("bkr", "G");

/////////////////////////////////////////////////////////////////////////

$ignore = array_flip(array('pageindex', 'search_it', 'email_add', 'voice_cat_id', 'question_text', 'vc_dir', 'votes_count', 'is_blocked')); //for reset type func

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


    #/ Move Voices into Dump Table
    $sql_in_ar = array(); $sql_ins = '';
    $moved_vc_flag = false;

    $voice_data = mysql_exec(sprintf("SELECT * FROM user_voices WHERE id IN (%s)", $rid_csv));
    if(is_array($voice_data) && count($voice_data)>0)
    {
        $sql_ins = "INSERT INTO voices_dump
    	(original_voice_id, user_id, voice_cat_id, voice_tag_ids, question_text, voice_details, voice_pic, added_on, dumped_on) VALUES";
        foreach($voice_data as $vd_v)
        {
            $sql_in_ar[]= "
            ('{$vd_v['id']}', '{$vd_v['user_id']}', '{$vd_v['voice_cat_id']}', '{$vd_v['voice_tag_ids']}', '{$vd_v['question_text']}', '{$vd_v['voice_details']}', '{$vd_v['voice_pic']}', '{$vd_v['added_on']}', NOW())";
        }

        if(count($sql_in_ar)>0){
        $sql_ins.=@implode(', ', $sql_in_ar);
        @mysql_exec($sql_ins, 'save');
        $moved_vc_flag = true;
        }
    }


    #/ Move Votes into Dump Table
    $sql_in_ar = array(); $sql_ins = '';
    $moved_vt_flag = false;

    $vote_data = @mysql_exec(sprintf("SELECT * FROM voices_votes WHERE voice_id IN (%s)", $rid_csv));
    if(is_array($vote_data) && count($vote_data)>0)
    {
        $sql_ins = "INSERT INTO voices_votes_dump
    	(voice_id, user_id, vote_value, voted_on) VALUES";
        foreach($vote_data as $vd_v)
        {
            $sql_in_ar[]= "
            ('{$vd_v['voice_id']}', '{$vd_v['user_id']}', '{$vd_v['vote_value']}', '{$vd_v['voted_on']}')";
        }

        if(count($sql_in_ar)>0){
        $sql_ins.=@implode(', ', $sql_in_ar);
        //die($sql_ins);
        @mysql_exec($sql_ins, 'save');
        $moved_vt_flag = true;
        }
    }

    //echo mysql_error(); var_dump($rid_csv); die('x');

    #/ Delete Voice
    if($moved_vc_flag == true){
    $query = sprintf("DELETE FROM user_voices WHERE id IN (%s)", $rid_csv);
    mysql_query($query);
    }

    #/ Delete Vote
    if($moved_vt_flag == true){
    $query = sprintf("DELETE FROM voices_votes WHERE voice_id IN (%s)", $rid_csv);
    mysql_query($query);
    }



    $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Record(s) were successfully Moved to Archive.');
    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_email_add = getgpcvar("email_add", "G");
$sr_voice_cat_id = getgpcvar("voice_cat_id", "G");
$sr_question_text = getgpcvar("question_text", "G");
$sr_vc_dir = getgpcvar("vc_dir", "G");
$sr_votes_count = getgpcvar("votes_count", "G");
$sr_is_blocked = getgpcvar("is_blocked", "G");

$operation_page = 'voices_opp.php';

/////////////////////////////////////////////////////////////////////////
$load_fancy = true;

$pg_title = "Member Voices (active)";
include_once("includes/header.php");
?>


<!-- Custom Functions -->
<script>
function filter_x(fld)
{
    var key = fld.name;
    var val = escape(fld.value);

    var url_x = '';
    if((key!='email_add') && (document.getElementById('email_add').value!='')){url_x+="&email_add="+escape(document.getElementById('email_add').value);}
    if((key!='voice_cat_id') && (document.getElementById('voice_cat_id').value!='')){url_x+="&voice_cat_id="+escape(document.getElementById('voice_cat_id').value);}
    if((key!='question_text') && (document.getElementById('question_text').value!='')){url_x+="&question_text="+escape(document.getElementById('question_text').value);}
    if((key!='vc_dir') && (document.getElementById('vc_dir').value!='')){url_x+="&vc_dir="+escape(document.getElementById('vc_dir').value);}
    if((key!='votes_count') && (document.getElementById('votes_count').value!='')){url_x+="&votes_count="+escape(document.getElementById('votes_count').value);}
    if((key!='is_blocked') && (document.getElementById('is_blocked').value!='')){url_x+="&is_blocked="+escape(document.getElementById('is_blocked').value);}

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
    location.href='<?=$operation_page?><?=$param3?>&vc_id='+rec_id;

}//end func......

function clear_all()
{
    location.href='<?php echo $cur_page.$param; ?>';
}

$(document).ready(function() {
	$(".fbox").fancybox({
	    minWidth    : 550,
	    minHeight   : 10,
		maxWidth	: 750,
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

<?php
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
?>

<div style="float:left;"><h1><?=$pg_title?></h1></div>
<div style="clear:both; height:1px;">&nbsp;</div>

<div style="float:left; width:100%">
<div style="float:right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" />&nbsp;
<?php /*<a class="button" href="<?=$operation_page?><?=$param3?>">&nbsp;Add New&nbsp;</a>*/ ?>
<?php if($btr>0){ ?><div style="float:right; margin-right:8px;"><input type="button" class="button" value="&laquo; Back" onclick="window.location='<?=urldecode($bkr)?>';" style="width:70px;" /></div><?php } ?>
</div>
<div style="clear:both;"></div>
</div>

<div style="clear:both; height:20px;">&nbsp;</div>

<div>
    <script>
    function validate_f1()
    {
        if (IsChecked(document.f1["RecordID[]"], "Please select atleast 1 record to Archive!") == false) return false;
        return confirm("Are you sure you want to Archive the selected record(s)?");
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

       if(!empty($sr_email_add)) $get_where .= $src->where_it($sr_email_add, 'email_add', 'email_add');
       if($sr_voice_cat_id!='') $get_where .= $src->where_it($sr_voice_cat_id, 'voice_cat_id', 'voice_cat_id', 'equals');
       if(!empty($sr_question_text)) $get_where.= $src->where_it($sr_question_text, 'question_text', 'question_text');

       $cdir = 'equals';
       if($sr_vc_dir=='lte')
       $cdir = 'less-than-equals';
       if($sr_vc_dir=='gte')
       $cdir = 'greater-than-equals';
       if($sr_votes_count!='') $get_having.= $src->where_it($sr_votes_count, 'votes_count', 'votes_count', $cdir);

       if($sr_is_blocked!='') $get_where .= $src->where_it($sr_is_blocked, 'uv.is_blocked', 'is_blocked', 'equals');

       $where.= $get_where;
       $having.= $get_having;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2, 3, 4, 5, 6)) == false) {
	$orderby = 6;
	}

	switch($orderby)
    {
		case '1': $orderby = 'email_add'; break;
		case '2': $orderby = 'voice_cat_id'; break;
		case '3': $orderby = 'question_text'; break;
		case '4': $orderby = 'votes_count'; break;
		case '5': $orderby = 'uv.is_blocked'; break;
		case '6': $orderby = 'added_on'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "DESC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT uv.*,
    IF(ISNULL(vv.cx), 0, vv.cx) AS votes_count,
    u.email_add

    FROM user_voices uv
    LEFT JOIN (SELECT vv.voice_id, COUNT(*) AS cx FROM voices_votes vv GROUP BY vv.voice_id) vv ON vv.voice_id=uv.id
    LEFT JOIN users u ON u.id=uv.user_id

    WHERE 1 = 1 %s
    GROUP BY uv.id
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
		case 'email_add': $orderby = '1'; break;
		case 'voice_cat_id': $orderby = '2'; break;
		case 'question_text': $orderby = '3'; break;
		case 'votes_count': $orderby = '4'; break;
		case 'uv.is_blocked': $orderby = '5'; break;
		case 'added_on': $orderby = '6'; break;
	}

    #/ Get Categories
    ##/ Fill select-options
    $cat_opts = '';
    $cats = @format_str(@mysql_exec("SELECT DISTINCT category, id FROM voice_categories GROUP BY category ORDER BY category"));
    $cats_x = cb89($cats, 'id');
    if(is_array($cats) && count($cats)>0){
    foreach($cats as $cat_v){$cat_opts.= "<option value='{$cat_v['id']}'>{$cat_v['category']}</option>";}
    }
	?>

    <tr>
        <th width="2%" nowrap>&nbsp;</th>

        <th valign="top" width="28%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 3, "orderdi" => $orderby == 3 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Voice Question
            <?php if ($orderby == 3 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 3 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
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

        <th valign="top" width="18%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Posted By
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="10%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 4, "orderdi" => $orderby == 4 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Votes
            <?php if ($orderby == 4 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 4 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="7%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 5, "orderdi" => $orderby == 5 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Blocked
            <?php if ($orderby == 5 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 5 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="14%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 6, "orderdi" => $orderby == 6 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Created On
            <?php if ($orderby == 6 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 6 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="8%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="question_text" name="question_text" value="<?php echo $sr_question_text; ?>" style="width:160px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="voice_cat_id" name="voice_cat_id" style="width:100px;" onchange="filter_x(this);"><option value=""></option><?php echo $cat_opts; ?></select><?php echo "<script>document.getElementById('voice_cat_id').value='{$sr_voice_cat_id}';</script>"; ?></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="email_add" name="email_add" value="<?php echo $sr_email_add; ?>" style="width:120px;" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br />
        <select id="vc_dir" name="vc_dir" style="width:40px;" nchange="filter_x(this);"><option value=""></option><option value="lte">&lt;=</option><option value="gte">&gt;=</option></select><?php echo "<script>document.getElementById('vc_dir').value='{$sr_vc_dir}';</script>"; ?>
        <input type="text" id="votes_count" name="votes_count" value="<?php echo $sr_votes_count; ?>" style="width:25px;" maxlength="3" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="is_blocked" name="is_blocked" style="width:50px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('is_blocked').value='{$sr_is_blocked}';</script>"; ?></th>

        <th class="inps"></th>
        <th class="inps"></th>
    </tr>


    <?php
	if($recrd == false)
	{
		?>
        <tr>
        <td colspan="8" align="center">
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

            $question_text = ($recrd["question_text"]);
            if(($sr_question_text !== false) && ($sr_question_text!='')){
            $question_text = $src->get_highlighted($question_text, 'question_text');
            if($question_text=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $email_add = ($recrd["email_add"]);
            if(($sr_email_add !== false) && ($sr_email_add!='')){
            $email_add = $src->get_highlighted($email_add, 'email_add');
            if($email_add=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $is_blocked = ($recrd["is_blocked"]);
            $recrd["votes_count"] = (int)$recrd["votes_count"];


            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            if($is_blocked=='1')
            $tr_bg = "#FFF0F0";
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"><input type="checkbox" name="RecordID[]" value="<?php echo $recrd["id"]; ?>" /></td>

                <td valign="top">
                    <a href="#vc_<?=$recrd["id"]?>" class="fbox" title="Voice - Full Question"><?php echo cut_str($question_text, 90); ?></a>
                    <div id="vc_<?=$recrd["id"]?>" style="display:none; max-width:500px;"><br />
                        <div style="color:#53A9E9; font-weight:bold;"><?php echo $recrd["question_text"]; ?></div><br />
                        <div><?php echo $recrd["voice_details"]; ?></div><br />
                    </div>
                </td>

                <td valign="top"><?php echo @$cats_x[$recrd['voice_cat_id']]['category']; ?></td>
                <td valign="top"><?php echo $email_add; ?></td>

                <td valign="top"><?php if($recrd["votes_count"]>0){echo "<a href=\"{$consts['DOC_ROOT_ADMIN']}voice_votes_ajax.php?&vc_id={$recrd["id"]}&ro=1\" class=\"fbox fancybox.ajax\" title=\"Votes\">".number_format((int)$recrd["votes_count"], 0)."</a>";}else{echo '0';} ?></td>

                <td valign="top"><?php echo ($is_blocked)?'Yes':'No'; ?></td>
                <td valign="top"><?php echo $recrd["added_on"]; ?></td>

                <td valign="top" align="right">
                    <?php if($is_blocked=='1') {?><img src="<?=DOC_ROOT_ADMIN?>/images/red.png" width="10" border="none" title="in-active" /><?php } ?>
                    <input type="button" class="button" value="Edit" onclick="update_rec('<?php echo $recrd['id']; ?>', '<?php echo $pageindex; ?>');" />
                </td>
            </tr>
            <?php
            }//end if skip.....

            $recrd = mysql_fetch_assoc($token);
		}//end while...
    ?>

    <tr>
        <td colspan="8"><br /><input type="submit" class="button" value="Archive Selected Record(s)"/></td>
    </tr>

    <tr style="background: none !important;">
        <td colspan="8"><br />
        <?php
		#### Build Paging
        $query = sprintf("SELECT count(*) AS C FROM
        (
        SELECT uv.id, IF(ISNULL(vv.cx), 0, vv.cx) AS votes_count

        FROM user_voices uv
        LEFT JOIN (SELECT vv.voice_id, COUNT(*) AS cx FROM voices_votes vv GROUP BY vv.voice_id) vv ON vv.voice_id=uv.id
        LEFT JOIN users u ON u.id=uv.user_id

        WHERE 1 = 1 %s
        GROUP BY uv.id
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

<div style="float:left; font-style:italic;">
Click on <b>Voice Questions</b> to review the full Question & its Details<br />
Click on <b>Votes</b> to review their details
</div>

<?php
include_once("includes/footer.php");
?>