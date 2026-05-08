<?php

namespace App\Policies;

use App\Models\Approval;
use App\Models\User;

class ApprovalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === User::ROLE_APPROVER_L1 || $user->role === User::ROLE_APPROVER_L2;
    }

    public function view(User $user, Approval $approval): bool
    {
        return $approval->approver_id === $user->id;
    }

    public function approve(User $user, Approval $approval): bool
    {
        return $approval->approver_id === $user->id;
    }

    public function reject(User $user, Approval $approval): bool
    {
        return $approval->approver_id === $user->id;
    }
}
