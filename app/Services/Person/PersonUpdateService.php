<?php

namespace App\Services\Person;

use App\Models\Person;
use App\Services\StorageDocumentService;
use Illuminate\Support\Facades\Storage;

class PersonUpdateService
{
    public function __construct(
        protected StorageDocumentService $storageDocumentService
    ) {}

    public function update($id, array $data)
    {
        $person = Person::findOrFail($id);
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        if (isset($data['document'])) {
            /// Deleta o arquivo antigo
            $data = $this->storageDocumentService->storageDocument($data);
            Storage::disk('s3')->delete($person->document_path);
        }
        $person->update($data);
        return $person;
    }
}

