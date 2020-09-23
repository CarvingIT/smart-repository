<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Urls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('collection_id')->unsigned();
            $table->string('title')->nullable();
            $table->string('url')->unique();
            $table->string('type');
            $table->longText('text_content')->nullable();
	    $table->binary('browsershot')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        //add foreign keys
        Schema::table('urls', function(Blueprint $table){
            $table->foreign('collection_id')->references('id')->on('collections');
        });

       // Full Text Index
       DB::statement('ALTER TABLE documents ADD FULLTEXT fulltext_index_urls (title, text_content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('urls');
    }
}
