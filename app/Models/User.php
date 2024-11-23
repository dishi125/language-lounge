<?php

namespace App\Models;

use App\Http\Controllers\Api\UserController;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'type',
        'username',
        'ticket_level',
        'is_admin_access',
        'is_tutor',
        'visit_status',
        'last_visited_at',
        'signup_type',
        'avatar',
        'org_password',
        'used_first_drink',
        'used_free_drink',
        'social_id',
        'apple_id',
        'apple_refresh_token',
        'apple_access_token',
    ];

    protected $appends = ['avatar_url'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function AauthAcessToken(){
        return $this->hasMany(OauthAccessToken::class);
    }

    public function getAvatarUrlAttribute()
    {
        $avatar = '';
//        $id = $this->attributes['id'];
//        $user = User::where('id',$id)->first();
//        dd($this->attributes['avatar']);
        if (isset($this->attributes['avatar']) && $this->attributes['avatar']!=NULL && !filter_var($this->attributes['avatar'], FILTER_VALIDATE_URL)) {
            $avatar = Storage::disk('s3')->url($this->attributes['avatar']);
        } else {
            $avatar = isset($this->attributes['avatar']) ? $this->attributes['avatar'] : asset('images/default-avatar.png');
        }
        return $this->attributes['avatar_url'] = $avatar;
    }

    public function getLastVisitedAtAttribute()
    {
        if (isset($this->attributes['last_visited_at']) && $this->attributes['last_visited_at']!=NULL){
            $last_visited_at = Carbon::parse($this->attributes['last_visited_at'])->format('Y-m-d\TH:i:s.u\Z');
        }
        return $this->attributes['last_visited_at'] = isset($last_visited_at) ? $last_visited_at : $this->attributes['last_visited_at'];
    }

    public function getExistUser($request)
    {
        $user = $this->query();
        $user = $user->where('social_id', $request->social_id);
        if (!empty($request->email)) {
            $email = $request->email;
            $user = $user->where(function ($q) use ($email) {
                $q->orWhere('email', $email);
            });
        }
        $user = $user->first();
        return $user;
    }

    public static function checkAppleRequest($request){
        $tempUser = null;
        //check into temp user if exist start
        $tempUser = TempUser::where(['social_id' => $request->social_id, 'social_type' => "apple"])->first();
        if(!empty($tempUser)) {
            return $tempUser;
        }
        //check into temp user if exist end
        //generate refresh token start
        $refreshToken = null;
        $appleAccessToken = null;
        if(!empty($request->auth_code)) {
            // echo $request->auth_code;exit;
            $refreshToken = UserController::getAppleRefreshToken($request->auth_code);

            if(!$refreshToken){
                Log::error('User Model : Something went wrong while generating refresh token for '.$request->social_id);
            }

            if(!empty($refreshToken)) {
                $appleAccessToken = UserController::getAccessToken($refreshToken);
            }
        }
        //generate refresh token end

        $tempUser = TempUser::firstOrCreate(
            ['social_id' => $request->social_id, 'social_type' => "apple"],
            ['email' => $request->email ?? NULL,'username' => $request->username ?? NULL,'auth_code' => $request->auth_code ?? NULL,'apple_refresh_token' => $refreshToken, 'apple_access_token' => $appleAccessToken]
        );
        return $tempUser;
    }

    public function deviceDetails()
    {
        return $this->hasMany(UserDeviceDetail::class);
    }

}
