<?php

namespace App\Services\Company;

use App\Models\Company;

class CompanyUpdateService
{
    public function update($id, array $data): Company
    {
        $company = Company::findOrFail($id);
        $company->update($data);
        return $company->refresh();
    }
}

