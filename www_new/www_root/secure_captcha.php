<?php
session_start();

if(!function_exists('gd_info')) { exit(); }

//header("Cache-Control: no-cache");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 2007 05:00:00 GMT");

/////////////////////////////////////////////////////////////////
###/ Validate Requesting Party
$show_js = false;

#/ check domain
$allowed = array('localhost', 'www.collaborateusa.com', 'new.collaborateusa.com', 'collaborateusa.com', 'cusa-local');
if(in_array($_SERVER['SERVER_NAME'], $allowed))
$show_js = true;

#/ Check if called directly
if(!isset($_SERVER['HTTP_REFERER']))
$show_js = false;

#/ Die if invalid
if($show_js!=true)
die();
/////////////////////////////////////////////////////////////////
header("Content-type: image/png");

$img_handle = @ImageCreate(67, 30);
$back_color = @ImageColorAllocate($img_handle, 82, 83, 85);
//$transparent_bg = @ImageColorTransparent($img_handle, $back_color);

$count = 0;
$code = "";
while($count<6)
{
  $count++;

  $x_axis = -5 + ($count * 10);
  $y_axis = rand(3, 10);

  $color1 = rand(240, 210);
  $color2 = rand(080, 240);
  $color3 = rand(20, 235);
  $txt_color[$count] = @ImageColorAllocate($img_handle, $color1, $color2, $color3);

  $size = rand(2,12);
  $number = rand(0,9);
  $code .= "{$number}";

  @ImageString($img_handle, $size, $x_axis, $y_axis, "$number", $txt_color[$count]);
}//end while...


$pixel_color = @ImageColorAllocate($img_handle, 100, 100, 100);
$_SESSION['cap_code'] = $code;
@ImagePng($img_handle);
?>