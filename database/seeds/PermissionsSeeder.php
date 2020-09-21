<?php

use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = array( 
            array('MAINTAINER', 'Administrator of a collection'),
            array('CREATE', 'Can create content'),
            array('EDIT_ANY', 'Can edit any content'),
            array('EDIT_OWN', 'Can edit own content'),
            array('DELETE_ANY', 'Can delete any content'),
            array('DELETE_OWN', 'Can delete own content'),
            array('VIEW', 'Can view content'),
	    array('APPROVE', 'Can approve content'),
        );
        // add permissions
        foreach($permissions as $p){
            DB::table('permissions')->insert(
                [
                'name'=>$p[0],
                'description'=>$p[1]
                ]
            );
        }
    }
}
