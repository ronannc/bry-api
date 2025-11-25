<?php

namespace App\Services\Person;

use Illuminate\Support\Facades\DB;

/**
 * Estratégia:
 * - chunkById para percorrer a tabela em batches
 * - para cada batch executa uma única query com JOIN LATERAL que, por cada linha p,
 *   busca até `perRowLimit` candidatos que satisfaçam (cpf igual OR metaphone igual OR similarity > threshold)
 * - coleta pares (p.id, q.id), deduplica e aplica union-find para formar grupos
 */
class DuplicateFinderService
{
    protected float $trigramThreshold;
    protected int $perRowLimit;
    protected int $chunkSize;

    public function __construct(array $config = [])
    {
        $this->trigramThreshold = $config['trigram_threshold'] ?? 0.40;
        $this->perRowLimit = $config['per_row_limit'] ?? 10;   // candidatos por linha
        $this->chunkSize = $config['chunk_size'] ?? 10;      // quantas linhas processar por chunk
    }

    /**
     * Roda todo o processo e retorna grupos com records anexados.
     *
     * @return array<int, array{group_id:int, ids:int[], records:array}>
     */
    public function findGroups(): array
    {
        $pairs = $this->findPairsChunked();

        if (empty($pairs)) {
            return [];
        }

        $groups = $this->pairsToGroups($pairs);

        $allIds = collect($groups)->flatten()->unique()->values()->all();

        $records = DB::table('persons')
            ->whereIn('id', $allIds)
            ->get()
            ->keyBy('id');

        $result = [];
        foreach ($groups as $groupId => $ids) {
            $result[] = [
                'group_id' => (int)$groupId,
                'ids' => $ids,
                'records' => array_map(fn($id) => $records[$id] ?? null, $ids),
            ];
        }

        return $result;
    }

    /**
     * Itera a tabela em chunks e para cada chunk executa uma query com LATERAL para buscar candidatos.
     *
     * Retorna array de pares [ ['id1'=>int,'id2'=>int, ...], ... ]
     *
     * @return array<int, array>
     */
    private function findPairsChunked(): array
    {
        $pairs = [];
        $trig = $this->trigramThreshold;
        $perRowLimit = (int)$this->perRowLimit;

        DB::table('persons')
            ->select('id')
            ->orderBy('id')
            ->chunkById($this->chunkSize, function ($rows) use (&$pairs, $trig, $perRowLimit) {

                $ids = $rows->pluck('id')->values()->all();
                if (empty($ids)) {
                    return;
                }

                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $sql = str_replace('{ids_placeholders}', $placeholders, $this->sqlTemplate());

                // Bindings: primeiro trig, perRowLimit, depois lista de ids (para IN)
                $bindings = array_merge([$trig, $perRowLimit], $ids);

                $rowsFound = DB::select($sql, $bindings);

                foreach ($rowsFound as $r) {
                    $id1 = (int)$r->id1;
                    $id2 = (int)$r->id2;
                    if ($id1 === $id2) {
                        continue;
                    }
                    if ($id1 > $id2) {
                        [$id1, $id2] = [$id2, $id1];
                    }
                    $pairs[] = [
                        'id1' => $id1,
                        'id2' => $id2,
                        'cpf_match' => (bool)$r->cpf_match,
                        'metaphone_match' => (bool)$r->metaphone_match,
                        'trigram_similarity' => isset($r->trigram_similarity) ? (float)$r->trigram_similarity : 0.0,
                        'name1' => $r->name1,
                        'name2' => $r->name2,
                    ];
                }
            });

        $unique = [];
        foreach ($pairs as $p) {
            $key = $p['id1'] . '_' . $p['id2'];
            if (!isset($unique[$key])) {
                $unique[$key] = $p;
            }
        }

        return array_values($unique);
    }

    /**
     * Union-find para transformar pares em grupos conectados
     *
     * @param array<int, array> $pairs
     * @return array<int, int[]>
     */
    private function pairsToGroups(array $pairs): array
    {
        $parent = [];
        $rank = [];

        // inicializa nós
        foreach ($pairs as $p) {
            $a = (int)$p['id1'];
            $b = (int)$p['id2'];
            if (!isset($parent[$a])) {
                $parent[$a] = $a;
                $rank[$a] = 0;
            }
            if (!isset($parent[$b])) {
                $parent[$b] = $b;
                $rank[$b] = 0;
            }
        }

        $find = function (int $x) use (&$parent, &$find): int {
            if ($parent[$x] !== $x) {
                $parent[$x] = $find($parent[$x]);
            }
            return $parent[$x];
        };

        $union = function (int $x, int $y) use (&$parent, &$rank, $find): void {
            $rx = $find($x);
            $ry = $find($y);
            if ($rx === $ry) return;
            if ($rank[$rx] < $rank[$ry]) {
                $parent[$rx] = $ry;
            } else {
                $parent[$ry] = $rx;
                if ($rank[$rx] === $rank[$ry]) {
                    $rank[$rx]++;
                }
            }
        };

        foreach ($pairs as $p) {
            $union((int)$p['id1'], (int)$p['id2']);
        }

        $groups = [];
        foreach ($parent as $node => $_) {
            $root = $find((int)$node);
            $groups[$root][] = (int)$node;
        }

        return $groups;
    }

    private function sqlTemplate(): string{
        return "SELECT
                    p.id as id1,
                    q.id as id2,
                    p.cpf as cpf1,
                    q.cpf as cpf2,
                    p.name as name1,
                    q.name as name2,
                    (p.cpf IS NOT NULL AND p.cpf = q.cpf) AS cpf_match,
                    (p.name_metaphone = q.name_metaphone) AS metaphone_match,
                    similarity(p.name_normalized, q.name_normalized) AS trigram_similarity
                FROM persons p
                JOIN LATERAL (
                    SELECT id, cpf, name, name_metaphone, name_normalized
                    FROM persons
                    WHERE id <> p.id
                      AND (
                        (cpf IS NOT NULL AND cpf = p.cpf)
                        OR (name_metaphone = p.name_metaphone)
                        OR (similarity(name_normalized, p.name_normalized) > ?)
                      )
                    ORDER BY
                      (cpf IS NOT NULL AND cpf = p.cpf) DESC,
                      (name_metaphone = p.name_metaphone) DESC,
                      similarity(name_normalized, p.name_normalized) DESC
                    LIMIT ?
                ) q ON true
                WHERE p.id IN ({ids_placeholders})";
    }
}
