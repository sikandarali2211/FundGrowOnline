@extends('layouts.user')

@section('content')
<style>
    :root {
        --gold: #f0c24b;
        --bgDark: #0b1f2a;
        --line: #1dd1a1;
        --cardGlass: linear-gradient(145deg, rgba(255, 215, 0, .03), rgba(0, 0, 0, .95));
    }

    .card-dark {
        border: 0;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .15);
        background: linear-gradient(145deg, #072d42, #22384e);
        border-radius: 16px;
        border: none;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        color: #fff;
        overflow: hidden;
    }

    .table-dark {
        background: transparent;
        color: #fff;
    }

    .table-dark th {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-weight: 600;
        padding: 15px 12px;
    }

    .table-dark td {
        border-color: rgba(255, 255, 255, 0.08);
        padding: 15px 12px;
        vertical-align: middle;
    }

    .table-dark tbody tr:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .badge-referral {
        background: rgba(29, 209, 161, 0.15);
        color: #1dd1a1;
        border-radius: 8px;
        padding: 4px 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-level {
        background: rgba(240, 194, 75, 0.15);
        color: #f0c24b;
        border-radius: 8px;
        padding: 4px 12px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .text-muted {
        color: rgba(255, 255, 255, 0.6) !important;
    }

    .text-success {
        color: #1dd1a1 !important;
    }

    .stats-card {
        background: linear-gradient(135deg, rgba(29, 209, 161, 0.1), rgba(240, 194, 75, 0.1));
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1dd1a1;
        margin-bottom: 5px;
    }

    .stats-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        font-weight: 500;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: rgba(255, 255, 255, 0.6);
    }

    .empty-state i {
        font-size: 4rem;
        color: rgba(255, 255, 255, 0.3);
        margin-bottom: 20px;
    }

    .referral-code-display {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 10px 15px;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        color: #f0c24b;
        display: inline-block;
    }

    .copy-btn {
        background: rgba(29, 209, 161, 0.2);
        border: 1px solid rgba(29, 209, 161, 0.3);
        color: #1dd1a1;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .copy-btn:hover {
        background: rgba(29, 209, 161, 0.3);
        color: #fff;
    }
</style>

<div class="main-panel">
    <div class="container py-4" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); margin-top:4rem;">
        <div class="row justify-content-center">
            <div class="col-12 col-xxl-10">
                {{-- Header --}}
                <div class="card card-dark mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h4 class="mb-2 text-white">My Referral Team</h4>
                                <p class="text-muted mb-0">Users who joined through your referral code</p>
                            </div>
                            <div class="text-end">
                                <div class="mb-2">
                                    <small class="text-muted">Your Referral Code:</small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="referral-code-display" id="referralCode">{{ $user->referral_code }}</span>
                                    <button class="copy-btn" onclick="copyReferralCode()" title="Copy Code">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-number">{{ $referrals->count() }}</div>
                            <div class="stats-label">Total Referrals</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-number">{{ $referrals->where('plan_status', 'Active')->count() }}</div>
                            <div class="stats-label">Active Plan Users</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-number">{{ $referrals->where('plan_status', 'Pending')->count() }}</div>
                            <div class="stats-label">Pending Plan Users</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-number">{{ $user->level }}</div>
                            <div class="stats-label">Your Level</div>
                        </div>
                    </div>
                </div>

                {{-- Referrals Table --}}
                <div class="card card-dark">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Referral Team Details</h6>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="levelFilter" style="width: auto; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                <option value="all">All Levels</option>
                                <option value="1">Level 1 Only</option>
                                <option value="2">Level 2 Only</option>
                            </select>
                            <select class="form-select form-select-sm" id="planStatusFilter" style="width: auto; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                <option value="all">All Plans</option>
                                <option value="Active">Active Plans</option>
                                <option value="Pending">Pending Plans</option>
                                <option value="No Plan">No Plans</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($referrals->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-dark table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Referral Code</th>
                                            <th>Referral Level</th>
                                            <th>Plan Status</th>
                                            <th>Joined Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($referrals as $referral)
                                            <tr data-level="{{ $referral->level }}" data-plan-status="{{ $referral->plan_status }}">
                                                <td>
                                                    <span class="badge badge-referral">#{{ $referral->id }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #1dd1a1, #0d9488); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                                            {{ strtoupper(substr($referral->name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold text-white">{{ $referral->name }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $referral->email }}</span>
                                                </td>
                                                <td>
                                                    <code class="text-warning">{{ $referral->referral_code }}</code>
                                                </td>
                                                <td>
                                                    <span class="badge badge-level">Level {{ $referral->level }}</span>
                                                </td>
                                                <td>
                                                    @if($referral->plan_status === 'Active')
                                                        <span class="badge bg-success">Active Plan</span>
                                                    @elseif($referral->plan_status === 'Pending')
                                                        <span class="badge bg-warning">Pending Plan</span>
                                                    @else
                                                        <span class="badge bg-secondary">No Plan</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $referral->created_at->format('M d, Y') }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">Active</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <h5 class="text-muted">No Referrals Yet</h5>
                                <p class="text-muted">Share your referral code to start building your team!</p>
                                <div class="mt-3">
                                    <span class="referral-code-display">{{ $user->referral_code }}</span>
                                    <button class="copy-btn ms-2" onclick="copyReferralCode()">
                                        <i class="fas fa-copy"></i> Copy
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Referral Link Section --}}
                @if($referrals->count() > 0)
                    <div class="card card-dark mt-4">
                        <div class="card-body">
                            <h6 class="text-white mb-3">Share Your Referral Link</h6>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="referralLink" 
                                               value="{{ url('/register?ref=' . $user->referral_code) }}" 
                                               readonly style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                                        <button class="btn btn-outline-success" type="button" onclick="copyReferralLink()">
                                            <i class="fas fa-copy"></i> Copy Link
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="shareOnWhatsApp()">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" onclick="shareOnTelegram()">
                                            <i class="fab fa-telegram"></i> Telegram
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Copy referral code to clipboard
    function copyReferralCode() {
        const code = document.getElementById('referralCode').textContent;
        navigator.clipboard.writeText(code).then(function() {
            showToast('Referral code copied to clipboard!', 'success');
        }).catch(function() {
            showToast('Failed to copy code', 'error');
        });
    }

    // Copy referral link to clipboard
    function copyReferralLink() {
        const link = document.getElementById('referralLink').value;
        navigator.clipboard.writeText(link).then(function() {
            showToast('Referral link copied to clipboard!', 'success');
        }).catch(function() {
            showToast('Failed to copy link', 'error');
        });
    }

    // Share on WhatsApp
    function shareOnWhatsApp() {
        const link = document.getElementById('referralLink').value;
        const message = `Join me on FundGrow Online! Use my referral code: {{ $user->referral_code }}\n\nLink: ${link}`;
        const url = `https://wa.me/?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');
    }

    // Share on Telegram
    function shareOnTelegram() {
        const link = document.getElementById('referralLink').value;
        const message = `Join me on FundGrow Online! Use my referral code: {{ $user->referral_code }}\n\nLink: ${link}`;
        const url = `https://t.me/share/url?url=${encodeURIComponent(link)}&text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');
    }

    // Filter functionality
    function applyFilters() {
        const selectedLevel = document.getElementById('levelFilter').value;
        const selectedPlanStatus = document.getElementById('planStatusFilter').value;
        const rows = document.querySelectorAll('tbody tr[data-level]');
        
        rows.forEach(row => {
            const level = row.getAttribute('data-level');
            const planStatus = row.getAttribute('data-plan-status');
            
            let showRow = true;
            
            // Level filter
            if (selectedLevel !== 'all' && level !== selectedLevel) {
                showRow = false;
            }
            
            // Plan status filter
            if (selectedPlanStatus !== 'all' && planStatus !== selectedPlanStatus) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }

    // Add event listeners
    document.getElementById('levelFilter').addEventListener('change', applyFilters);
    document.getElementById('planStatusFilter').addEventListener('change', applyFilters);

    // Toast notification function
    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }
</script>
@endsection
