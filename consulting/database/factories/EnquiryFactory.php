<?php

namespace Database\Factories;

use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
class EnquiryFactory extends Factory
{
    protected $model = Enquiry::class;

    public function definition(): array
    {
        return [
            'type' => Enquiry::TYPE_CONTACT,
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company' => fake()->company(),
            'message' => fake()->paragraph(),
        ];
    }

    public function consultation(): static
    {
        return $this->state(fn () => [
            'type' => Enquiry::TYPE_CONSULTATION,
            'service' => array_key_first(config('site.services')),
            'budget' => array_key_first(config('site.budget_bands')),
        ]);
    }
}
