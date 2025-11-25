<?php

namespace Database\Factories;

use App\Models\PersonDuplicatesCache;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonDuplicatesCacheFactory extends Factory
{
    protected $model = PersonDuplicatesCache::class;

    public function definition(): array
    {
        return [
            'person_id' => Person::factory()->lazy(),
            'duplicate_ids' => [Person::factory()->lazy()],
            'updated_at' => now(),
        ];
    }
}
