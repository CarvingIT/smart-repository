<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CollectionContentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::table('collections', function (Blueprint $table) {
  	  $table->enum('content_type', ['Uploaded documents', 'Web resources'])->default('Uploaded documents');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	Schema::table('collections', function (Blueprint $table) {
    	$table->dropColumn('content_type');
	});
    }
}
