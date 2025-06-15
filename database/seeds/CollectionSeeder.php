<?php

use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('collections')->insert(
            [
                'name'=>'Misc documents',
                'description'=>'Miscellaneous documents',
                'type'=>'Public',
                'user_id'=>1,
                'storage_drive'=>'local',
		        'content_type'=>'Uploaded documents'
            ]
        );

        DB::table('collections')->insert(
            [
                'name'=>'Demo web resources',
                'description'=>'External web pages and documents',
                'type'=>'Public',
                'user_id'=>1,
                'storage_drive'=>'local',
        		'content_type'=>'Web resources'
            ]
        );

        DB::table('collections')->insert(
            [
                'name'=>'Private collection',
                'description'=>'Members only collection for testing',
                'type'=>'Members Only',
                'user_id'=>1,
                'storage_drive'=>'local',
		        'content_type'=>'Uploaded documents'
            ]
        );

	// set first user as the maintainer for these collections
        DB::table('user_permissions')->insert(
            [
		'user_id'=>1,
		'collection_id'=>1,
		'permission_id'=>1
	    ]
	);
        DB::table('user_permissions')->insert(
            [
		'user_id'=>1,
		'collection_id'=>2,
		'permission_id'=>1
	    ]
	);
        DB::table('user_permissions')->insert(
            [
		'user_id'=>1,
		'collection_id'=>3,
		'permission_id'=>1
	    ]
	);

    }
}
