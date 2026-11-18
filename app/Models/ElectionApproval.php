<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElectionApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'election_id',
        'required_approvals',
        'status',
        'admin_user_id',
        'admin_approved',
        'admin_approved_at',
        'admin_notes',
        'supervisor_user_id',
        'supervisor_approved',
        'supervisor_approved_at',
        'supervisor_notes',
    ];

    protected $casts = [
        'admin_approved' => 'boolean',
        'supervisor_approved' => 'boolean',
        'admin_approved_at' => 'datetime',
        'supervisor_approved_at' => 'datetime',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    /**
     * Cek apakah persetujuan dua pihak sudah lengkap
     */
    public function isFullyApproved(): bool
    {
        return $this->admin_approved && $this->supervisor_approved && $this->status === 'approved';
    }
}
