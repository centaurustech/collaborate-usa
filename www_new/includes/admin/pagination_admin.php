<?php
echo sprintf("<div style='float:left;'>Showing %d - %d &nbsp;&nbsp;of&nbsp; %d</div>", $pageindex * $pagesize + 1, min($pageindex * $pagesize + $pagesize, $count), $count);
echo "<div style='float:right;'>";


if ($pageindex == 0){
echo sprintf("<b>&laquo;</b>\n");
}else{
echo sprintf("<a href=\"%s\">&laquo;</b>\n", rebuildurl(array("pageindex" => $pageindex - 1)));
}


/////////////////////////////
$total_pages = ceil($count/$pagesize);
if($total_pages<=0)
$total_pages = 1;

$max_i = 20; //ie how many on each side of current page
$max_l = floor($max_i/2);
$max_r = ceil($max_i/2);

$cur_page_no = $pageindex;
$pr = $pr0 = $pr2 = array();



## current page + right to current page
$a = $cur_page_no+1;
for($i=$cur_page_no; $i<($cur_page_no+$max_r); $i++)
{
	$b=$a-1;

    if(($i) >= $total_pages)
    break;

    if($b==$cur_page_no)
    {
        $pr_x = "
        <b>{$a}</b>";
    }
    else
    {
        $pr_x = "
        <a href=\"".rebuildurl(array("pageindex" => $b))."\" style='cursor:pointer;";
    	$pr_x.= "'>{$a}</a>";
    }

    if(($a==($cur_page_no+$max_r)) && (($cur_page_no+$max_r)<$total_pages)){
    $pr_x.= " ...";
    }
    $pr2[] = $pr_x;

    $a++;
}
##--


## Left to current page
$j = 1;
$a = $cur_page_no;

if($cur_page_no!=0)
for($i=($cur_page_no-1); $i>=0; $i--)
{
    $b=$a-1;
    $pr_y = '';

    if($j==$max_l)
    {
        if($i>=0)
        $pr0[] = "... ";

        break;
    }
    $j++;

    $pr_y.= "
    <a href=\"".rebuildurl(array("pageindex" => $b))."\" style='cursor:pointer;";
	$pr_y.= "'>{$a}</a>";
    $pr0[] = $pr_y;

    $a--;
}
$pr0 = array_reverse($pr0);
##--

$pr = array_merge($pr0, $pr2);
echo implode('', $pr);

/////////////////////////////

if ($pageindex == ceil($count / $pagesize) - 1){
echo sprintf("&nbsp;<b>&raquo;</b>\n");
}else{
echo sprintf("&nbsp;<a href=\"%s\">&raquo;</a>\n", rebuildurl(array("pageindex" => $pageindex + 1)));
}
?>