<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;

class Person extends Authenticatable
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'login',
        'name',
        'cpf',
        'email',
        'address',
        'password',
        'type',
        'document_path',
    ];

    protected $hidden = [
        'password',
    ];

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(
            Company::class,
            'rl_persons_companies',
            'person_id',
            'company_id',
            'id',
            'id',
        );
    }

    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document_path ? Storage::disk('s3temp')->temporaryUrl($this->document_path, now()->addMinutes(5)) : null;
    }

    protected function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'company_id':
                    $query->whereHas('companies', function ($query) use ($value) {
                        $query->where('company_id', $value);
                    });
                    break;
                default:
                    $query->where($key, $value);
            }
        }
        return $query;
    }
}

