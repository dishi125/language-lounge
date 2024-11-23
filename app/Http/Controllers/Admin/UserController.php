<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Models\DailyDiary;
use App\Models\UserDeviceDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use app\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception, Hash, Auth, Crypt;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show user list
     *
     */
    public function index()
    {
        return view('admin.user.index');
    }

    /**
     * Get User List by AJax
     */
    public function userList(Request $request)
    {
        $columns = array(
            0 => 'name',
            1 => 'created_at',
            2 => 'email',
            3 => 'ticket_level',
            4 => 'action',
        );
        $searchVal = !empty($request->input('search.value')) ? $request->input('search.value') : null;
        $orderVal = isset($columns[$request->input('order.0.column')]) ? $columns[$request->input('order.0.column')] : null;
        $direction = $request->input('order.0.dir');
        $limit = $request->input('length');
        $start = $request->input('start');
        $draw = $request->input('draw');
        $adminTimezone = $this->getAdminUserTimezone();

        try {
            $userList = User::query();
            $totalUser = $userList->count();
            if (!empty($searchVal)) {
                $userList = $userList->where(function ($q) use ($searchVal) {
                    $q->where("name", 'LIKE', "%$searchVal%");
                    $q->orwhere("email", 'LIKE', "%$searchVal%");
                });
            }
            $totalFilterUser = $userList->count();
            $userList = $userList->offset($start)->limit($limit);
            if (!empty($orderVal)) {
                $userList = $userList->orderBy($orderVal, $direction);
            }
            $userList = $userList->get();

            $pdata = [];
            if (count($userList) > 0) {
                $i = 1;
                foreach ($userList as $key => $value) {
                    $data = [];
                    $data['name'] = $value->name;
//                $display_created_at = Carbon::parse($value->created_at)->format('Y-m-d H:i:s');
                    $data['signup_date'] = $this->formatDateTimeCountryWise($value->created_at, $adminTimezone);
                    $data['email'] = $value->email;
                    $data['ticket'] = $value->ticket_level;

                    $editbtn = '<a href="javascript:void(0)" onclick="editUser(' . $value->id . ')"><i class="fas fa-pencil-alt"></i></a>';
                    $removeBtn = '<a href="javascript:void(0)" onclick="removeUser(' . $value->id . ')"><i class="fas fa-trash"></i></a>';
                    $editPasswordBtn = '<a href="javascript:void(0)" onclick="editPassword(' . $value->id . ')" class="btn btn-outline-primary">Edit Password</a>';
                    $data['action'] = "$editbtn $removeBtn";
                    $data['edit_password'] = "$editPasswordBtn";
                    $pdata[] = $data;
                    $i++;
                }
            }

            $jsonData = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalUser),
                "recordsFiltered" => intval($totalFilterUser),
                "data" => $pdata,
            );
            return response()->json($jsonData);
        }
        catch (\Exception $ex) {
            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
            return response()->json($jsonData);
        }
    }

    public function userForm(Request $request)
    {
        $formTitle = __('pages.user.add_user');

        return view('admin.user.form',compact('formTitle'));
    }

    public function userStore(UserStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $email = !empty($request->email)?$request->email:NULL;
            $username = !empty($request->name)?$request->name:NULL;
            $password = !empty($request->password) ? Hash::make($request->password) : NULL;
            $ticket_level = !empty($request->ticket_level)?$request->ticket_level:NULL;
            $message = __('admin_message.user_save_success');

            User::create([
                "email" => $email,
                "name" => $username,
                "password" => $password,
                "type" => "user",
                "ticket_level" => $ticket_level,
            ]);
            DB::commit();
            notify()->success($message);
            return redirect()->route('admin.users');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::info('Exception while storing user');
            Log::error($th);
            notify()->error(__('admin_message.exception_message'));
            return redirect()->back()->withInput();
        }
    }

    public function userEdit($id)
    {
        $activate_users = User::where('visit_status','activate')->get();
        foreach ($activate_users as $user){
            if ($user->last_visited_at!=null) {
                $currentDateTime = \Carbon\Carbon::now();
                $targetDateTime = \Carbon\Carbon::parse($user->last_visited_at);
                $hoursDifference = $currentDateTime->diffInHours($targetDateTime);
            }

            if ($user->last_visited_at==null || (isset($hoursDifference) && $hoursDifference>=6)){
                User::where('id',$user->id)->update([
                    'visit_status' => 'non_visit',
                    'last_visited_at' => null,
                    'used_first_drink' => 0,
                    'used_free_drink' => 0,
                ]);
            }
        }

        $user = User::where('id', $id)
            ->select(['id','is_admin_access','is_tutor','visit_status','last_visited_at'])
            ->first();
        if ($user->last_visited_at!=null){
            $storedDatetime = Carbon::parse($user->last_visited_at);
            $currentTime = Carbon::now();
            $targetTime = $storedDatetime->copy()->addHours(6);
            $remainingSeconds = $targetTime->diffInSeconds($currentTime);
            $remainingTime = $remainingSeconds * 1000;
            $user->remaining_time = $remainingTime;
        }
//        dd($user->toArray());
        return view('admin.user.edit-user', compact('user'));
    }

    public function editAdminAccess(Request $request)
    {
        DB::beginTransaction();
        try {
            User::where('id', $request->user_id)->update(['is_admin_access' => $request->is_admin_access]);

            DB::commit();
            $jsonData = [
                'success' => true,
                'message' => "Admin access updated successfully.",
            ];
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            DB::rollBack();
            $jsonData = [
                'success' => false,
                'message' => "Failed to update admin access!!",
            ];
            return response()->json($jsonData);
        }
    }

    public function editIsTutor(Request $request)
    {
        DB::beginTransaction();
        try {
            User::where('id', $request->user_id)->update(['is_tutor' => $request->is_tutor]);

            DB::commit();
            $jsonData = [
                'success' => true,
                'message' => "Tutor access updated successfully.",
            ];
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            DB::rollBack();
            $jsonData = [
                'success' => false,
                'message' => "Failed to update tutor access!!",
            ];
            return response()->json($jsonData);
        }
    }

    public function delete($id)
    {
        return view('admin.user.delete', compact('id'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            DailyDiary::where('user_id', $id)->delete();
            UserDeviceDetail::where('user_id', $id)->delete();
            User::where('id', $id)->delete();

            DB::commit();
            notify()->success("User deleted successfully");
            return redirect()->route('admin.users');
        } catch (\Exception $ex) {
            DB::rollBack();
            notify()->error("Failed to delete user");
            return redirect()->route('admin.users');
        }
    }

    public function changeVisitStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = $request->all();

            if ($inputs['type']=="activate") {
                User::where('id', $inputs['user_id'])->update([
                    'visit_status' => 'activate',
                    'last_visited_at' => Carbon::now()
                ]);

                $jsonData = [
                    'success' => true,
                    'message' => "User activated successfully.",
                ];
            }
            elseif ($inputs['type']=="non_visit"){
                User::where('id',$inputs['user_id'])->update([
                    'visit_status' => 'non_visit',
                    'last_visited_at' => null,
                    'used_first_drink' => 0,
                    'used_free_drink' => 0,
                ]);

                $jsonData = [
                    'success' => true,
                    'message' => "User de-activated successfully.",
                ];
            }

            DB::commit();
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            DB::rollBack();
            $jsonData = [
                'success' => false,
                'message' => "Failed to activate user!!",
            ];
            return response()->json($jsonData);
        }
    }

    public function editPassword(Request $request)
    {
        DB::beginTransaction();
        try {
            User::where('id', $request->user_id)->update([
                'password' => Hash::make($request->new_password),
                'org_password' => $request->new_password
            ]);

            DB::commit();
            $jsonData = [
                'success' => true,
                'message' => "Password updated successfully.",
            ];
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            DB::rollBack();
            $jsonData = [
                'success' => false,
                'message' => "Failed to update password!!",
            ];
            return response()->json($jsonData);
        }
    }

}
