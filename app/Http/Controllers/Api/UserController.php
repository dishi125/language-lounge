<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyDiary;
use App\Models\UserDeviceDetail;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Traits\UploadFileTrait;
use App\Validators\UserValidator;
use App\Models\User;
use App\Models\DeviceDetail;
use DB, Auth, Hash;
use Illuminate\Support\Facades\Log;
use Notification;
use App\Notifications\AppNotification;

class UserController extends Controller
{
    private $userValidator;
    use ResponseTrait,UploadFileTrait;
    public function __construct()
    {
        $this->middleware('auth');
        $this->userValidator = new UserValidator();
    }

    //user detail
    public function userDetail(Request $request){
        try{
            $userInfo = Auth::user();
            return $this->sendSuccessResponse(__('api_messages.user_detail'),200,$userInfo);

        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while get user detail";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function userList(Request $request){
        try{
            $userInfo = User::get();
            return $this->sendSuccessResponse(__('api_messages.user_list'),200,$userInfo);

        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while get user list";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    //logout user
    public function logout(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();

        try {
            if(!Auth::check()) {
                return $this->sendFailResponse(__('api_messages.logout_error'),200);
            }
            //remove existing token and logout user
//            Auth::user()->AauthAcessToken()->delete();
            $result = $request->user()->token()->revoke(); //logout current device only
            UserDeviceDetail::where('device_id', $inputs['device_id'])->delete();

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.logout_success'),200);
        } catch (\Throwable $ex) {
            DB::rollBack();
            $logMessage = "Something went wrong while logout";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function addDiary(Request $request){
        DB::beginTransaction();
        try{
            //validate request start
            $userAddDiaryValidate = $this->userValidator->validateAddDiary($request);
            if ($userAddDiaryValidate->fails()) {
                return $this->sendFailResponse($userAddDiaryValidate->errors()->first(), 422);
            }
            //validate request end

            $userInfo = Auth::user();
            if ($userInfo->type == "user"){
                DailyDiary::updateOrCreate([
                    'user_id' => $userInfo->id,
                    'created_date' => date("Y-m-d"),
                ],[
                    'texts' => $request->text,
                ]);

                DB::commit();
                return $this->sendSuccessResponse(__('api_messages.add_diary_success'),200);
            }
            else {
                return $this->sendFailResponse(__('api_messages.passport_unauthorised'),401);
            }

        } catch(\Throwable $ex){
            DB::rollBack();
            $logMessage = "Something went wrong while add diary";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function searchUser(Request $request)
    {
        try{
            $search = $request->search;
            $users = User::where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->select(['id', 'name', 'email'])
                ->get();
            return $this->sendSuccessResponse(__('api_messages.user_list'),200,$users);

        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while search user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function MemberList(Request $request){
        try{
            $tab_type = $request->tab_type;
            $search = $request->search;

            // update visit status for display visit button again after 6 hours
            $activate_users = \App\Models\User::where('visit_status','activate')->get();
            foreach ($activate_users as $user){
                if ($user->last_visited_at!=null) {
                    $currentDateTime = \Carbon\Carbon::now();
                    $targetDateTime = \Carbon\Carbon::parse($user->last_visited_at);
                    $hoursDifference = $currentDateTime->diffInHours($targetDateTime);
//                    $hoursDifference = $currentDateTime->diffInMinutes($targetDateTime);
                }

                if ($user->last_visited_at==null || (isset($hoursDifference) && $hoursDifference>=6)){
                    \App\Models\User::where('id',$user->id)->update([
                        'visit_status' => 'non_visit',
                        'last_visited_at' => null,
                        'used_first_drink' => 0,
                        'used_free_drink' => 0,
                    ]);
                }
            }

            if ($tab_type=="all") {
                $users = User::query();
            }
            elseif ($tab_type=="visited"){
                $users = User::where('visit_status','activate');
            }

            if (isset($search)){
                $users = $users->where('name','LIKE','%'.$search.'%');
            }

            $users = $users->get(['id','name', 'email', 'visit_status', 'last_visited_at']);
            $users->makeHidden('avatar_url');

            return $this->sendSuccessResponse(__('api_messages.all_member_success'),200,$users);

        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while get all users";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function changeVisitStatus(Request $request){
        try{
            $inputs = $request->all();
            $last_visited_at = "";
            if ($inputs['type'] == "activate"){
                $last_visited_at = Carbon::now();
                User::where('id',$inputs['user_id'])->update([
                    'visit_status' => 'activate',
                    'last_visited_at' => $last_visited_at
                ]);
                $last_visited_at = $last_visited_at->format('Y-m-d H:i:s');
                $last_visited_at = Carbon::parse($last_visited_at)->format('Y-m-d\TH:i:s.u\Z');
            }
            else if ($inputs['type'] == "non_visit"){
                $last_visited_at = null;
                User::where('id',$inputs['user_id'])->update([
                    'visit_status' => 'non_visit',
                    'last_visited_at' => $last_visited_at,
                    'used_first_drink' => 0,
                    'used_free_drink' => 0,
                ]);
            }

            $data['visit_status'] = $inputs['type'];
            $data['last_visited_at'] = $last_visited_at;
            return $this->sendSuccessResponse(__('api_messages.visit_status_update'),200,$data);
        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while get all users";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function home(Request $request){
        try{
            $user = Auth::user();
            $data['ticket_level'] = $user->ticket_level;
            $data['visit_status'] = $user->visit_status;
            $data['used_first_drink'] = $user->used_first_drink;
            $data['used_free_drink'] = $user->used_free_drink;
            $data['is_admin_access'] = $user->is_admin_access;

            return $this->sendSuccessResponse(__('api_messages.home_success'),200,$data);
        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while get home page";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function profileInfo(Request $request){
        try{
            $user = Auth::user();
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['avatar_url'] = $user->avatar_url;
            $data['is_admin_access'] = $user->is_admin_access;

            return $this->sendSuccessResponse(__('api_messages.profile_success'),200,$data);
        } catch(\Throwable $ex){
            $logMessage = "Something went wrong while get user profile";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function deleteUser(Request $request){
        DB::beginTransaction();
        try{
            $user = Auth::user();
            $user_id = $user->id;

            if (!empty($user_id)) {
                DailyDiary::where('user_id', $user_id)->delete();
                UserDeviceDetail::where('user_id', $user_id)->delete();
                User::where('id', $user_id)->delete();
            }

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.remove_user_success'),200);
        } catch(\Throwable $ex){
            DB::rollBack();
            $logMessage = "Something went wrong while remove user profile";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public static function getAppleRefreshToken($authCode) {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'form_params' => [
                'client_id' => config('constant.apple_client_id'),
                'client_secret' => config('constant.apple_client_secret'),
                'code' => $authCode,
                'grant_type' => 'authorization_code',
                'redirect_uri' => env('APPLE_REDIRECT_URI')
            ]
        ];
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://appleid.apple.com/auth/token', $headers);
        $res = $client->sendAsync($request, $options)->wait();
        if($res->getStatusCode() == 200){
            Log::info("getAppleRefreshToken");
            Log::info($res->getBody());
            $json = json_decode($res->getBody());
            return $json->refresh_token;
        }
        return false;
    }

    public static function getAccessToken($refreshToken)
    {
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
        $options = [
            'form_params' => [
                'client_id' => config('apple_client_id'),
                'client_secret' => config('apple_client_secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken
            ]
        ];
        $request = new \GuzzleHttp\Psr7\Request('POST', 'https://appleid.apple.com/auth/token', $headers);
        $res = $client->sendAsync($request, $options)->wait();

        if ($res->getStatusCode() == 200) {
            $json = json_decode($res->getBody());
            return $json->access_token;
        }
        return false;
    }

}
