<?php
namespace App\Jobs;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BatchUpdatePersonDuplicatesCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $offset;
    protected int $limit;

    public function __construct(int $offset = 0, int $limit = 1000)
    {
        $this->offset = $offset;
        $this->limit = $limit;
    }

    public function handle(): void
    {
        $persons = Person::query()->offset($this->offset)->limit($this->limit)->get();
        foreach ($persons as $person) {
            UpdatePersonDuplicatesCacheJob::dispatch($person->id);
        }
    }
}

