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
        Schema::create('template_details', function (Blueprint $table) {
            $table->id();
            $table->string('website_title')->nullable();
            $table->string('website_description')->nullable();
            $table->string('primary_colour')->nullable();
            $table->string('secondary_colour')->nullable();
            $table->string('banner_style')->nullable();
            $table->foreignId('template_id')->constrained()->nullable();
            $table->foreignId('company_id')->constrained()->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('template_details');
    }
};
