<?php

namespace Ajimoti\RolesAndPermissions\Tests\Factories;

use Ajimoti\RolesAndPermissions\Tests\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

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
