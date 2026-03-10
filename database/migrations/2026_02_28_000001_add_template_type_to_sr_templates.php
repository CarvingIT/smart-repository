<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTemplateTypeToSrTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sr_templates', function (Blueprint $table) {
            $table->string('template_type')->default('search_result')->after('template_name');
            // template_type values: 'search_result' | 'details_page'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sr_templates', function (Blueprint $table) {
            $table->dropColumn('template_type');
        });
    }
}
