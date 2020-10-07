<?php

use Illuminate\Database\Seeder;

class SysconfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //add system related details
	DB::table('sysconfig')->insert(
            [
                'param'=>'company_logo',
                'value'=>'/i/logo_site_name1.png',
                'created_at'=>NOW(),
                'updated_at'=>NOW(),
            ]
        );
    }
}
