<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_persons()
    {
        Person::factory()->count(3)->create();
        $response = $this->getJson('/api/persons');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                0 => ['id', 'login', 'name', 'cpf', 'email', 'address', 'type', 'document_path']
            ]
        ]);
    }

    public function test_can_create_person()
    {
        $data = Person::factory()->make()->toArray();
        $company = Company::factory()->create();
        $data['companies_id'][] = $company->id;
        $data['password'] = 'senha123';
        $response = $this->postJson('/api/persons', $data);
        $response->assertStatus(201);
        unset($data['companies_id']);
        unset($data['password']);
        $this->assertDatabaseHas('persons', $data);
        $person = $response->json();
        $this->assertDatabaseHas('rl_persons_companies', ['person_id' => $person['id'], 'company_id' => $company->id]);
    }

    public function test_can_update_person()
    {
        $person = Person::factory()->create();
        $update = $person->toArray();
        $update['name'] = 'Novo Nome';
        $response = $this->putJson("/api/persons/$person->id", $update);
        $response->assertStatus(200);
        $this->assertDatabaseHas('persons', $update);
    }

    public function test_can_delete_person()
    {
        $person = Person::factory()->create();
        $response = $this->deleteJson("/api/persons/{$person->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('persons', ['id' => $person->id]);
    }
}

