<?php

namespace Tests\Unit;

use App\Models\Person;
use App\Services\Person\DuplicateFinderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DuplicateFinderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DuplicateFinderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DuplicateFinderService();
    }

    /** @test */
    public function test_find_groups_returns_empty_when_no_duplicates()
    {
        Person::factory()->count(5)->create();
        $groups = $this->service->findGroups();
        $this->assertIsArray($groups);
        $this->assertEmpty($groups);
    }

    // Adicione outros cenários de teste conforme a lógica do serviço evoluir
}

