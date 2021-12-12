<?php

namespace Tarzancodes\RolesAndPermissions\Tests\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tarzancodes\RolesAndPermissions\Tests\Models\Merchant;

class MerchantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Merchant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
        ];
    }

    protected function withFaker()
    {
        return \Faker\Factory::create('en');
    }
}
