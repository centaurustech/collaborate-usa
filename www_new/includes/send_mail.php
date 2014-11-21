<?php
function send_mail($to, $subject, $heading, $body_in, $frm_nm='collaborateUSA.com', $frm_email='support@collaborateUSA.com')
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    ##define Body
    $messg  = '';
    $messg .= '<div style="width:650px; padding:2px; color:#464646; font-family:Arial, Helvetica, sans-serif; font-size:13px;">';
    $messg .= '<div style="padding:20px 15px 12px 15px;">';

    $messg .= '<div>';
    $messg .= '<div style="padding-left:1px; margin-left:-1px;"><a href="'.$site_url.'" target="_blank"><img src="'.$site_url.'assets/images/lgo_y.png" border="none" /></a></div><br />';

    $messg .= '<div style="padding-top:5px;">';
    //$messg .= "<div style='padding:3px 5px 3px 1px; border:none; border-bottom:solid 1px #53A9E9;'><b style='font-size:15px; color:#53A9E9;'>".$heading."</b></div><br />";
    $messg .= "<div style='padding:10px; border:none; background:#53A9E9; border-radius:3px;'><b style='font-size:15px; color:#FFFFFF;'>".$heading."</b></div><br />"; //2nd design
    $messg .= "<div style='padding:10px 10px 3px 0; color:#464646;'>".$body_in."</div>";
    $messg .= "<br /><br /><b style='color:#2CA1F4; font-size:14px;'>Regards,<br />collaborateUSA.com</b>";

    $messg.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #eee; margin:10px 0 5px 0; width:90%;' />";
    $messg .= "<div style='font-size:11px; color:#aaa; font-style:italic;'>This is an auto-generated email. Please do not reply as it will not be received.</div>";

    $messg .= '</div>';

    $messg .= '</div><br />';

    $messg .= '</div>';
    $messg .= '</div>';

    //echo $messg; die();

    $messg = chunk_split(base64_encode($messg));



    ## Additional Params
    //$to = 'raheelhasan.fsd@gmail.com';


    ## Defining Header
    $hdr='';
    $hdr.='MIME-Version: 1.0'."\n";
    $hdr.='Content-type: text/html; charset=utf-8'."\n";
    $hdr.='Content-Transfer-Encoding: base64'."\n";
	$hdr.="From: {$frm_nm}<{$frm_email}>\n";
    $hdr.="Reply-To: {$frm_nm}<{$frm_email}>\n";
    $hdr.="Return-Path: <{$frm_email}>\n";
    $hdr.="Message-ID: <".time()."@{$frm_email}>\n";
    $hdr.='X-Mailer: DSP/1.0';


    #/ Hide PHP Script Identifiers (X-PHP-Script)
    $phpself = $_SERVER['PHP_SELF'];
    $phpremoteaddr = $_SERVER['REMOTE_ADDR'];
    $phpservername = $_SERVER['SERVER_NAME'];
    $_SERVER['PHP_SELF'] = "/";
    $_SERVER['REMOTE_ADDR'] = "0.0.0.0";
    $_SERVER['SERVER_NAME'] = "none";


    #/ Send Email
    $x=@mail($to, $subject, $messg, $hdr);
    if($x==0)
    {
    	$to = str_replace('@', '\@', $to);
    	$hdr = str_replace('@', '\@', $hdr);

    	$x = @mail($to, $subject, $messg, $hdr);
    }

    #/ restore obfuscated server variables
    $_SERVER['PHP_SELF'] = $phpself;
    $_SERVER['REMOTE_ADDR'] = $phpremoteaddr;
    $_SERVER['SERVER_NAME'] = $phpservername;


    return $x;

}//end func.....
?>