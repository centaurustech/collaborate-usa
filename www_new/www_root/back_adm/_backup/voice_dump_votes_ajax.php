<?php
require_once('../../includes/config.php');
include_once('includes/session_ajax.php');

$section_id = 8; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('vc_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$vc_id = (int) getgpcvar("vc_id", "G");

$cur_page = cur_page();
/////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


#### Get record if EDIT Mode
$empt = $empt_2 = $total = array();
$vote_list = '';
if (($vc_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT vv.*, uv.question_text, u.email_add
    FROM voices_dump uv
    RIGHT JOIN voices_votes_dump vv ON vv.voice_id=uv.original_voice_id
    LEFT JOIN users u ON u.id=vv.user_id
    WHERE voice_id='%d'
    ORDER BY voted_on DESC", $vc_id);

	/*$token  = mysql_query($query, $cn1); //or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);
    $empt2 = $empt;*/
    $empt = mysql_exec($query);

	if (($empt == false) || (empty($empt)))
	{
		die("Record Not Found !");
	}

    foreach($empt as $empt_v)
    {
        //var_dump($empt_v, '<br /><br />');
        $vote_txt = ucwords(str_replace('_', ' ', $empt_v['vote_value']));

        if(!array_key_exists($empt_v['vote_value'], $total))
        $total[$empt_v['vote_value']] = array('title'=>$vote_txt, 'count'=>1);
        else
        $total[$empt_v['vote_value']]['count']++;

        $user = $empt_v['email_add'];
        if(empty($user))$user = 'Member';

        $vote_list.="<li><span style=\"color:#53A9E9;\">{$user}</span> &nbsp;voted&nbsp; \"<span style=\"color:#53A9E9; font-weight:bold;\">{$vote_txt}</span>\" &nbsp;&nbsp;-&nbsp; on {$empt_v['voted_on']}</li>";
    }
}

///////////////////////////////////////////////////////////////////
$no_header = true;

$pg_title = "Voice Votes";
include_once("includes/header.php");
?>

<div style="float:left; margin-top: -10px;"><h1><?=$pg_title?></h1></div>
<div style="clear:both; height:15px;">&nbsp;</div>

<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>VOICE</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="color:#53A9E9;"><?=format_str(@$empt[0]['question_text'])?></div>
        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<th>TOTAL</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <?php
        if(is_array($total))foreach($total as $total_v)
        {
            echo "<div>{$total_v['title']} &nbsp;=&nbsp; ".(int)$total_v['count']."</div>";
        }
        ?>
        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<th>VOTES DETAIL</th>
	</tr>


    <tr>
    <td valign="middle" style="padding:6px 4px;">

        <style>
        .votes li{padding:3px;}
        </style>
        <div class="votes">
        <ul style="padding-left:20px; margin-bottom: 2px;">
            <?php echo $vote_list; ?>
        </ul>
        </div>
        <div style="clear:both;"></div>
    </td>
    </tr>
</table>

<?php
include_once("includes/footer.php");
?>