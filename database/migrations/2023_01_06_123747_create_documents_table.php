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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('documentable_id')->comment('reference to document table');
            $table->string('documentable_type')->comment('reference to document table');
            $table->string('path')->comment('reference to document table');
            $table->string('file_type')->comment('reference to document table');
            $table->string('file_name')->comment('reference to document table');
            $table->timestamps();
            $table->softDeletes();

            //$table->index(['documentable_id','documentable_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
