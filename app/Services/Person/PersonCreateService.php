<?php

namespace App\Services\Person;

use App\Models\Person;
use Illuminate\Support\Facades\Storage;

class PersonCreateService
{
    public function create(array $data): Person
    {
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        // Upload do documento se existir
        if (request()->hasFile('document')) {
            $data = $this->storageDocument($data);
        }
        return Person::create($data);
    }

    public function storageDocument(array $data): array
    {
        $file = request()->file('document');
        $path = Storage::disk('s3')->put('documents', $file);
        $data['document_path'] = $path;
        return $data;
    }
}
