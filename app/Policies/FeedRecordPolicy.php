<?php

namespace App\Policies;

use App\Models\FeedRecord;
use App\Models\User;

class FeedRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('feed.view') || $user->can('feed-usage.create');
    }

    public function view(User $user, FeedRecord $feedRecord): bool
    {
        return $user->can('feed.view') || $user->can('feed-usage.create');
    }

    public function create(User $user): bool
    {
        return $user->can('feed.manage') || $user->can('feed-usage.create');
    }

    public function update(User $user, FeedRecord $feedRecord): bool
    {
        return $user->can('feed.manage');
    }

    public function delete(User $user, FeedRecord $feedRecord): bool
    {
        return $user->can('feed.manage');
    }
}
