<?php

namespace App\Policies;

use App\Models\DailyRecord;
use App\Models\User;

class DailyRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('daily-records.view');
    }

    public function view(User $user, DailyRecord $dailyRecord): bool
    {
        return $user->can('daily-records.view');
    }

    public function create(User $user): bool
    {
        return $user->can('daily-records.create');
    }

    public function update(User $user, DailyRecord $dailyRecord): bool
    {
        return $user->can('daily-records.update');
    }

    public function delete(User $user, DailyRecord $dailyRecord): bool
    {
        return $user->can('daily-records.update');
    }
}
