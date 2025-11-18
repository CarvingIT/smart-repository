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
        Schema::table('collections', function (Blueprint $table) {
            $table->bigInteger("document_count")->default(0);
            $table->bigInteger("size_active")->default(0);
            $table->bigInteger("size_revisions")->default(0);
            $table->bigInteger("size_deleted")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn(['document_count', 'size_active', "size_revisions", 'size_deleted']);
        });
    }
};
