<?php
function generate_ppoints($user_id, $action_keys, $action_amount=0)
{
    if(($user_id<=0) || empty($action_keys)){return false;}

    #/ get PP-Config data
    $sql_1 = "SELECT * FROM patronage_points_config
    WHERE action_key='{$action_keys}' AND is_active='1'";
    $ppc_info = format_str(@mysql_exec($sql_1, 'single'));
    if(!is_array($ppc_info) || !array_key_exists('points', $ppc_info)){return false;}


    #/ Get User and their PP Info (within last 24 hours only), against the given Key
    $sql_2 = "SELECT u.id AS u_id, upp.*, (NOW() - INTERVAL 1 DAY) intvervl
    FROM users u
    LEFT JOIN user_petronage_points upp ON (upp.user_id=u.id) AND (pp_config_id='{$ppc_info['id']}') AND (received_on > (NOW() - INTERVAL 1 DAY))
    WHERE u.id='{$user_id}'";
    $upp_info = format_str(@mysql_exec($sql_2));
    if(!is_array($upp_info) || count($upp_info)<=0){return false;}


    #/ Check Limits
    $limits_per_day = (int)@$ppc_info['limits_per_day'];
    $total_given_today = 0;
    foreach($upp_info as $uiv){
    if(isset($uiv['points_received']) && !empty($uiv['points_received'])){$total_given_today++;}
    }
    //var_dump("<pre>", $limits_per_day, $total_given_today, $ppc_info['id'], $user_id); die();
    if(($limits_per_day>0) && ($total_given_today>=$limits_per_day))
    {
        return false;
    }


    #/ Calculate Points to be given
    $points = (int)$ppc_info['points'];
    $ppc_info['percentage_points'] = (int)$ppc_info['percentage_points'];
    if(($ppc_info['points']=='0') && ($ppc_info['percentage_points']>0) && ($action_amount>0))
    {
        $points = round($action_amount*($ppc_info['percentage_points']/100));
    }


    #/ Allocate Points when all is checked above
    $sql_3 = "INSERT INTO user_petronage_points
	(user_id, pp_config_id, points_received, received_on)
    VALUES('{$user_id}', '{$ppc_info['id']}', '{$points}', NOW())";
    @mysql_exec($sql_3, 'save');
    $upp_id = @mysql_insert_id();

    if($points>0){
    $sql_4 = "UPDATE user_info SET total_patronage_points = (total_patronage_points+'{$points}')
    WHERE user_id='{$user_id}'";
    @mysql_exec($sql_4, 'save');
    }



    #/ Generate PP Notification
    include_once('../includes/notif_func.php');
    $notif_data = array(
    'template_id' => "10",
    'user_id' => "{$user_id}", //receiver
    'from_user_by' => "0",
    'objects' => "{$points}:{$ppc_info['category']}",
    'object_id' => "{$upp_id}",
    'object_location' => 'user_petronage_points',
    'visit_url' => '', //to visit after click from top menu and from emails
    );
    generate_notification($notif_data);

    return $points;

}//end func....



/**
 * function to get all Patronage Points details for a User
 * [Params]
 * $user_id = user
 * $user_ppoints_id = to get a specific point data (leave empty to pull all data instead)
 * $fetch_total_only = set as `true` to get total count only, instead of full data set from user_petronage_points table
*/
function get_ppoints($user_id, $user_ppoints_id='', $fetch_total_only=false)
{
    if($user_id<=0){return false;}

    if($fetch_total_only!=false)
    {
        $sql_1 = "SELECT total_patronage_points FROM user_info WHERE user_id='{$user_id}'";
        $upp_res = @mysql_exec($sql_1, 'single');
        if(!is_array($upp_res) || count($upp_res)<0){return false;}

        return (int)@$upp_res['total_patronage_points'];
    }
    else
    {
        $sql_1 = "SELECT * FROM user_petronage_points WHERE user_id='{$user_id}' ";
        if($user_ppoints_id>0){$sql_1.= " AND id='{$user_ppoints_id}'";}
        $sql_1.= " ORDER BY received_on DESC";

        $upp_res = @format_str(@mysql_exec($sql_1));
        //var_dump("<pre>", $upp_res);
        if(!is_array($upp_res) || count($upp_res)<0){return false;}

        return $upp_res;
    }

}//end func....
?>