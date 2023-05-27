<?php

namespace Database\Factories;

use App\Models\Bot;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $firstName = fake()->firstName;
        $lastName = fake()->lastName;
        $nexCustomerId = DB::table('customers')->max('id') + 1;
        $append = zeroappend($nexCustomerId);
        $memberID = 'M' . $append . $nexCustomerId;
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'display_name' => $firstName . ' ' . $lastName,
            'idd' => '+855',
            'phone_number' => fake()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'referral_code' => generateReferralCode(6),
            'member_ID' => $memberID . rand(00, 99)
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            // 'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */

    public function configure()
    {
        return $this->afterCreating(function (Customer $customer) {
            $botData = [
                'customer_id' => $customer->id,
                'type' => 'customer'
            ];
            Bot::create($botData);
        });
    }
}
