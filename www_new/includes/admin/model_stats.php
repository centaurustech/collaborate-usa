<?php
function interpolate($pBegin, $pEnd, $pStep, $pMax)
{
    if ($pBegin < $pEnd) {
      return (($pEnd - $pBegin) * ($pStep / $pMax)) + $pBegin;
    } else {
      return (($pBegin - $pEnd) * (1 - ($pStep / $pMax))) + $pEnd;
    }
}

function color_pie_chart($ret)
{
    $theColorBegin = hexdec('BC2800');
    $theColorEnd = hexdec('D8D0D0');

    $theR0 = ($theColorBegin & 0xff0000) >> 16;
    $theG0 = ($theColorBegin & 0x00ff00) >> 8;
    $theB0 = ($theColorBegin & 0x0000ff) >> 0;

    $theR1 = ($theColorEnd & 0xff0000) >> 16;
    $theG1 = ($theColorEnd & 0x00ff00) >> 8;
    $theB1 = ($theColorEnd & 0x0000ff) >> 0;

    $ix=0;
    $theNumSteps = count($ret);
    foreach($ret  as $ret_k=>$ret_v)
    {
        $theR = interpolate($theR0, $theR1, $ix, $theNumSteps);
        $theG = interpolate($theG0, $theG1, $ix, $theNumSteps);
        $theB = interpolate($theB0, $theB1, $ix, $theNumSteps);

        $theVal = ((($theR << 8) | $theG) << 8) | $theB;
        $theVal = sprintf("#%06X", $theVal);

        $ret[$ret_k]['color'] = $theVal;
        $ix++;
    }

    return $ret;
}

/////////////////////////////////////////////////////////////////////////

function user_country()
{
    $sql_1 = "SELECT country_code, COUNT(country_code) cx
    FROM user_info
    GROUP BY country_code
    ORDER BY cx DESC
    ";

    $ret = @mysql_exec($sql_1);
    return $ret;

}//end func...


function package_usage_amt()
{
    $sql_1 = "SELECT ldb_package_id, lp.title,
    ROUND(SUM(amount_t), 2) AS total_t, SUM(quantity) as qty

    FROM order_cart oc
    LEFT JOIN ldb_packages lp ON lp.id=oc.ldb_package_id

    GROUP BY ldb_package_id
    ORDER BY total_t DESC
    LIMIT 10
    ";

    $ret = '';
    $result = @mysql_exec($sql_1);
    if(count($result)>0)
    {
        $ret = @format_str($result);

        #/ Set Gradient
        $ret = color_pie_chart($ret);
    }
    return $ret;

}//end func...


function package_usage_qty()
{
    $sql_1 = "SELECT ldb_package_id, lp.title,
    ROUND(SUM(amount_t), 2) AS total_t, SUM(quantity) as qty

    FROM order_cart oc
    LEFT JOIN ldb_packages lp ON lp.id=oc.ldb_package_id

    GROUP BY ldb_package_id
    ORDER BY qty DESC
    LIMIT 10
    ";

    $ret = '';
    $result = @mysql_exec($sql_1);
    if(count($result)>0)
    {
        $ret = @format_str($result);

        #/ Set Gradient
        $ret = color_pie_chart($ret);
    }
    return $ret;

}//end func...


function payment_activity()
{
    $sql_1 = "SELECT dt, amount, SUM(cx) paid FROM (
    	SELECT DATE(paid_on) dt, amount, COUNT(*) cx
    	FROM user_payments up
    	WHERE paid_on BETWEEN DATE_SUB(NOW(), INTERVAL 6 MONTH) AND NOW()
    	GROUP BY dt
    ) AS t1
    GROUP BY dt
    ORDER BY dt DESC
    ";

    $ret = @mysql_exec($sql_1);
    return $ret;

}//end func...


function cust_order_status()
{
    $sql_1 = "SELECT

    DISTINCT order_status,
    COUNT(*) AS t_os

    FROM cust_orders

    GROUP BY order_status
    ORDER BY t_os DESC
    LIMIT 10
    ";

    $ret = '';
    $result = @mysql_exec($sql_1);
    if(count($result)>0)
    {
        $ret = @format_str($result);

        #/ Set Gradient
        $ret = color_pie_chart($ret);
    }
    return $ret;
}//end func...
?>