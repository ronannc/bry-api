<?php

namespace App\Models;

use App\Enums\PersonType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cnpj',
        'address',
    ];

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'rl_persons_companies', 'company_id', 'person_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->persons()->where('type', PersonType::FUNCIONARIO);
    }

    public function clients(): BelongsToMany
    {
        return $this->persons()->where('type', PersonType::CLIENTE);
    }

    protected function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'person_id':
                    $query->whereHas('persons', function ($query) use ($value) {
                        $query->where('person_id', $value);
                    });
                    break;
                default:
                    $query->where($key, $value);
            }
        }
        return $query;
    }
}

