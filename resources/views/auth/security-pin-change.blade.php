@extends('layouts.user')
@section('content')
    <style>
        .security-card {
            
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid #3bd17a;
            max-width: 500px;
        }

        .security-icon {
            background: #3bd17a;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.50rem;
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
            background: #101b1f;
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
    </style>
    <div class="main-panel mb-4" style="margin-top:7rem;">
        <div class="container-fluid">
            <div class="row min-vh-100">
                <div class="col-12 d-flex justify-content-center align-items-center">
                    <div class="security-card p-4 w-100">
                        <div class="text-center mb-4">
                            <i class="fas fa-key security-icon"></i>
                            <h3 style="color:#3bd17a " class="mt-3 mb-2">Change Security Pin</h3>
                            <p class="text-white">Update your security PIN for better protection</p>
                        </div>

                        <div class="security-notice">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle text-warning "></i> 
                                <small class="text-white">  
                                      Make sure to remember your new PIN. You'll need it for sensitive operations.
                                </small>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('security.pin.change.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="current_pin" class="form-label text-white">Current Security PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock" style="color: #3bd17a"></i>
                                    </span>
                                    <input type="password" class="form-control pin-input" id="current_pin"
                                        name="current_pin" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required
                                        autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_pin" class="form-label text-white">New Security PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key" style="color: #3bd17a"></i>
                                    </span>
                                    <input type="password" class="form-control pin-input" id="new_pin" name="new_pin"
                                        placeholder="000000" maxlength="6" pattern="[0-9]{6}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="new_pin_confirmation" class="form-label text-white">Confirm New Security
                                    PIN</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-key" style="color: #3bd17a"></i>
                                    </span>
                                    <input type="password" class="form-control pin-input" id="new_pin_confirmation"
                                        name="new_pin_confirmation" placeholder="000000" maxlength="6" pattern="[0-9]{6}"
                                        required>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>  Change PIN
                                </button>
                            </div>

                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pinInputs = document.querySelectorAll('.pin-input');
            const currentPinInput = document.getElementById('current_pin');
            const newPinInput = document.getElementById('new_pin');
            const confirmPinInput = document.getElementById('new_pin_confirmation');

            // Allow only numbers in PIN inputs
            pinInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            });

            // Auto-focus next input when 6 digits entered
            currentPinInput.addEventListener('input', function() {
                if (this.value.length === 6) {
                    newPinInput.focus();
                }
            });

            newPinInput.addEventListener('input', function() {
                if (this.value.length === 6) {
                    confirmPinInput.focus();
                }
            });

            // Validate PIN confirmation
            confirmPinInput.addEventListener('input', function() {
                if (this.value.length === 6) {
                    if (this.value !== newPinInput.value) {
                        this.classList.add('is-invalid');
                        if (!this.nextElementSibling) {
                            const feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            feedback.textContent = 'PIN confirmation does not match';
                            this.parentElement.appendChild(feedback);
                        }
                    } else {
                        this.classList.remove('is-invalid');
                        const feedback = this.parentElement.querySelector('.invalid-feedback');
                        if (feedback) {
                            feedback.remove();
                        }
                    }
                }
            });

            // Form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                if (newPinInput.value !== confirmPinInput.value) {
                    e.preventDefault();
                    alert('New PIN and confirmation do not match!');
                    return false;
                }

                if (currentPinInput.value === newPinInput.value) {
                    e.preventDefault();
                    alert('New PIN must be different from current PIN!');
                    return false;
                }

                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Changing...';
                submitBtn.disabled = true;
            });

            // Focus on first input when page loads
            currentPinInput.focus();
        });
    </script>
@endsection
