<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\ProductPromotion;

class ProductPromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product_promotions = [
            [
                'product_id' => 1,
                'promotion_id' => 1,
            ],
            [
                'product_id' => 2,
                'promotion_id' => 2,
            ],
            [
                'product_id' => 3,
                'promotion_id' => 3,
            ],
            [
                'product_id' => 4,
                'promotion_id' => 4,
            ],
            [
                'product_id' => 5,
                'promotion_id' => 1,
            ],
            [
                'product_id' => 6,
                'promotion_id' => 1,
            ],
            [
                'product_id' => 7,
                'promotion_id' => 1,
            ],

        ];
        foreach ($product_promotions as $product_promotion) {
            // filters
            ProductPromotion::updateOrcreate($product_promotion);
        }
    }
}
