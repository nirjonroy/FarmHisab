<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use App\Support\AccessControl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private UserService $users)
    {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $users = User::query()
            ->with('roles')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => AccessControl::ROLES,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->users->create($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $user->load('roles');

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => AccessControl::ROLES,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->users->update($user, $request->validated());

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        DB::transaction(function () use ($user) {
            $user->delete();
        });

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->is_active) {
            $this->authorize('deactivate', $user);
        } else {
            abort_unless($request->user()->can('users.activate'), 403);
        }

        $this->users->setActive($user, ! $user->is_active);

        return redirect()->route('admin.users.index')->with('success', 'User status updated successfully.');
    }
}
