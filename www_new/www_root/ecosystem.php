<?php
require_once('../../includes/config.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once('../../includes/model_main.php');

$_POST = format_str($_POST);
$_GET = format_str($_GET);

////////////////////////////////////////////////////////////////////////

##/ Break URL Components into identifiers
include_once('../../includes/url_components.php');
//var_dump("<pre>", $_SERVER['REQUEST_URI'], $REQUEST_URI, $url_path, $url_comp); die();
#-

////////////////////////////////////////////////////////////////////////

##/ Identify Controller
$controller = 'x';
$seo_tag = '';
$seo_tag_id = 0;
if(is_array($url_comp) && !@empty($url_comp[0]))
{
    $seo_tag = @format_str(@$url_comp[0]);
    //$seo_tag = @rtrim($seo_tag, '.html');
    $seo_tag = @preg_replace('/.{1,}\.html/ims', '', $seo_tag);
    //var_dump("<pre>", $seo_tag); die();

    $seo_tag_id_e = mysql_exec("SELECT * FROM seo_tags WHERE seo_tag='{$seo_tag}'", 'single');
    $seo_tag_id = @$seo_tag_id_e['id'];
    //var_dump("<pre>", $seo_tag_id); die();

    if(!empty($seo_tag_id))
    {
        $sql_seo = "
        (SELECT controller AS pg FROM static_routes WHERE seo_tag_id='{$seo_tag_id}')
        UNION
        (SELECT 'site_pages' AS pg FROM site_pages sp WHERE seo_tag_id='{$seo_tag_id}' AND is_active='1' AND self_managed='0')
        LIMIT 1
        ";
        //var_dump("<pre>", $sql_seo); die();

        $seo_info = @mysql_exec($sql_seo, 'single');

        if(is_array($seo_info) && count($seo_info)>0)
        {
            $controller = @$seo_info['pg'];
            $seo_tag = @$seo_tag_id_e['seo_tag'];
        }
    }
}
else
{
    $controller = 'home';
    $seo_tag_id = 11;
}

if($seo_tag_id == 14) //manually change route of 'index' to 'home'
$seo_tag_id = 11;

//@var_dump("<pre>", $seo_info, $controller, $seo_tag, $seo_tag_id); die();
#-
////////////////////////////////////////////////////////////////////////
#/ get Page Info
$page_info = @mysql_exec("SELECT * FROM site_pages WHERE seo_tag_id='{$seo_tag_id}' AND is_active='1'", 'single');
if(!is_array($page_info)){redirect_me('404');}
//var_dump("<pre>", $page_info); die();

$site_menus = @get_dynamic_menu();
//var_dump("<pre>", $site_menus); die();
/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=>format_str($page_info['title']),
    'meta_keywords'=>format_str($page_info['meta_keywords']),
    'meta_descr'=>format_str($page_info['meta_descr']),
);
$page_heading = format_str($page_info['page_heading']);

#include_once("includes/header.php");
?>
