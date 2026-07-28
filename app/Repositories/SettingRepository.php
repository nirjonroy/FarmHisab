<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Collection;

class SettingRepository
{
    public function allKeyed(): Collection
    {
        return Setting::query()
            ->pluck('value', 'key');
    }

    public function updateMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'group' => $this->groupFor($key),
                    'value' => $value,
                ]
            );
        }
    }

    private function groupFor(string $key): string
    {
        foreach (config('settings.groups', []) as $group => $keys) {
            if (in_array($key, $keys, true)) {
                return $group;
            }
        }

        return 'general';
    }
}
