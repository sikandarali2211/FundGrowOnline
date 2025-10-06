<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FundGrow-Online - Forgot Password</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

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

        .lock-icon {
            width: 80px;
            height: 80px;
            background: rgba(59, 209, 122, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .lock-icon i {
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
            border-radius: 8px;
        }

        .alert-success {
            background: rgba(59, 209, 122, 0.2);
            color: var(--light);
            border: 1px solid var(--light);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Lock Icon on Top -->
        <div class="logo">
            <div class="lock-icon">
                <i class="fas fa-lock"></i>
            </div>
        </div>

        <h3>{{ __('Forgot Password?') }}</h3>
        <p class="subtitle">{{ __('Enter your email to receive a password reset link') }}</p>

        {{-- Session status --}}
        @if (session('status'))
            <div class="alert alert-success py-2">
                <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
            </div>
        @endif

        <form id="forgotForm" method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Field -->
            <div class="form-group mb-4 text-start">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input id="email" type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" 
                    placeholder="you@example.com"
                    value="{{ old('email') }}" 
                    required 
                    autocomplete="email" 
                    autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">{{ __('SEND RESET LINK') }}</button>
        </form>

        <div class="footer-links">
            <a href="{{ route('login') }}" class="footer-link">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Login') }}
            </a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        const form = document.getElementById('forgotForm');
        const submitBtn = document.getElementById('submitBtn');
        const emailInput = document.getElementById('email');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = (emailInput.value || '').trim();
            if (!email) {
                Toastify({ text: 'Please enter your email', backgroundColor: '#ff6b6b' }).showToast();
                return;
            }

            submitBtn.disabled = true;

            fetch('{{ route('auth.check-email-exists') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ email })
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.exists) {
                    form.submit();
                } else {
                    Toastify({ text: 'Email not found', backgroundColor: '#ff6b6b' }).showToast();
                    submitBtn.disabled = false;
                }
            })
            .catch(() => {
                Toastify({ text: 'Unable to verify email right now', backgroundColor: '#ff6b6b' }).showToast();
                submitBtn.disabled = false;
            });
        });
    </script>
</body>

</html>