<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BatchUpdatePersonDuplicatesCacheJob;
use App\Models\Person;

class PersonDuplicatesBatchCommand extends Command
{
    protected $signature = 'person:duplicates {--batch=1000}';
    protected $description = 'Processa duplicidades de pessoas em lotes e atualiza o cache';

    public function handle(): void
    {
        $batchSize = (int)$this->option('batch');
        $total = Person::count();
        $this->info("Processando $total registros em lotes de $batchSize...");
        for ($offset = 0; $offset < $total; $offset += $batchSize) {
            BatchUpdatePersonDuplicatesCacheJob::dispatch($offset, $batchSize);
            $this->info("Lote iniciado: offset $offset");
        }
        $this->info('Jobs de duplicidade disparados. Acompanhe a fila para progresso.');
    }
}

