<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => (bool) ($data['is_active'] ?? true),
            ]);

            $user->syncRoles($data['role']);

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (auth()->user()?->cannot('changeRole', [$user, $data['role']])) {
                throw ValidationException::withMessages([
                    'role' => 'The last active admin cannot lose the admin role.',
                ]);
            }

            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $user->update($payload);
            $user->syncRoles($data['role']);

            return $user;
        });
    }

    public function setActive(User $user, bool $active): User
    {
        return DB::transaction(function () use ($user, $active) {
            $user->forceFill(['is_active' => $active])->save();

            return $user;
        });
    }
}
