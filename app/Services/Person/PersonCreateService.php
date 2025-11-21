<?php

namespace App\Services\Person;

use App\Models\Person;
use App\Services\StorageDocumentService;
use Illuminate\Support\Facades\DB;
use Throwable;

class PersonCreateService
{
    public function __construct(
        protected StorageDocumentService $storageDocumentService
    ) {}

    /**
     * @throws Throwable
     */
    public function create(array $data): Person
    {
        return DB::transaction(function () use ($data)
        {
            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }
            // Upload do documento se existir
            if (request()->hasFile('document')) {
                $data = $this->storageDocumentService->storageDocument($data);
            }

            return Person::create($data);
        });
    }
}
