<?php

namespace Database\Factories;

use App\Enums\Module;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleAccess>
 */
class ModuleAccessFactory extends Factory
{
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'module' => fake()->randomElement(Module::cases())->value,
            'active' => false,
        ];
    }
}
