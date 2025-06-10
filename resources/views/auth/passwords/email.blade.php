<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('foto/del.png') }}">
    <title>Lupa Password - SIPA</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .field .error-message { color: #D8000C; font-size: 0.875rem; margin-top: 0.3rem; display: block; text-align: left; padding-left: 2px; width: 100%; }
        .logo-besar { width: 200px; height: auto; }

        /* Style untuk notifikasi */
        .alert {
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            box-sizing: border-box;
            text-align: left;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .back-to-login {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-to-login a {
            font-size: 0.9rem;
            color: #555;
            text-decoration: none;
        }
        .back-to-login a:hover {
            text-decoration: underline;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="form-wrapper">
            <img src="{{ asset('foto/sipa.png') }}" alt="Logo IT Del" class="logo-besar">
            <h1>Lupa Password</h1>
            <p style="margin-top:-10px; margin-bottom:20px; color:#555; font-size:0.95rem;">Masukkan email Anda untuk menerima link reset password.</p>

            {{-- Notifikasi Sukses atau Gagal --}}
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="field">
                    <div class="input-area">
                        <input class="inp" type="email" value="{{ old('email') }}" name="email" id="email" required autocomplete="email" autofocus>
                        <label class="label" for="email">Alamat Email</label>
                        <span class="bi bi-envelope"></span> {{-- Ikon email --}}
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <input type="submit" value="Kirim Link Reset" id="login-btn">
            </form>

            <div class="back-to-login">
                <a href="{{ route('login') }}">Kembali ke halaman Login</a>
            </div>
        </div>
        <div class="bg"></div>
    </div>
</body>
</html>
