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
        if (Schema::hasTable('translations')) {
            Schema::table('translations', function (Blueprint $table) {
                $table->tinyInteger('purpose')->default(0)->after('translation')->comment("1:English,2:Chinese,3:Khmer,4:Vietnamese,5:Thai");
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
        if (Schema::hasTable('translations')) {
            Schema::table('translations', function (Blueprint $table) {
                $table->dropColumn('purpose');
            });
        }
    }
};
