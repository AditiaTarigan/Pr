<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('foto/del.png') }}">
    <title>Reset Password - SIPA</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .field .error-message { color: #D8000C; font-size: 0.875rem; margin-top: 0.3rem; display: block; text-align: left; padding-left: 2px; width: 100%; }
        .logo-besar { width: 200px; height: auto; }
        .alert { width: 100%; padding: 0.75rem 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; box-sizing: border-box; text-align: left; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-wrapper">
            <img src="{{ asset('foto/sipa.png') }}" alt="Logo IT Del" class="logo-besar">
            <h1>Buat Password Baru</h1>

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                {{-- Field tersembunyi untuk token dan email --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                <div class="field">
                    <div class="input-area">
                        <input class="inp" type="password" name="password" id="password" required autocomplete="new-password">
                        <label class="label" for="password">Password Baru</label>
                        <span class="toggle-pass bi bi-eye-slash"></span>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="field">
                    <div class="input-area">
                        <input class="inp" type="password" name="password_confirmation" id="password-confirm" required autocomplete="new-password">
                        <label class="label" for="password-confirm">Konfirmasi Password</label>
                        <span class="toggle-pass bi bi-eye-slash"></span>
                    </div>
                </div>

                <input type="submit" value="Reset Password" id="login-btn">
            </form>
        </div>
        <div class="bg"></div>
    </div>

<script>
    // Script ini lebih baik karena bisa menangani beberapa tombol show/hide di satu halaman
    document.querySelectorAll('.toggle-pass').forEach(icon => {
        icon.addEventListener('click', () => {
            const inputArea = icon.closest('.input-area');
            if (inputArea) {
                const passwordInput = inputArea.querySelector('.inp');
                if (passwordInput) {
                    // Toggle kelas ikon
                    icon.classList.toggle('bi-eye-slash');
                    icon.classList.toggle('bi-eye');
                    // Toggle tipe input password
                    passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
                }
            }
        });
    });
</script>
</body>
</html>
