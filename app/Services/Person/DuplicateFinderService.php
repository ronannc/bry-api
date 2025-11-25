<?php

namespace App\Services\Person;

use App\Models\Person;
use Illuminate\Support\Facades\DB;

class DuplicateFinderService
{
    /**
     * Identifica grupos de possíveis duplicidades por CPF ou nome semelhante.
     *
     * @return array Grupos de IDs de pessoas duplicadas
     */
    public function findGroups(): array
    {
        $cpfGroups = $this->findByCpf();
        $nameGroups = $this->findBySimilarName();

        // Junta os grupos, evitando duplicidade de IDs
        $allGroups = array_merge($cpfGroups, $nameGroups);
        $result = [];
        $seen = [];
        foreach ($allGroups as $group) {
            $unique = array_unique($group);
            sort($unique);
            $key = implode('-', $unique);
            if (!isset($seen[$key]) && count($unique) > 1) {
                $result[] = $unique;
                $seen[$key] = true;
            }
        }
        return $result;
    }

    /**
     * Busca grupos de pessoas com o mesmo CPF
     *
     * @return array
     */
    public function findByCpf(): array
    {
        $rows = Person::select('cpf', DB::raw('array_agg(id) as ids'))
            ->groupBy('cpf')
            ->havingRaw('count(*) > 1')
            ->get();
        $groups = [];
        foreach ($rows as $row) {
            $groups[] = $row->ids;
        }
        return $groups;
    }

    /**
     * Busca grupos de pessoas com nomes semelhantes (fonética e sem acentuação) de forma eficiente para grandes volumes
     *
     * @return array
     */
    protected function findBySimilarName(): array
    {
        // Agrupa diretamente no banco por name_metaphone, e filtra por similaridade
        $sql = "SELECT array_agg(id) as ids FROM persons WHERE name_metaphone IS NOT NULL GROUP BY name_metaphone HAVING count(*) > 1";
        $rows = DB::select($sql);
        $groups = [];
        foreach ($rows as $row) {
            $ids = $row->ids;
            // Busca apenas os pares realmente semelhantes dentro do grupo
            if (count($ids) > 1) {
                $similar = [];
                foreach ($ids as $i) {
                    $name_i = DB::table('persons')->where('id', $i)->value('name_normalized');
                    foreach ($ids as $j) {
                        if ($i >= $j) continue;
                        $name_j = DB::table('persons')->where('id', $j)->value('name_normalized');
                        $sim = DB::selectOne("SELECT similarity(?, ?) as sim", [$name_i, $name_j])->sim;
                        if ($sim > 0.7) {
                            $similar[] = $i;
                            $similar[] = $j;
                        }
                    }
                }
                $similar = array_unique($similar);
                if (count($similar) > 1) {
                    $groups[] = $similar;
                }
            }
        }
        return $groups;
    }
}
