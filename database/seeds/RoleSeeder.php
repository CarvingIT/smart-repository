<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // add the admin role in the roles table
        DB::table('roles')->insert(
            [
                'name'=>'admin'
            ]
        );
    }
}
