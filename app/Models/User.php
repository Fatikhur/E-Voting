<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_name',
        'role_id',
        'resident_id',
        'village_id',
        'rw_id',
        'rt_id',
        'phone',
        'status',
        'must_change_password',
        'two_factor_secret',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
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

    public function voterParticipations(): HasMany
    {
        return $this->hasMany(ElectionEligibleVoter::class);
    }

    public function observerReports(): HasMany
    {
        return $this->hasMany(ObserverReport::class, 'supervisor_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // Role helper methods
    public function isSuperAdmin(): bool
    {
        return $this->role_name === 'superadmin' || ($this->role && $this->role->name === 'superadmin');
    }

    public function isAdmin(): bool
    {
        return $this->role_name === 'admin' || ($this->role && $this->role->name === 'admin');
    }

    public function isPengawas(): bool
    {
        return $this->role_name === 'pengawas' || ($this->role && $this->role->name === 'pengawas');
    }

    public function isVoter(): bool
    {
        return $this->role_name === 'voter' || ($this->role && $this->role->name === 'voter');
    }

    public function hasRole(string|array $roles): bool
    {
        $rolesArray = is_array($roles) ? $roles : func_get_args();
        return in_array($this->role_name, $rolesArray, true);
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->role && $this->role->hasPermission($permissionName)) {
            return true;
        }

        return $this->roles()->whereHas('permissions', function ($q) use ($permissionName) {
            $q->where('name', $permissionName);
        })->exists();
    }
}
