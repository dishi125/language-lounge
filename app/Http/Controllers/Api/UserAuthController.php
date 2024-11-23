<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\TempUser;
use App\Models\UserDeviceDetail;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Traits\UploadFileTrait;
use App\Validators\UserValidator;
use App\Models\User;
use App\Models\DeviceDetail;
use DB, Exception, Auth, Log, Mail, Hash;
use Illuminate\Support\Facades\Storage;

class UserAuthController extends Controller
{
    private $userValidator;
    use ResponseTrait,UploadFileTrait;
    public function __construct()
    {
        $this->userValidator = new UserValidator();
    }

    // user register
    public function register(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();
        try {
            //validate request start
            $userRegisterValidate = $this->userValidator->validateRegister($request);
            if ($userRegisterValidate->fails()) {
                return $this->sendFailResponse($userRegisterValidate->errors()->first(), 422);
            }
            //validate request end

            $check_user_exist = User::where('email',$request->email)->pluck('signup_type')->first();
            if (!empty($check_user_exist)){
                if ($check_user_exist=="normal"){
                    $err_text = "The email is signed up already";
                }
                elseif ($check_user_exist=="google"){
                    $err_text = "The email is signed up already (Google)";
                }
                elseif ($check_user_exist=="kakao"){
                    $err_text = "The email is signed up already (Kakao)";
                }
                elseif ($check_user_exist=="apple"){
                    $err_text = "The email is signed up already (Apple)";
                }
                return $this->sendFailResponse($err_text, 403);
            }

            if ($request->hasFile('profile_pic')) {
                $profileFolder = config('constant.avatar');
                if (!Storage::exists($profileFolder)) {
                    Storage::makeDirectory($profileFolder);
                }
                $avatar = Storage::disk('s3')->putFile($profileFolder, $request->file('profile_pic'),'public');
                $fileName = basename($avatar);
                $profile_pic = $profileFolder . '/' . $fileName;
            }

            $userData = [
                "email" => $request->email,
                "name" => $request->name,
                "password" => Hash::make($request->password),
                "ticket_level" => isset($request->ticket_level) ? $request->ticket_level : "language_lounge_gold", //default gold
                "type" => "user",
                "signup_type" => "normal",
                "avatar" => isset($profile_pic) ? $profile_pic : null
            ];
            $userInfo = User::create($userData);

            $this->saveDeviceDetails($userInfo->id, $inputs);

            //generate passport token
            $userInfo->token = $userInfo->createToken(env('PASSPORT_TOKEN_STR'))->accessToken;

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.register_success'),200,$userInfo);
        } catch (\Throwable $ex) {
            DB::rollback();
            $logMessage = "Something went wrong while register user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    //user login
    public function login(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();
        try{
            //validate request start
            $userLoginValidate = $this->userValidator->validateUserLogin($request);
            if ($userLoginValidate->fails()) {
                return $this->sendFailResponse($userLoginValidate->errors()->first(), 422);
            }
            //validate request end

            $is_email_exist = User::where('email',$request->email)->first();
            if (empty($is_email_exist)){
                return $this->sendFailResponse(__('api_messages.not_found_user'), 402);
            }

            //check credential as per its data
            $credentials = $this->credentials($request->email, $request->password);
            if(!Auth::attempt($credentials)){
                return $this->sendFailResponse(__('api_messages.login_fail'), 403);
            }
            $userInfo = Auth::user();

            //remove older token / one device login start
//            Auth::user()->AauthAcessToken()->delete();
            //remove older token / one device login end
            $this->saveDeviceDetails($userInfo->id, $inputs);

            $userInfo->token = $userInfo->createToken(env('PASSPORT_TOKEN_STR'))->accessToken;

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.login_success'),200,$userInfo);
        } catch(\Throwable $ex){
            DB::rollBack();
            $logMessage = "Something went wrong while login user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    protected function credentials($username = "", $password = "")
    {
        return ['email' => $username, 'password' => $password];
    }

    public function google_signup(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();
//        try {
            //validate request start
            $userRegisterValidate = $this->userValidator->validateGoogleSignup($request);
            if ($userRegisterValidate->fails()) {
                return $this->sendFailResponse($userRegisterValidate->errors()->first(), 422);
            }
            //validate request end

            $check_user_exist = User::where('email',$request->email)->pluck('signup_type')->first();
            if (!empty($check_user_exist)){
                if ($check_user_exist=="normal"){
                    $err_text = "The email is signed up already";
                }
                elseif ($check_user_exist=="google"){
                    $err_text = "The email is signed up already (Google)";
                }
                elseif ($check_user_exist=="kakao"){
                    $err_text = "The email is signed up already (Kakao)";
                }
                elseif ($check_user_exist=="apple"){
                    $err_text = "The email is signed up already (Apple)";
                }
                return $this->sendFailResponse($err_text, 403);
            }

            if (isset($request->profile_pic) && $request->profile_pic!="" && $request->profile_pic!=null) {
                $file_url = $request->profile_pic;
                $fileContents = file_get_contents($file_url);
                $tempFilePath = storage_path('app/public/temp.jpg');
                file_put_contents($tempFilePath, $fileContents);
                $file = new File($tempFilePath);

                $profileFolder = config('constant.avatar');
                if (!Storage::exists($profileFolder)) {
                    Storage::makeDirectory($profileFolder);
                }
                $avatar = Storage::disk('s3')->putFile($profileFolder, $file,'public');
                $fileName = basename($avatar);
                $profile_pic = $profileFolder . '/' . $fileName;

                // Delete the temporary file
                unlink($tempFilePath);
            }

            $userData = [
                "email" => $request->email,
                "name" => $request->name,
                "ticket_level" => isset($request->ticket_level) ? $request->ticket_level : "language_lounge_gold", //default gold
                "type" => "user",
                "signup_type" => "google",
                "avatar" => isset($profile_pic) ? $profile_pic : null,
                "password" => Hash::make($request->password),
                "org_password" => $request->password
            ];
            $userInfo = User::create($userData);

            $this->saveDeviceDetails($userInfo->id, $inputs);

            //generate passport token
            $userInfo->token = $userInfo->createToken(env('PASSPORT_TOKEN_STR'))->accessToken;

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.register_success'),200,$userInfo);
        /*} catch (\Throwable $ex) {
            DB::rollback();
            $logMessage = "Something went wrong while signup user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }*/
    }

    public function google_login(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();
        try{
            //validate request start
            $userLoginValidate = $this->userValidator->validateGoogleLogin($request);
            if ($userLoginValidate->fails()) {
                return $this->sendFailResponse($userLoginValidate->errors()->first(), 422);
            }
            //validate request end

            $is_email_exist = User::where('email',$request->email)->where('signup_type','google')->first();
            if (empty($is_email_exist)){
                return $this->sendFailResponse(__('api_messages.not_found_user'), 402);
            }

            //check credential as per its data
            $credentials = $this->credentials($request->email, $is_email_exist->org_password);
            if(!Auth::attempt($credentials)){
                return $this->sendFailResponse(__('api_messages.login_fail'), 403);
            }
            $userInfo = Auth::user();

            //remove older token / one device login start
//            Auth::user()->AauthAcessToken()->delete();
            //remove older token / one device login end
            $this->saveDeviceDetails($userInfo->id, $inputs);

            $userInfo->token = $userInfo->createToken(env('PASSPORT_TOKEN_STR'))->accessToken;

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.login_success'),200,$userInfo);
        } catch(\Throwable $ex){
            DB::rollBack();
            $logMessage = "Something went wrong while login user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function kakao_signup(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();

        try {
            //validate request start
            $userRegisterValidate = $this->userValidator->validateKakaoSignup($request);
            if ($userRegisterValidate->fails()) {
                return $this->sendFailResponse($userRegisterValidate->errors()->first(), 422);
            }
            //validate request end

            $check_user_exist = User::where('email',$request->email)->pluck('signup_type')->first();
            if (!empty($check_user_exist)){
                if ($check_user_exist=="normal"){
                    $err_text = "The email is signed up already";
                }
                elseif ($check_user_exist=="google"){
                    $err_text = "The email is signed up already (Google)";
                }
                elseif ($check_user_exist=="kakao"){
                    $err_text = "The email is signed up already (Kakao)";
                }
                elseif ($check_user_exist=="apple"){
                    $err_text = "The email is signed up already (Apple)";
                }
                return $this->sendFailResponse($err_text, 403);
            }

            if (isset($request->profile_pic) && $request->profile_pic!="" && $request->profile_pic!=null) {
                $file_url = $request->profile_pic;
                $fileContents = file_get_contents($file_url);
                $tempFilePath = storage_path('app/public/temp.jpg');
                file_put_contents($tempFilePath, $fileContents);
                $file = new File($tempFilePath);

                $profileFolder = config('constant.avatar');
                if (!Storage::exists($profileFolder)) {
                    Storage::makeDirectory($profileFolder);
                }
                $avatar = Storage::disk('s3')->putFile($profileFolder, $file,'public');
                $fileName = basename($avatar);
                $profile_pic = $profileFolder . '/' . $fileName;

                // Delete the temporary file
                unlink($tempFilePath);
            }

            $userData = [
                "email" => $request->email,
                "name" => $request->name,
                "ticket_level" => isset($request->ticket_level) ? $request->ticket_level : "language_lounge_gold", //default gold
                "type" => "user",
                "signup_type" => "kakao",
                "avatar" => isset($profile_pic) ? $profile_pic : null,
                "password" => Hash::make($request->password),
                "org_password" => $request->password
            ];
            $userInfo = User::create($userData);

            $this->saveDeviceDetails($userInfo->id, $inputs);

            //generate passport token
            $userInfo->token = $userInfo->createToken(env('PASSPORT_TOKEN_STR'))->accessToken;

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.register_success'),200,$userInfo);
        } catch (\Throwable $ex) {
            DB::rollback();
            $logMessage = "Something went wrong while signup user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function kakao_login(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();

        try{
            //validate request start
            $userLoginValidate = $this->userValidator->validateKakaoLogin($request);
            if ($userLoginValidate->fails()) {
                return $this->sendFailResponse($userLoginValidate->errors()->first(), 422);
            }
            //validate request end

            $is_email_exist = User::where('email',$request->email)->where('signup_type','kakao')->first();
            if (empty($is_email_exist)){
                return $this->sendFailResponse(__('api_messages.not_found_user'), 402);
            }

            //check credential as per its data
            $credentials = $this->credentials($request->email, $is_email_exist->org_password);
            if(!Auth::attempt($credentials)){
                return $this->sendFailResponse(__('api_messages.login_fail'), 403);
            }
            $userInfo = Auth::user();

            //remove older token / one device login start
//            Auth::user()->AauthAcessToken()->delete();
            //remove older token / one device login end
            $this->saveDeviceDetails($userInfo->id, $inputs);

            $userInfo->token = $userInfo->createToken(env('PASSPORT_TOKEN_STR'))->accessToken;

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.login_success'),200,$userInfo);
        } catch(\Throwable $ex){
            DB::rollBack();
            $logMessage = "Something went wrong while login user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function apple_signup(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();

        try {
            //validate request start
            $userRegisterValidate = $this->userValidator->validateAppleSignup($request);
            if ($userRegisterValidate->fails()) {
                return $this->sendFailResponse($userRegisterValidate->errors()->first(), 422);
            }
            //validate request end

            $check_user_exist = User::where('email',$request->email)->pluck('signup_type')->first();
            if (!empty($check_user_exist)){
                if ($check_user_exist=="normal"){
                    $err_text = "The email is signed up already";
                }
                elseif ($check_user_exist=="google"){
                    $err_text = "The email is signed up already (Google)";
                }
                elseif ($check_user_exist=="kakao"){
                    $err_text = "The email is signed up already (Kakao)";
                }
                elseif ($check_user_exist=="apple"){
                    $err_text = "The email is signed up already (Apple)";
                }
                return $this->sendFailResponse($err_text, 403);
            }

            if ($request->hasFile('profile_pic')) {
                $profileFolder = config('constant.avatar');
                if (!Storage::exists($profileFolder)) {
                    Storage::makeDirectory($profileFolder);
                }
                $avatar = Storage::disk('s3')->putFile($profileFolder, $request->file('profile_pic'),'public');
                $fileName = basename($avatar);
                $profile_pic = $profileFolder . '/' . $fileName;
            }

            //find user from temp table start
            if(!empty($request->apple_id)) {
                $tempUserInfo = TempUser::where(['social_id' => $request->apple_id, 'social_type' => 'apple'])->first();
            }
            //find user from temp table end
            $refreshToken = $tempUserInfo->apple_refresh_token ?? NULL;
            $accessToken = $tempUserInfo->apple_access_token ?? NULL;

            $userData = [
                "email" => $request->email,
                "name" => $request->name,
                "ticket_level" => isset($request->ticket_level) ? $request->ticket_level : "language_lounge_gold", //default gold
                "type" => "user",
                "signup_type" => "apple",
                "avatar" => isset($profile_pic) ? $profile_pic : null,
                "password" => Hash::make($request->password),
                "org_password" => $request->password,
                "social_id" => isset($request->social_id) ? $request->social_id : null,
//                "apple_id" => isset($request->apple_id) ? $request->apple_id : null,
                "apple_refresh_token" => $refreshToken,
                "apple_access_token" => $accessToken,
            ];
            $userInfo = User::create($userData);

            //remove user from temp table start
            if(!empty($request->apple_id)) {
                $tempUser = TempUser::where(['social_id' => $request->apple_id, 'social_type' => 'apple'])->delete();
            }
            //remove user from temp table end

            $this->saveDeviceDetails($userInfo->id, $inputs);

            //generate passport token
            $userInfo->token = $userInfo->createToken(env('PASSPORT_TOKEN_STR'))->accessToken;

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.register_success'),200,$userInfo);
        } catch (\Throwable $ex) {
            DB::rollback();
            $logMessage = "Something went wrong while signup user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function apple_login(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();
//        try{
            //validate request start
            $userLoginValidate = $this->userValidator->validateAppleLogin($request);
            if ($userLoginValidate->fails()) {
                return $this->sendFailResponse($userLoginValidate->errors()->first(), 422);
            }
            //validate request end
            $userInfo = (object) [];

            $user = new User();
            $user = $user->getExistUser($request);
//            $userInfo = Auth::user();
            if ($user) {
                $userInfo = $user;
                $userInfo->user_exist = 1;
                $userInfo->token = $userInfo->createToken(env('PASSPORT_TOKEN_STR'))->accessToken;
                $this->saveDeviceDetails($userInfo->id, $inputs);
            } else {
                $appleRequest = User::checkAppleRequest($request);
                $userInfo->temp_user_detail = $appleRequest;
                $userInfo->user_exist = 0;
            }

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.login_success'),200,$userInfo);
        /*} catch(\Throwable $ex){
            DB::rollBack();
            $logMessage = "Something went wrong while login user";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }*/
    }

    public function saveDeviceDetails($userID, $inputs)
    {
        $device = UserDeviceDetail::where('user_id', $userID)->where("device_id", $inputs['device_id'])->first();

        $deviceDetail = [
            "user_id" => $userID,
            "device_token" => $inputs['device_token'],
            "device_id" => $inputs['device_id'],
            "device_type" => $inputs['device_type'],
        ];
        if (!empty($device)) {
            UserDeviceDetail::where('id', $device->id)->update($deviceDetail);
        } else {
            $user = User::find($userID);

            if (!empty($user)) {
                $user->deviceDetails()->create([
                    'device_token' => $inputs['device_token'],
                    "device_id" => $inputs['device_id'],
                    "device_type" => $inputs['device_type'],
                ]);
            }
        }
    }

    public function checkAccountExist(Request $request) {
        DB::beginTransaction();
        $inputs = $request->all();

        try {
            $check_user_exist = User::where('email',$inputs['email'])->where('signup_type','normal')->first();
            $data = (!empty($check_user_exist)) ? true : false;

            DB::commit();
            return $this->sendSuccessResponse(__('api_messages.check_email'),200, $data);
        } catch (\Throwable $ex) {
            DB::rollBack();
            $logMessage = "Something went wrong while check email";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function forgotPassword(Request $request)
    {
        DB::beginTransaction();
        $inputs = $request->all();
        try {
            $validation = $this->userValidator->validateForgotPassword($request);

            if ($validation->fails()) {
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }
            $user = User::where('email', $inputs['email'])->first();
            if (!empty($user)) {
                $otp = mt_rand(100000, 999999);
                $user->otp = $otp;
                Mail::to($user->email)->send(new ForgotPassword($user));

                DB::commit();
                return $this->sendSuccessResponse("Mail send successfully", 200, $otp);
            } else {
                return $this->sendFailResponse("E-mail doesn't exists", 422);
            }
        } catch (\Throwable $ex) {
            DB::rollBack();
            $logMessage = "Something went wrong while forgot password";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

    public function changePassword(Request $request)
    {
        $inputs = $request->all();
        $email = $inputs['email'];
        $password = $inputs['password'];
        DB::beginTransaction();
        try {
            $validation = $this->userValidator->validateUpdatePassword($request);
            if ($validation->fails()) {
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }

            $user_detail = User::where('email', $email)->first();
            if (!empty($user_detail)) {
                $user = User::where('id', $user_detail->id)->update([
                    'password' => Hash::make($password),
                    'org_password' => $password,
                ]);

                DB::commit();
                return $this->sendSuccessResponse("Password changed successfully", 200);
            } else {
                return $this->sendFailResponse("Email ID doesn't exists", 400);
            }
        } catch (\Throwable $ex) {
            DB::rollBack();
            $logMessage = "Something went wrong while forgot password";
            return $this->sendServerFailResponse(__('api_messages.exception_msg'),500,$ex,$logMessage);
        }
    }

}
