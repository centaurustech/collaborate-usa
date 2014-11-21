<?php
// for encrypt
// using like: $pass = encrypt($pass, "abc123");
function encrypt($string, $key) {
  $result = '';
  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)+ord($keychar));
    $result.=$char;
  }

  return base64_encode($result);
}

// for decrypt
// using like: $pass = decrypt($password,"abc123");
function decrypt($string, $key='%key&') {
	$result = '';
	$string = base64_decode($string);
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$ordChar = ord($char);
		$ordKeychar = ord($keychar);
		$sum = $ordChar - $ordKeychar;
		$char = chr($sum);
		$result.=$char;
	}
	return $result;
}

// for old users from 1.0 site
function osc_validate_p($plain, $encrypted)
{
    if(!empty($plain) && !empty($encrypted))
    {
          $stack = explode(':', $encrypted);
          if (sizeof($stack) != 2) return false;

          if (md5($stack[1].$plain) == $stack[0]) {
          return true;
          }
    }
}//end func....

///////////////////////////////////////////////////////////////////////
function md5_encrypt($plain, $key='«¡¼½»àÜêYêP_óÅ(0µª¿¬', $key2='ª!¦æk+}¦ÑÜúééEíÑ+ä¿', $key3='ô¿Ñ¬¦ºÖ}Ü«ÑLW}+»})')
{
    $plain = encrypt($plain, $key);

    $enc=strrev($plain);
	$enc3='';
	$enc2=array();
	for($i=0;$i<strlen($enc);$i++)
	{
		if($i%2)
		$enc2[$i]=pack('H*', md5($key2))^($enc{$i}^$key^$key2);
		else
		$enc2[$i]=~($enc{$i}^$key^$key3);
	}

	foreach($enc2 as $en)
	$enc3.=$en;
	$enc3.= ";99";

	return base64_encode(serialize($enc3));
}//end func....


function md5_decrypt($enc, $key='«¡¼½»àÜêYêP_óÅ(0µª¿¬', $key2='ª!¦æk+}¦ÑÜúééEíÑ+ä¿', $key3='ô¿Ñ¬¦ºÖ}Ü«ÑLW}+»})')
{
	$plain = @unserialize(base64_decode($enc));
	$plain3='';
    $plain2 = array();
	for($i=0; $i<strlen($plain); $i++)
	{
		if($i%2)
		$plain2[$i]=pack('H*', md5($key2))^($plain{$i}^$key^$key2);
		else
		$plain2[$i]=~($plain{$i}^$key^$key3);
	}

    foreach($plain2 as $en)
	$plain3.=$en;

	$plain3=(strrev($plain3));
	$plain3=substr($plain3,3,strlen($plain3));

    $plain = decrypt($plain3, $key);
    return $plain;
}//end func....

//////////////////////////////////////////////////////////////////

function createRandomPassword($length=7)
{
    $chars = "wxyIgJNB02745abcUdeIKfhBtuCDEGmHnopqr6389svFijkAz";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i <= $length)
	{
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}//end func....
?>