<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FundGrow-Online - Reset Password</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

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

        .shield-icon {
            width: 80px;
            height: 80px;
            background: rgba(59, 209, 122, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .shield-icon i {
            font-size: 36px;
            color: var(--light);
        }

        h3 {
            color: var(--light);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #ccc;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
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

        .form-control::placeholder {
            color: #999;
        }

        .form-control:focus {
            border-color: var(--light);
            box-shadow: 0 0 0 2px rgba(59, 209, 122, .2);
            background: transparent;
            color: #fff;
        }

        .input-group-text {
            background: var(--light);
            color: var(--dark);
            border: none;
            cursor: pointer;
        }

        .password-hint {
            color: #999;
            font-size: 0.8rem;
            text-align: left;
            margin-top: 0.3rem;
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
            transition: .3s;
        }

        .submit-btn:hover {
            background: #2fa665;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: .6rem;
        }

        .footer-link {
            color: var(--light);
            text-decoration: none;
            font-size: .85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        .invalid-feedback {
            display: block;
            text-align: left;
            color: #ff6b6b;
        }

        .alert {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Shield Icon on Top -->
        <div class="logo">
            <div class="shield-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>

        <h3>{{ __('Reset Password') }}</h3>
        <p class="subtitle">{{ __('Enter your new password below') }}</p>

        {{-- Session status / flash (optional) --}}
        @if (session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Field -->
            <div class="form-group mb-3 text-start">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email" readonly
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="you@example.com"
                    value="{{ $email ?? old('email') }}"
                    required
                    autocomplete="email"
                    autofocus>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group mb-3 text-start">
                <label for="password" class="form-label">{{ __('New Password') }}</label>
                <div class="input-group">
                    <input id="password" type="password" name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Enter new password"
                        required
                        autocomplete="new-password">
                    <span class="input-group-text" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="password-hint">{{ __('Minimum 8 characters') }}</div>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div class="form-group mb-3 text-start">
                <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                <div class="input-group">
                    <input id="password-confirm" type="password" name="password_confirmation"
                        class="form-control"
                        placeholder="Confirm your password"
                        required
                        autocomplete="new-password">
                    <span class="input-group-text" onclick="togglePassword('password-confirm', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="submit-btn">{{ __('RESET PASSWORD') }}</button>
        </form>

        <div class="footer-links">
            <a href="{{ route('login') }}" class="footer-link">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Login') }}
            </a>
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