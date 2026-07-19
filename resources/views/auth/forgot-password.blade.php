<x-layouts.guest>
    <h1 class="h4 mb-3">Forgot password</h1>
    <p class="text-muted">Enter your email address and FarmHisab will send a password reset link.</p>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
        </div>
        <button type="submit" class="btn btn-success w-100">Send reset link</button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}">Back to login</a>
    </div>
</x-layouts.guest>
