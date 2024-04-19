<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CollectionNameParentUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('collections', function (Blueprint $table) {
			$table->dropUnique('collections_name_unique');
			//$table->unique(['name','parent_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::create('collections', function (Blueprint $table) {
			$table->unique(['name']);
		});
    }
}
