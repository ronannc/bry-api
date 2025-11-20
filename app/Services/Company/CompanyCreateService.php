<?php

namespace App\Services\Company;

use App\Models\Company;

class CompanyCreateService
{
    public function create(array $data): Company
    {
        return Company::create($data);
    }
}

