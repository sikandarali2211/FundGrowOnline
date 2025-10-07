@extends('layouts.user')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                <div class="email-change-container">
                    <!-- Logo/Title Section -->
                    <div class="text-center mb-4">
                        <div class="logo-icon mb-3">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4 class="page-title">CHANGE EMAIL</h4>
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

                    <!-- Form -->
                    <form action="{{ route('user.update.email') }}" method="POST">
                        @csrf

                        <!-- Current Email -->
                        <div class="form-group">
                            <label for="current_email" class="form-label">Current Email Address</label>
                            <input type="email"
                                class="form-control @error('current_email') is-invalid @enderror"
                                id="current_email"
                                name="current_email"
                                value="{{ old('current_email', $user->email) }}"
                                readonly
                                placeholder="current@example.com">
                            @error('current_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Email -->
                        <div class="form-group">
                            <label for="new_email" class="form-label">New Email Address</label>
                            <input type="email"
                                class="form-control @error('new_email') is-invalid @enderror"
                                id="new_email"
                                name="new_email"
                                value="{{ old('new_email') }}"
                                placeholder="newemail@example.com"
                                required>
                            @error('new_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm New Email -->
                        <div class="form-group">
                            <label for="confirm_email" class="form-label">Confirm New Email Address</label>
                            <input type="email"
                                class="form-control @error('confirm_email') is-invalid @enderror"
                                id="confirm_email"
                                name="confirm_email"
                                value="{{ old('confirm_email') }}"
                                placeholder="newemail@example.com"
                                required>
                            @error('confirm_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Security Notice -->
                        <div class="security-notice mb-4">
                            <i class="fas fa-shield-alt me-2"></i>
                            <span>Changing your email will update your login credentials. Make sure you have access to the new email.</span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-update">
                            UPDATE EMAIL
                        </button>

                       
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Main Container */
    .main-panel {
        background: linear-gradient(135deg, #0a3d4a 0%, #0d5563 50%, #106d7d 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 60px 20px;
    }

    .content-wrapper {
        width: 100%;
        background: transparent;
    }

    /* Email Change Container */
    .email-change-container {
        background: linear-gradient(145deg, #0f3840, #0a2f38);
        border-radius: 20px;
        padding: 50px 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(34, 211, 238, 0.1);
    }

    /* Logo Icon */
    .logo-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #22d3ee, #14b8a6);
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(32, 147, 90, 1);
    }

    .logo-icon i {
        font-size: 2.5rem;
        color: #0a3d4a;
    }

    /* Page Title */
    .page-title {
        color: #ffffff;
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: 2px;
        margin-top: 15px;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        color: #22d3ee;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 10px;
        display: block;
        letter-spacing: 0.5px;
    }

    /* Form Controls */
    .form-control {
        background: rgba(255, 255, 255, 0.05);
        border: 2px solid rgba(34, 211, 238, 0.2);
        color: #ffffff;
        border-radius: 10px;
        padding: 15px 20px;
        font-size: 15px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.08);
        border-color: #22d3ee;
        box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.1);
        color: #ffffff;
        outline: none;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .form-control[readonly] {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(34, 211, 238, 0.1);
        color: rgba(255, 255, 255, 0.5);
        cursor: not-allowed;
    }

    /* Security Notice */
    .security-notice {
        background: rgba(34, 211, 238, 0.08);
        border: 1px solid rgba(34, 211, 238, 0.2);
        border-radius: 10px;
        padding: 15px 18px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 13px;
        display: flex;
        align-items: flex-start;
        line-height: 1.6;
    }

    .security-notice i {
        color: #22d3ee;
        margin-top: 2px;
        font-size: 16px;
    }

    /* Update Button */
    .btn-update {
        background: linear-gradient(135deg, #22d3ee, #14b8a6);
        color: #0a3d4a;
        border: none;
        border-radius: 10px;
        padding: 16px 30px;
        font-weight: 700;
        font-size: 15px;
        letter-spacing: 1px;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(34, 211, 238, 0.3);
        margin-bottom: 20px;
    }

    .btn-update:hover {
        background: linear-gradient(135deg, #14b8a6, #22d3ee);
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(34, 211, 238, 0.4);
    }

    /* Divider */
    .divider-section {
        text-align: center;
        margin: 30px 0;
        position: relative;
    }

    .divider-section::before,
    .divider-section::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 42%;
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
    }

    .divider-section::before {
        left: 0;
    }

    .divider-section::after {
        right: 0;
    }

    .divider-section span {
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        font-weight: 500;
        padding: 0 15px;
        background: linear-gradient(145deg, #0f3840, #0a2f38);
    }

    /* Back Button */
    .btn-back {
        background: transparent;
        color: #22d3ee;
        border: 2px solid #22d3ee;
        border-radius: 10px;
        padding: 14px 30px;
        font-weight: 600;
        font-size: 14px;
        width: 100%;
        transition: all 0.3s ease;
        display: inline-block;
        text-align: center;
        text-decoration: none;
    }

    .btn-back:hover {
        background: rgba(34, 211, 238, 0.1);
        color: #22d3ee;
        border-color: #22d3ee;
        transform: translateY(-2px);
    }

    /* Alerts */
    .alert {
        border-radius: 10px;
        border: none;
        padding: 15px 20px;
        margin-bottom: 25px;
        font-size: 14px;
    }

    .alert-success {
        background: rgba(34, 211, 238, 0.15);
        color: #22d3ee;
        border-left: 4px solid #22d3ee;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #fca5a5;
        border-left: 4px solid #ef4444;
    }

    .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.6;
    }

    .btn-close:hover {
        opacity: 1;
    }

    /* Validation */
    .invalid-feedback {
        color: #fca5a5;
        font-size: 13px;
        margin-top: 6px;
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .email-change-container {
            padding: 40px 30px;
        }

        .page-title {
            font-size: 1.3rem;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
        }

        .logo-icon i {
            font-size: 2rem;
        }
    }

    @media (max-width: 576px) {
        .main-panel {
            padding: 20px 15px;
        }

        .email-change-container {
            padding: 30px 20px;
        }

        .form-control {
            padding: 13px 16px;
            font-size: 14px;
        }

        .btn-update,
        .btn-back {
            padding: 14px 24px;
            font-size: 14px;
        }
    }
</style>
@endsection