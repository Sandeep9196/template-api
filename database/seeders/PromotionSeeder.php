<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Promotion;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $promotions = [
            [
                'id' => 1,
                'name' => 'Hot Deals',
                'slug' => 'hot_deals',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Top Deals',
                'slug' => 'top_deal',
                'status' => 'active',
            ],
            [
                'id' => 3,
                'name' => 'Trending Now',
                'slug' => 'trending_now',
                'status' => 'active',
            ],
            [
                'id' => 4,
                'name' => 'Latest',
                'slug' => 'latest',
                'status' => 'active',
            ],


        ];
        foreach ($promotions as $promotion) {
            // filters
            Promotion::updateOrcreate(['id' => $promotion['id']], $promotion);
        }
    }
}
