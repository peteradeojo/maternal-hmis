<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\PatientCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'card_number' => $this->faker->unique()->randomNumber(8),
            'name' => $this->faker->name,
            'gender' => $this->faker->randomElement(Gender::getValues()),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'address' => $this->faker->address,
            'dob' => $this->faker->date(),
            'marital_status' => $this->faker->randomElement([1, 2, 3, 4]),
            'occupation' => $this->faker->jobTitle,
            'religion' => $this->faker->randomElement([1, 2, 3]),
            'tribe' => $this->faker->word,
            'place_of_origin' => $this->faker->locale,
            'nok_name' => $this->faker->name,
            'nok_phone' => $this->faker->phoneNumber,
            'nok_address' => $this->faker->address,
            'spouse_name' => $this->faker->name,
            'spouse_phone' => $this->faker->phoneNumber,
            'spouse_occupation' => $this->faker->jobTitle,
            'spouse_educational_status' => $this->faker->word,
            'category_id' => $this->faker->randomElement(PatientCategory::pluck('id')->toArray()),
        ];
    }
}
