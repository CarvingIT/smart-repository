<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Similarities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('similar_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
	    $table->bigInteger('document_id');
	    $table->bigInteger('target_document_id');
	    $table->double('cosine_similarity', 10, 9);
	    $table->timestamp('source_updated_at')->nullable();
	    $table->timestamp('target_updated_at')->nullable();
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('similar_documents');
    }
}
