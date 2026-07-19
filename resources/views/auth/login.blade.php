<x-layouts.guest>
    <h1 class="h4 mb-4">{{ __('auth.login') }}</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('auth.email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('auth.password_label') }}</label>
            <input id="password" type="password" name="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input id="remember" type="checkbox" name="remember" value="1" class="form-check-input">
            <label for="remember" class="form-check-label">{{ __('auth.remember_me') }}</label>
        </div>
        <button type="submit" class="btn btn-success w-100">{{ __('auth.login') }}</button>
    </form>
    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('password.request') }}">{{ __('auth.forgot_password') }}</a>
        <a href="{{ route('register') }}">{{ __('auth.create_account') }}</a>
    </div>
</x-layouts.guest>
