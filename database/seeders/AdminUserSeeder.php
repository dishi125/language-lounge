<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userData = [
            [
                "email" => "gwb9160@nate.com",
                "password" => "dnjs9160",
                "email_verified_at" => NULL,
                "type" => "admin",
            ]
        ];

        foreach ($userData as $user) {
           $user = \App\Models\User::firstOrCreate([
               "email" => $user['email'],
               "password" => Hash::make($user['password']),
               "type" => $user['type']
           ]);
        }
    }
}
