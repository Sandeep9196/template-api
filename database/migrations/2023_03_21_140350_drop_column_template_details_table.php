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
            $table->dropColumn('website_title');
            $table->dropColumn('website_description');
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('company_name');
            $table->dropColumn('copy_right');
        });
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('address_line');
            $table->dropColumn('address_line_2');
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
            $table->string('website_title')->nullable();
            $table->string('website_description')->nullable();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->string('company_name');
            $table->string('copy_right')->nullable();
        });
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('address_line');
            $table->string('address_line_2')->nullable();
        });
    }
};
