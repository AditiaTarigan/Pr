<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('foto/del.png') }}">
    <title>Login - SIPA</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    {{-- Bootstrap Icons sudah di-import via login.css --}}
    <style>
        /* --- INI BAGIAN YANG DIPERBARUI (dari kode Anda, dipertahankan) --- */
        .field .error-message {
            color: #D8000C;
            font-size: 0.875rem;
            margin-top: 0.3rem;
            display: block;
            text-align: left;
            padding-left: 2px;
            width: 100%;
        }

        .alert.alert-danger {
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
            border-radius: 0.25rem;
            background-color: #f8d7da;
            color: #721c24;
            box-sizing: border-box;
            text-align: left;
        }

        .alert.alert-danger ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .alert.alert-danger li {
            margin-bottom: 0.25rem;
        }
        .alert.alert-danger li:last-child {
            margin-bottom: 0;
        }
        /* --- AKHIR BAGIAN YANG DIPERBARUI --- */

        .logo-besar { /* Gaya ini dari kode Anda, dipertahankan */
            width: 200px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-wrapper">
            <img src="{{ asset('foto/sipa.png') }}" alt="Logo IT Del" class="logo-besar">
            <h1>Institut Teknologi Del</h1>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                    @foreach ($errors->all() as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                    </ul>
                </div>
            @endif

           <form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="field">
        <div class="input-area">
            <input class="inp" type="email" value="{{ old('email') }}" name="email" id="email" required autocomplete="email" autofocus>
            <label class="label" for="email">Alamat Email</label>
            <span class="bi bi-person"></span> {{-- Ikon untuk email --}}
        </div>
        @error('email')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="field">
        <div class="input-area">
            <input class="inp" type="password" name="password" id="password" required autocomplete="current-password">
            <label class="label" for="password">Password</label>
            <span class="toggle-pass bi bi-eye-slash"></span> {{-- Ikon untuk password --}}
        </div>
        @error('password')
            <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <div class="action">
        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember">Ingat Saya</label>
    </div>

    <div class="forgot-password">
        <a href="{{ route('password.request') }}">Lupa Password?</a>
    </div>

    <input type="submit" value="Login" id="login-btn">
</form>
        </div>
        <div class="bg"></div>
   </div>

<script>
    const passwordInput = document.querySelector('#password');
    const togglePasswordIcon = document.querySelector('.toggle-pass'); // Menargetkan elemen dengan kelas .toggle-pass

    if (togglePasswordIcon && passwordInput) {
        togglePasswordIcon.addEventListener('click', () => {
            // Toggle kelas ikon
            togglePasswordIcon.classList.toggle('bi-eye-slash');
            togglePasswordIcon.classList.toggle('bi-eye');

            // Toggle tipe input password
            passwordInput.type = (passwordInput.type === 'password') ? 'text' : 'password';
        });
    }
</script>
</body>
</html>
