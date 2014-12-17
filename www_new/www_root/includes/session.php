<?php
if(!isset($_SESSION["CUSA_Main_usr_id"]) || empty($_SESSION["CUSA_Main_usr_id"]))
{
    @header("Location: {$consts['DOC_ROOT']}login");
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}login';</script>";
	exit;
}
else
{
    $LAST_ACTIVITY = @$_SESSION['LAST_CUSA_Main_ACTIVITY'];

    #/ logout if last activity is over 30 minutes old
    if (time() - $LAST_ACTIVITY > 1500) //
    {
        //this concept needs more work
        //its not working currently as session is destroyed at logout
        if(isset($seo_tag) && !empty($seo_tag)){
        $_SESSION['last_visited_seo'] = $seo_tag;
        }


        @header("Location: {$consts['DOC_ROOT']}ecosystem/logout");
        echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}ecosystem/logout';</script>";
    	exit;
    }

    $_SESSION['LAST_CUSA_Main_ACTIVITY'] = time();
    //var_dump("<pre>", $_SESSION); die();
}
?>