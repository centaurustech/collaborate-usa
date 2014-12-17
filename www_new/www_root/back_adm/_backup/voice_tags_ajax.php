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

$my_user_id = (int)@$_SESSION["cusa_admin_usr_id"];
if($my_user_id<=0){exit;}

/////////////////////////////////////////////////////////////////////////

$search_it = (int)@getgpcvar("search_it", "G");
$sr_tag = @getgpcvar("term", "G");

if(empty($sr_tag) || $search_it<=0){exit;}

/////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

$recrd = false;

#### Build SQL
$where="";

##### Search String
if($search_it)
{
   include_once("../../includes/srch_lib.php");
   $src = new srch_h();
   $get_where = '';

   if(!empty($sr_tag)) $get_where.= $src->where_it($sr_tag, 'tag', 'tag');

   $where.= $get_where;
}
#####--

$query = sprintf("SELECT vt.id, vt.tag
FROM voice_tags vt
WHERE 1 = 1 %s
ORDER BY tag ASC, added_on DESC
", $where
);
//echo '<pre>'.$query; die();
#### ---

$recrds = format_str(@mysql_exec($query));

///////////////////////////////
if(empty($recrds)){
exit;
}
else
{
    $tags = array();
    foreach($recrds as $rv)
	{
        $tag = @$rv["tag"];
        //$tags[$rv["id"]] = $tag; //with ids
        $tags[] = $tag;
	}//end while...

    if(!empty($tags))
    {
        @header('Content-Type: application/json');
        //echo json_encode($tags);

        $tgr = array('json'=>$tags);
        echo json_encode($tgr);
    }

}//end else........
?>