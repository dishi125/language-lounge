<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Messaging\CloudMessage;

function getDateTime()
{
    $date = Carbon::now("UTC")->format("Y-m-d H:i:s");
    return $date;
}

function getUuid()
{
    $uuid = DB::select('SELECT uuid() as uid');
    return $uuid[0]->uid;
}

function getUtcDate()
{
    $date = Carbon::now("UTC")->format("Y-m-d");
    return $date;
}

function getWeekday(){
    $week_day = date('l');
    return $week_day;
}

function getTime()
{
    $time = date('H:i:s');
    return $time;
}

function getHour()
{
    $time = date('H:i');
    return $time;
}

function getDefaultImage()
{
    return asset('assets/img/default.jpeg');
}

function sendPushNotification($title, $body, $data, $deviceToken)
{
    $return = false;
    $url = "https://fcm.googleapis.com/fcm/send";

    $token = $deviceToken;
    $serverKey = env('FCM_SERVER_KEY');

    $clickAction = "FLUTTER_NOTIFICATION_CLICK";
    $notification = ['title' => $title, 'body' => $body];
    $notificationData = $data;
    $arrayToSend = ['registration_ids' => $token, 'data' => $notificationData, 'notification' => $notification, "click_action" => $clickAction];

    $json = json_encode($arrayToSend);
    
    $headers = [];
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key=' . $serverKey;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Send the request
    if (curl_exec($ch)) {
        $return = true;
    }
    curl_close($ch);
    return $return;
}

// send single push notificatin using cloud messaging
function sendPushNotificationNew($notification, $notificationData, $deviceToken)
{
    $messaging = app('firebase.messaging');
    $clickAction = "FLUTTER_NOTIFICATION_CLICK";
    $message = CloudMessage::fromArray([
        'token' => $deviceToken,
        'notification' => $notification,
        'data' => $notificationData,
        'click_action' => $clickAction,
    ]);

    $messaging->send($message);
    return true;
}

// send multiple push notificatin using cloud messaging
function sendMulticastPushNotification($notification, $notificationData, $deviceTokens)
{
    $messaging = app('firebase.messaging');
    $clickAction = "FLUTTER_NOTIFICATION_CLICK";
    $message = CloudMessage::fromArray([
        'notification' => $notification,
        'data' => $notificationData,
        'click_action' => $clickAction,
    ]);
    $messaging->sendMulticast($message,$deviceTokens);
    return true;
}

function thousandsFormat($num) {
    if($num>1000) {

        $x = round($num);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = array('k', 'm', 'b', 't');
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];

        return $x_display;
    }

    return $num;
}
