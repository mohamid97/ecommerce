<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Api\Admin\Lang;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $adminUser = User::firstOrCreate(
            ['email' => 'cangrow@gmail.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'username' => 'superadmin',
                'password' => Hash::make('123456'),
                'type' => 'admin',
            ]
            
        );

        Lang::firstOrCreate(['code' => 'ar'], ['lang' => 'Arabic']);

        $adminRole = Role::firstOrCreate(['name' => 'admin' , 'guard_name' => 'sanctum']);
        $managerRole = Role::firstOrCreate(['name' => 'manager' , 'guard_name' => 'sanctum']);
       
        
        $models = ['user','productoption' , 'option','page','des','specification','certificate','faq','brand','applicant','branch', 'mediaimage', 'mediavideo','metasetting','ourteam','setting','offer' ,'product','coupon' ,'service' ,'role' , 'client' , 'event', 'feedback','achivement', 'ourwork','blog' ,'permission' ,'post', 'service' , 'lang' , 'slider' ,'message' ,'category' , 'about' , 'contact' , 'location' , 'maincontact' , 'social'];
        $actions = ['view', 'create', 'update', 'delete'];
        foreach ($models as $model) {
            foreach ($actions as $action) {
               $permission = Permission::firstOrCreate(['name' => "{$action} {$model}" , 'guard_name' => 'sanctum']);
               $adminRole->givePermissionTo($permission);             
            }
        }
        
        $adminUser->assignRole($adminRole);
    }

    

}