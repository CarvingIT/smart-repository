<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaFieldSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('meta_field_series')) {
            Schema::create('meta_field_series', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('meta_field_id')->index();
                $table->string('prefix')->nullable();
                $table->string('date_format')->nullable();
                $table->unsignedBigInteger('next_sequence')->default(1);
                $table->integer('pad_length')->default(0);
                $table->string('reset_period')->nullable(); // e.g., none, monthly, yearly (future use)
                $table->timestamps();

                $table->foreign('meta_field_id')->references('id')->on('meta_fields')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meta_field_series');
    }
}
