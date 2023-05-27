<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $cities = [
//Cambodia Phnom Penh Cities Starts here
           [
                'id' => 1,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Chamkar Mon',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Daun Penh',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Prampir Makara',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Tuol Kouk',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Dangkao',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 6,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Mean Chey',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 7,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Russey Keo',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 8,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Sen Sok',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 9,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Pou Senchey',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 10,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Chroy Changvar',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 11,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Prek Pnov',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 12,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Chbar Ampov',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 13,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Boeng Keng Kang',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 14,
                'country_id' => 2,
                'state_id' => 58,
                'city_name' => 'Kamboul',
                'city_code' => null,
                'status' => 'active',
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ],
//Cambodia Phnom Penh Cities Ends here

        ];
        foreach ($cities as $city)
        {
            City::updateOrcreate(['id' => $city['id']], $city);
        }
    }
}
