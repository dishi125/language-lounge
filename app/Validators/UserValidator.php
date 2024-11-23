<?php

namespace App\Validators;
use Auth;

class UserValidator extends ModelValidator
{
    private $userid;
    public function __construct()
    {
        $this->userid = (Auth::check())?Auth::user()->id:0;
    }

    // user register rule
    private $userRegisterRules = [
        'email' => 'required|email',
        'name' => 'required',
        'password' => 'required|min:6',
        'profile_pic' => 'image'
//        'ticket_level' => 'required',
    ];

    private $userGoogleSignupRules = [
        'email' => 'required|email',
        'name' => 'required',
    ];

    private $userAppleSignupRules = [
        'email' => 'required|email',
        'name' => 'required',
        'profile_pic' => 'image'
    ];

    private $userKakaoSignupRules = [
        'email' => 'required|email',
        'name' => 'required',
    ];

    // user login rule
    private $userLoginRules = [
        'email' => 'required',
        'password' => 'required',
    ];

    private $googleLoginRules = [
        'email' => 'required',
    ];

    private $kakaoLoginRules = [
        'email' => 'required',
    ];

    private $appleLoginRules = [
        'social_id' => 'required',
        'auth_code' => 'required',
    ];

    private $addDiaryRules = [
//        'date' => 'required',
        'text' => 'required',
    ];

    private $forgotPasswordRules = [
        'email' => 'required|exists:users,email,deleted_at,NULL',
    ];

    private $updatePasswordRules = [
        'email' => 'required|exists:users,email,deleted_at,NULL',
        'password' => 'required'
    ];

    // user register rule
    public function validateRegister($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userRegisterRules);
    }

    //google signup
    public function validateGoogleSignup($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userGoogleSignupRules);
    }

    //apple signup
    public function validateAppleSignup($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userAppleSignupRules);
    }

    //kakao signup
    public function validateKakaoSignup($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userKakaoSignupRules);
    }

    // user login rule
    public function validateUserLogin($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->userLoginRules);
    }

    public function validateGoogleLogin($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->googleLoginRules);
    }

    public function validateKakaoLogin($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->kakaoLoginRules);
    }

    public function validateAppleLogin($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->appleLoginRules);
    }

    // user login rule
    public function validateAddDiary($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->addDiaryRules);
    }

    public function validateForgotPassword($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->forgotPasswordRules);
    }

    public function validateUpdatePassword($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->updatePasswordRules);
    }
}
