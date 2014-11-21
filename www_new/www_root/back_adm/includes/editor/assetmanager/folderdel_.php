<?php
$sMsg = "";

if(isset($_POST["inpCurrFolder"]))
{
	$sDestination = pathinfo($_POST["inpCurrFolder"]);

	//DELETE ALL FILES IF FOLDER NOT EMPTY
    $dir = $_POST["inpCurrFolder"];
    $handle = opendir($dir);
    $dx=0;
	while($file = readdir($handle))
	if($file != "." && $file != "..")
	{
		$fx=$dir."/".$file;
		if(is_dir($fx))
		{
			$dx=1;
		}
	}

	rewinddir($handle);
	if($dx==0)
	{
		while($file = readdir($handle))
		if($file != "." && $file != "..")
		{
			$fx=$dir."/".$file;
			unlink($fx);
		}
    }
	closedir($handle);

	if($dx==0)
	{
		if(rmdir($_POST["inpCurrFolder"])==0)
			$sMsg = "";
		else
			$sMsg = "<script>document.write(getText('Folder deleted.'))</script>";
	}
	else if($dx==1)
	{
		$sMsg = "Inner sub-Folders must be deleted separately !";
	}
}//end if..
?>
<base target="_self">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<script>
	if(navigator.appName.indexOf('Microsoft')!=-1)
		var sLang=dialogArguments.sLang;
	else
		var sLang=window.opener.sLang;
	document.write("<scr"+"ipt src='language/"+sLang+"/folderdel_.js'></scr"+"ipt>");
</script>
<script>writeTitle()</script>
<script>
function refresh()
	{
	if(navigator.appName.indexOf('Microsoft')!=-1)
		dialogArguments.refreshAfterDelete(inpDest.value);
	else
		window.opener.refreshAfterDelete(document.getElementById("inpDest").value);
	}
</script>
</head>
<body onload="loadText()" style="overflow:hidden;margin:0px;background: #f4f4f4;">

<table width=100% height=100% align=center style="" cellpadding=0 cellspacing=0 ID="Table1">
<tr>
<td valign=top style="padding-top:5px;padding-left:15px;padding-right:15px;padding-bottom:12px;height=100%">

	<br>
	<input type="hidden" ID="inpDest" NAME="inpDest" value="<?php echo $sDestination['dirname']; ?>">
	<div><b><?php echo $sMsg; ?>&nbsp;</b></div>

</td>
</tr>
<tr>
<td class="dialogFooter" style="height:45px;padding-right:10px;" align=right valign=middle>
	<input type="button" name="btnCloseAndRefresh" id="btnCloseAndRefresh" value="close<?php if($dx==0) echo " & refresh"; ?>" onclick="<?php if($dx==0) echo 'refresh();'; ?>self.close();" class="inpBtn" onmouseover="this.className='inpBtnOver';" onmouseout="this.className='inpBtnOut'">
</td>
</tr>
</table>


</body>
</html>