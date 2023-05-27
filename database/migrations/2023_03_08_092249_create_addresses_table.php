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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->comment('reference to countries table');
            $table->foreignId('state_id')->nullable()->constrained()->comment('reference to states table');
            $table->foreignId('city_id')->nullable()->constrained()->comment('reference to cities table');
            $table->string('address_line');
            $table->string('address_line_2')->nullable();
            $table->integer('zipcode');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
