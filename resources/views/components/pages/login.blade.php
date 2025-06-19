<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - LSP UPNVJ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
        }

        .login-container {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
        }

        .left-panel {
            background-color: #f25c05;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            width: 100%;
            max-width: 460px;
            flex-shrink: 0;
            height: 100dvh;
        }

        .left-panel img {
            width: 120px;
            margin-bottom: 1rem;
        }

        .left-panel h4 {
            margin-bottom: 2rem;
            font-weight: bold;
        }

        .login-form {
            width: 100%;
            max-width: 320px;
        }

        .right-panel {
            flex-grow: 1;
            height: 100vh;
        }

        .right-panel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-login {
            background-color: #1a1a1a;
            color: white;
        }

        .btn-login:hover {
            background-color: #000;
        }

        @media (max-width: 768px) {
        .login-container {
            flex-direction: column;
        }

        .right-panel {
            display: none;
        }

        .left-panel {
            max-width: 100%;
            height: 100vh;
            justify-content: center;
        }

        .login-form {
            max-width: 90%;
        }
    }

    </style>
</head>
<body>
    <div class="login-container">
        {{-- Panel Form --}}
        <div class="left-panel">
            <img src="{{ asset('img/logo.png') }}" alt="Logo LSP UPNVJ">
            <h4>LOGIN</h4>

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="login-form">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Password" value="{{ old('password') }}" required>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login w-100">LOGIN</button>
            </form>
        </div>

        {{-- Panel Gambar --}}
        <div class="right-panel">
            <img src="{{ asset('img/bg-login.png') }}" alt="Login Illustration">
        </div>
    </div>
</body>
</html>
