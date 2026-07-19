<x-layouts.guest>
    <h1 class="h4 mb-4">{{ __('auth.register') }}</h1>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('auth.name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('auth.password_label') }}</label>
            <input id="password" type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('auth.confirm_password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">{{ __('auth.register') }}</button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}">{{ __('auth.already_have_account') }}</a>
    </div>
</x-layouts.guest>
