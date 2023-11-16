<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApprovalsPolymorphic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->bigInteger('approvable_id');
			$table->string('approvable_type');
			$table->bigInteger('approved_by')->unsigned()->nullable();
			$table->integer('approved_by_role')->nullable();
			$table->tinyInteger('approval_status')->nullable();
			$table->text('comments')->nullable();
	    	$table->timestamps();

			// add foreign key
			$table->foreign('approved_by')->references('id')->on('users');
		});	
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approvals');
    }
}
