<?php
function get_all_packages($get_benefits=true)
{
    #/ get Packages
    $packs_sql = "SELECT mp.*, mp.id AS mp_id
    FROM membership_packages mp
    WHERE is_active='1'
    ORDER BY is_basic DESC, display_order
    ";

    $packages = @format_str(@mysql_exec($packs_sql));
    //var_dump("<pre>", $packages); die();

    if($get_benefits==false)
    {
        return $packages;
    }

    $services = array();
    if(is_array($packages) && count($packages)>0)
    {
        #/ ADD Benefits
        $packages_t1 = @array_keys(@cb89($packages, 'mp_id'));
        if(is_array($packages_t1) && count($packages_t1)>0)
        {
            $p_keys = "'".implode("', '", $packages_t1)."'";

            $sql_2 = "SELECT package_id, title
            FROM package_benefits pb
            WHERE package_id in ({$p_keys})
            ORDER BY id
            ";

            $services = @cb79(format_str(@mysql_exec($sql_2)), 'package_id');
            //var_dump("<pre>", $p_keys, $services); die();
        }
        //$packages = @cb79($packages, 'buss_type_id', false);
    }
    //var_dump("<pre>", $packages); die();

    return array($packages, $services);

}//end func..


function get_package_info($pk_id, $get_benefits=false)
{
    if(empty($pk_id)) return false;

    $sql_part = '';
    if($get_benefits!=false)
    $sql_part = "";

    $sql_1= "SELECT title, cost, is_basic, is_recursive, recursive_cost
    FROM membership_packages mp
    WHERE is_active='1'
    AND id='{$pk_id}'
    ";

    $package_info = @format_str(@mysql_exec($sql_1));

    return $package_info;
}
?>