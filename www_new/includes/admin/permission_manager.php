<?php
function get_admin_sections()
{
    $sq_perm = "SELECT * FROM admin_sections ORDER BY id";
    $admin_sections = mysql_exec($sq_perm);
    return $admin_sections;
}

function get_admin_user_perms($user_id, $select='*')
{
    $sq_perm = "SELECT {$select} FROM admin_permissions WHERE admin_users_id='{$user_id}'";
    $admin_perm = mysql_exec($sq_perm);
    return $admin_perm;
}


function insert_permissions($au_id, $permissions)
{
    if(!is_array($permissions) || count($permissions)==0)
    return false;

    $sql_admin_permissions = "INSERT INTO admin_permissions (admin_users_id, admin_section_id) VALUES ";

    $sql_admin_perm_arr = array();
    foreach($permissions as $perms){
    $sql_admin_perm_arr[]="
    ('{$au_id}', '".(int)$perms."')";
    }

    $sql_admin_permissions.= @implode(',', $sql_admin_perm_arr);

    $sql_admin_permissions.= "
    ON DUPLICATE KEY UPDATE admin_section_id=admin_section_id";

    //var_dump("<pre>", $sql_admin_permissions); die();
    mysql_exec($sql_admin_permissions, 'save');

}//end func...


function update_permissions($au_id, $permissions)
{
    if(!is_array($permissions) || count($permissions)==0)
    return false;

    $sql_1 = "DELETE FROM admin_permissions WHERE admin_users_id='{$au_id}'";
    $res = mysql_exec($sql_1, 'save');
    //var_dump($res, mysql_error()); die();

    insert_permissions($au_id, $permissions);

}//end func...
?>