<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rw extends Model
{
    use HasFactory;

    protected $fillable = [
        'village_id',
        'rw_number',
        'name',
        'description',
    ];

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function rts(): HasMany
    {
        return $this->hasMany(Rt::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    public function elections(): HasMany
    {
        return $this->hasMany(Election::class);
    }

    public function getFullDisplayAttribute(): string
    {
        return 'RW ' . $this->rw_number . ($this->name ? ' (' . $this->name . ')' : '');
    }
}
