<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Resident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nik_hash',
        'nik_encrypted',
        'nik_masked',
        'name',
        'no_kk_encrypted',
        'birth_place',
        'birth_date',
        'gender',
        'address',
        'village_id',
        'rw_id',
        'rt_id',
        'phone',
        'status',
        'is_eligible_voter',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_eligible_voter' => 'boolean',
    ];

    /**
     * Compute HMAC-SHA256 for deterministic unique lookup.
     */
    public static function hashNik(string $nik): string
    {
        $normalized = preg_replace('/\D/', '', trim($nik));
        $appKey = config('app.key') ?? 'e-voting-default-secret-key-salt';
        return hash_hmac('sha256', $normalized, $appKey);
    }

    /**
     * Mask NIK for secure, privacy-preserving display (e.g. 3201**********12).
     */
    public static function maskNik(string $nik): string
    {
        $clean = preg_replace('/\D/', '', trim($nik));
        $len = strlen($clean);
        if ($len <= 6) {
            return str_repeat('*', $len);
        }
        return substr($clean, 0, 4) . str_repeat('*', max(0, $len - 6)) . substr($clean, -2);
    }

    /**
     * Decrypt NIK.
     */
    public function getDecryptedNikAttribute(): ?string
    {
        if (empty($this->nik_encrypted)) {
            return null;
        }
        try {
            return Crypt::decryptString($this->nik_encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Decrypt KK.
     */
    public function getDecryptedNoKkAttribute(): ?string
    {
        if (empty($this->no_kk_encrypted)) {
            return null;
        }
        try {
            return Crypt::decryptString($this->no_kk_encrypted);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class);
    }

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function voterParticipations(): HasMany
    {
        return $this->hasMany(ElectionEligibleVoter::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * Age in years.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? Carbon::parse($this->birth_date)->age : null;
    }

    /**
     * Check if resident is legally eligible to vote (e.g., active, age >= 17, is_eligible_voter flag).
     */
    public function checkVoterEligibility(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        if (!$this->is_eligible_voter) {
            return false;
        }
        if ($this->birth_date && $this->age < 17) {
            return false;
        }
        return true;
    }
}
