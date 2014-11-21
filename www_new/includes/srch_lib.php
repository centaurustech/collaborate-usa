<?php
/**
## Search & Hightlight

## Author: Raheel Hasan
## version: 2.5 [Mar 2012]


## USAGE
#
# [TOP - before query - (for search)]
# include_once("../socialnetwork/include/srch_lib.php");
# $src = new srch_h();
* if(!empty($sr_division_title)) $get_where .= $src->where_it($sr_division_title, 'divisions.title', 'division_title');
* if(!empty($sr_requester_name)) $get_where .= $src->where_it($sr_requester_name, 'event_planner.full_name', 'requester_name');
# $where .= $get_where;
#
# [BOTTOM - at display - (for highlight)]
* $division_title = ($recrd["division_title"]);
* if(($sr_division_title !== false) && ($sr_division_title!='')){
* $division_title = $src->get_highlighted($division_title, 'division_title');
* if($division_title=='--continue--continue--continue--continue--') {goto conti;}
* }
#

## IMP NOTE
* Must put non-highlighting at the top before highlighting
* LIKE:
* if($sr_is_enabled!=='') $get_where .= $src->where_it($sr_is_enabled, 'is_enabled', 'is_enabled', 'equals'); //this is to generate query only and doesnot need highlighting
* if(!empty($sr_c_name)) $get_where .= $src->where_it($sr_c_name, 'c_name', 'c_name');
*
**/

class srch_h
{
    public $srx = array();


    /**
    ##
    * $srch_text = text to search
    * $table_field [array or string] = list of tables to search from
    * $hightight_key = set a key for highlight functionality. This key will be used when we call the highlight function
    * $type = SQL query type search. for example; 'equals', 'greater-than-equals', 'less-than-equals', 'like' etc
    ##
    **/
    function where_it($srch_text, $table_field, $hightight_key='', $type='contains')
    {
       $do = $do_par = $where = '';
       $srx = array();

       $srch = format_str($srch_text);
       $srch = stripslashes($srch);

       $srch_it = addslashes(mysql_real_escape_string($srch));


       ### Generating Tokens within quotes
       $qx = substr_count($srch_it, "&quot;");

       if($qx>1)
    	{
    		$off1=$off2=$len=0;
    		$rem=$qx;
    		$srz=$sry=array();

            for($i=1; $i<=$qx; $i+=2)
            {
                $pos1=strpos($srch_it,"&quot;",$off2);//1st quote start
    			$off1=$pos1+6;//1st quote end
    			$pos2=strpos($srch_it,"&quot;",$off1);//2nd quote start
    			$off2=$pos2+6;//2nd quote end

                if($i==1)
    			$srz[]=substr($srch_it,0,$pos1);
    			$sry[]=substr($srch_it,$off1,$pos2-$off1);

                $pos3=strpos($srch_it,"&quot;",$off2);//3rd quote start

                $rem-=2;
    			if($rem<2)
    			{
    				$srz[]=str_replace("&quot;"," ",substr($srch_it,$off2));
    				break;
    			}
                else
    			{
    				$srz[]=substr($srch_it,$off2,$pos3-$off2);
    			}
            }//end for...

            $srz = explode(" ",implode(" ",$srz));
            $srx = array_values(array_unique(array_diff(array_diff(array_merge($sry,$srz),array("")),array(" ")) ));
        }//end if...
        else
    	{
    		$srx = array_values(array_unique(array_diff(array_diff(explode(" ",str_replace("&quot;"," ",$srch_it)),array("")),array(" "))));
    	}
        ##---

        ////
        if(!empty($hightight_key)){
        $sr_tempxt = $this->srx;
        if(!is_array($sr_tempxt)) $sr_tempxt = array();
        $sr_tempxt[] = array('key'=>$hightight_key, 'val'=>$srx);
        $this->srx = $sr_tempxt;
        }
        else
        $this->srx = $srx;
        ////


        if(is_array($table_field)==false)
        $table_fiel = array($table_field);
        else
        $table_fiel = $table_field;


        ### Building Query
        $ie=0;
        foreach($table_fiel as $v_table_field)
        {
            $do = '';
            if(count($srx)>1)
        	{
        		$i=1;
                foreach($srx as $v)
        		{
        			if($type=='equals')
                    $do.="{$v_table_field}='{$v}'";
                    else if($type=='greater-than-equals')
                    $do.="{$v_table_field}>='{$v}'";
                    else if($type=='less-than-equals')
                    $do.="{$v_table_field}<='{$v}'";
                    else
                    $do.="{$v_table_field} like '%{$v}%'";

        			if($i<count($srx)){
        			$do.=" OR ";
        			}

        			$i++;
        		}
            }//end if $srx...
            else
        	{
        		$t0 = @$srx[0];
                if($type=='equals')
                $do .= "{$v_table_field} = '{$t0}'";
                else if($type=='greater-than-equals')
                $do .= "{$v_table_field} >= '{$t0}'";
                else if($type=='less-than-equals')
                $do .= "{$v_table_field} <= '{$t0}'";
                else
                $do .= "{$v_table_field} like '%{$t0}%'";
        	}

           $ie++;


           $do_par .= " ({$do}) ";
           if($ie<count($table_fiel))
           $do_par .= "OR";

        }//end foreach...
        ##--


       $where = " AND ({$do_par}) ";

       return $where;
    }//end func.....




    function highlight($x, $var)
    {
       $var = explode(" ", $var);

       for($j=0; $j<count($var); $j++)
       {
            $xtemp = "";
            $i=0;
            $ic = 0;

            #/*
            while($i<strlen($x))
    		{
                $var[$j]=str_replace("<->"," ",$var[$j]);
                if( (($i + strlen($var[$j])) <= strlen($x)) && (strcasecmp($var[$j], substr($x, $i, strlen($var[$j]))) == 0) )
    			{
                        $xtemp .= "<>" . substr($x, $i, strlen($var[$j])) . "< >";
                        $i += strlen($var[$j]);
                }
                else
    			{
                    $xtemp .= $x{$i};
                    $i++;
                }

                $ic++;
            }
            $x = $xtemp;
            //echo $ic.'<br />';
            #*/
        }

        $x = str_replace(array('<>', '< >'), array('<b>', '</b>'), $x);
        return $x;

    }///end function....




    function get_highlighted($field, $hightight_key='', $is_simple=0)
    {
        $srx = $this->srx;

        $name = $field;
        if($is_simple==0) {
        $name = trim(html_entity_decode(strip_tags(html_entity_decode($field)), ENT_COMPAT, 'UTF-8'));
        //$name = @iconv("ISO-8859-1", "UTF-8//IGNORE", $name);
        }

        ### Highlight Names
        if(count($srx)>0)
        {
           if(!empty($hightight_key))
           {
                if(isset($srx[0]['key']))
                {
                    foreach($srx as $v)
                    {
                        if($v['key']==$hightight_key){
                        $srxx = $v['val'];
                        break;
                        }
                    }
                }
           }
           else
           {
                $srxx = $srx;
           }

           $s2=implode("<#>", $srxx);
           $s2=str_replace(" ", "<->", $s2);
           $s2=str_replace("<#>" ," ", $s2);
           //var_dump($name, $s2, '<br />');

           $name_x = $this->highlight($name, $s2);


           ##due to html_entities
           if(strcmp($name_x, $name)==0){
           $name = '--continue--continue--continue--continue--';
           }else{
           $name = $name_x;
           }
           ##--
        }
        ##--

        return $name;

    }//end func.....
}
?>