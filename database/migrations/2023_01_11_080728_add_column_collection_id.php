<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCollectionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_comments', function (Blueprint $table) {
            //
		$table->unsignedBigInteger('collection_id')->nullable();
		$table->foreign('collection_id')->references('id')->on('collections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_comments', function (Blueprint $table) {
            //
		$table->dropColumn('collection_id');
        });
    }
}
