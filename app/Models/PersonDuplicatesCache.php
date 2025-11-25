<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class PersonDuplicatesCache extends Model
{
    use HasFactory;

    protected $table = 'person_duplicates_cache';

    protected $fillable = [
        'person_id',
        'duplicate_ids',
        'updated_at',
    ];

    protected $casts = [
        'duplicate_ids' => 'array',
        'updated_at' => 'date:d/m/Y H:i:s',
    ];

    public $timestamps = false;

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
}


