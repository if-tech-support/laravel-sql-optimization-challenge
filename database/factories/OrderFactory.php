<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'paid', 'shipped', 'cancelled']),
            'ordered_at' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }
}
