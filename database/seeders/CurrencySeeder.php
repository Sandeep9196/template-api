<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [

            [
                'id' => 1,
                'name' => 'Dollars',
                'code' => 'USD',
                'symbol' => '$',
                'status' => 'active',
            ],

            [
                'id' => 2,
                'name' => 'Riels',
                'code' => 'KHR',
                'symbol' => 'áŸ›',
                'status' => 'inactive',
            ],

            [
                'id' => 3,
                'name' => 'Yuan Renminbi',
                'code' => 'CNY',
                'symbol' => 'Â¥',
                'status' => 'inactive',
            ],

            [
                'id' => 4,
                'name' => 'Dong',
                'code' => 'VND',
                'symbol' => 'â‚«',
                'status' => 'inactive',
            ],
            [
                'id' => 5,
                'name' => 'Thai baht',
                'code' => 'THB',
                'symbol' => 'thb',
                'status' => 'inactive',
            ],
        ];
        foreach ($currencies as $currency) {
            Currency::updateOrcreate(['id' => $currency['id']], $currency);
        }
    }
}
