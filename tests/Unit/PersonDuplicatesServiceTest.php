<?php

namespace Tests\Unit;

use App\Models\Person;
use App\Models\PersonDuplicatesCache;
use App\Services\Person\PersonDuplicatesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonDuplicatesServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_groups_returns_paginated_groups_with_duplicate_persons()
    {
        $persons = Person::factory()->count(3)->create();
        PersonDuplicatesCache::factory()->create([
            'person_id' => $persons[0]->id,
            'duplicate_ids' => [$persons[1]->id, $persons[2]->id],
        ]);

        $service = new PersonDuplicatesService();
        $groups = $service->getAllGroups();
        $this->assertEquals(1, $groups->total());
        $group = $groups->first();
        $this->assertEquals($persons[0]->id, $group->person->id);
        $this->assertCount(2, $group->duplicate_persons);
        $this->assertEqualsCanonicalizing(
            [$persons[1]->id, $persons[2]->id],
            $group->duplicate_persons->pluck('id')->toArray()
        );
    }
}
