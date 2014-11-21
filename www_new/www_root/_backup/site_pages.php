<?php
#/ Determine Popup-Only request
$ro_path = '';
$ro = 0;
//array_key_exists('ro', $_GET) &&
if((array_key_exists('HTTP_X_FANCYBOX', $_SERVER) && ($_SERVER['HTTP_X_FANCYBOX']=='true'))){
$ro = (int)(bool)$_SERVER['HTTP_X_FANCYBOX']; //$_GET['ro'];
$ro_path = '/?ro=1';
}
//var_dump("<pre>", $ro, $_SERVER); die();

/////////////////////////////////////////////////////////////////////

if(!isset($seo_tag_id) || empty($seo_tag_id)){if($ro<=0){redirect_me('404');}else{exit;}}

#/ get Page Info
$page_info = @mysql_exec("SELECT * FROM site_pages WHERE seo_tag_id='{$seo_tag_id}' AND is_active='1'", 'single');
if(!is_array($page_info)){if($ro<=0){redirect_me('404');}else{exit;}}
//var_dump("<pre>", $seo_tag_id, $page_info); die();


##/Special Process for PDF Content
if(isset($page_info['pdf_content']) && @strlen($page_info['pdf_content'])>10)
{
    $pdf = DOC_ROOT.'assets/media/docs/'.$page_info['pdf_content'];

    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: Sat, 26 Jul 2007 05:00:00 GMT");

    header('Accept-Ranges: bytes');
    header('Content-Length: ' . filesize($pdf));

    header('Content-Encoding: none');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename=' . $page_info['pdf_content']); // Make the browser display the Save As dialog
    readfile($pdf);  //this is necessary in order to get it to actually download the file, otherwise it will be 0Kb

    exit;

}//end if pdf....
#-

/////////////////////////////////////////////////////////////////////

#/ Process Media Placeholders
if ((!empty($page_info['pg_content'])) && (stristr($page_info['pg_content'], '{media_')!=false)){ //for {media___:__} placeholders
include_once("../includes/model_media_placement.php");
$page_info['pg_content'] = place_media($page_info['pg_content']);
}


#/ Fill pg_meta
$pg_meta = array(
    'page_title'=>format_str($page_info['title']),
    'meta_keywords'=>format_str($page_info['meta_keywords']),
    'meta_descr'=>format_str($page_info['meta_descr']),
);
$page_heading = format_str($page_info['page_heading']);
//$head_msg = format_str($page_info['head_msg']);

if($ro>0){
$no_header = true;
$no_header_ajax = true; //i.e. content is fetched via ajax... so dont load css & js
}

include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>

<?php if($ro>0){ ?>

<div class='popup_bdy' style='max-width:700px;'>
<div class='header2 hdx'><div class='head_ttles'><?=format_str($pg_meta['page_title'])?></div></div>
<div class="content"><?php echo $page_info['pg_content']; ?></div>
</div>

<?php } else { ?>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">

    <h1>
        <strong><?=format_str($pg_meta['page_title'])?></strong>
        <?php /*if(($page_info['parent_tag']!='') && (!empty($par_seo_tag))) {
        echo "<a href='{$consts['SITE_URL']}{$par_seo_tag}' style='display:block; font-size:12px; font-style:italic;'>(Back to {$page_info['parent_title']} List)</a>";
        }*/ #to be used later, when we need articles.. ?>
    </h1>
    <br />

    <div class="content"><?php echo $page_info['pg_content']; ?></div>

</div>

</div>
<div class="clear"></div>
</div>
</div>
<?php } ?>

<?php
include_once("includes/footer.php");
?>