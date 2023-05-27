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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('contact_zip',191)->nullable();
            $table->string('contact_phone1',191)->nullable();
            $table->string('contact_phone2',191)->nullable();
            $table->string('admin_email_id',191)->nullable();
            $table->string('general_email_id',191)->nullable();
            $table->string('contact_email_id',191)->nullable();
            $table->string('website_name',191)->nullable();
            $table->string('meta_title',100)->nullable();
            $table->string('meta_description',200)->nullable();
            $table->text('meta_keywords',200)->nullable();
            $table->text('instagram_link',191)->nullable();
            $table->enum('instagram_status',['on','off'])->default('on');
            $table->string('youtube_link',191)->nullable();
            $table->enum('youtube_status',['on','off'])->default('on');
            $table->string('facebook_link',191)->nullable();
            $table->enum('facebook_status',['on','off'])->default('on');
            $table->string('pinterest_link',191)->nullable();
            $table->enum('pinterest_status',['on','off'])->default('on');
            $table->string('twitter_link',191)->nullable();
            $table->enum('twitter_status',['on','off'])->default('on');
            $table->string('qq_link',191)->nullable();
            $table->enum('qq_status',['on','off'])->default('on');
            $table->string('skype_link',191)->nullable();
            $table->enum('skype_status',['on','off'])->default('on');
            $table->string('telegram_link',191)->nullable();
            $table->enum('telegram_status',['on','off'])->default('on');
            $table->string('whatsapp_link',191)->nullable();
            $table->enum('whatsapp_status',['on','off'])->default('on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
