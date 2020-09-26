<?php

use Illuminate\Database\Seeder;

class SpideredDomainsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('spidered_domains')->insert(
	[
		'collection_id'=>2,
		'web_address'=>'http://firstray.in'
	]
	);

        DB::table('spidered_domains')->insert(
	[
		'collection_id'=>2,
		'web_address'=>'http://carvingit.com'
	]
	);

        DB::table('spidered_domains')->insert(
	[
		'collection_id'=>2,
		'web_address'=>'https://grubba.net'
	]
	);
    }
}
