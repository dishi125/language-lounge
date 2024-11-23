<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\ChangePasswordRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Exception, Hash, Auth;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show change password form
     * 
     */
    public function index()
    {
        return view('admin.profile.change-password');
    }

    //Submit change password
    public function submitChangePassword(ChangePasswordRequest $request) 
    {
        try {
            $userId = Auth::user()->id;
            $oldPassword = $request->old_password;
            $newPassword = $request->new_password;

            if ((Hash::check($oldPassword, Auth::user()->password)) == false) {
                notify()->error(__('admin_message.old_password_not_valid'));
                return redirect()->back();
            } else if ((Hash::check($newPassword, Auth::user()->password)) == true) {
                notify()->error(__('admin_message.new_pwd_not_same_old_pwd'));
                return redirect()->back();
            }
            //change password
            User::where('id', $userId)->update(['password' => Hash::make($newPassword)]);
            notify()->success(__('admin_message.pwd_change_success'));
            return redirect()->back();
        } catch (\Exception $ex) {
            Log::error($ex);
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back();
        }
    }
}