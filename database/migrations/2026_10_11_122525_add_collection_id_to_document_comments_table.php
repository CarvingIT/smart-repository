<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollectionIdToDocumentCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_comments', function (Blueprint $table) {
            // Add collection_id so comments can be looked up / filtered per collection
            // and so the delete redirect works without querying the document first.
            if (!Schema::hasColumn('document_comments', 'collection_id')) {
                $table->unsignedInteger('collection_id')->nullable()->after('document_id');
                $table->foreign('collection_id')->references('id')->on('collections')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_comments', function (Blueprint $table) {
            $table->dropForeign(['collection_id']);
            $table->dropColumn('collection_id');
        });
    }
}
