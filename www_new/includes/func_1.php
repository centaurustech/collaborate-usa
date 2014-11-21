<?php
#/ new Similar Function for mysql_error - to prevent mysql_error from being displayed on LIVE.
#/ But still visible on local
function mysql_error_1()
{
    if(SERVER_TYPE!='LOCAL')
    return false;
    else
    return mysql_error();
}

function cut_str($post, $size=80)
{
	$output=$post;
	if(strlen($output)>$size)
		$output=substr($output, 0, ($size))."...";
	return $output;
}//end func.....


function wrap_space($str, $size, $str_brk=' ')
{
    $out = trim($str);
    $out = wordwrap($out, $size, $str_brk, true);
    $out = trim($out);

    return $out;

}//end func.....


## Get Current Page / Section
function cur_page()
{
    $cur_page='';
    if(isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF']!='')
    {
        $temp_var1 = explode('/', $_SERVER['PHP_SELF']);
        $cur_page = $temp_var1[count($temp_var1)-1];
    }
    else if(isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME']!='')
    {
        $temp_var1 = explode('/', $_SERVER['SCRIPT_NAME']);
        $cur_page = $temp_var1[count($temp_var1)-1];
    }
    else if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!='')
    {
        $temp_var1 = explode('/', $_SERVER['REQUEST_URI']);
        $cur_page = $temp_var1[count($temp_var1)-1];
        $temp_var2 = explode('?', $cur_page);
        $cur_page = $temp_var2[0];
    }
    else if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME']!='')
    {
        $temp_var1 = explode('/', $_SERVER['SCRIPT_FILENAME']);
        $cur_page = $temp_var1[count($temp_var1)-1];
    }
    return $cur_page;
}//end func.....

////////////////////////////////////////////////////////////////////

/**
 * function cb89
 * Set top key of a multi-dimensional array from a kay=>value in its 2nd level
 * USAGE Example: $contact_res_t = cb89($contact_res, 'id');
 */
function cb89($a, $set_key){$ret=array(); foreach($a as $v){$ret[$v[$set_key]]=$v;} return $ret;}


/**
 * Function print_opts
 * To Print deep Parent-Child array into Select's Options
 * @param $ar = Array to print (must already be processed from 'array_recursive_tree' function)
 * @param $sep = separator for childern (ignore as its auto generated)
*/
function print_opts($ar, $sep='', $ignore_key=0)
{
    if(!is_array($ar) || count($ar)<0){return false;}
    foreach($ar as $v)
    {
        //var_dump($v, '<br /><br />');

        if(isset($v['title']))
        {
            if($v['id']!=$ignore_key)
            echo "<option value=\"{$v['id']}\">{$sep}{$v['title']}</option>";
            //echo "<br /><br />"; //debug
        }

        if(is_array($v))
        foreach($v as $k2=>$v2)
        {
            if(is_numeric($k2))
            {
                //var_dump($v2, '<br /><br />');
                print_opts(array(0=>$v2), $sep.'&raquo;&nbsp;', $ignore_key);
            }
        }
    }

}//end func...


/**
 * Function array_recursive_tree
 * To convert recursive relation table into full deep parent-child (i.e. n-numbers)
 * @param $ret = array
 *
 * [IMP]
 * query must be ORDERed BY parent_id
 * Must pass data after cb791

 * [usage]
   $cats = @cb791($cats, 'parent_id', 'id');
   $cats = @array_recursive_tree($cats);
*/
function array_recursive_tree($ret)
{
    if(!is_array($ret) || count($ret)<0) return '';

    $ret = array_reverse($ret, true);
    //return $ret; //debug

    $rett = array();

    $loop = 0;
    foreach($ret as $k=>$v)
    {
        $loop++;

        if($k==0)
        {
            if(!array_key_exists($k, $rett))$rett[$k] = array();

            $merged = array_replace_recursive($rett[$k], $v);
            $rett[$k] = $merged;

            break;
        }
        else
        {
            for($i=0; $i<$k; $i++)
            {
                if(@isset($ret[$i][$k]))
                {
                    if(!array_key_exists($i, $rett))$rett[$i] = array();
                    if(!array_key_exists($k, $rett[$i]))$rett[$i][$k] = array();

                    $rett[$i][$k] = $v;

                    if(@isset($rett[$k]))
                    {
                        //echo "|{$k}|";
                        //array_push($rett[$i][$k], $rett[$k]);

                        $merged = array_replace_recursive($rett[$i][$k], $rett[$k]);
                        //var_dump("<pre>", $rett[$i][$k], $rett[$k], array_replace_recursive($rett[$i][$k], $rett[$k]), '---');
                        $rett[$i][$k] = $merged;

                        unset($rett[$k]);
                    }

                    break;
                }
            }
        }

        //var_dump($k);
        //if($loop==4){break;}

    }//end foreach...

    return $rett;

}//end func...


/**
 * Function cb791
 * Purpose = re-arange array into Parent-Child with 1st dim and 2nd dim keys set
 * @param $a = array
 * @param $groupby_key = key for grouping of 1st dim
 * @param $key_id = key for grouping of 2nd dim
 * USAGE Example: $cats = @cb791($cats, 'parent_id', 'id');
 */
function cb791($a, $groupby_key, $key_id)
{
    $ret=array();

    if(!empty($a))
    foreach($a as $k=>$v)
    {
        if(!isset($ret[$v[$groupby_key]])) $ret[$v[$groupby_key]] = array();
        $ret[$v[$groupby_key]][$v[$key_id]] = $v;
        continue;
    }

    return $ret;

}//end func....



/**
 * Function cb79
 * Purpose = re-arange array into Parent-Child with 1st dimension's key set with groupby_key
 * @param $a = array
 * @param $groupby_key = key to find (for grouping)
 * USAGE Example: $frustrated_users = cb79($frustrated_users, 'event_cat');
*/
function cb79($a, $groupby_key, $set_key=true)
{
    $ret=array();

    if(!empty($a))
    foreach($a as $k=>$v)
    {
        if(!isset($ret[$v[$groupby_key]])) $ret[$v[$groupby_key]] = array();
        if($set_key){
        $ret[$v[$groupby_key]][$k] = $v;
        } else {
        $ret[$v[$groupby_key]][] = $v;
        }
        continue;
    }

    return $ret;

}//end func....



/**
 * Function cb99
 * Purpose = re-arange 2-dimensional array into Parent-Child and set top key from 1st dimension
 * @param $a = array
 * @param $find_key = key to find (for grouping)
 * @param $set_key = key to set
 * USAGE Example: $frustrated_users = cb99($frustrated_users, 'parent_id', 'comment_id');
 */
function cb99($a, $find_key, $set_key)
{
    $ret=array();

    if(!empty($a))
    foreach($a as $v)
    {
        #/if Parent is 0
        if($v[$find_key]==0){
        if(!isset($ret[$v[$set_key]])) $ret[$v[$set_key]] = array();
        $ret[$v[$set_key]]['parent'] = $v;
        continue;
        }

        #/if Parent is not 0 = form tree
        if(!isset($ret[$v[$find_key]])) $ret[$v[$find_key]] = array();
        $ret[$v[$find_key]][$v[$set_key]] = $v;
    }

    return $ret;

}//end func....


////////////////////////////////////////////////////////////////////

function leadingZeros($num, $numDigits){
return sprintf("%0".$numDigits."d", $num);
}


/**
 * Function to Redirect
*/
function return_back($url_part='index')
{
    global $consts;

    #/ Redirect
    @header("Location: {$consts['DOC_ROOT']}{$url_part}");
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}{$url_part}';</script>";
    exit;

}//end func......


/**
 * Function to Redirect
*/
function redirect_me($to, $full_url=false, $hard=false, $di=true)
{
    global $consts;

    $url = "{$consts['DOC_ROOT']}{$to}";
    if($full_url){
    $url = "{$to}";
    }
    else if($hard){
    $url = "{$consts['SITE_URL']}{$to}";
    }

    @header("Location: {$url}");
    echo "<script language=\"javascript\">location.href='{$url}';</script>";

    if($di){
    exit;
    }
}//end func......


/**
 * Function to Redirect
*/
function return_back_post($url_main='index', $POST='')
{
    global $consts;

    #/ process POST data
    $url_part = '';
    if(!empty($POST))
    {
        $form = '';
        foreach($POST as $k=>$v)
        {
            if($k!='vercode'){
            $v = urlencode($v); //will require [$_GET = array_map('urldecode', $_GET);] at the top of the field display page
            $form .= "{$k}={$v}&";
            }
        }
        $form = str_replace("'", '&#39;', $form);
        $form = str_replace("#", '', $form);
        $form = preg_replace("/[\n\r]{1,}/", 'Xnl2nlX', $form);

        $url_part = "/?{$form}";
    }


    #/ Return
    @header("Location: {$consts['DOC_ROOT']}{$url_main}{$url_part}");
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}{$url_main}{$url_part}';</script>";
    exit;

}//end func......
?>