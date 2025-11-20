<?php

namespace Database\Factories;

use App\Enums\PersonType;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        return [
            'login' => $this->faker->unique()->userName,
            'name' => $this->faker->name,
            'cpf' => $this->faker->unique()->numerify('###.###.###-##'),
            'email' => $this->faker->unique()->safeEmail,
            'address' => $this->faker->address,
            'password' => bcrypt('senha123'),
            'type' => $this->faker->randomElement(PersonType::cases()),
            'document_path' => null,
        ];
    }
}

