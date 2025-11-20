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
            return $this->query()->findOrFail($id);
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

