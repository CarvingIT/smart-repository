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
        Schema::table('shared_links', function (Blueprint $table) {
            $table->enum('permission_level', ['view', 'download', 'both'])->default('both')->after('is_active');
            $table->text('description')->nullable()->after('permission_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shared_links', function (Blueprint $table) {
            $table->dropColumn(['permission_level', 'description']);
        });
    }
};
