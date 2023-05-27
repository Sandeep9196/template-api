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
        Schema::create('ontime_passwords', function (Blueprint $table) {
            $table->id();
            $table->string('idd');
            $table->string('phone_number');
            $table->string('value')->comment('OTP code');
            $table->string('type')->default('registration');
            $table->dateTime('expire_at')->nullable();
            $table->boolean('is_verify')->default(false);
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
        Schema::dropIfExists('ontime_passwords');
    }
};
