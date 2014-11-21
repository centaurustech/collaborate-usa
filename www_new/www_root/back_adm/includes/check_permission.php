<?php
if(@array_key_exists('cusa_adm_perm', $_SESSION) && @in_array($section_id, $_SESSION['cusa_adm_perm'])){
}
else
{
    $read_only = (int)@$_GET['ro'];

    if($read_only>0)
    {
        echo "<br /><b>ERROR 403 :: PERMISSION DENIED</b><br />";
        echo "You do not have the Permissions to access this section!<br /><br />";
    }
    else
    {
        @header("Location: {$consts['DOC_ROOT_ADMIN']}403P");
        echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT_ADMIN']}403P';</script>";
    }

    exit;
}
?>