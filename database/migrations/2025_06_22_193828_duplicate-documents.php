<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duplicates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('document_id');
            $table->text('duplicates');
            $table->timestamps();
        });

        Schema::table('documents', function (Blueprint $table) {
          $table->string('hash', 64)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('duplicates');
        Schema::table('documents', function (Blueprint $table) {
          $table->dropColumn('hash');
        });
    }
};
