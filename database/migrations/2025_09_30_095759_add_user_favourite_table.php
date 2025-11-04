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
        Schema::create("user_favourite", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("user_id")->unsigned();
            $table->bigInteger("document_id")->unsigned();
            $table->timestamps();

            // Add foreign key constraints
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign("document_id")->references("id")->on("documents")->onDelete("cascade");

            // Ensure a user can't favourite the same document multiple times
            $table->unique(["user_id", "document_id"]);

            // Add indexes for faster lookups
            $table->index("user_id");
            $table->index("document_id");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("user_favourite");
    }
};
