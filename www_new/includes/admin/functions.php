<?php
function fixgpcvar($a)
{
	if (is_array($a))
	{
		foreach ($a as $k => $x)
		{
			$a[ $k ] = fixgpcvar($a[ $k ]);
		}
	}
	else
	{
		$a = get_magic_quotes_gpc() ? stripslashes(trim($a)) : trim($a);
	}
	return $a;
}


function getgpcvar($v, $gpc)
{
	switch ($gpc)
	{
		case "G":
			$v = array_key_exists($v, $_GET) ? $_GET[ $v ] : "";
			break;
		case "P":
			$v = array_key_exists($v, $_POST) ? $_POST[ $v ] : "";
			break;
		case "C":
			$v = array_key_exists($v, $_COOKIE) ? $_COOKIE[ $v ] : "";
			break;
	}
	return fixgpcvar($v);
}


function usercheck()
{
    return array_key_exists("cusa_admin_usr_id", $_SESSION);
    //global $user_id;
    //return $user_id;
}


function rtext($s)
{
	return htmlspecialchars($s);
}


function getsetting($s)
{
    return SITE_TITLE;
}



function rebuildurl($r = array())
{
	$s = basename($_SERVER[ "PHP_SELF" ]);
	$q = array_key_exists("QUERY_STRING", $_SERVER) && $_SERVER[ "QUERY_STRING" ] ? "&{$_SERVER['QUERY_STRING']}" : "";
	foreach ($r as $n => $v)
	{
		$q = preg_replace("/&$n=[^&]*/", "", $q);
		$q .= $v === "" ? "" : ("&$n=" . urlencode($v));
	}
	if ($q)
	{
		$q[ 0 ] = "?";
	}
	return $s . $q;
}


function csvfromintarray($a, $b)
{
	if (is_array($a) == false || count($a) == 0)
	{
		return $b;
	}
	$a = implode(",", $a);
	if (preg_match("/^[0-9]+(,[0-9]+)*$/", $a) == false)
	{
		return $b;
	}
	return $a;
}
?>