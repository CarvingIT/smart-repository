<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExternalLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('documents', function (Blueprint $table) {
		    $table->string('external_link')->nullable()->after('text_content');
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
            $table->dropColumn('external_link');
        });	
    }
}
