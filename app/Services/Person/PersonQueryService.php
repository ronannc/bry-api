<?php

namespace App\Services\Person;

use App\Models\Person;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class PersonQueryService
{
    public function search(int $id = null): Paginator|Person
    {
        if ($id) {
            $person = $this->query()->findOrFail($id);
            $person->append('document_url');
            return $person;
        }

        return $this->query()->simplePaginate();
    }

    private function query(): Builder
    {
        return Person::with([
            'companies',
        ]);
    }
}

