<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpideredDomainIdInDesiredUrls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('desired_urls', function (Blueprint $table) {
            //
	$table->integer('spidered_domain_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('desired_urls', function (Blueprint $table) {
            //
	$table->dropColumn('spidered_domain_id');
        });
    }
}
