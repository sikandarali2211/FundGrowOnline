<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>FundGrow-Online — Register</title>

    <!-- Bootstrap & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />

    <style>
        :root {
            --dark: #072d42;
            --light: #3bd17a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--dark), var(--light));
            color: #fff;
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

        /* ===== Logo ===== */
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
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: #fff;
            background: transparent;
            text-decoration: none;
            transition: all 0.3s ease;
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
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            color: var(--light);
        }

        .form-control {
            padding: 0.75rem 1rem;
            border: 1px solid var(--light);
            border-radius: 6px;
            font-size: 0.95rem;
            background-color: transparent;
            color: #fff;
        }

        .form-control::placeholder {
            color: #ccc;
        }

        .form-control:focus {
            border-color: var(--light);
            box-shadow: 0 0 0 2px rgba(59, 209, 122, 0.2);
        }

        .input-group-text {
            background: var(--light);
            border: none;
            cursor: pointer;
            color: var(--dark);
        }

        .submit-btn {
            width: 100%;
            padding: 0.9rem;
            background-color: var(--light);
            color: var(--dark);
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background-color: #2fa665;
        }

        .google-signin-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px 20px;
            margin-bottom: 16px;
            background: var(--light);
            color: var(--dark);
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .google-signin-btn:hover {
            background: #2fa665;
            color: var(--dark);
        }

        .google-icon {
            width: 18px;
            height: 18px;
            margin-right: 12px;
        }

        .text-danger small { display: block; margin-top: 6px; }
        .badge-lock {
            background: rgba(59, 209, 122, 0.15);
            border: 1px solid var(--light);
            color: #bff1d1;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            margin-left: 6px;
        }

        /* Mobile */
        @media (max-width: 768px) {
            body {
                padding: 30px 15px;
            }

            .login-container {
                max-width: 100%;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Logo on Top -->
        <div class="logo">
            <img src="/assets/images/logo/FundGrow-logo.png" alt="Fund-Grow">
        </div>

        <div class="toggle-buttons">
            <a href="{{ route('login') }}" class="toggle-btn">Login</a>
            <a href="{{ route('register') }}" class="toggle-btn active">Register</a>
        </div>

        {{-- Global validation errors (optional) --}}
        @if ($errors->any())
            <div class="alert alert-danger text-start py-2">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li class="small">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" novalidate>
            @csrf

            {{-- Lock referral if came from ?ref= --}}
            @php
                $refParam = $ref ?? request('ref'); // $ref passed from controller showRegistrationForm
                $isLocked = !empty($refParam);
            @endphp
            @if($isLocked)
                <input type="hidden" name="ref_lock" value="1">
            @endif

            <div class="form-group mb-3 text-start">
                <label for="referral" class="form-label">
                    Referral Code
                    @if($isLocked)
                        <span class="badge-lock"><i class="fa fa-lock me-1"></i>Locked</span>
                    @else
                        (Optional)
                    @endif
                </label>
                <input
                    id="referral"
                    type="text"
                    name="referral"
                    class="form-control @error('referral') is-invalid @enderror"
                    placeholder="e.g. ABC123"
                    value="{{ old('referral', $refParam) }}"
                    @if($isLocked) readonly @endif
                >
                @error('referral')
                    <div class="text-danger"><small>{{ $message }}</small></div>
                @enderror
            </div>

            <div class="form-group mb-3 text-start">
                <label for="name" class="form-label">Enter Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    placeholder="Your full name"
                    required
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                >
                @error('name')
                    <div class="text-danger"><small>{{ $message }}</small></div>
                @enderror
            </div>

            <div class="form-group mb-3 text-start">
                <label for="email" class="form-label">Enter Your Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    placeholder="you@example.com"
                    required
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                >
                @error('email')
                    <div class="text-danger"><small>{{ $message }}</small></div>
                @enderror
            </div>

            <div class="form-group mb-3 text-start">
                <label for="phone" class="form-label">Enter Phone Number</label>
                <input
                    id="phone"
                    type="text"
                    name="phone"
                    placeholder="+92 3XX XXXXXXX"
                    required
                    class="form-control @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}"
                >
                @error('phone')
                    <div class="text-danger"><small>{{ $message }}</small></div>
                @enderror
            </div>

            <div class="form-group mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="form-control @error('password') is-invalid @enderror"
                    >
                    <span class="input-group-text" onclick="togglePassword('password', this)"><i class="fas fa-eye"></i></span>
                </div>
                @error('password')
                    <div class="text-danger"><small>{{ $message }}</small></div>
                @enderror
            </div>

            <div class="form-group mb-4 text-start">
                <label for="password-confirm" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input
                        id="password-confirm"
                        type="password"
                        name="password_confirmation"
                        required
                        class="form-control"
                    >
                    <span class="input-group-text" onclick="togglePassword('password-confirm', this)"><i class="fas fa-eye"></i></span>
                </div>
            </div>

            {{-- Keep your existing Google link; replace "#" with your route if available and you want to forward ref:
                 href="{{ route('google.redirect', ['ref' => $refParam]) }}" --}}
            <a href="#" class="google-signin-btn">
                <img src="https://www.google.com/favicon.ico" class="google-icon" alt="Google"> Sign in with Google
            </a>

            <button type="submit" class="submit-btn">REGISTER</button>
        </form>
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
