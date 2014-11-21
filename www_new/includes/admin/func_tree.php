<?php
/**
 * find all PARENT ids of Recursive Relationship - with just 1 sql query call
 *
 * @param $f1 = node id
 * @param $table_name = name of the table
 * @param $dir = direction of breadcrumbs
 * @param $parent_field_name = field name of parent id
*/
function get_uppers($f1, $table_name, $dir='left', $parent_field_name='parent_id', $ori='1')
{
    $frm  = array();
    static $res_ary = array();
    $res = false;


    $breadcr = '&laquo;&laquo;';
    if($dir=='right')
    $breadcr = '&raquo;&raquo;';


    ## get dataset
    if($ori!='0')
    {
        $sql = "select id, {$parent_field_name}, title from {$table_name}"; //echo $sql;
        $res = mysql_exec($sql);

        if($res!==false){
        foreach($res as $k=>$v)
        {
            $res_ary["{$table_name}"][$v['id']]= array();
            $res_ary["{$table_name}"][$v['id']][]= $v;
        }
        }
    }
    $res = @$res_ary["{$table_name}"][$f1];
    //var_dump($res_ary); //die();
    ##--


    ## process dataset recursively
    if($res!=false){
    foreach($res as $k=>$v)
    {
        $frm []= format_str($v['title']);

        $t1 = get_uppers($v[$parent_field_name], $table_name, $dir, $parent_field_name, '0');
        if((!empty($t1))){
        $frm = array_merge($frm, $t1);
        }

    }//end foreach....

    if($ori!='0')
    {
        //var_dump($frm);
        $frm = array_reverse($frm);
        $frm[count($frm)-1] = '<u>'.$frm[count($frm)-1].'</u>';
        $frm = implode(" {$breadcr} ", $frm);
    }
    }
    ##--

    return $frm;

}//end func....



/**
 * Get all Recursive Relationship into select field options
 *
 * @param $f1 = root/parent id
 * @param $table_name = name of the table
 * @param $dir = direction of breadcrumbs
*/
function get_options($f1, $table_name, $dir='left')
{
    $frm  = '';

    $breadcr = '&laquo;&laquo;';
    if($dir=='right')
    $breadcr = '&raquo;&raquo;';


    $sql = "select id, parent_id, title from {$table_name} where parent_id='{$f1}' order by sort_order";
    //echo $sql;
    $res = mysql_exec($sql);


    if($res!==false){
    foreach($res as $k=>$v)
    {
        if($v['parent_id']!=0)
        $frm .= '<option value="'.$v['id'].'">'.get_uppers($v['id'], $table_name, 'right').'</option>';

        $t1 = get_options($v['id'], $table_name, $dir);
        if((!empty($t1)) && ($t1!='0'))
        $frm .= $t1;

    }//end foreach....
    }

    return $frm;
}//end func....
?>