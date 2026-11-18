<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'username',
        'role',
        'action',
        'event_type',
        'ip_address',
        'user_agent',
        'description',
        'metadata',
        'prev_hash',
        'current_hash',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mengurutkan key array secara kanonikal (rekursif) agar serialisasi JSON deterministik
     * terlepas dari urutan key atau penyimpanan internal basis data.
     */
    public static function canonicalizeMetadata(mixed $data): mixed
    {
        if (!is_array($data)) {
            return $data;
        }

        $isAssoc = !array_is_list($data);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::canonicalizeMetadata($value);
            }
        }

        if ($isAssoc) {
            ksort($data);
        }

        return $data;
    }

    /**
     * Menghitung hash integritas log dengan hash chaining
     */
    public static function calculateHash(
        string $prevHash,
        string $timestamp,
        ?int $userId,
        string $action,
        string $eventType,
        string $description,
        ?array $metadata
    ): string {
        $canonicalMetadata = self::canonicalizeMetadata($metadata ?? []);

        $data = implode('|', [
            $prevHash,
            $timestamp,
            (string) ($userId ?? 'system'),
            $action,
            $eventType,
            $description,
            json_encode($canonicalMetadata, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ]);

        return hash('sha256', $data);
    }
}
