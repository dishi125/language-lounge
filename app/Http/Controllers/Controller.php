<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getAdminUserTimezone(){

        $timezone = isset($_COOKIE['admin_timezone_new']) ? $_COOKIE['admin_timezone_new'] : '';

        if(empty($timezone)){
            Log::info('Admin Timezone Function.');
            $timezone = '';
            $ip = $this->getIPAddress();
            $json = file_get_contents( 'http://ip-api.com/json/' . $ip);
            $ipData = json_decode( $json, true);
            Log::info(Carbon::now()->format('Y-m-d H:i:s'));
            Log::info($ip);
            Log::info($json);
            if (!empty($ipData) && !empty($ipData['timezone'])) {
                $timezone = $ipData['timezone'];
                $countryCode = $ipData['countryCode'];
            } else {
                $timezone = 'UTC';
                $countryCode = 'KR';
            }
            setcookie('admin_timezone_new',$timezone,time()+60*60*24*365, '/');
            setcookie('admin_country_code',$countryCode,time()+60*60*24*365, '/');
            Log::info($timezone);
            return $timezone;
        }else{
            return $timezone;
        }
    }

    public function getIPAddress() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /* Format DateTime Country wise */
    public static function formatDateTimeCountryWise($date,$adminTimezone,$format='Y-m-d H:i:s'){
        if(empty($date)) return;
        $dateShow = Carbon::createFromFormat('Y-m-d H:i:s',Carbon::parse($date), "UTC")->setTimezone($adminTimezone)->toDateTimeString();
        return Carbon::parse($dateShow)->format($format);
    }

}
