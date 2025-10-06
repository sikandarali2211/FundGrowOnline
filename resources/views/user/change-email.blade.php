@extends('layouts.user')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-envelope me-2 text-primary"></i>
                                        Change Email Address
                                    </h4>
                                    <a href="{{ route('user.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Success/Error Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row justify-content-center">
                            <div class="col-md-8 col-lg-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="text-center mb-4">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                <i class="fas fa-envelope text-primary" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="mb-2">Update Your Email Address</h5>
                                            <p class="text-muted">Enter your current email and new email address to update your account.</p>
                                        </div>

                                        <form action="{{ route('user.update.email') }}" method="POST">
                                            @csrf
                                            
                                            <!-- Current Email -->
                                            <div class="mb-4">
                                                <label for="current_email" class="form-label fw-semibold">
                                                    <i class="fas fa-envelope me-2 text-muted"></i>Current Email Address
                                                </label>
                                                <input type="email" 
                                                       class="form-control @error('current_email') is-invalid @enderror" 
                                                       id="current_email" 
                                                       name="current_email" 
                                                       value="{{ old('current_email', $user->email) }}" 
                                                       readonly
                                                       style="background-color: #f8f9fa;">
                                                @error('current_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">This is your current registered email address.</small>
                                            </div>

                                            <!-- New Email -->
                                            <div class="mb-4">
                                                <label for="new_email" class="form-label fw-semibold">
                                                    <i class="fas fa-envelope-open me-2 text-success"></i>New Email Address
                                                </label>
                                                <input type="email" 
                                                       class="form-control @error('new_email') is-invalid @enderror" 
                                                       id="new_email" 
                                                       name="new_email" 
                                                       value="{{ old('new_email') }}" 
                                                       placeholder="Enter your new email address"
                                                       required>
                                                @error('new_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Confirm New Email -->
                                            <div class="mb-4">
                                                <label for="confirm_email" class="form-label fw-semibold">
                                                    <i class="fas fa-envelope-check me-2 text-info"></i>Confirm New Email Address
                                                </label>
                                                <input type="email" 
                                                       class="form-control @error('confirm_email') is-invalid @enderror" 
                                                       id="confirm_email" 
                                                       name="confirm_email" 
                                                       value="{{ old('confirm_email') }}" 
                                                       placeholder="Confirm your new email address"
                                                       required>
                                                @error('confirm_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Security Notice -->
                                            <div class="alert alert-warning border-0 mb-4">
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-shield-alt me-3 mt-1 text-warning"></i>
                                                    <div>
                                                        <h6 class="alert-heading mb-2">Security Notice</h6>
                                                        <p class="mb-0 small">
                                                            Changing your email address will update your login credentials. 
                                                            Make sure you have access to the new email address before proceeding.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-primary btn-lg">
                                                    <i class="fas fa-save me-2"></i>Update Email Address
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Main Container */
.main-panel {
    background: linear-gradient(135deg, #041a2f, #072d42 60%);
    min-height: 100vh;
}

.card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.card-body {
    background: transparent;
}

/* Form Container */
.card.border-0.shadow-sm {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(59, 209, 122, 0.2) !important;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4) !important;
}

/* Form Controls */
.form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    border-radius: 10px;
    padding: 12px 16px;
    transition: all 0.3s ease;
    font-size: 14px;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #3bd17a;
    box-shadow: 0 0 0 0.2rem rgba(59, 209, 122, 0.25);
    color: #fff;
}

.form-control[readonly] {
    background: rgba(255, 255, 255, 0.05);
    color: #a0a0a0;
    border-color: rgba(255, 255, 255, 0.1);
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

/* Labels */
.form-label {
    color: #e0e0e0;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
}

/* Icons */
.fa-envelope, .fa-envelope-open, .fa-envelope-check {
    font-size: 16px;
}

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, #3bd17a, #00d4aa);
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 16px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #00d4aa, #3bd17a);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(59, 209, 122, 0.3);
}

.btn-outline-secondary {
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #e0e0e0;
    border-radius: 10px;
    transition: all 0.3s ease;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: #3bd17a;
    color: #3bd17a;
}

/* Alerts */
.alert {
    border-radius: 10px;
    border: none;
    font-size: 14px;
}

.alert-success {
    background: linear-gradient(135deg, rgba(59, 209, 122, 0.2), rgba(0, 212, 170, 0.2));
    color: #3bd17a;
    border-left: 4px solid #3bd17a;
}

.alert-danger {
    background: linear-gradient(135deg, rgba(255, 107, 107, 0.2), rgba(255, 82, 82, 0.2));
    color: #ff6b6b;
    border-left: 4px solid #ff6b6b;
}

.alert-warning {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 179, 0, 0.2));
    color: #ffc107;
    border-left: 4px solid #ffc107;
}

/* Validation */
.invalid-feedback {
    color: #ff6b6b;
    font-size: 0.875rem;
    margin-top: 5px;
}

.is-invalid {
    border-color: #ff6b6b !important;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25) !important;
}

/* Text Colors */
.text-muted {
    color: rgba(255, 255, 255, 0.6) !important;
}

.text-primary {
    color: #3bd17a !important;
}

.text-success {
    color: #3bd17a !important;
}

.text-info {
    color: #00d4aa !important;
}

/* Card Title */
.card-title {
    color: #fff !important;
    font-weight: 700;
    font-size: 1.5rem;
}

/* Icon Container */
.bg-primary.bg-opacity-10 {
    background: rgba(59, 209, 122, 0.2) !important;
    border: 2px solid rgba(59, 209, 122, 0.3);
}

/* Security Notice */
.alert-warning .alert-heading {
    color: #ffc107 !important;
    font-weight: 600;
}

/* Form Text */
.form-text {
    color: rgba(255, 255, 255, 0.7) !important;
    font-size: 0.875rem;
}

/* Page Title */
h4.card-title {
    color: #fff !important;
}

/* Back Button */
.btn-outline-secondary {
    border-color: rgba(255, 255, 255, 0.3);
    color: #e0e0e0;
}

.btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: #3bd17a;
    color: #3bd17a;
}

/* Responsive */
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem;
    }
    
    .form-control {
        padding: 10px 14px;
    }
    
    .btn-primary {
        padding: 10px 20px;
        font-size: 14px;
    }
}
</style>
@endsection
