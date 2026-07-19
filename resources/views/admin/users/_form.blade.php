@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label for="password" class="form-label">Password</label>
        <input id="password" type="password" name="password" class="form-control" @if (! isset($user)) required @endif>
        @isset($user)
            <div class="form-text">Leave blank to keep the current password.</div>
        @endisset
    </div>
    <div class="col-md-6">
        <label for="password_confirmation" class="form-label">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" @if (! isset($user)) required @endif>
    </div>
    <div class="col-md-6">
        <label for="role" class="form-label">Operational role</label>
        <select id="role" name="role" class="form-select" required>
            @foreach ($roles as $role)
                <option value="{{ $role }}" @selected(old('role', isset($user) ? $user->roles->pluck('name')->first() : '') === $role)>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </select>
    </div>
    @if (! isset($user))
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked(old('is_active', true))>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
        </div>
    @endif
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
