<?php

namespace Milenmk\LaravelSimpleDatatablesAndForms\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Milenmk\LaravelSimpleDatatablesAndForms\Tests\Models\TestModel;

class TestModelFactory extends Factory
{
    protected $model = TestModel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'is_active' => $this->faker->boolean(),
            'category' => $this->faker->randomElement(['Electronics', 'Clothing', 'Books', 'Food', 'Other']),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-1 year'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month'),
        ];
    }

    public function active(): self
    {
        return $this->state(function () {
            return [
                'is_active' => true,
            ];
        });
    }

    public function inactive(): self
    {
        return $this->state(function () {
            return [
                'is_active' => false,
            ];
        });
    }
}
