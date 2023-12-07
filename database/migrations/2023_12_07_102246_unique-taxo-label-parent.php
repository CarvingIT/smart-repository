<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UniqueTaxoLabelParent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::table('taxonomies', function (Blueprint $table) {
	    $table->unique(['label','parent_id']);
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('taxonomies', function (Blueprint $table) {
		$table->dropUnique(['label','parent_id']);
		});
    }
}
