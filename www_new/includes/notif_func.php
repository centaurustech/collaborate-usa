<?php
/** Function generate_notification
  * Purpose: Create and Save Notifications in DB
  * [params]
  * $notif_data = array containing keys (template_id, user_id, from_user_id, objects, object_id, object_location, visit_url, notif_details)
  * all fields within the param are compulsory/required.
*/
function generate_notification($notif_data, $send_email=false)
{
    if(!is_array($notif_data) || count($notif_data)<=0){return false;}
    if(!array_key_exists('user_id', $notif_data)){return false;}

    #/ Check Receiver's Notification settings
    $user_notif_settings = get_notif_perm($notif_data['user_id'], $notif_data['template_id']);
    if($user_notif_settings=='stop'){return false;}


    $created_on = date('Y-m-d H:i:s');

    #/ Save in DB & Generate Notification
    $sql_notif = "INSERT INTO user_notifications
    (template_id, user_id, from_user_id, objects, object_id, object_location, visit_url, created_on)
    VALUES ('{$notif_data['template_id']}', '{$notif_data['user_id']}', '{$notif_data['from_user_id']}', '{$notif_data['objects']}', '{$notif_data['object_id']}', '{$notif_data['object_location']}', '{$notif_data['visit_url']}', '{$created_on}')";
    @mysql_exec($sql_notif, 'save');


    if($send_email!=false)
    {

        #/ Get Notification & its Participant data
        $notification_data = get_notification_msg($notif_data); //, $send_email

        if(empty($notification_data) || !is_array($notification_data) || count($notification_data)<2){return false;}
        if(!array_key_exists('users_info', $notification_data) || !array_key_exists('notification', $notification_data)){return false;}
        //var_dump("<pre>", $notification_data); die();


        ##/ Send Email if Allowed
        if(function_exists('send_mail')!=true){
        include_once('../includes/send_mail.php');
        }

        $from_usr = (empty($notif_data['from_user_id']))? '':@$notification_data['users_info'][$notif_data['from_user_id']];
        $to_usr = @$notification_data['users_info'][$notif_data['user_id']];
        if(!is_array($to_usr) || count($to_usr)<=0){return false;}
        //var_dump($from_usr, $to_usr); die();

        if(function_exists('notification_email')!=true){
        include_once('../includes/email_templates.php');
        }
        $subject = strip_tags($notification_data['notification']);
        $heading = "New Notification from collaborateUSA.com";
        $body_in = notification_email($from_usr, $to_usr, $notification_data['notification'], $created_on, @$notif_data['visit_url'], @$notif_data['notif_details']);
        send_mail($to_usr['email_add'], $subject, $heading, $body_in);
        #-

    }//end if email...

}//end func...


/**
 * get specific Notification from Templates
*/
function get_notif_template($template_id)
{
    $sql_ae = "SELECT * FROM notification_templates WHERE id='{$template_id}'";
    $notif_template = @format_str(@mysql_exec($sql_ae, 'single'));
    //var_dump("<pre>", $notif_template); die();

    return $notif_template;

}//end func..


/**
 * get Notification Permission for a specific user
 * [params]
 * $user_id
 * $template_id (def=0) = provide template_id to check permission against it.
*/
function get_notif_perm($user_id, $template_id=0)
{
    $sql_not_set = "SELECT noti_templates_disallow FROM user_notif_settings WHERE user_id='{$user_id}'";
    $user_notif_settings = @mysql_exec($sql_not_set, 'single');
    //var_dump($sql_not_set, $user_notif_settings); die('x');

    if($template_id==0)
    return $user_notif_settings;

    #/ Test/Check permission for a specific Template
    if(isset($user_notif_settings['noti_templates_disallow']) && !empty($user_notif_settings['noti_templates_disallow']))
    {
        $user_noti_disallow_ar = @explode(',', $user_notif_settings['noti_templates_disallow']);
        if(in_array($template_id, $user_noti_disallow_ar)){
        return 'stop';
        }
    }

    return $user_notif_settings;

}//end func..


function get_notification_msg($notif_data, $send_email=false, $get_user_info=true)
{
    $user_id = $notif_data['user_id'];
    $from_user_id = $notif_data['from_user_id'];
    $template_id = $notif_data['template_id'];
    $objects = $notif_data['objects'];

    #/ Get user's info
    $users_info_sql = "SELECT id as user_id, email_add, profile_pic, (CASE
    WHEN identify_by='screen_name' THEN screen_name
    WHEN identify_by='full_name' THEN CONCAT(first_name, ' ', middle_name, ' ', last_name)
    WHEN identify_by='company_name' THEN company_name
    ELSE 'Member'
    END) AS user_ident
    FROM users WHERE id IN ('{$user_id}', '{$from_user_id}')";

    $users_info = @format_str(@mysql_exec($users_info_sql));
    $users_info = @cb89($users_info, 'user_id');
    //var_dump("<pre>", $users_info); die();

    if(empty($users_info) || !is_array($users_info)){return false;}
    if(($from_user_id<=0 && count($users_info)<1) || ($from_user_id>0 && count($users_info)<2)){return false;}


    #/ get Notification from Templates
    $notif_template = get_notif_template($template_id);
    if(empty($notif_template) || count($notif_template)<=0){return false;}


    ##/ generate notification string
    $objects_ar = array();
    if(stristr($objects, ':')!=false)
    {
        $objects_ar = @explode(':', $objects);
    }
    else
    {
        $objects_ar[0] = $objects;
        $objects_ar[1] = $objects;
    }

    @$users_info[$from_user_id]['user_ident'] = str_replace('  ', ' ', @$users_info[$from_user_id]['user_ident']);
    @$users_info[$user_id]['user_ident'] = str_replace('  ', ' ', @$users_info[$user_id]['user_ident']);

    $from_user_intend = (empty($users_info[$from_user_id]['user_ident'])) ? "":"<b>".(@$users_info[$from_user_id]['user_ident'])."</b>";
    $to_user_intend = (empty($users_info[$user_id]['user_ident'])) ? "":"<b>".(@$users_info[$user_id]['user_ident'])."</b>";

    $notification = str_ireplace(
    array('{USER}', '{USER2}', '{OBJECT}', '{OBJECT2}'),
    array($from_user_intend, $to_user_intend,  "<b>{$objects_ar[0]}</b>", "<b>{$objects_ar[1]}</b>"),
    $notif_template['notification']);
    #-


    #/ Set Notification Details for email
    /*$notif_details = '';
    if(!empty($notif_template['notif_details']) && ($send_email!=false) && ($notif_data['object_id']>0) && ($notif_data['object_location']!=''))
    {
        $object_info_sql = "SELECT * FROM `{$notif_data['object_location']}` WHERE id='{$notif_data['object_id']}'";
        $object_info = @format_str(@mysql_exec($object_info_sql, 'single'));

        if(is_array($object_info) && count($object_info)>0)
        {
            $notif_details = str_ireplace(
            array('{OBJECT_INFO}', '{USER2}', '{OBJECT}', '{OBJECT2}'),
            array(@$object_info['user_ident'], @$users_info[$user_id]['user_ident'],  "<b>{$objects_ar[0]}</b>", "<b>{$objects_ar[1]}</b>"),
            $notif_template['notif_details']);
        }
    }*/


    #/ Return data
    if($get_user_info)
    return array('users_info'=>$users_info, 'notification'=>$notification); //'notif_details'=>$notif_details
    else
    return $notification;

}//end func..


/**
 * Read Notifications
 * [params]
 * $user_id = pull notifications of this user
 * $notification_id = read specific notification only
 * $limits = limit results (def=20)
*/
function read_notification($user_id, $notification_id='0', $limits=8)
{
    if($user_id<=0){return false;}

    $sql_notif = "SELECT * FROM user_notifications un WHERE un.user_id='{$user_id}' ";
    if($notification_id>0){$sql_notif.= " AND un.id='{$notification_id}'";}
    $sql_notif.= " ORDER BY un.created_on DESC";
    if($limits>0){$sql_notif.= " LIMIT {$limits}";}

    $notif_res = @format_str(@mysql_exec($sql_notif));
    //var_dump("<pre>", $sql_notif, $notif_res);
    if(!is_array($notif_res) || count($notif_res)<0){return false;}

    $notifis = array();
    foreach($notif_res as $notif_v)
    {
        if(!is_array($notif_v) || !array_key_exists('template_id', $notif_v)){continue;}

        $notif_data = array(
        'id' => "{$notif_v['id']}",
        'template_id' => "{$notif_v['template_id']}",
        'user_id' => "{$notif_v['user_id']}", //receiver
        'from_user_id' => "{$notif_v['from_user_id']}",
        'objects' => "{$notif_v['objects']}",
        'object_id' => "{$notif_v['object_id']}",
        'object_location' => "{$notif_v['object_location']}",
        'visit_url' => "{$notif_v['visit_url']}",
        'is_read' => "{$notif_v['is_read']}",
        'created_on' => "{$notif_v['created_on']}",
        );

        $notification = @get_notification_msg($notif_data, false, true);
        $notifis[] = array('notif_data'=>$notif_data, 'notification'=>$notification);

    }//end foreach...
    //var_dump("<pre>", $notifis);

    return $notifis;

}//end func..


/**
 * Count Notifications
 * [params]
 * $user_id = count notifications of this user
 * $only_unread (def=true) = count only unread.
*/
function count_notification($user_id, $only_unread=true)
{
    if($user_id<=0){return false;}

    $sql_notif = "SELECT count(*) as cx FROM user_notifications WHERE user_id='{$user_id}' ";
    if($only_unread)$sql_notif.= " AND is_read='0'";

    $notif_res = @mysql_exec($sql_notif, 'single');
    if(!is_array($notif_res) || count($notif_res)<0){return false;}

    $notif_count = 0;
    $notif_count = (int)@$notif_res['cx'];
    return $notif_count;

}//end func..
?>