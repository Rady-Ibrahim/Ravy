<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login</title>
</head>
<body style="font-family: system-ui, sans-serif; max-width: 22rem; margin: 3rem auto;">
    <h1 style="font-size: 1.25rem;">Admin</h1>
    @if ($errors->any())
        <p style="color: #b91c1c;">{{ $errors->first() }}</p>
    @endif
    <form method="post" action="{{ route('admin.auth.login.submit') }}" style="display: grid; gap: 0.75rem;">
        @csrf
        <label>
            Email
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" style="width: 100%; box-sizing: border-box;">
        </label>
        <label>
            Password
            <input type="password" name="password" required autocomplete="current-password" style="width: 100%; box-sizing: border-box;">
        </label>
        <label style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            Remember me
        </label>
        <button type="submit" style="padding: 0.5rem 1rem;">Sign in</button>
    </form>
</body>
</html>
