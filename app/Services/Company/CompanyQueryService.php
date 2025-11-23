<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class CompanyQueryService
{
    public function search(int $id = null): Paginator|Company
    {
        if ($id) {
            return Company::findOrFail($id);
        }

        return Company::paginate();
    }
}

