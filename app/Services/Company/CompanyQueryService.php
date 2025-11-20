<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Contracts\Pagination\Paginator;

class CompanyQueryService
{
    public function search(int $id = null): Paginator|Company
    {
        if ($id) {
            return Company::with([
                'employees',
                'clients'
            ])->findOrFail($id);
        }

        return Company::with([
            'employees',
            'clients'
        ])->simplePaginate();
    }
}

