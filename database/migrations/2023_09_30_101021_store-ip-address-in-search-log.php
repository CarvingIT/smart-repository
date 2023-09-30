<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StoreIpAddressInSearchLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('searches', function (Blueprint $table) {
		    $table->ipAddress('ip_address')->after('results');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('searches', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });	
    }
}
