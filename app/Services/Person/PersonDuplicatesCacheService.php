<?php
namespace App\Services\Person;

use Illuminate\Support\Facades\DB;

class PersonDuplicatesCacheService
{
    /**
     * Retorna os IDs duplicados para uma pessoa
     * @param int $personId
     * @return array
     */
    public function getDuplicates(int $personId): array
    {
        $row = DB::table('person_duplicates_cache')->where('person_id', $personId)->first();
        return $row ? json_decode($row->duplicate_ids, true) : [];
    }

    /**
     * Retorna todos os grupos de duplicados
     * @return array
     */
    public function getAllGroups(): array
    {
        $rows = DB::table('person_duplicates_cache')->get();
        $groups = [];
        foreach ($rows as $row) {
            $ids = json_decode($row->duplicate_ids, true);
            if (is_array($ids) && count($ids) > 1) {
                $groups[] = $ids;
            }
        }
        return $groups;
    }
}

