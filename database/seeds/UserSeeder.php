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
                'password'=>bcrypt('ketan123'),
            ]
        );
        
        DB::table('users')->insert(
            [
                'name'=>'Shraddha Kulkarni',
                'email'=>'shraddha@carvingit.com',
                'password'=>bcrypt('shraddha123'),
            ]);
        DB::table('users')->insert(
            [
                'name'=>'Rutuja Bhoyar',
                'email'=>'rutuja@carvingit.com',
                'password'=>bcrypt('rutuja123'),
            ]);
        DB::table('users')->insert(
            [
                'name'=>'Rupali Patil',
                'email'=>'rupali@carvingit.com',
                'password'=>bcrypt('rupali123'),
            ]);
        DB::table('users')->insert(
            [
                'name'=>'Shweta Minde',
                'email'=>'shweta@carvingit.com',
                'password'=>bcrypt('shweta123'),
            ]);
        DB::table('users')->insert(
            [
                'name'=>'Priyanka Jadhav',
                'email'=>'priyanka@carvingit.com',
                'password'=>bcrypt('priyanka123'),
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
