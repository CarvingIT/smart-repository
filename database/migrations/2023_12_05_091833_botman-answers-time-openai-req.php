<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BotmanAnswersTimeOpenaiReq extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::table('botman_answers', function (Blueprint $table) {
	    $table->integer('answering_time')->nullable()->after('answer');
	    $table->integer('openai_req')->nullable()->after('answering_time');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	Schema::table('botman_answers', function (Blueprint $table) {
            $table->dropColumn('answering_time');
            $table->dropColumn('openai_req');
        });	
    }
}
