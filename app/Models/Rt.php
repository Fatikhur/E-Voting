<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rt extends Model
{
    use HasFactory;

    protected $fillable = [
        'rw_id',
        'rt_number',
        'name',
        'description',
    ];

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class);
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
        return 'RT ' . $this->rt_number . ($this->rw ? ' / RW ' . $this->rw->rw_number : '');
    }
}
