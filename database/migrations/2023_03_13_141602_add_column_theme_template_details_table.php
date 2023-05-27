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
        Schema::table('template_details', function (Blueprint $table) {
            $table->dropColumn('primary_colour');
            $table->dropColumn('secondary_colour');

        });
        Schema::table('template_details', function (Blueprint $table) {
            $table->string('theme')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_details', function (Blueprint $table) {
            $table->string('primary_colour');
            $table->string('secondary_colour');
            $table->dropColumn('theme');
        });
    }
};
