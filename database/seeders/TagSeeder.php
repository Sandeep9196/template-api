<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Tag;


class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            [
                'id' => 1,
                'name' => 'Mobile',
                'slug' => 'mobile',
                'status' => 'active',
            ],
            [
                'id' => 2,
                'name' => 'Laptop',
                'slug' => 'laptop',
                'status' => 'active',
            ],

        ];
        foreach ($tags as $tag) {
            // filters
            Tag::updateOrcreate(['id' => $tag['id']], $tag);
        }
    }
}
