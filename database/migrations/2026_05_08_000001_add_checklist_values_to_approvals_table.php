<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChecklistValuesToApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->json('checklist_values')->nullable()->after('comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn('checklist_values');
        });
    }
}