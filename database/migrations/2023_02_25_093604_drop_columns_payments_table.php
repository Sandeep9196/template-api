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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('request_data');
                $table->dropColumn('response_data');
                $table->dropColumn('final_response');
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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->json('request_data')->nullable()->comment("the data that will be sent to pay in third party payment");
                $table->json('response_data')->nullable()->comment("the data that is the response from third party payment");
                $table->json('final_response')->nullable()->comment("the final response that will be sent from third party payment");
            });
        }
    }
};
