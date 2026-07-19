<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\AccessControl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\PermissionRegistrar;

class DefaultAdminSeeder extends Seeder
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function run(): void
    {
        $data = [
            'name' => env('DEFAULT_ADMIN_NAME'),
            'email' => env('DEFAULT_ADMIN_EMAIL'),
            'password' => env('DEFAULT_ADMIN_PASSWORD'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $user->forceFill([
                'name' => $data['name'],
                'is_active' => true,
            ])->save();
        } else {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);
        }

        $user->syncRoles(AccessControl::ROLE_ADMIN);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
