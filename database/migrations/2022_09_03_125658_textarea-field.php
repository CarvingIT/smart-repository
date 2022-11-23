<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TextareaField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		
		DB::statement("ALTER TABLE meta_fields CHANGE type type enum('Text','Textarea','Numeric','Select','SelectCombo','Date')");
		DB::statement("ALTER TABLE meta_field_values CHANGE value value text");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
