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
        Schema::table('meta_fields', function (Blueprint $table) {
            //
            $table->tinyInteger('with_rich_text_editor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meta_fields', function (Blueprint $table) {
            //
            $table->dropColumn('with_rich_text_editor');
        });
    }
};
