<?php

namespace Database\Seeders;

use App\Models\ImportantSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ImportantSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settingData = [
            [
                "name" => "Normal mode",
                "value" => 1,
            ],
            [
                "name" => "Timer mode",
                "value" => 0,
            ]
        ];

        foreach ($settingData as $data) {
            $settings = ImportantSetting::firstOrCreate([
                "name" => $data['name'],
                "value" => $data['value'],
            ]);
        }
    }
}
