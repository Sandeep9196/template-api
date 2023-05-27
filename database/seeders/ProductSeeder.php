<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'id' => 1,
                'category_id' => 1,
                'sub_category_id' => 1,
                'sku' => 'MOBRED1',
                'slug' => 'Samsung_Galaxy_M04_Dark_Blue',
                'quantity' => 1,

                'meta_title' => 'Mobile',
                'meta_description' => 'Mobile',
                'meta_keywords' => 'Mobile',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'sub_category_id' => 1,
                'sku' => 'MOBRED2',
                'slug' => 'iQOO_Z6_Lite_5G',
                'quantity' => 1,

                'meta_title' => 'Mobile',
                'meta_description' => 'Mobile',
                'meta_keywords' => 'Mobile',
                'status' => 'active',
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED3',
                'slug' => 'OnePlus_Nord_CE_2_Lite_5G',
                'quantity' => 1,

                'meta_title' => 'Mobile',
                'meta_description' => 'Mobile',
                'meta_keywords' => 'Mobile',
                'status' => 'active',
            ],
            [
                'id' => 4,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED4',
                'slug' => 'OPPO_A74_5G',
                'quantity' => 1,

                'meta_title' => 'Mobile',
                'meta_description' => 'Mobile',
                'meta_keywords' => 'Mobile',
                'status' => 'active',
            ],
            [
                'id' => 5,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED5',
                'slug' => 'OPPO_A74_4G',
                'quantity' => 1,

                'meta_title' => 'Mobile',
                'meta_description' => 'Mobile',
                'meta_keywords' => 'Mobile',
                'status' => 'active',
            ],
            [
                'id' => 6,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED6',
                'slug' => 'samsung_galaxy',
                'quantity' => 1,

                'meta_title' => 'Mobile',
                'meta_description' => 'Mobile',
                'meta_keywords' => 'Mobile',
                'status' => 'active',
            ],
            [
                'id' => 7,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED7',
                'slug' => 'Redmi_11_Prime 5G',
                'quantity' => 1,

                'meta_title' => 'Redmi 11 Prime 5G',
                'meta_description' => 'Redmi 11 Prime 5G',
                'meta_keywords' => 'Redmi 11 Prime 5G',
                'status' => 'active',
            ],
            [
                'id' => 8,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED8',
                'slug' => 'Redmi_10_Power',
                'quantity' => 1,

                'meta_title' => 'Redmi 10 Power',
                'meta_description' => 'Redmi 10 Power',
                'meta_keywords' => 'Redmi 10 Power',
                'status' => 'active',
            ],
            [
                'id' => 9,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED9',
                'slug' => 'Tecno_Spark_9',
                'quantity' => 1,

                'meta_title' => 'Tecno Spark 9',
                'meta_description' => 'Tecno Spark 9',
                'meta_keywords' => 'Tecno Spark 9',
                'status' => 'active',
            ],
            [
                'id' => 10,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED10',
                'slug' => 'Redmi_10A_Sport',
                'quantity' => 1,

                'meta_title' => 'Redmi 10A Sport',
                'meta_description' => 'Redmi 10A Sport',
                'meta_keywords' => 'Redmi 10A Sport',
                'status' => 'active',
            ],
            [
                'id' => 11,
                'category_id' => 1,
                'sub_category_id' => 2,
                'sku' => 'MOBRED11',
                'slug' => 'iQOO_Z6_Lite_5G',
                'quantity' => 1,

                'meta_title' => 'iQOO Z6 Lite 5G',
                'meta_description' => 'iQOO Z6 Lite 5G',
                'meta_keywords' => 'iQOO Z6 Lite 5G',
                'status' => 'active',
            ],
            [
                'id' => 12,
                'category_id' => 2,
                'sub_category_id' => 2,
                'sku' => 'MOBRED12',
                'slug' => 'Samsung_Galaxy_M13',
                'quantity' => 1,

                'meta_title' => 'Samsung Galaxy M13',
                'meta_description' => 'Samsung Galaxy M13',
                'meta_keywords' => 'Samsung Galaxy M13',
                'status' => 'active',
            ],
            [
                'id' => 13,
                'category_id' => 2,
                'sub_category_id' => 2,
                'sku' => 'MOBRED13',
                'slug' => 'OnePlus_Nord_2T_5G',
                'quantity' => 1,

                'meta_title' => 'OnePlus Nord 2T 5G',
                'meta_description' => 'OnePlus Nord 2T 5G',
                'meta_keywords' => 'OnePlus Nord 2T 5G',
                'status' => 'active',
            ],
            [
                'id' => 14,
                'category_id' => 2,
                'sub_category_id' => 2,
                'sku' => 'MOBRED14',
                'slug' => 'Redmi_K50i_5G',
                'quantity' => 1,

                'meta_title' => 'Redmi K50i 5G',
                'meta_description' => 'Redmi K50i 5G',
                'meta_keywords' => 'Redmi K50i 5G',
                'status' => 'active',
            ],
            [
                'id' => 15,
                'category_id' => 2,
                'sub_category_id' => 2,
                'sku' => 'MOBRED15',
                'slug' => 'iKALL_z12_smartphone',
                'quantity' => 1,

                'meta_title' => 'IKALL Z12 Smartphone',
                'meta_description' => 'IKALL Z12 Smartphone',
                'meta_keywords' => 'IKALL Z12 Smartphone',
                'status' => 'active',
            ],


        ];
        foreach ($products as $product) {
            // filters
            Product::updateOrcreate(['id' => $product['id']], $product);
        }
    }
}
