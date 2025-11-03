<?php

namespace Database\Seeders;

use App\Models\Api\Admin\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
class mainSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            DB::table('settings')->insert([
            [
                'work_hours' => '5 Hours',
                'favicon' => null,
                'icon'=>null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
        DB::table('setting_translations')->insert([
            [
                'aboutus_id' => Setting::first()->id,
                'locale'=>'ar',
                'title'=>'Setting ar',
                'breif'=>'TEST BREIUF',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
        DB::table('setting_translations')->insert([
            [
                'aboutus_id' => Setting::first()->id,
                'locale'=>'en',
                'title'=>'Setting en',
                'breif'=>'TEST BREIUF',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
    }
}