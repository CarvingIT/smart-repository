<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MailMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailmessages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mailbox_id')->unsigned();
            $table->string('message_id');
            $table->timestamps();

            $table->foreign('mailbox_id')->references('id')->on('collectionmailboxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailmessages');
    }
}
