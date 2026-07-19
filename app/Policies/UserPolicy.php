<?php

namespace App\Policies;

use App\Models\User;
use App\Support\AccessControl;

class UserPolicy
{
    public function delete(User $actor, User $target): bool
    {
        if (! $actor->can('users.delete') || $actor->is($target)) {
            return false;
        }

        return ! $this->isLastActiveAdmin($target);
    }

    public function deactivate(User $actor, User $target): bool
    {
        if (! $actor->can('users.activate') || $actor->is($target)) {
            return false;
        }

        return ! $this->isLastActiveAdmin($target);
    }

    public function changeRole(User $actor, User $target, string $newRole): bool
    {
        if (! $actor->can('users.update')) {
            return false;
        }

        if ($target->hasRole(AccessControl::ROLE_ADMIN) && $newRole !== AccessControl::ROLE_ADMIN) {
            return ! $this->isLastActiveAdmin($target);
        }

        return true;
    }

    private function isLastActiveAdmin(User $user): bool
    {
        if (! $user->is_active || ! $user->hasRole(AccessControl::ROLE_ADMIN)) {
            return false;
        }

        return User::role(AccessControl::ROLE_ADMIN)
            ->where('is_active', true)
            ->whereKeyNot($user->getKey())
            ->doesntExist();
    }
}
