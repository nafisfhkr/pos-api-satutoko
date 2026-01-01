<?php

namespace Database\Factories;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Outlet>
 */
class OutletFactory extends Factory
{
    protected $model = Outlet::class;

    public function definition(): array
    {
        return [
            'name' => 'Outlet ' . $this->faker->city(),
            'address' => $this->faker->streetAddress(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
