<?php

namespace App\Policies;

use App\Models\MortalityRecord;
use App\Models\User;

class MortalityRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('mortality.view');
    }

    public function view(User $user, MortalityRecord $mortalityRecord): bool
    {
        return $user->can('mortality.view');
    }

    public function create(User $user): bool
    {
        return $user->can('mortality.create');
    }

    public function update(User $user, MortalityRecord $mortalityRecord): bool
    {
        return $user->can('mortality.update');
    }

    public function delete(User $user, MortalityRecord $mortalityRecord): bool
    {
        return $user->can('mortality.update');
    }
}
