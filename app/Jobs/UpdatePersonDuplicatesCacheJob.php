<?php

namespace App\Jobs;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdatePersonDuplicatesCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $personId;

    public function __construct(int $personId)
    {
        $this->personId = $personId;
    }

    public function handle(): void
    {
        // Limiar de similaridade para encontrar duplicidades por nome
        $limiar = 0.3;

        $person = Person::find($this->personId);
        if (!$person) return;

        $duplicates = $this->findDuplicatesByNameOrCpf($person, $limiar);

        // Se não houver duplicidades, remove o registro
        if (count($duplicates) == 0) {
            DB::table('person_duplicates_cache')->where('person_id', $person->id)->delete();
            return;
        }

        // Atualiza ou cria registro na tabela de cache
        DB::table('person_duplicates_cache')->updateOrInsert(
            ['person_id' => $person->id],
            [
                'duplicate_ids' => json_encode($duplicates),
                'updated_at' => now(),
            ]
        );
    }

    private function findDuplicatesByNameOrCpf($person, float $limiar): array
    {
        return Person::where(function ($query) use ($person, $limiar) {
            $query->where('cpf', $person->cpf)
                ->orWhere(function ($subQuery) use ($person, $limiar) {
                    // Usa o dmetaphone para encontrar duplicidades por nome
                    $subQuery->where('name_metaphone', $person->name_metaphone)
                        // Usa a similaridade para encontrar duplicidades por nome.
                        ->whereRaw('similarity(name_normalized, ?) > ?', [$person->name_normalized, $limiar]);
                });
        })->where('id', '!=', $person->id)
            ->pluck('id')->toArray();
    }
}

