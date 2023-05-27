<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banners = [
            [
                'id' => 1,
                'type'=>'homePage',
                //'name' => 'Banner1',
                'slug' => 'banner1',
                'link' => '#banner1',
                //'content' => 'Banner1',
                'position' => 1,
                'status' => 'active',
            ],
            [
                'id' => 2,
                'type'=>'homePage',
                //'name' => 'Banner2',
                'slug' => 'banner2',
                'link' => '#banner2',
                //'content' => 'Banner2',
                'position' => 2,
                'status' => 'active',
            ],
            [
                'id' => 3,
                'type'=>'homePage',
                //'name' => 'Banner3',
                'slug' => 'banner3',
                'link' => '#banner3',
                //'content' => 'Banner3',
                'position' => 3,
                'status' => 'active',
            ],
            [
                'id' => 4,
                'type'=>'homePage',
                //'name' => 'Banner4',
                'slug' => 'banner4',
                'link' => '#banner4',
                //'content' => 'Banner4',
                'position' => 4,
                'status' => 'active',
            ],
            [
                'id' => 5,
                'type'=>'categoryPage',
                //'name' => 'Banner5',
                'slug' => 'banner5',
                'link' => '#banner5',
                //'content' => 'Banner5',
                'position' => 5,
                'status' => 'active',
            ],
            [
                'id' => 6,
                'type'=>'categoryPage',
                //'name' => 'Banner6',
                'slug' => 'banner6',
                'link' => '#banner6',
                //'content' => 'Banner6',
                'position' => 6,
                'status' => 'active',
            ],
            [
                'id' => 7,
                'type'=>'categoryPage',
                //'name' => 'Banner7',
                'slug' => 'banner7',
                'link' => '#banner7',
                //'content' => 'Banner7',
                'position' => 7,
                'status' => 'active',
            ],
        ];
        foreach ($banners as $banner) {
            // filters
            Banner::updateOrcreate(['id' => $banner['id']], $banner);
        }
    }
}
