<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocumentRevisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_revisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('document_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();
            $table->string('path');
            $table->string('type');
            $table->bigInteger('size');
            $table->timestamps();
            $table->softDeletes();
        });
        //add foreign keys
        Schema::table('document_revisions', function(Blueprint $table){
            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_revisions');
    }
}
