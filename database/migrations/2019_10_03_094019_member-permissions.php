<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function(Blueprint $table){
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('description');
        });

        Schema::create('user_permissions', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('collection_id');
            $table->unsignedInteger('permission_id');
        });

        Schema::table('user_permissions', function(Blueprint $table){
            $table->unique(['user_id','collection_id','permission_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('collection_id')->references('id')->on('collections');
            $table->foreign('permission_id')->references('id')->on('permissions');
            $table->timestamps();
            //$table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permissions');
    }
}
