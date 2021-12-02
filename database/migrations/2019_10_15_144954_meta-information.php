<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MetaInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_fields', function(Blueprint $table){
            $table->increments('id');
            $table->integer('collection_id')->unsigned();
            $table->string('label');
            $table->string('placeholder');
            $table->enum('type', ['Text','Numeric', 'Select', 'Date']);
            $table->text('options')->nullable();
            $table->integer('display_order');
            $table->timestamps();
            $table->softDeletes();
        });
        //add foreign keys
        Schema::table('meta_fields', function(Blueprint $table){
            $table->foreign('collection_id')->references('id')->on('collections');
        });

        Schema::create('meta_field_values', function(Blueprint $table){
            $table->increments('id');
            $table->bigInteger('document_id')->unsigned();
            $table->integer('meta_field_id')->unsigned();
            $table->string('value', 255);
            $table->timestamps();
        });
        //add foreign keys
        Schema::table('meta_field_values', function(Blueprint $table){
            $table->foreign('meta_field_id')->references('id')->on('meta_fields');
            $table->foreign('document_id')->references('id')->on('documents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('meta_fields');
    }
}
