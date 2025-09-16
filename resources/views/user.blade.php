@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- User Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">Welcome back, {{ Auth::user()->name }}!</h2>
                            <p class="mb-0">Manage your investments and track your progress</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex flex-column align-items-md-end">
                                <span class="badge bg-light text-dark fs-6 mb-2">
                                    Level {{ Auth::user()->level ?? '1' }} Member
                                </span>
                                <small class="text-white-50">
                                    Member since {{ Auth::user()->created_at->format('M Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-wallet fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">Total Investment</h5>
                    <h3 class="text-primary">$0.00</h3>
                    <small class="text-muted">Across all plans</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                    <h5 class="card-title">Total Returns</h5>
                    <h3 class="text-success">$0.00</h3>
                    <small class="text-muted">Lifetime earnings</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-info mb-2"></i>
                    <h5 class="card-title">Referrals</h5>
                    <h3 class="text-info">{{ Auth::user()->referral_count ?? '0' }}</h3>
                    <small class="text-muted">Total referrals</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-trophy fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">Rank</h5>
                    <h3 class="text-warning">#1</h3>
                    <small class="text-muted">In your network</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Recent Activity -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activity</h5>
                    <a href="#" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Account Created</h6>
                                <p class="text-muted small mb-0">{{ Auth::user()->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Welcome Bonus Received</h6>
                                <p class="text-muted small mb-0">$10.00 bonus added to your account</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">First Referral</h6>
                                <p class="text-muted small mb-0">You referred a new member</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Investment Plans -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Available Investment Plans</h5>
                    <a href="{{ route('user.plans.index') }}" class="btn btn-outline-primary btn-sm">View All Plans</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-primary">Basic Plan</h6>
                                    <h4 class="text-primary">5% Daily</h4>
                                    <p class="card-text small">Minimum: $100<br>Maximum: $1,000</p>
                                    <button class="btn btn-primary btn-sm">Invest Now</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-success">Premium Plan</h6>
                                    <h4 class="text-success">7% Daily</h4>
                                    <p class="card-text small">Minimum: $1,000<br>Maximum: $10,000</p>
                                    <button class="btn btn-success btn-sm">Invest Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-4">

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.plans.index') }}" class="btn btn-primary">
                            <i class="fas fa-chart-line me-2"></i>View Investment Plans
                        </a>
                        <a href="{{ route('user.referral.index') }}" class="btn btn-info">
                            <i class="fas fa-share-alt me-2"></i>Referral Program
                        </a>
                        <a href="{{ route('user.team.index') }}" class="btn btn-success">
                            <i class="fas fa-users me-2"></i>My Team
                        </a>
                        <a href="/user/profile" class="btn btn-outline-secondary">
                            <i class="fas fa-user me-2"></i>Profile Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Referral Link -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Your Referral Link</h5>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" class="form-control" id="referralLink" 
                               value="{{ url('/register?ref=' . Auth::user()->referral_code) }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyReferralLink()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <small class="text-muted">Share this link to earn referral bonuses</small>
                </div>
            </div>

            <!-- Support -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p class="card-text small">Our support team is here to help you succeed.</p>
                    <div class="d-grid gap-2">
                        <a href="mailto:support@fundgrowonline.com" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-2"></i>Email Support
                        </a>
                        <a href="#" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-question-circle me-2"></i>FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}
</style>

<script>
function copyReferralLink() {
    const referralLink = document.getElementById('referralLink');
    referralLink.select();
    referralLink.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show success message
    const button = event.target.closest('button');
    const originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i>';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

// Update stats periodically (if you have real-time data)
function updateStats() {
    // This would typically fetch data from your API
    console.log('Updating stats...');
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Any initialization code here
    console.log('User dashboard loaded');
});
</script>
@endsection
