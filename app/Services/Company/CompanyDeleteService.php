<?php

namespace App\Services\Company;

use App\Models\Company;

class CompanyDeleteService
{
    public function delete($id): void
    {
        $company = Company::findOrFail($id);
        $company->persons()->detach();
        $company->delete();
    }
}

