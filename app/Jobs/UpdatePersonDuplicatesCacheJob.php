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
        $person = Person::find($this->personId);
        if (!$person) return;

        // Busca duplicidades por CPF
        $cpfDuplicates = Person::where('cpf', $person->cpf)
            ->where('id', '!=', $person->id)
            ->pluck('id')->toArray();

        // Busca duplicidades por nome semelhante
        $nameDuplicates = Person::where('id', '!=', $person->id)
            ->where('name_metaphone', $person->name_metaphone)
            ->whereRaw('similarity(name_normalized, ?) > 0.7', [$person->name_normalized])
            ->pluck('id')->toArray();

        $duplicates = array_unique(array_merge($cpfDuplicates, $nameDuplicates));

        // Atualiza ou cria registro na tabela de cache
        DB::table('person_duplicates_cache')->updateOrInsert(
            ['person_id' => $person->id],
            [
                'duplicate_ids' => json_encode($duplicates),
                'updated_at' => now(),
            ]
        );
    }
}

