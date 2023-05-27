<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            ['id' => '1', 'name' => 'China', 'iso' => 'CN', 'iso3' => 'CHN', 'idd' => '86', 'flag_url' => 'flags/cn.png', 'status' => 'active', 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'nationality' => ''],
            ['id' => '2', 'name' => 'Cambodia', 'iso' => 'KH', 'iso3' => 'KHM', 'idd' => '855', 'flag_url' => 'flags/kh.png', 'status' => 'active', 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'nationality' => ''],
            ['id' => '3', 'name' => 'Viet Nam', 'iso' => 'VN', 'iso3' => 'VNM', 'idd' => '84', 'flag_url' => 'flags/vn.png', 'status' => 'active', 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'nationality' => ''],
            ['id' => '4', 'name' => 'Thailand', 'iso' => 'TH', 'iso3' => 'THA', 'idd' => '66', 'flag_url' => 'flags/th.png', 'status' => 'active', 'created_at' => null, 'updated_at' => null, 'deleted_at' => null, 'nationality' => ''],
                    ];
        foreach ($countries as $country)
        {
            Country::updateOrcreate(['id' => $country['id']], $country);
        }
    }
}
