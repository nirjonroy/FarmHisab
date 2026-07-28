<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WeightRecord;

class WeightRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('weights.view');
    }

    public function view(User $user, WeightRecord $weightRecord): bool
    {
        return $user->can('weights.view');
    }

    public function create(User $user): bool
    {
        return $user->can('weights.create');
    }

    public function update(User $user, WeightRecord $weightRecord): bool
    {
        return $user->can('weights.update');
    }

    public function delete(User $user, WeightRecord $weightRecord): bool
    {
        return $user->can('weights.update');
    }
}
