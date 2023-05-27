<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Mobile & Accessories',
                'slug' => 'mobile_accessories',
                'description' => 'Mobile & Accessories',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Laptop',
                'slug' => 'laptop',
                'description' => 'mobile',
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => 'Health & Beauty',
                'slug' => 'health_beauty',
                'description' => 'Health & Beauty',
                'status' => 'active',
            ],
            [
                'id' => 4,
                'name' => 'Baby & Toys',
                'slug' => 'baby_boys',
                'description' => 'Baby & Toys',
                'status' => 'active',
            ],
            [
                'id' => 5,
                'name' => 'Groceries & Pets',
                'slug' => 'groceries_pets',
                'description' => 'Groceries & Pets',
                'status' => 'active',
            ],
            [
                'id' => 6,
                'name' => 'Automotive',
                'slug' => 'automotive',
                'description' => 'Automotive',
                'status' => 'active',
            ],
            [
                'id' => 7,
                'name' => 'Bag',
                'slug' => 'bag',
                'description' => 'Bag',
                'status' => 'active',
            ],
            [
                'id' => 8,
                'name' => 'Watches',
                'slug' => 'watches',
                'description' => 'Watches',
                'status' => 'active',
            ],
            [
                'id' => 9,
                'name' => 'Cosmetic Product',
                'slug' => 'cosmetic_product',
                'description' => 'Cosmetic Product',
                'status' => 'active',
            ],
            [
                'id' => 10,
                'name' => 'Footwear',
                'slug' => 'footwear',
                'description' => 'Footwear',
                'status' => 'active',
            ],

        ];
        foreach ($categories as $category) {
            // filters
            Category::updateOrcreate(['id' => $category['id']], $category);
        }
    }
}
