<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubCategory;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subcategories = [
            [
                'id' => 1,
                'category_id' => 1,
                'parent_sub_category_id' => NULL,
                'name' => 'iPhone',
                'slug' => 'iphone',
                'description' => 'iPhone',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'parent_sub_category_id' => NULL,
                'name' => 'Andriod',
                'slug' => 'andriod',
                'description' => 'Andriod',
                'status' => 'active',
            ],
            [
                'id' => 3,
                'category_id' => 2,
                'parent_sub_category_id' => NULL,
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Apple',
                'status' => 'active',
            ],
            [
                'id' => 4,
                'category_id' => 2,
                'parent_sub_category_id' => NULL,
                'name' => 'Dell',
                'slug' => 'dell',
                'description' => 'Dell',
                'status' => 'active',
            ],
            [
                'id' => 5,
                'category_id' => 7,
                'parent_sub_category_id' => NULL,
                'name' => 'Womens Bag',
                'slug' => 'womens_bag',
                'description' => 'Womens Bag',
                'status' => 'active',
            ],
            [
                'id' => 6,
                'category_id' => 7,
                'parent_sub_category_id' => NULL,
                'name' => 'Mens Bags & Wallets',
                'slug' => 'mens_bags_wallets',
                'description' => 'Mens Bags & Wallets',
                'status' => 'active',
            ],
            [
                'id' => 7,
                'category_id' => 10,
                'parent_sub_category_id' => NULL,
                'name' => 'Mens Footwear',

                'slug' => 'mens_footwear',
                'description' => 'Mens Footwear',
                'status' => 'active',
            ],
            [
                'id' => 8,
                'category_id' => 10,
                'parent_sub_category_id' =>NULL,
                'name' => 'Women Footwear',
                'slug' => 'women_footwear',
                'description' => 'Women Footwear',
                'status' => 'active',
            ],
            [
                'id' => 9,
                'category_id' => 1,
                'parent_sub_category_id' => 1,
                'name' => 'iPhone9',
                'slug' => 'iPhone9',
                'description' => 'iPhone9',
                'status' => 'active',
            ],
            [
                'id' => 10,
                'category_id' => 1,
                'parent_sub_category_id' => 1,
                'name' => 'iPhone15',
                'slug' => 'iPhone15',
                'description' => 'iPhone15',
                'status' => 'active',
            ],

        ];
        foreach ($subcategories as $subcategory) {
            // filters
            SubCategory::updateOrcreate(['id' => $subcategory['id']], $subcategory);
        }
    }
}
