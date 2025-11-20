<?php

namespace App\Services\Person;

use App\Models\Person;
use Illuminate\Support\Facades\Storage;

class PersonDeleteService
{
    public function delete($id): void
    {
        $person = Person::findOrFail($id);
        if ($person->document_path) {
            Storage::disk('s3')->delete($person->document_path);
        }
        $person->companies()->detach();
        $person->delete();
    }
}

