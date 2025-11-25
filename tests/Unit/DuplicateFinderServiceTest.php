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

    /** @test */
    public function test_find_groups_by_cpf()
    {
        $cpf = '12345678900';
        $p1 = Person::factory()->create(['cpf' => $cpf]);
        $p2 = Person::factory()->create(['cpf' => $cpf]);
        $p3 = Person::factory()->create(['cpf' => '99999999999']);
        $groups = $this->service->findGroups();
        $this->assertContains([$p1->id, $p2->id], $groups);
    }

    /** @test */
    public function test_find_groups_by_similar_name()
    {
        $p1 = Person::factory()->create(['name' => 'José da Silva']);
        $p2 = Person::factory()->create(['name' => 'Jose da Silva']);
        $p3 = Person::factory()->create(['name' => 'Maria Oliveira']);
        // Força atualização dos campos normalizados/metaphone
        $p1->refresh();
        $p2->refresh();
        $groups = $this->service->findGroups();
        $ids = [$p1->id, $p2->id];
        $found = false;
        foreach ($groups as $group) {
            if (count(array_intersect($group, $ids)) === 2) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    // Adicione outros cenários de teste conforme a lógica do serviço evoluir
}
