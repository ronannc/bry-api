<?php

namespace App\Services\Person;

use App\Models\Person;

class PersonUpdateService
{
    public function update($id, array $data)
    {
        $person = Person::findOrFail($id);
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        $person->update($data);
        return $person;
    }
}

