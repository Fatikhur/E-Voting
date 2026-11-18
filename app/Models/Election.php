<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'type',
        'village_id',
        'rw_id',
        'rt_id',
        'term_period',
        'start_time',
        'end_time',
        'description',
        'status',
        'tie_breaker_rule',
        'is_published',
        'published_at',
        'published_by',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class)->orderBy('candidate_number', 'asc');
    }

    public function eligibleVoters(): HasMany
    {
        return $this->hasMany(ElectionEligibleVoter::class);
    }

    public function ballots(): HasMany
    {
        return $this->hasMany(Ballot::class);
    }

    public function approval(): HasOne
    {
        return $this->hasOne(ElectionApproval::class);
    }

    public function observerReports(): HasMany
    {
        return $this->hasMany(ObserverReport::class);
    }

    public function votingTokens(): HasMany
    {
        return $this->hasMany(VotingToken::class);
    }

    // Status checks
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }
        $now = Carbon::now();
        return $now->gte($this->start_time) && $now->lte($this->end_time);
    }

    public function isTimeExpired(): bool
    {
        return Carbon::now()->gt($this->end_time);
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'published'], true) || ($this->status === 'open' && $this->isTimeExpired());
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' || $this->is_published;
    }

    public function canVoteNow(): bool
    {
        return $this->isOpen();
    }

    public function getScopeDisplayAttribute(): string
    {
        if ($this->type === 'ketua_rw') {
            return 'Wilayah RW ' . ($this->rw ? $this->rw->rw_number : '-');
        }
        return 'Wilayah RT ' . ($this->rt ? $this->rt->rt_number : '-') . ' / RW ' . ($this->rw ? $this->rw->rw_number : '-');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['text' => 'Draft', 'class' => 'bg-gray-100 text-gray-800 border-gray-300'],
            'scheduled' => ['text' => 'Terjadwal', 'class' => 'bg-blue-100 text-blue-800 border-blue-300'],
            'open' => ['text' => 'Sedang Berlangsung', 'class' => 'bg-green-100 text-green-800 border-green-300 animate-pulse'],
            'closed' => ['text' => 'Ditutup', 'class' => 'bg-yellow-100 text-yellow-800 border-yellow-300'],
            'published' => ['text' => 'Hasil Dipublikasi', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-300'],
            'cancelled' => ['text' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800 border-red-300'],
            default => ['text' => ucfirst($this->status), 'class' => 'bg-gray-100 text-gray-800 border-gray-300'],
        };
    }
}
