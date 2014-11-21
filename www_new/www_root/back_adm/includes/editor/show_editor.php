<?php
/**
FCK Editor Integration Class

Author: Raheel Hasan
version: 2.0

onsubmit: document.getElementById('{$fld_name}').value=oEdit1_{$fld_name}.getHTMLBody();
*/

class make_editor
{

    function make_editor($browser)
    {
        $edi = '';

        //if(stristr($browser, 'msie')!=false)
        //$edi.="<script language=JavaScript src='includes/editor/scripts/editor.js'></script>";
        //else
        $edi.="<script language=JavaScript src='includes/editor/scripts/moz/editor.js'></script>";

        echo $edi;

    }//end constructor..


    function show_editor($fld_name, $height='300', $width='600', $current_content='')
    {
        $edi = '';


        $old_options = '"FullScreen","|","XHTMLSource","HTMLSource",
		"BRK", "StyleAndFormatting","TextFormatting","ListFormatting","BoxFormatting","ParagraphFormatting","CssText","Styles","|","Paragraph","FontName","FontSize","|","Bold","Italic","Underline","Strikethrough","|","Superscript","Subscript","|","ForeColor","BackColor",
		"BRK","JustifyLeft","JustifyCenter","JustifyRight","JustifyFull","|","Numbering","Bullets","|","Indent","Outdent","LTR","RTL","|","Table","Guidelines","|","Hyperlink", "Characters","Line","|","Image","|","ClearAll"
        ';

        $options = '"FullScreen","|","FontName","FontSize","|","Bold","Italic","Underline","|",
        "JustifyLeft","JustifyCenter","JustifyRight","JustifyFull","|",
        "ForeColor","BackColor","|","Superscript","Subscript","|",
        "Image","Flash","Characters","|","XHTMLSource","ClearAll",
        "BRK","Numbering","Bullets","|","Table","|","Indent","Outdent","|","Hyperlink"
        ';


        $edi.="
        <style>
        #div_par_{$fld_name} td{padding:0 !important; margin:0 !important; border:none !important;}
        #idContentoEdit1_{$fld_name} table{border:solid 1px gray;}
        </style>

        <div id=\"div_par_{$fld_name}\">
        <pre id=\"idHead_{$fld_name}\" name=\"idHead_{$fld_name}\" style=\"display:none; width:560px;
        background-color:#D5F446;\">{$current_content}</pre>

    	<script language=javascript>
        var oEdit1_{$fld_name} = new InnovaEditor(\"oEdit1_{$fld_name}\");

    	oEdit1_{$fld_name}.arrStyle = [
        ['body, table, td, p, div, span, a, i, b, u', false, '' ,\"font-family:Arial, Helvetica, sans-serif; color:#000;\"],
        ['body, table, td', false, '' ,'font-size:14px;'],
        ['table', false, '', 'border:dotted 1px gray;'],
        ['td', false, '', 'border:dotted 1px gray; height:20px;'],
        ['p', false, '', \"font:14px/18px Arial, Helvetica, sans-serif; margin:0 0 12px;\"],
        ['h1, h2, h3, h4, h5', false, '', \"font-family:'Lato', sans-serif;\"],
        ['b, strong', false, '', \"font: bold 14px Arial, Helvetica, sans-serif;\"],
        ['ul, ol', false, '', 'padding-left:20px; margin:10px 0; '],
        ['li', false, '', 'margin: 2px 0 10px;'],
        ['a, a:hover, a:visited, a:active', false, '' ,'text-decoration:none; color:#2CA1F4;']
        ];

    	oEdit1_{$fld_name}.btnHTMLFullSource=false;
    	oEdit1_{$fld_name}.btnHTMLSource=false;
    	oEdit1_{$fld_name}.btnXHTMLFullSource=false;

    	oEdit1_{$fld_name}.width=\"{$width}\";
    	oEdit1_{$fld_name}.height=\"{$height}\";

    	if(navigator.appName==\"Microsoft Internet Explorer\")
    	{
    		oEdit1_{$fld_name}.features=[{$options}];
    		oEdit1_{$fld_name}.cmdAssetManager=\"modalDialogShow('../../editor/assetmanager/assetmanager.php', {$width}, 450)\";
    	}
    	else
    	{
    		oEdit1_{$fld_name}.features=[{$options}];
    		oEdit1_{$fld_name}.cmdAssetManager=\"modalDialogShow('../../../editor/assetmanager/assetmanager.php', {$width}, 450)\";
    	}

    	oEdit1_{$fld_name}.onSave = new Function(\"submitForm()\");
    	oEdit1_{$fld_name}.RENDER(document.getElementById('idHead_{$fld_name}').innerHTML);
        </script>

    	<input type=\"text\" name=\"{$fld_name}\" id=\"{$fld_name}\" style=\"font-size:1px; border:none; background:none;\">
        </div>
        ";

        return $edi;
    }//end func.....

}
?>