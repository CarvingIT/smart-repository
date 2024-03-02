<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RelatedDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_documents', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('document_id');
            $table->bigInteger('related_document_id');
            $table->integer('display_order')->nullable();
            $table->bigInteger('parent')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();

			$table->unique(['document_id', 'related_document_id']);
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_documents');
    }
}
