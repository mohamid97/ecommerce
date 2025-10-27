<?php

namespace Database\Seeders;

use App\Models\Api\Admin\ContactUs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('contact_us')->insert([
        [
            'breadcrumb' => null,
            'image' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            
        ],

        ]);
        DB::table('contact_us_translations')->insert([
            [
                'contatcus_id' => ContactUs::first()->id,
                'locale'=>'ar',
                'title'=>'fdsfdsf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
        DB::table('contact_us_translations')->insert([
            [
                'contatcus_id' => ContactUs::first()->id,
                'locale'=>'en',
                'title'=>'fdsfdsf',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);



    }
}
