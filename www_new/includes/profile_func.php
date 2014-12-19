<?php
function get_member_info($member_id, $user_id)
{
    if($member_id<=0){return false;}

    #/ All Fields
    $fetch_fields = array('package_id', 'email_add', 'screen_name', 'profile_pic', 'first_name', 'middle_name', 'last_name',
	'company_name', 'identify_by', 'joined_on',
    'country_code', 'state', 'city', 'address_ln_1', 'address_ln_2', 'zip', 'phone_number'
    );

    #/ Get user_permission
    $user_permissions = array();
    $sql_1 = "SELECT fields_perm FROM user_permissions WHERE user_id='{$member_id}'";
    $up_ar = @format_str(@mysql_exec($sql_1, 'single'));
    if(is_array($up_ar) && array_key_exists('fields_perm', $up_ar)){
    $user_permissions = @explode(',', @$up_ar['fields_perm']);
    }

    #/ Filter fields based on permissions
    if($member_id!=$user_id)//dont filter fields for myself
    {
        if(!empty($user_permissions)){
        $fetch_fields = array_diff($fetch_fields, $user_permissions);
        }
    }
    $fetch_fields_str = implode(',', $fetch_fields);
    //var_dump("<pre>", $user_permissions, $fetch_fields, $fetch_fields_str); die();

    if(empty($fetch_fields_str)){return false;}



    #/ Get Member's Info
    $fetch_fields_str = str_replace('country_code', 'ui.country_code', $fetch_fields_str);

    $sql_2 = "SELECT u.id as user_id,
    {$fetch_fields_str},
    (CASE
    WHEN identify_by='screen_name' THEN screen_name
    WHEN identify_by='full_name' THEN CONCAT(first_name, ' ', middle_name, ' ', last_name)
    WHEN identify_by='company_name' THEN company_name
    ELSE 'Member'
    END) AS user_ident,
    c.country_name,
    st.state_name

    FROM users u
    LEFT JOIN user_info ui ON ui.user_id=u.id
    LEFT JOIN countries c USING(country_code)
    LEFT JOIN states st ON st.state_code=ui.state

    WHERE u.id='{$member_id}'
    AND u.is_blocked='0'
    ";

    $members_ar = @format_str(@mysql_exec($sql_2, 'single'));
    //var_dump("<pre>", $members_ar); die();

    return array($members_ar, $user_permissions);

}//end func...
?>
