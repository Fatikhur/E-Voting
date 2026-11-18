<?php

namespace App\Policies;

use App\Models\Resident;
use App\Models\User;

class ResidentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isPengawas();
    }

    public function view(User $user, Resident $resident): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isPengawas();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function update(User $user, Resident $resident): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function delete(User $user, Resident $resident): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function import(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isPengawas();
    }
}
