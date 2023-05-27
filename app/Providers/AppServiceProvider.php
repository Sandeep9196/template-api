<?php

namespace App\Providers;

use App\Models\Language;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (Schema::hasTable('languages')) {
            //set localization for each request from user with different language
            $requestLang = request('lang_id');
            $languageLocale  = @Language::find($requestLang)->locale_web;
            if(in_array($languageLocale,['en','ch','kh','vt','th'])){
                app()->setLocale($languageLocale);
            }
            else if(in_array(request('locale'),['en','ch','kh','vt','th'])){
                app()->setLocale(request('locale'));
            }

        }
    }
}
