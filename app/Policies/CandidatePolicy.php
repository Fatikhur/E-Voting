<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;

class CandidatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Candidate $candidate): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function update(User $user, Candidate $candidate): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->isAdmin()) {
            return false;
        }

        // Kandidat hanya boleh diubah jika status pemilihan masih draft atau scheduled
        return in_array($candidate->election->status, ['draft', 'scheduled'], true);
    }

    public function delete(User $user, Candidate $candidate): bool
    {
        if (!$user->isSuperAdmin() && !$user->isAdmin()) {
            return false;
        }

        return in_array($candidate->election->status, ['draft', 'scheduled'], true);
    }
}
