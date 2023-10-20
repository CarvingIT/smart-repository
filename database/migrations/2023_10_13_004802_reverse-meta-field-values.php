<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReverseMetaFieldValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reverse_meta_field_values', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->integer('meta_field_id');
			$table->string('meta_value');
	    	$table->unsignedBigInteger('document_id');
        	//$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reverse_meta_field_values');
    }
}
