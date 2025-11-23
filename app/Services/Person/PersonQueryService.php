<?php

namespace App\Services\Person;

use App\Models\Person;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class PersonQueryService
{
    public function search(array $filters, int $id = null): Paginator|Person
    {
        if ($id) {
            $person = $this->person($filters)->findOrFail($id);
            $person->append('document_url');
            return $person;
        }

        return $this->person($filters)->paginate();
    }

    private function person(array $filters): Builder
    {
        return Person::filter($filters);
    }
}

