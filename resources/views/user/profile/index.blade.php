@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <!-- User Profile Sidebar -->
            <div class="card">
                <div class="card-header text-center">
                    <h5 class="mb-0">User Profile</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h6>{{ Auth::user()->name }}</h6>
                    <p class="text-muted small">{{ Auth::user()->email }}</p>
                    <span class="badge bg-success">Active Member</span>
                </div>
                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="mb-0">Level</h6>
                            <small class="text-muted">{{ Auth::user()->level ?? '1' }}</small>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-0">Referrals</h6>
                            <small class="text-muted">{{ Auth::user()->referral_count ?? '0' }}</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-md-9">
            <!-- Profile Tabs -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Personal Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="wallet-tab" data-bs-toggle="tab" data-bs-target="#wallet" type="button" role="tab">
                                <i class="fas fa-wallet me-2"></i>Wallet Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Security
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Personal Information Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            <h5 class="mb-4">Personal Information</h5>
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" value="{{ Auth::user()->name }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" placeholder="Enter phone number">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <select class="form-select" id="country">
                                            <option selected>Select Country</option>
                                            <option value="US">United States</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="CA">Canada</option>
                                            <option value="AU">Australia</option>
                                            <option value="IN">India</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </form>
                        </div>

                        <!-- Account Information Tab -->
                        <div class="tab-pane fade" id="wallet" role="tabpanel">
                            <h5 class="mb-4">Account Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-user me-2"></i>Account Status
                                            </h6>
                                            <p class="card-text">
                                                <span class="badge bg-success">Active</span>
                                            </p>
                                            <small class="text-muted">Your account is active and ready to use</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-chart-line me-2"></i>Investment Status
                                            </h6>
                                            <p class="card-text">
                                                <span class="text-muted">No active investments</span>
                                            </p>
                                            <a href="{{ route('user.plans.index') }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-plus me-2"></i>Start Investing
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <h5 class="mb-4">Security Settings</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-key me-2"></i>Change Password
                                            </h6>
                                            <form>
                                                <div class="mb-3">
                                                    <label for="current-password" class="form-label">Current Password</label>
                                                    <input type="password" class="form-control" id="current-password">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="new-password" class="form-label">New Password</label>
                                                    <input type="password" class="form-control" id="new-password">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="confirm-password" class="form-label">Confirm Password</label>
                                                    <input type="password" class="form-control" id="confirm-password">
                                                </div>
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fas fa-save me-2"></i>Update Password
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication
                                            </h6>
                                            <p class="card-text">Add an extra layer of security to your account.</p>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="2fa-toggle">
                                                <label class="form-check-label" for="2fa-toggle">
                                                    Enable 2FA
                                                </label>
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
    </div>
</div>

@endsection
