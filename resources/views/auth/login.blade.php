<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FundGrow-Online Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <style>
        :root {
            --dark: #072d42;
            --light: #3bd17a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark), var(--light));
            color: #fff;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background: var(--dark);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(59, 209, 122, 0.4);
            text-align: center;
        }

        .logo {
            margin-bottom: 1.5rem;
        }

        .logo img {
            max-width: 120px;
            height: auto;
        }

        .toggle-buttons {
            display: flex;
            justify-content: center;
            border: 1px solid var(--light);
            border-radius: 50px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .toggle-btn {
            flex: 1;
            text-align: center;
            padding: .6rem 1rem;
            font-size: .95rem;
            font-weight: 500;
            color: #fff;
            text-decoration: none;
            transition: all .3s;
        }

        .toggle-btn.active {
            background: var(--light);
            color: var(--dark);
        }

        .toggle-btn:hover,
        .toggle-btn:focus {
            background: var(--light);
            color: var(--dark);
            text-decoration: none;
        }

        .form-label {
            color: var(--light);
            font-weight: 500;
            font-size: .9rem;
        }

        .form-control {
            padding: .75rem;
            border: 1px solid var(--light);
            border-radius: 6px;
            background: transparent;
            color: #fff;
        }

        .form-control:focus {
            border-color: var(--light);
            box-shadow: 0 0 0 2px rgba(59, 209, 122, .2);
        }

        .input-group-text {
            background: var(--light);
            color: var(--dark);
            border: none;
            cursor: pointer;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: .85rem;
        }

        .remember-me label {
            margin-left: .4rem;
            color: #ccc;
        }

        .submit-btn {
            width: 100%;
            padding: .9rem;
            background: var(--light);
            color: var(--dark);
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #2fa665;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.2rem 0;
            font-size: .85rem;
            color: #ccc;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid var(--light);
            margin: 0 8px;
        }

        .google-signin-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: .8rem;
            background: var(--light);
            color: var(--dark);
            border-radius: 6px;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-decoration: none;
            transition: .3s;
        }

        .google-signin-btn:hover {
            background: #2fa665;
        }

        .google-icon {
            width: 18px;
            margin-right: 10px;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .6rem;
        }

        .footer-link {
            color: var(--light);
            text-decoration: none;
            font-size: .85rem;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        .invalid-feedback {
            display: block;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Logo on Top -->
        <div class="logo">
            <img src="{{ asset('assets/images/logo/FundGrow-logo.png') }}" alt="Fund-Grow">
        </div>

        <div class="toggle-buttons">
            <a href="{{ route('login') }}" class="toggle-btn active">Login</a>
            <a href="{{ route('register') }}" class="toggle-btn">Register</a>
        </div>

        {{-- Session status / flash (optional) --}}
        @if (session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif

        {{-- 🔗 Connect form to Laravel auth --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group mb-3 text-start">
                <label for="email" class="form-label">Enter Your Email</label>
                <input id="email" type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com"
                    value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror" required>
                    <span class="input-group-text" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-me">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Remember Me</label>
            </div>

            <button type="submit" class="submit-btn">LOGIN</button>
        </form>

        <div class="divider">OR</div>

        {{-- Social login (enable later if using Socialite) --}}
        <a href="" class="google-signin-btn">
            <img src="https://www.google.com/favicon.ico" class="google-icon" alt="Google"> Sign in with Google
        </a>

        <div class="footer-links">
            <a href="{{ route('register') }}" class="footer-link">REGISTER</a>
            <a href="{{ route('password.request') }}" class="footer-link">Forgot Your Password?</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconSpan) {
            const field = document.getElementById(fieldId);
            const icon = iconSpan.querySelector('i');
            if (field.type === "password") {
                field.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>

</html>
