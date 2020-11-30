<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpideredDomainIdInUrlSuppressions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('url_suppressions', function (Blueprint $table) {
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
        Schema::table('url_suppressions', function (Blueprint $table) {
            //
	$table->dropColumn('spidered_domain_id')->nullable();
        });
    }
}
