<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CompanyQueryService
{
    public function search(array $filters, bool $paginated = true, int $id = null): Paginator|Company|Collection
    {
        if ($id) {
            return $this->company($filters)->findOrFail($id);
        }

        $query = $this->company($filters);
        if ($paginated) {
            return $query->paginate();
        }

        return $query->get();
    }

    private function company(array $filters): Builder
    {
        return Company::filter($filters);
    }
}

