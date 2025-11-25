<?php

namespace App\Services\Person;

use App\Models\Person;
use Illuminate\Support\Facades\Storage;

class PersonDeleteService
{
    public function delete($id): void
    {
        $person = Person::findOrFail($id);
        $person->companies()->detach();
        $documentPath = $person->document_path;
        $person->delete();
        if ($documentPath) {
            Storage::disk('s3')->delete($documentPath);
        }
    }
}

