<?php
function get_home_packages()
{
    mysql_exec("SET @num := 0;", 'save');
    mysql_exec("SET @tpe := '';", 'save');

    $sql_1 = "
    SELECT * FROM
    (

    SELECT *,
    @num := IF(@tpe=t_val, @num+1, 1) AS row_number,
    @tpe := t_val AS dummy_2

    FROM
    (
        SELECT mp.*, mp.id AS mp_id, is_basic AS t_val
        FROM membership_packages mp
        WHERE is_active='1'
        ORDER BY is_basic DESC, display_order
    ) AS TX
    GROUP BY id
    HAVING row_number<2

    ) AS TY

    ORDER BY is_basic DESC, display_order
    ";

    $home_packages = @format_str(@mysql_exec($sql_1));

    $services = array();
    if(is_array($home_packages) && count($home_packages)>0)
    {
        #/ ADD Benefits
        $home_packages_t1 = @array_keys(@cb89($home_packages, 'mp_id'));
        if(is_array($home_packages_t1) && count($home_packages_t1)>0)
        {
            $hp_keys = "'".implode("', '", $home_packages_t1)."'";

            $sql_2 = "SELECT package_id, title
            FROM package_benefits pb
            WHERE package_id in ({$hp_keys})
            ORDER BY id
            ";

            $services = @cb79(format_str(@mysql_exec($sql_2)), 'package_id');
            //var_dump("<pre>", $services, $hp_keys); die();
        }
        //$home_packages = @cb79($home_packages, 'buss_type_id', false);
    }
    //var_dump("<pre>", $home_packages); die();

    return array($home_packages, $services);

}//end func...


function get_home_voices()
{
    $home_voices = array();

    $sql_1 = "SELECT uv.*, u.profile_pic
    FROM user_voices uv
    LEFT JOIN users u ON u.id=uv.user_id

    WHERE uv.is_blocked='0'
    ORDER BY RAND()
    LIMIT 20
    ";

    $home_voices = @format_str(@mysql_exec($sql_1));

    return $home_voices;
}//end func....
?>