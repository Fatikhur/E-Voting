<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditCheckpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkpoint_sequence',
        'election_id',
        'start_audit_id',
        'end_audit_id',
        'total_logs_counted',
        'checkpoint_hash',
        'signature_note',
        'signed_by',
    ];

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}
