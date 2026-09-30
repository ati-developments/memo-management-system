<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.theme-init')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | Memo Management System</title>
    @include('auth.styles')
</head>
<body>
<div class="auth-theme-toggle">@include('layouts.theme-toggle')</div>
    <main class="login-page">
        <section class="login-area">
            @include('auth.brand')
            <div class="login-card">
                <p class="form-eyebrow">Your workspace, ready when you are</p>
                <header class="login-header"><h1>Welcome back.</h1><p>Enter your credentials to sign in to your account.</p></header>
                @if(session('success'))
                    <div class="alert alert-success" role="status"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg><span>{{ session('success') }}</span></div>
                @endif
                @if($errors->any())
                    <div class="alert alert-error" role="alert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg><span>{{ $errors->first() }}</span></div>
                @endif
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="form-group"><label for="username">Username</label><div class="input-wrap"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><circle cx="12" cy="8" r="3.25"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0"/></svg><input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" placeholder="Enter your username" autocomplete="username" required autofocus></div></div>
                    <div class="form-group"><label for="password">Password</label><div class="input-wrap"><svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg><input type="password" id="password" name="password" class="form-control password-input" placeholder="Enter your password" autocomplete="current-password" required><button class="toggle-password" type="button" aria-label="Show password" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg></button></div></div>
                    <button type="submit" class="login-button">Sign in <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></button>
                </form>
                <p class="register-link">New to Memo? <a href="{{ route('register') }}">Create an account</a></p>
            </div>
            <p class="auth-footer">Memo Management System &middot; Your organization, connected.</p>
        </section>
    </main>
    <script>
        const passwordField = document.getElementById('password');
        const passwordToggle = document.querySelector('.toggle-password');
        passwordToggle.addEventListener('click', () => {
            const isHidden = passwordField.type === 'password';
            passwordField.type = isHidden ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', String(isHidden));
            passwordToggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    </script>
@include('layouts.theme-styles')
</body>
</html>
