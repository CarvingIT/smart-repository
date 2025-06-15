<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BotmanAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('botman_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->text('question')->nullable();
			$table->text('keywords')->nullable();
			$table->text('answer')->nullable();
        	$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('botman_answers');
    }
}
