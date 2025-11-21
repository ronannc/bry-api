<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
}

