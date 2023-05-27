<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\File;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $files = [
            [
                'id' => 1,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/mobile.png',
                'type' => 'image',
            ],
            [
                'id' => 2,
                'fileable_id' => 2,
                'fileable_type' => 'App\Models\Category',
                'path' =>  'images/categories/laptop.png',
                'type' => 'image',
            ],
            [
                'id' => 3,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\SubCategory',
                'path' =>  'images/subcategories/iphone.png',
                'type' => 'image',
            ],
            [
                'id' => 4,
                'fileable_id' => 2,
                'fileable_type' => 'App\Models\SubCategory',
                'path' =>  'images/subcategories/andriod.png',
                'type' => 'image',
            ],
            [
                'id' => 5,
                'fileable_id' => 3,
                'fileable_type' => 'App\Models\SubCategory',
                'path' =>  'images/subcategories/apple.png',
                'type' => 'image',
            ],
            [
                'id' => 6,
                'fileable_id' => 4,
                'fileable_type' => 'App\Models\SubCategory',
                'path' =>  'images/subcategories/dell.png',
                'type' => 'image',
            ],
            [
                'id' => 7,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Banner',
                'path' =>  'images/banners/banner.png',
                'type' => 'image',
            ],
            [
                'id' => 8,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/1.png',
                'type' => 'image',
            ],
            [
                'id' => 9,
                'fileable_id' => 2,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/2.png',
                'type' => 'image',
            ],
            [
                'id' => 10,
                'fileable_id' => 3,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/3.png',
                'type' => 'image',
            ],
            [
                'id' => 11,
                'fileable_id' => 4,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/4.png',
                'type' => 'image',
            ],
            [
                'id' => 12,
                'fileable_id' => 3,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/health_beauty.png',
                'type' => 'image',
            ],
            [
                'id' => 13,
                'fileable_id' => 4,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/baby_boys.png',
                'type' => 'image',
            ],
            [
                'id' => 14,
                'fileable_id' => 5,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/groceries_pets.png',
                'type' => 'image',
            ],
            [
                'id' => 15,
                'fileable_id' => 6,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/automotive.png',
                'type' => 'image',
            ],
            [
                'id' => 16,
                'fileable_id' => 7,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/bag.png',
                'type' => 'image',
            ],
            [
                'id' => 17,
                'fileable_id' => 8,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/watches.png',
                'type' => 'image',
            ],
            [
                'id' => 18,
                'fileable_id' => 9,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/cosmetic_product.png',
                'type' => 'image',
            ],
            [
                'id' => 19,
                'fileable_id' => 10,
                'fileable_type' => 'App\Models\Category',
                'path' => 'images/categories/footwear.png',
                'type' => 'image',
            ],
            [
                'id' => 20,
                'fileable_id' => 5,
                'fileable_type' => 'App\Models\SubCategory',
                'path' =>  'images/subcategories/womens_bag.png',
                'type' => 'image',
            ],
            [
                'id' => 21,
                'fileable_id' => 6,
                'fileable_type' => 'App\Models\SubCategory',
                'path' =>  'images/subcategories/mens_bags_wallets.png',
                'type' => 'image',
            ],
            [
                'id' => 22,
                'fileable_id' => 7,
                'fileable_type' => 'App\Models\SubCategory',
                'path' =>  'images/subcategories/mens_footwear.png',
                'type' => 'image',
            ],
            [
                'id' => 23,
                'fileable_id' => 8,
                'fileable_type' => 'App\Models\SubCategory',
                'path' =>  'images/subcategories/women_footwear.png',
                'type' => 'image',
            ],
            [
                'id' => 24,
                'fileable_id' => 5,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/5.png',
                'type' => 'image',
            ],
            [
                'id' => 25,
                'fileable_id' => 6,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/6.png',
                'type' => 'image',
            ],
            [
                'id' => 26,
                'fileable_id' => 7,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/7.png',
                'type' => 'image',
            ],
            [
                'id' => 27,
                'fileable_id' => 8,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/8.png',
                'type' => 'image',
            ],
            [
                'id' => 28,
                'fileable_id' => 9,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/9.png',
                'type' => 'image',
            ],
            [
                'id' => 29,
                'fileable_id' => 10,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/10.png',
                'type' => 'image',
            ],
            [
                'id' => 30,
                'fileable_id' => 11,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/11.png',
                'type' => 'image',
            ],
            [
                'id' => 31,
                'fileable_id' => 12,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/12.png',
                'type' => 'image',
            ],
            [
                'id' => 32,
                'fileable_id' => 13,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/13.png',
                'type' => 'image',
            ],
            [
                'id' => 33,
                'fileable_id' => 14,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/14.png',
                'type' => 'image',
            ],
            [
                'id' => 34,
                'fileable_id' => 15,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/15.png',
                'type' => 'image',
            ],
            [
                'id' => 35,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/1-1.png',
                'type' => 'image',
            ],
            [
                'id' => 36,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/1-2.png',
                'type' => 'image',
            ],
            [
                'id' => 37,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/1-3.png',
                'type' => 'image',
            ],
            [
                'id' => 38,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/1-4.png',
                'type' => 'image',
            ],
            [
                'id' => 39,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/1-5.png',
                'type' => 'image',
            ],
            [
                'id' => 40,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/1-6.png',
                'type' => 'image',
            ],
            [
                'id' => 41,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Product',
                'path' =>  'images/products/1-7.png',
                'type' => 'image',
            ],

            [
                'id' => 42,
                'fileable_id' => 1,
                'fileable_type' => 'App\Models\Banner',
                'path' =>  'images/banners/banner1.jpg',
                'type' => 'image',
            ],
            [
                'id' => 43,
                'fileable_id' => 2,
                'fileable_type' => 'App\Models\Banner',
                'path' =>  'images/banners/banner2.jpg',
                'type' => 'image',
            ],
            [
                'id' => 44,
                'fileable_id' => 3,
                'fileable_type' => 'App\Models\Banner',
                'path' =>  'images/banners/banner3.jpg',
                'type' => 'image',
            ],
            [
                'id' => 45,
                'fileable_id' => 4,
                'fileable_type' => 'App\Models\Banner',
                'path' =>  'images/banners/banner4.jpg',
                'type' => 'image',
            ],
            [
                'id' => 46,
                'fileable_id' => 5,
                'fileable_type' => 'App\Models\Banner',
                'path' =>  'images/banners/category-banner.jpg',
                'type' => 'image',
            ],
            [
                'id' => 47,
                'fileable_id' => 6,
                'fileable_type' => 'App\Models\Banner',
                'path' =>  'images/banners/category-banner.jpg',
                'type' => 'image',
            ],
            [
                'id' => 48,
                'fileable_id' => 7,
                'fileable_type' => 'App\Models\Banner',
                'path' =>  'images/banners/category-banner.jpg',
                'type' => 'image',
            ],

        ];
        foreach ($files as $file) {
            // filters
            File::updateOrcreate(['id' => $file['id']], $file);
        }
    }
}
