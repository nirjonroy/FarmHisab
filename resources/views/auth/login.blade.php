<x-layouts.guest>
    <h1 class="h4 mb-4">Login</h1>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input id="remember" type="checkbox" name="remember" value="1" class="form-check-input">
            <label for="remember" class="form-check-label">Remember me</label>
        </div>
        <button type="submit" class="btn btn-success w-100">Login</button>
    </form>
    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('password.request') }}">Forgot password?</a>
        <a href="{{ route('register') }}">Create account</a>
    </div>
</x-layouts.guest>
