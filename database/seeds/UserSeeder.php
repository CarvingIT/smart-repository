<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // add users
        DB::table('users')->insert(
            [
                'name'=>'Ketan Kulkarni',
                'email'=>'ketan@carvingit.com',
                'password'=>bcrypt('SmartPass!@#'),
                'remember_token'=>0,
                'created_at'=>NOW(),
                'updated_at'=>NOW(),
            ]
        );
        
        DB::table('users')->insert(
            [
                'name'=>'Shraddha Kulkarni',
                'email'=>'shraddha@carvingit.com',
                'password'=>bcrypt('SmartPass!@#'),
                'remember_token'=>0,
                'created_at'=>NOW(),
                'updated_at'=>NOW(),
            ]);
        DB::table('users')->insert(
            [
                'name'=>'Rutuja Bhoyar',
                'email'=>'rutuja@carvingit.com',
                'password'=>bcrypt('SmartPass!@#'),
                'remember_token'=>0,
                'created_at'=>NOW(),
                'updated_at'=>NOW(),
            ]);
        DB::table('users')->insert(
            [
                'name'=>'Rupali Patil',
                'email'=>'rupali@carvingit.com',
                'password'=>bcrypt('SmartPass!@#'),
                'remember_token'=>0,
                'created_at'=>NOW(),
                'updated_at'=>NOW(),
            ]);
        DB::table('users')->insert(
            [
                'name'=>'Shweta Minde',
                'email'=>'shweta@carvingit.com',
                'password'=>bcrypt('SmartPass!@#'),
                'remember_token'=>0,
                'created_at'=>NOW(),
                'updated_at'=>NOW(),
            ]);
        DB::table('users')->insert(
            [
                'name'=>'Priyanka Jadhav',
                'email'=>'priyanka@carvingit.com',
                'password'=>bcrypt('SmartPass!@#'),
                'remember_token'=>0,
                'created_at'=>NOW(),
                'updated_at'=>NOW(),
            ]
        );


        // create default roles
        DB::table('roles')->insert(
            [
                'name'=>'admin'
            ]
        );

        // make first user admin
        DB::table('user_roles')->insert(
            [
                'user_id'=>1,
                'role_id'=>1
            ]
        );
    }
}
