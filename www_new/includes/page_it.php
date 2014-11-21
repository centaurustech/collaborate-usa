<?php
/**
 * Paginations
 * @author = Raheel Hasan
 * @version = 1.71
 * @modDate = Aug 2014
 **/

class page_it
{
    public $GET = array();
    public $query, $lim, $start, $tot, $first_page;


    /**
     * Constructor
     * $GET = $_GET
     * $query = SQL query (this will be used for calculating total records and not for generating results)
     * $lim = max per page
     * $first_page = first page number (i.e. either 0 or 1)
     **/
    function page_it($GET, $query, $lim, $first_page=0)
    {
        $this->GET = $GET;

        ## Setting Parameters Received
        if(!isset($this->GET['page']))
        $this->GET['page'] = 1;

        if(strstr($this->GET['page'], '-')){
        $t1 = explode('-', $this->GET['page']);
        $this->GET['page'] = $t1[0];
        $this->GET['tot'] = $t1[1];
        }

        if($first_page!=0)
        $this->GET['page']--;
        ##--

        $this->query = $query;
        $this->lim = $lim;
        $this->first_page = $first_page;
    }


    /**
     * returns LIMITS - to be used in sql query
     **/
    function query_limits()
    {
        ## Set Limits
        $this->start = $this->GET['page']*$this->lim;
        ##--

        ## Calculate Total
        if(!isset($this->GET['tot'])){
        $r2 = mysql_exec($this->query);
        $this->tot = count($r2);
        }else{
        $this->tot = $this->GET['tot'];
        }
        ##--

        return " LIMIT {$this->start}, {$this->lim}";
    }


    /**
     * Returns Page Links
     * $type = ajax/href
     * $action = URL for href (incase type='href'). Pass it using placeholders PAGE_NO, TOTAL_RECS, like: "x/y/z/{PAGE_NO}/{TOTAL_RECS}/e" OR like: "a.php?page={PAGE_NO}-{TOTAL_RECS}"
     * $action = function for onclick (incase type='ajax'). Pass it also using Place Holder, like: paging('{PAGE_NO}', '{TOTAL_RECS}');
     * $showing_what = text to be placed instead of Cards in "Showing 1-2 of 2 {Cards}"
     * apply special css via classes: tot_show_cls and tot_links_cls
     * */
    function page_links($type='ajax', $action='', $showing_what='Results')
    {
        $tot = $this->tot;
        $lim = $this->lim;
        $ret = '';

        ## Set Paging Parameters
        $total_pages = ceil($tot/$lim);
        if($total_pages<=0)
        $total_pages = 1;
        ##--


        if(($tot>0) && ($total_pages>1))
        {
            $ret .= "<div style='float:right;' class='tot_links_cls'>";

            $ret .= "Page&nbsp;&nbsp;&nbsp;";


            $max_i = 10; //ie how many on each side of current page
            $max_l = floor($max_i/2);
            $max_r = ceil($max_i/2);

            $page_x = $this->GET['page'];
            $cur_page = $page_x;

            $pr = $pr0 = $pr2 = array();



            ## current page + right to current page
            $a = $cur_page+1;
            for($i=$cur_page; $i<($cur_page+$max_r); $i++)
            {
            	$b=$a-1;

                if(($i) >= $total_pages)
                break;

                if($type=='ajax')
                {
                    $pr_x = "<span ";
                    if($b!=$page_x)
                    $pr_x .= "onclick=\"".str_replace(array('{PAGE_NO}', '{TOTAL_RECS}'), array(($b+$this->first_page), $this->tot), $action)."\" ";
                    $pr_x .= "style='cursor:pointer; color:#DB6A26;";
                }
                else
                {
                    $pr_x = "<a href=\"".str_replace(array('{PAGE_NO}', '{TOTAL_RECS}'), array(($b+$this->first_page), $this->tot), $action)."\" style='cursor:pointer; color:#DB6A26;";
                }


            	if($b==$page_x)
            	$pr_x.= "font-size:11pt; border:solid 1px #A9CBE7; padding:13px 3px;";
                else
                $pr_x.= "padding:0 1px;";


                if($type=='ajax')
                $pr_x.= "'>&nbsp;{$a}&nbsp;</span>";
                else
                $pr_x.= "'>&nbsp;{$a}&nbsp;</a>";


                if(($a==($cur_page+$max_r)) && (($cur_page+$max_r)<$total_pages)){
                $pr_x.= " ...";
                }
                $pr2[] = $pr_x;

                $a++;
            }
            ##--



            ## Left to current page
            $j = 1;
            $a = $cur_page;

            if($cur_page!=0)
            for($i=($cur_page-1); $i>=0; $i--)
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

                if($type=='ajax'){
                $pr_y.= "<span onclick=\"".str_replace(array('{PAGE_NO}', '{TOTAL_RECS}'), array(($b+$this->first_page), $this->tot), $action)."\" style='cursor:pointer; color:#2568AD; padding:0 1px;";
            	$pr_y.= "'>&nbsp;{$a}&nbsp;</span>";
                }
                else{
                $pr_y.= "<a href=\"".str_replace(array('{PAGE_NO}', '{TOTAL_RECS}'), array(($b+$this->first_page), $this->tot), $action)."\" style='cursor:pointer; color:#2568AD; padding:0 1px;";
            	$pr_y.= "'>&nbsp;{$a}&nbsp;</a>";
                }

                $pr0[] = $pr_y;
                $a--;
            }
            $pr0 = array_reverse($pr0);
            ##--


            $pr = array_merge($pr0, $pr2);
            $ret .= implode('', $pr);


            $s1 = ($this->tot<=0)? '0':($this->start+1);
            $s2 = ($this->lim+$this->start);
            if($s2>($this->tot)) $s2 = $this->tot;
            $ret .= "</div><div style='float:left; color:#4B4B49; padding-top:2px;' class='tot_show_cls'>Showing &nbsp;{$s1}&nbsp;-&nbsp;{$s2} &nbsp;of&nbsp; {$this->tot}&nbsp;{$showing_what}</div>";

        }//end if....

        return $ret;
    }
}
?>