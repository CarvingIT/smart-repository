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
	DB::table('sysconfig')->insert(
            [
                'param'=>'logo_url',
                'value'=>'/i/logo_site_name1.png',
            ]
        );
    }
}
