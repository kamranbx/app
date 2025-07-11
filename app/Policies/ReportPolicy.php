<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::MANAGER]);
    }

    public function view(User $user, Report $report): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::MANAGER]);
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function update(User $user, Report $report): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    public function delete(User $user, Report $report): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}
