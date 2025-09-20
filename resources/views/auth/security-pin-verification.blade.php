<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Security PIN - FundGrow Online</title>
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
        .security-notice {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .attempts-warning {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="security-card p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-shield-alt security-icon"></i>
                        <h2 class="mt-3 mb-2">Security Verification</h2>
                        <p class="text-muted">Enter your security PIN to continue</p>
                    </div>

                    <div class="security-notice">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <small class="text-muted">
                                This verification is required for sensitive operations like transactions and payments.
                            </small>
                        </div>
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

                    @if(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('security.pin.verify.store') }}">
                        @csrf
                        
                        <div class="mb-4">
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
                                       required
                                       autofocus>
                            </div>
                            <div class="form-text text-muted">
                                Enter your 6-digit security PIN
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Verify PIN
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('security.pin.change') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-key me-1"></i>Change PIN
                            </a>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Verification expires in 5 minutes
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pinInput = document.getElementById('security_pin');

            // Allow only numbers in PIN input
            pinInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Auto-submit when 6 digits entered
            pinInput.addEventListener('input', function() {
                if (this.value.length === 6) {
                    // Small delay to show the complete PIN
                    setTimeout(() => {
                        this.form.submit();
                    }, 500);
                }
            });

            // Focus on input when page loads
            pinInput.focus();

            // Add some visual feedback
            pinInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && this.value.length === 6) {
                    const submitBtn = this.form.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verifying...';
                    submitBtn.disabled = true;
                }
            });
        });
    </script>
</body>
</html>
