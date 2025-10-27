<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Api\Admin\AboutUs;
use Illuminate\Support\Carbon;

class AboutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


       DB::table('about_us')->insert([
            [
                'breadcrumb' => null,
                'image' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
        DB::table('about_us_translations')->insert([
            [
                'aboutus_id' => AboutUs::first()->id,
                'locale'=>'ar',
                'title'=>'about ar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
        DB::table('about_us_translations')->insert([
            [
                'aboutus_id' => AboutUs::first()->id,
                'locale'=>'en',
                'title'=>'about en',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);


    }
}
