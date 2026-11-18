<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'district',
        'regency',
        'postal_code',
    ];

    public function rws(): HasMany
    {
        return $this->hasMany(Rw::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    public function elections(): HasMany
    {
        return $this->hasMany(Election::class);
    }
}
