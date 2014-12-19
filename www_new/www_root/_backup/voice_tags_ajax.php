<?php
//example call = /voice-tags-list?search_it=1&ro=1&term=life
if(!isset($seo_tag_id) || empty($seo_tag_id)){exit;}

include_once('includes/session_ajax.php');

if(!isset($_SERVER['HTTP_REFERER'])){exit;}

$allowed = array('localhost', 'www.collaborateusa.com', 'collaborateusa.com', 'new.collaborateusa.com', 'cusa-local');
if(!in_array($_SERVER['SERVER_NAME'], $allowed)) {exit;}

/////////////////////////////////////////////////////////////////////////

$user_id = (int)@$_SESSION["CUSA_Main_usr_id"];
if($user_id<=0){exit;}
$user_info = @$_SESSION["CUSA_Main_usr_info"];

$_POST = format_str($_POST);
$_GET = format_str($_GET);

/////////////////////////////////////////////////////////////////////////

$search_it = (int)@$_GET["search_it"];
$sr_tag = @$_GET["term"];

if(empty($sr_tag) || $search_it<=0){exit;}

/////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

$recrd = false;

#### Build SQL
$where="";

##### Search String
if($search_it)
{
   include_once("../includes/srch_lib.php");
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