<?php
define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/webscr&cmd=');
define('DEVELOPER_PORTAL', 'https://developer.paypal.com');

define('PAYPAL_URL', 'https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay');
//define('PAYPAL_URL', 'https://www.paypal.com/webapps/adaptivepayment/flow/pay');

define('PAYPAL_URL_DIRECT', 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_ap-payment&paykey=');
//define('PAYPAL_URL_DIRECT', 'https://www.sandbox.paypal.com/webapps/adaptivepayment/flow/pay?paykey='); //via light menu

$PAYPAL_URL = PAYPAL_URL;
$PAYPAL_URL_DIRECT = PAYPAL_URL_DIRECT;