<?php

namespace App\Policies;

use App\Models\Election;
use App\Models\User;

class ElectionPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Semua role terotentikasi dapat melihat daftar pemilihan yang relevan
    }

    public function view(User $user, Election $election): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function update(User $user, Election $election): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isAdmin()) {
            return false;
        }

        // Admin hanya boleh mengedit jika status masih draft atau scheduled
        return in_array($election->status, ['draft', 'scheduled'], true);
    }

    public function delete(User $user, Election $election): bool
    {
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            return false;
        }

        // Pemilihan hanya boleh dihapus jika masih draft dan belum memiliki surat suara
        return $election->isDraft() && $election->ballots()->count() === 0;
    }

    public function open(User $user, Election $election): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function close(User $user, Election $election): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function vote(User $user, Election $election): bool
    {
        if (!$user->isVoter()) {
            return false;
        }

        return $election->isOpen();
    }

    public function approve(User $user, Election $election): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isPengawas();
    }

    public function publish(User $user, Election $election): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }
}
