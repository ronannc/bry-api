<?php

namespace App\Services\Person;

use App\Models\Person;
use App\Models\PersonDuplicatesCache;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonDuplicatesService
{
    public function getAllGroups(): LengthAwarePaginator
    {
        $groups = PersonDuplicatesCache::with('person:id,name')->paginate();

        $allDuplicateIds = $groups->getCollection()->pluck('duplicate_ids')->flatten()->toArray();

        $persons = Person::whereIn('id', $allDuplicateIds)->get(['id', 'name']);
        $personMap = $persons->keyBy('id');

        $groups->getCollection()->transform(function ($group) use ($personMap) {
            $group->duplicate_persons = collect($group->duplicate_ids)->map(function ($id) use ($personMap) {
                return $personMap->get($id);
            })->filter()->values();
            return $group;
        });
        return $groups;
    }

    /**
     * Retorna os IDs duplicados para um person específico.
     *
     * @param int $personId
     * @return array
     */
    public function getDuplicates(int $personId): array
    {
        $row = PersonDuplicatesCache::where('person_id', $personId)->first();
        return $row ? (array) $row->duplicate_ids : [];
    }
}
