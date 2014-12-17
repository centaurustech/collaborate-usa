<?php
/**
 * FUNCTION get_site_media
 * @param $cat_in[array] = sql-part for 'category in ()'
*/
function get_site_media($cats=array(), $grouped=true)
{
    $site_media = array();

    #/ Set Category
    if(empty($cats) || (@count($cats)<0)){return false;}
    $cat_in = "'".implode("', '", format_str($cats))."'";

    #/ Get Data
    $sql_1 = "SELECT * FROM site_media
    WHERE
    m_cat in ({$cat_in})
    ORDER BY m_cat, added_on";
    //var_dump("<pre>", $sql_1); die();

    $site_media = @format_str(@mysql_exec($sql_1));
    if(is_array($site_media) && ($grouped!=false))$site_media = @cb79($site_media, 'm_cat', false);
    #-

    return $site_media;

}//end func...


function get_site_misc_data($cats=array(), $grouped=true)
{
    $site_misc_data = array();

    #/ Set Category
    if(empty($cats) || (@count($cats)<0)){return false;}
    $cat_in = "'".implode("', '", format_str($cats))."'";


    $sql_1 = "SELECT * FROM site_misc_data
    WHERE
    m_cat in ({$cat_in})
    ORDER BY m_cat, id";
    //var_dump("<pre>", $sql_1); die();

    $site_misc_data = @mysql_exec($sql_1);


    #/ Sanitize & Format Str
    function cb_risky($smd_v)
    {
        if($smd_v['content_type']=='html')
        {
            $m_value = rem_risky_tags($smd_v['m_value']);
            unset($smd_v['m_value']);
            $smd_v = @format_str($smd_v);
            $smd_v['m_value'] = $m_value;
        }
        else
        {
            $smd_v = @format_str($smd_v);
        }
        return $smd_v;
    }
    $site_misc_data = array_map('cb_risky', $site_misc_data);
    //var_dump("<pre>", $site_misc_data); die();

    if(is_array($site_misc_data) && ($grouped!=false))$site_misc_data = @cb79($site_misc_data, 'm_cat', false);
    #-

    return $site_misc_data;

}//end func...


/**
 * Function get_dynamic_menu
*/
function get_dynamic_menu($footer=false)
{
    $menu = array();

    $sql_inc = '';
    if($footer!=false)
    $sql_inc = "AND show_in_footer='1'";

    $sql_1 = "(SELECT pc.id, 0 as parent_id, pc.title, '' as seo_tag, '0' AS popup_only
    FROM page_categories pc
    WHERE pc.is_active='1'
    )";

    $sql_1.= "
    UNION
    ";

    $sql_1.= "
    (SELECT CONCAT('pg_', sp.id) AS id, sp.cat_id AS parent_id, title, st.seo_tag, popup_only
    FROM site_pages sp
    LEFT JOIN seo_tags st ON st.id=sp.seo_tag_id
    WHERE sp.is_active='1' {$sql_inc}
    )";

    $sql_1.= "
    ORDER BY parent_id, id, title";

    $menu = @format_str(@mysql_exec($sql_1));
    //var_dump("<pre>", $sql_1, $menu); die();

    $menu = @cb791($menu, 'parent_id', 'id');
    $menu = @array_recursive_tree($menu);
    $menu = @$menu[0];
    //var_dump("<pre>", $menu); die();

    return $menu;

}//end func...


/** Get basic dynamic Page Info from the table site_pages
 * $pg_id = CSV of pages
*/
function get_page_info($pg_id)
{
    if(empty($pg_id)) return false;

    $sql_1= "SELECT CONCAT('pg_', sp.id) AS id, sp.cat_id AS parent_id, title, st.seo_tag, popup_only
    FROM site_pages sp
    LEFT JOIN seo_tags st ON st.id=sp.seo_tag_id
    WHERE sp.is_active='1'
    AND sp.id in ({$pg_id})
    ";

    $menu = @format_str(@mysql_exec($sql_1));
    $menu = @cb89($menu, 'id');

    return $menu;

}//end func...
?>