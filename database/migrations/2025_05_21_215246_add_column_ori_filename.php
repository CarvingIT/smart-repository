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
        Schema::table('documents', function (Blueprint $table) {
            //
            $table->text('ori_filename')->nullable();
        });
        Schema::table('document_revisions', function (Blueprint $table) {
            //
            $table->text('path')->change()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            //
            $table->dropColumn('ori_filename');
        });
        Schema::table('document_revisions', function (Blueprint $table) {
            //
            $table->string('path');
        });
    }
};
