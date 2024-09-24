<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionSrtemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sr_templates', function (Blueprint $table) {
            //
		$table->longText('description')->nullable();
		$table->unique(['template_name','collection_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sr_templates', function (Blueprint $table) {
            //
		$table->dropColumn('description');
        });
    }
}
