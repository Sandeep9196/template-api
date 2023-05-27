<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCurrency;

class ProductCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product_currencies = [
            [
                'id' => 1,
                'product_id' => 1,
                'currency_id' => 1,
                'price' => 100,
                'sale_price' => 110,
                'purchase_price' => 105,
            ],
            [
                'id' => 2,
                'product_id' => 1,
                'currency_id' => 2,
                'price' => 150,
                'sale_price' => 160,
                'purchase_price' => 155,
            ],
            [
                'id' => 3,
                'product_id' => 1,
                'currency_id' => 3,
                'price' => 160,
                'sale_price' => 170,
                'purchase_price' => 165,
            ],
            [
                'id' => 4,
                'product_id' => 1,
                'currency_id' => 4,
                'price' => 200,
                'sale_price' => 230,
                'purchase_price' => 210,
            ],
            [
                'id' => 5,
                'product_id' => 1,
                'currency_id' => 5,
                'price' => 300,
                'sale_price' => 330,
                'purchase_price' => 310,
            ],

        ];
        foreach ($product_currencies as $product_currency) {
            // filters
            ProductCurrency::updateOrcreate(['id' => $product_currency['id']], $product_currency);
        }
    }
}
