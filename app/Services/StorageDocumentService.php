<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StorageDocumentService
{
    public function storageDocument(array $data): array
    {
        $file = request()->file('document');
        $path = Storage::disk('s3')->put('documents', $file);
        $data['document_path'] = $path;
        return $data;
    }
}
