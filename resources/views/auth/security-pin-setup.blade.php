<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Security PIN - FundGrow Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .security-card {
            background: #072d42;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid #3bd17a;
        }
        .security-icon {
            background: #3bd17a;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 3.75rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #3bd17a;
            padding: 12px 15px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #3bd17a;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3bd17a 0%, #072d42 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: #071d33;
            border: 2px solid #3bd17a;
            border-right: none;
            border-radius: 20px 0 0 20px;
            width: 3.75rem;
            height: 3rem;
        }
        .input-group-text i {
         margin-left: 0.75rem;
        }
        .form-control {
            border-left: none;
            border-radius: 0 20px 20px 0;
             height: 3rem;
             background :#101b1f;
        }
        .pin-input {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            font-family: 'Courier New', monospace;
        }
        .security-notice {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        /* Step Indicator Container */
.step-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
}

/* Individual Steps */
.step {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #ddd;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    margin: 0 10px;
    transition: all 0.3s ease;
    cursor: pointer;
}

/* Active Step */
.step.active {
    background-color: #3bd17a; /* Green */
    color: #fff;
}

/* Inactive Step */
.step:not(.active) {
    background-color: #bbb;
}

/* Optional: Adding a line between steps */
.step:not(:last-child)::after {
    content: '';
    width: 20px;
    height: 2px;
    background-color: #bbb;
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
}

    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="security-card p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt security-icon"></i>
                        <h2 class="mt-3 mb-2"  style="color: #3bd17a">Security PIN Setup</h2>
                        <p class="text-muted">Secure your account with a 6-digit PIN</p>
                    </div>

                    <!-- Step Indicator (temporarily single step) -->
                    <div class="step-indicator">
                        <div class="step active" id="step2">1</div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form id="pinSetupForm" method="POST" action="{{ route('security.pin.setup.store') }}">
                        @csrf
                        
                        <!-- Step 1 removed temporarily: OTP Verification (hidden) -->

                        <!-- PIN Setup -->
                        <div id="pinStep" class="step-content" style="display: block;">
                            <div class="mb-4">
                                <h5 class="text-center mb-3"  style="color: #3bd17a">Set Your Security PIN</h5>
                                <p class="text-muted text-center small">
                                    Create a 6-digit PIN that you'll use for sensitive operations.
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="security_pin" class="form-label text-white">Security PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i  style="color: #3bd17a" class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control pin-input" 
                                           id="security_pin" 
                                           name="security_pin" 
                                           placeholder="000000"
                                           maxlength="6"
                                           pattern="[0-9]{6}"
                                           required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="security_pin_confirmation" class="form-label text-white">Confirm Security PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i  style="color: #3bd17a" class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control pin-input" 
                                           id="security_pin_confirmation" 
                                           name="security_pin_confirmation" 
                                           placeholder="000000"
                                           maxlength="6"
                                           pattern="[0-9]{6}"
                                           required>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-shield-alt me-2"></i>Setup Security PIN
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // OTP elements hidden/removed temporarily
            const pinStep = document.getElementById('pinStep');
            const step2 = document.getElementById('step2');
            // Show PIN step by default
            pinStep.style.display = 'block';

            // Allow only numbers in PIN inputs
            document.querySelectorAll('.pin-input').forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });

            // Auto-focus next input when 6 digits entered
            document.querySelectorAll('.pin-input').forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.length === 6) {
                        const nextInput = this.parentElement.parentElement.nextElementSibling?.querySelector('.pin-input');
                        if (nextInput) {
                            nextInput.focus();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
