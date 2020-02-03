<?php

use Illuminate\Database\Seeder;

class NewPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
$permissions = array(
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
