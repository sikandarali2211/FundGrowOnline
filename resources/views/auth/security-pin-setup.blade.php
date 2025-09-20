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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .security-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .security-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 3rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        .btn-outline-primary:hover {
            background: #667eea;
            color: white;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .pin-input {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            font-family: 'Courier New', monospace;
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
                        <h2 class="mt-3 mb-2">Security PIN Setup</h2>
                        <p class="text-muted">Secure your account with a 6-digit PIN</p>
                    </div>

                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active" id="step1">1</div>
                        <div class="step" id="step2">2</div>
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
                        
                        <!-- Step 1: OTP Verification -->
                        <div id="otpStep" class="step-content">
                            <div class="mb-4">
                                <h5 class="text-center mb-3">Step 1: Verify Your Email Address</h5>
                                <p class="text-muted text-center small">
                                    We'll send a 6-digit OTP to your registered email address to verify your identity.
                                </p>
                            </div>

                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="text" class="form-control" value="{{ auth()->user()->email }}" readonly>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-center">
                                    <div class="d-inline-flex align-items-center bg-light rounded p-3">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <span class="text-muted">OTP will be sent to your email address</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mb-4">
                                <button type="button" class="btn btn-outline-primary" id="sendOTPBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Send OTP
                                </button>
                                <div id="otpStatus" class="mt-2 small"></div>
                            </div>

                            <div class="mb-3" id="otpInputGroup" style="display: none;">
                                <label for="otp_code" class="form-label">Enter OTP Code</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control pin-input" 
                                           id="otp_code" 
                                           name="otp_code" 
                                           placeholder="000000"
                                           maxlength="6"
                                           pattern="[0-9]{6}"
                                           required>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-primary" id="verifyOTPBtn" style="display: none;">
                                    <i class="fas fa-check me-2"></i>Verify OTP
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: PIN Setup -->
                        <div id="pinStep" class="step-content" style="display: none;">
                            <div class="mb-4">
                                <h5 class="text-center mb-3">Step 2: Set Your Security PIN</h5>
                                <p class="text-muted text-center small">
                                    Create a 6-digit PIN that you'll use for sensitive operations.
                                </p>
                            </div>

                            <div class="mb-3">
                                <label for="security_pin" class="form-label">Security PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
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
                                <label for="security_pin_confirmation" class="form-label">Confirm Security PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
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
            const sendOTPBtn = document.getElementById('sendOTPBtn');
            const verifyOTPBtn = document.getElementById('verifyOTPBtn');
            const otpInputGroup = document.getElementById('otpInputGroup');
            const otpStatus = document.getElementById('otpStatus');
            const otpStep = document.getElementById('otpStep');
            const pinStep = document.getElementById('pinStep');
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');
            const otpInput = document.getElementById('otp_code');

            // Send OTP
            sendOTPBtn.addEventListener('click', function() {
                sendOTPBtn.disabled = true;
                sendOTPBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
                
                fetch('{{ route("security.pin.send-otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        otpStatus.innerHTML = '<span class="text-success"><i class="fas fa-check me-1"></i>OTP sent successfully!</span>';
                        otpInputGroup.style.display = 'block';
                        verifyOTPBtn.style.display = 'inline-block';
                        sendOTPBtn.style.display = 'none';
                    } else {
                        otpStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Failed to send OTP</span>';
                        sendOTPBtn.disabled = false;
                        sendOTPBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send OTP';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    otpStatus.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i>Error sending OTP</span>';
                    sendOTPBtn.disabled = false;
                    sendOTPBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send OTP';
                });
            });

            // Verify OTP
            verifyOTPBtn.addEventListener('click', function() {
                if (otpInput.value.length === 6) {
                    // Move to PIN setup step
                    step1.classList.remove('active');
                    step1.classList.add('completed');
                    step2.classList.add('active');
                    
                    otpStep.style.display = 'none';
                    pinStep.style.display = 'block';
                } else {
                    alert('Please enter a valid 6-digit OTP code.');
                }
            });

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
