@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <!-- Settings Navigation -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="tab" data-bs-target="#general">
                        <i class="fas fa-cog me-2"></i>General
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#notifications">
                        <i class="fas fa-bell me-2"></i>Notifications
                    </a>
                    <a href="#privacy" class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#privacy">
                        <i class="fas fa-user-secret me-2"></i>Privacy
                    </a>
                    <a href="#billing" class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#billing">
                        <i class="fas fa-credit-card me-2"></i>Billing
                    </a>
                    <a href="#api" class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#api">
                        <i class="fas fa-code me-2"></i>API Keys
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">General Settings</h5>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="language" class="form-label">Language</label>
                                        <select class="form-select" id="language">
                                            <option value="en" selected>English</option>
                                            <option value="es">Spanish</option>
                                            <option value="fr">French</option>
                                            <option value="de">German</option>
                                            <option value="hi">Hindi</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="timezone" class="form-label">Timezone</label>
                                        <select class="form-select" id="timezone">
                                            <option value="UTC" selected>UTC</option>
                                            <option value="EST">Eastern Time</option>
                                            <option value="PST">Pacific Time</option>
                                            <option value="GMT">Greenwich Mean Time</option>
                                            <option value="IST">India Standard Time</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="currency" class="form-label">Default Currency</label>
                                        <select class="form-select" id="currency">
                                            <option value="USD" selected>USD - US Dollar</option>
                                            <option value="EUR">EUR - Euro</option>
                                            <option value="GBP">GBP - British Pound</option>
                                            <option value="INR">INR - Indian Rupee</option>
                                            <option value="BTC">BTC - Bitcoin</option>
                                            <option value="ETH">ETH - Ethereum</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="theme" class="form-label">Theme</label>
                                        <select class="form-select" id="theme">
                                            <option value="light" selected>Light</option>
                                            <option value="dark">Dark</option>
                                            <option value="auto">Auto</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Settings
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="tab-pane fade" id="notifications">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Notification Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Email Notifications</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="email-investments" checked>
                                        <label class="form-check-label" for="email-investments">
                                            Investment Updates
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="email-referrals" checked>
                                        <label class="form-check-label" for="email-referrals">
                                            Referral Notifications
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="email-security">
                                        <label class="form-check-label" for="email-security">
                                            Security Alerts
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Push Notifications</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="push-investments" checked>
                                        <label class="form-check-label" for="push-investments">
                                            Investment Updates
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="push-referrals">
                                        <label class="form-check-label" for="push-referrals">
                                            Referral Notifications
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="push-promotions">
                                        <label class="form-check-label" for="push-promotions">
                                            Promotional Offers
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Notification Settings
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div class="tab-pane fade" id="privacy">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Privacy Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Profile Visibility</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="profile-public">
                                        <label class="form-check-label" for="profile-public">
                                            Make Profile Public
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="show-email" checked>
                                        <label class="form-check-label" for="show-email">
                                            Show Email Address
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="show-referrals" checked>
                                        <label class="form-check-label" for="show-referrals">
                                            Show Referral Count
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Data Sharing</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="analytics" checked>
                                        <label class="form-check-label" for="analytics">
                                            Allow Analytics
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="marketing">
                                        <label class="form-check-label" for="marketing">
                                            Marketing Communications
                                        </label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="third-party">
                                        <label class="form-check-label" for="third-party">
                                            Third-party Sharing
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Privacy Settings
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Billing Settings -->
                <div class="tab-pane fade" id="billing">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Billing Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Payment Methods</h6>
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-credit-card me-2"></i>
                                                    <strong>Credit Card</strong>
                                                    <br>
                                                    <small class="text-muted">**** **** **** 1234</small>
                                                </div>
                                                <button class="btn btn-outline-danger btn-sm">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-primary">
                                        <i class="fas fa-plus me-2"></i>Add Payment Method
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <h6>Billing Address</h6>
                                    <form>
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control" id="address" rows="3"></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="city" class="form-label">City</label>
                                                <input type="text" class="form-control" id="city">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="zip" class="form-label">ZIP Code</label>
                                                <input type="text" class="form-control" id="zip">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Update Address
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Keys -->
                <div class="tab-pane fade" id="api">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">API Keys</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                API keys allow you to access your account data programmatically. Keep them secure and never share them publicly.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>Generate New API Key</h6>
                                    <form>
                                        <div class="mb-3">
                                            <label for="api-name" class="form-label">Key Name</label>
                                            <input type="text" class="form-control" id="api-name" placeholder="e.g., My Trading Bot">
                                        </div>
                                        <div class="mb-3">
                                            <label for="api-permissions" class="form-label">Permissions</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="read-permission" checked>
                                                <label class="form-check-label" for="read-permission">
                                                    Read Access
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="write-permission">
                                                <label class="form-check-label" for="write-permission">
                                                    Write Access
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-key me-2"></i>Generate API Key
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <h6>Existing Keys</h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <strong>My Bot</strong>
                                                <button class="btn btn-outline-danger btn-sm">Revoke</button>
                                            </div>
                                            <small class="text-muted">Created: 2024-01-15</small>
                                            <br>
                                            <small class="text-muted">Last used: 2024-01-20</small>
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

<script>
// Handle tab switching
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active class from all nav items
        document.querySelectorAll('.list-group-item').forEach(item => {
            item.classList.remove('active');
        });
        // Add active class to clicked item
        this.classList.add('active');
    });
});
</script>
@endsection
