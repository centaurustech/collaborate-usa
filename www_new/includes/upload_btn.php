<?php
/**
Upload Button Replacements

Author: Raheel Hasan
version: 2.2

@param  $fld_name = name/id of html file field (name of text field will be pImage_{$fld_name})
@param  $field_class = css class of input [type=text] field (optional)
@param  $field_style = css inline style of input [type=text] field (optional)
@param  $btn_class = css class of button (optional)
@param  $package_style = css inline style of package div (optional) - e.g. float, margin, padding etc.
@param  $btn_style = css inline style of button (optional)
@param  $file_field_style = css inline style of the actual input type=file field (optional)
@param  $file_type = accept/filter to show only these meme types in browse window (like "image/*")
*/

function upload_btn($fld_name, $field_class='', $field_style='', $btn_class='', $package_style='', $btn_style='', $file_field_style='', $btn_value='browse', $file_type='')
{
    $btn = $btn_t = '';

    $browser = $_SERVER['HTTP_USER_AGENT'];
    if((stristr($browser, 'msie')!=false) || (stristr($browser, 'trident')!=false)) {$browser = 'msie';}


    ### browser based styling
    $style_2 = $margin_top = '';
    if(stristr($browser, 'msie')!=false) $margin_top='margin-top:1px;';
    else if(stristr($browser, 'firefox')!=false) $margin_top='margin-top:1px;';
    else if(stristr($browser, 'chrome')!=false) $margin_top='margin-top:1px;';
    else if(stristr($browser, 'safari')!=false) $margin_top='margin-top:1px;';
    else $margin_top='margin-top:0px;';
    ##--
    //echo $browser;


    $btn .="
    <div id=\"div_{$fld_name}\" style=\"{$package_style}\">
    <div>
        <div style=\"float:left; padding-right:5px;\">
    	   <input type=\"text\" name=\"pImage_{$fld_name}\" id=\"pImage_{$fld_name}\" readonly='1' class='{$field_class}'
    	   style=\"width:210px; cursor:default; background-color:#FFFFFF; {$margin_top}; {$field_style} \">
        </div>

        <div style=\"float:left;\">
        <input type=\"button\" class=\"{$btn_class}\" value=\"{$btn_value}\" style=\"width:55px; cursor:pointer; margin:0; {$btn_style}\">
        </div>

        <div style=\"clear:both;\"></div>
    </div>
    ";

    ### browser based styling
    if(stristr($browser, 'msie')!=false) {$style_2 = 'color: #FFFFFF; width:255px;';}
    ##--

    $accept = '';
    if(!empty($file_type))
    $accept = 'accept="'.$file_type.'"';

    $btn .="
    <div>
        <style>
    	input[type=file]{font-size:16px !important;}
    	</style>

        <!-- some browsers need width, some font-size, and some need both -->
        <input type=\"file\" id=\"{$fld_name}\" name=\"{$fld_name}\"
        {$accept}
    	onchange=\"document.getElementById('pImage_{$fld_name}').value=this.value.replace(/^.*fakepath.{1}/i, '');\"
    	style=\"cursor:default; position:absolute; z-index:2; width:255px; height:22px;
        margin-top:-23px; {$style_2} {$file_field_style}
    	filter:alpha(opacity=00);-moz-opacity:0.00; opacity:0.00;\">
    </div>
    </div>
    ";

    return $btn;

}//end func......
?>