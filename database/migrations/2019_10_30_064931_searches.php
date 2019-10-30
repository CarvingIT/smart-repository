<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Searches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->integer('collection_id');
            $table->string('search_query')->nullable();
            $table->string('meta_query')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('results');
            $table->timestamp('added_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('searches');
    }
}
