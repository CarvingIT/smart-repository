<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
	    $table->unsignedBigInteger('document_id')->nullable();
	    $table->foreign('document_id')->references('id')->on('documents');
	    $table->unsignedBigInteger('approved_by')->nullable();
	    $table->foreign('approved_by')->references('id')->on('users');
	    $table->unsignedInteger('approved_by_role')->nullable();
	    $table->foreign('approved_by_role')->references('id')->on('roles');
	    $table->tinyInteger('approval_status')->default('0');
	    $table->longText('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_approvals');
    }
}
