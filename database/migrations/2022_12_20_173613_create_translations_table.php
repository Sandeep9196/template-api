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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->integer('translationable_id');
            $table->string('translationable_type');
            $table->foreignId('language_id')->constrained();
            $table->string('field_name');
            $table->text('translation');
            $table->timestamps();
            $table->softDeletes();

            //$table->index(['translationable_id','translationable_type','language_id','translation']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translations');
    }
};
