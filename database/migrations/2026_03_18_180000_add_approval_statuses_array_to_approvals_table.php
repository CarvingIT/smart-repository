<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalStatusesArrayToApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds a JSON field to store approval status for all roles in the approval workflow chain.
     * Structure: {
     *   "role_id_1": {"status": null|0|1, "approved_at": "timestamp", "approved_by_user_id": user_id},
     *   "role_id_2": {"status": null|0|1, "approved_at": "timestamp", "approved_by_user_id": user_id},
     *   ...
     * }
     * 
     * @return void
     */
    public function up()
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->json('approval_statuses')->nullable()->comment('Array of approval statuses for all roles in the approval chain');
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
            $table->dropColumn('approval_statuses');
        });
    }
}
