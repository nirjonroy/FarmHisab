<?php

namespace App\Http\Requests\FeedRecord;

class UpdateFeedRecordRequest extends StoreFeedRecordRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('feed.manage') ?? false;
    }
}
