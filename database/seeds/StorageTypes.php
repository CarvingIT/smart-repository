<?php

use Illuminate\Database\Seeder;

class StorageTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	$storage_types = array(
            array('Local'),
            array('AWS'),
        );
        // add storage types
        foreach($storage_types as $storage){
            DB::table('storage_types')->insert(
                [
                'storage_name'=>$storage[0],
                ]
            );
        }

    }
}
