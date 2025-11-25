<?php

namespace App\Services\Person;

use App\Models\Person;
use App\Services\StorageDocumentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Jobs\UpdatePersonDuplicatesCacheJob;

class PersonUpdateService
{
    public function __construct(
        protected StorageDocumentService $storageDocumentService
    )
    {
    }

    public function update($id, array $data)
    {
        $person = Person::findOrFail($id);
        return DB::transaction(function () use ($person, $data) {
            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }
            if (isset($data['document'])) {
                /// Deleta o arquivo antigo
                $data = $this->storageDocumentService->storageDocument($data);
                if($person->document_path) {
                    Storage::disk('s3')->delete($person->document_path);
                }
            }
            $person->update($data);
            if(isset($data['companies_id'])) {
                $person->companies()->sync($data['companies_id']);
            }

            UpdatePersonDuplicatesCacheJob::dispatch($person->id);
            return $person;
        });
    }
}

