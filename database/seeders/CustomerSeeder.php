<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Customer;
class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = [
            [
                'idd' => '+855',
                'phone_number' => '1111111111',
                'password' => '$2y$10$u7XVW8edl7o45Av3uxeHd.tASgU7rZrUCBDqhKv7BCRAiV.71fDQi',
            ],
            [
                'idd' => '+855',
                'phone_number' => '2222222222',
                'password' => '$2y$10$u7XVW8edl7o45Av3uxeHd.tASgU7rZrUCBDqhKv7BCRAiV.71fDQi',
            ],
            [
                'idd' => '+855',
                'phone_number' => '3333333333',
                'password' => '$2y$10$u7XVW8edl7o45Av3uxeHd.tASgU7rZrUCBDqhKv7BCRAiV.71fDQi',
            ],
            [
                'idd' => '+855',
                'phone_number' => '4444444444',
                'password' => '$2y$10$u7XVW8edl7o45Av3uxeHd.tASgU7rZrUCBDqhKv7BCRAiV.71fDQi',
            ],
            [
                'idd' => '+855',
                'phone_number' => '5555555555',
                'password' => '$2y$10$u7XVW8edl7o45Av3uxeHd.tASgU7rZrUCBDqhKv7BCRAiV.71fDQi',
            ],
            [
                'idd' => '+855',
                'phone_number' => '0000000000',
                'password' => '$2y$10$u7XVW8edl7o45Av3uxeHd.tASgU7rZrUCBDqhKv7BCRAiV.71fDQi',
            ],
            [
                'idd' => '+855',
                'phone_number' => '7837774682',
                'password' => '$2y$10$u7XVW8edl7o45Av3uxeHd.tASgU7rZrUCBDqhKv7BCRAiV.71fDQi',
            ]
        ];
        foreach ($customers as $customer) {
            // filters
            Customer::updateOrcreate($customer);
        }
    }
}
