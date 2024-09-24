<?php

use Illuminate\Database\Seeder;

class SearchResultTemplate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
	DB::table('sr_templates')->insert(
            [
                'template_name'=>'Search Result',
                'html_code'=>'<div class="col-lg-__width__">
                                        <div class="__classname__">&nbsp;</div>
                                                __document_meta_value__
                                        </div>',
                'collection_id'=>1,
                'created_at'=>NOW(),
                'updated_at'=>NOW(),
            ]
        );
    }
}
