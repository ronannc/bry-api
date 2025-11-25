<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;


    public function test_can_list_companies()
    {
        Company::factory()->count(3)->create();
        $response = $this->getJson('/api/companies');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                0 => ['id', 'name', 'cnpj', 'address']
            ]
        ]);
    }

    public function test_can_create_company()
    {
        $data = Company::factory()->make()->toArray();
        $response = $this->postJson('/api/companies', $data);
        $response->assertStatus(201);
        $this->assertDatabaseHas('companies', $data);
    }

    public function test_can_update_company()
    {
        $company = Company::factory()->create();
        $update = [
            'name' => 'Empresa Atualizada',
            'cnpj' => $company->cnpj,
            'address' => (string) ($company->address ?? '')
        ];
        $response = $this->putJson("/api/companies/{$company->id}", $update);
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Empresa Atualizada']);
    }

    public function test_can_delete_company()
    {
        $company = Company::factory()->create();
        $response = $this->deleteJson("/api/companies/{$company->id}");
        $response->assertStatus(204);
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }
}
