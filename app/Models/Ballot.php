<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ballot extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * KEAMANAN RAHASIA SUARA (SECRET BALLOT):
     * Model ini secara sengaja TIDAK MENYIMPAN dan TIDAK MEMILIKI RELASI ke User, Resident, Token, atau IP address.
     */
    protected $fillable = [
        'id',
        'election_id',
        'candidate_id',
        'cast_date',
        'ballot_hash',
        'is_valid',
    ];

    protected $casts = [
        'cast_date' => 'date',
        'is_valid' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    /**
     * Menghasilkan hash bukti integritas surat suara
     */
    public static function generateHash(int $electionId, int $candidateId, string $salt): string
    {
        return hash('sha256', $electionId . '|' . $candidateId . '|' . $salt);
    }
}
