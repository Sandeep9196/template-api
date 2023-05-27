<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductTag;

class ProductTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product_tags = [
            [
                'product_id' => 1,
                'tag_id' => 1,
            ],
            [
                'product_id' => 2,
                'tag_id' => 1,
            ],
            [
                'product_id' => 3,
                'tag_id' => 1,
            ],
            [
                'product_id' => 4,
                'tag_id' => 1,
            ],
            [
                'product_id' => 5,
                'tag_id' => 1,
            ],
            [
                'product_id' => 6,
                'tag_id' => 1,
            ],
            [
                'product_id' => 7,
                'tag_id' => 1,
            ],

        ];
        foreach ($product_tags as $product_tag) {
            // filters
            ProductTag::updateOrcreate($product_tag);
        }
    }
}
