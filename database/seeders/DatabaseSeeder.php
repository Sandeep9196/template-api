<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Artisan::call('storage:link');

        $this->call([
            LanguageSeeder::class,
           // CategorySeeder::class,
           // SubCategorySeeder::class,
            //FileSeeder::class,
            //TranslationSeeder::class,
         //   PromotionSeeder::class,
           // TagSeeder::class,
            BannerSeeder::class,
            CurrencySeeder::class,
          //  ProductSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
         //   ProductCurrencySeeder::class,
         //   ProductPromotionSeeder::class,
           // ProductTagSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminSeeder::class
        ]);
    }
}
