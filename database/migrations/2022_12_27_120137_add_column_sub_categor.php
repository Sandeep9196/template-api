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
        if (Schema::hasTable('sub_categories')) {
            Schema::table('sub_categories', function (Blueprint $table) {
                $table->bigInteger('parent_sub_category_id')->nullable()->unsigned()->after('category_id');
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
        if (Schema::hasTable('sub_categories')) {
            Schema::table('sub_categories', function (Blueprint $table) {
                $table->dropColumn('parent_sub_category_id');
            });
        }
    }
};
