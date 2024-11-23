<?php

namespace App\Console\Commands;

use App\Models\ImportantSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ActivateUserCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activateusers:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate all users between 8pm to 2am on Saturday (korean time)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("===================ActivateUserCron Start======================");
        $check_mode = ImportantSetting::where('value',1)->pluck('name')->toArray();
        $currentDateTime = Carbon::now();
        $currentDayOfWeek = $currentDateTime->dayOfWeek;
        $currentHour = $currentDateTime->hour;

        if (in_array("Timer mode",$check_mode)){
            if ($currentDayOfWeek == Carbon::SATURDAY && $currentHour >= 11 && $currentHour < 17) {
                $users = User::where('type','user')->where('visit_status','non_visit')->get();
                foreach ($users as $user){
                    User::where('id',$user->id)->update([
                        'visit_status' => 'activate',
                        'last_visited_at' => Carbon::now()
                    ]);
                }
            }
        }

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

        Log::info("===================ActivateUserCron End======================");
    }

}
