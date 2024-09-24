<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSRTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	Schema::create('sr_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('template_name');
            $table->longText('html_code')->nullable();
            $table->unsignedInteger('collection_id');
            $table->timestamps();

            $table->foreign('collection_id')->references('id')->on('collections');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sr_templates');
    }
}
