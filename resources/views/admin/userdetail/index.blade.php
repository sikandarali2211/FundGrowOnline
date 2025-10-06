@extends('layouts.admin')
@section('content')
<style>
    /* ===============================
       MODERN USER MANAGEMENT UI
    =============================== */

    body {
        font-family: 'Inter', 'Segoe UI', sans-serif;
        color: #fff;
        margin: 0;
    }

    /* Page Header with Modern Design */
    .page-header {
        background: linear-gradient(145deg, #072d42, #22384e);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        margin-top: 4rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(59, 209, 122, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-title {
        font-weight: 700;
        font-size: 2.2rem;
        margin-bottom: .5rem;
        letter-spacing: -0.5px;
        position: relative;
    }

    .page-subtitle {
        font-size: 1rem;
        opacity: .7;
        font-weight: 400;
    }

    /* Statistics Card */
    .card.card-statistics {
        background: linear-gradient(145deg, #072d42, #22384e);
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        color: #fff;
        overflow: hidden;
    }

    .card-body {
        padding: 2rem;
    }

    .statistics-item {
        padding: 1.5rem;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(59, 209, 122, 0.1);
        transition: all 0.3s ease;
    }

    .statistics-item:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(59, 209, 122, 0.3);
        transform: translateY(-4px);
    }

    .statistics-item p {
        font-size: 0.85rem;
        color: #bbb;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .statistics-item h2 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #3bd17a;
        margin: 0.5rem 0;
        text-shadow: 0 0 20px rgba(59, 209, 122, 0.3);
    }

    .badge {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .badge-outline-success {
        background: rgba(59, 209, 122, 0.15);
        color: #3bd17a;
        border: 1px solid rgba(59, 209, 122, 0.3);
    }

    .badge-outline-warning {
        background: rgba(255, 193, 7, 0.15);
        color: #ffc107;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .badge-outline-info {
        background: rgba(0, 212, 170, 0.15);
        color: #00d4aa;
        border: 1px solid rgba(0, 212, 170, 0.3);
    }

    /* Modern Search Section */
    .search-section {
        margin-bottom: 2rem;
    }

    .search-container {
        display: flex;
        align-items: center;
        background: linear-gradient(145deg, #072d42, #22384e);
        border-radius: 16px;
        padding: 1rem 1.5rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(59, 209, 122, 0.1);
        transition: all 0.3s ease;
    }

    .search-container:focus-within {
        border-color: rgba(59, 209, 122, 0.4);
        box-shadow: 0 8px 30px rgba(59, 209, 122, 0.2);
    }

    .search-icon {
        color: #3bd17a;
        margin-right: 1rem;
        font-size: 1.1rem;
    }

    .search-input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        color: #fff;
        font-size: 1rem;
        font-weight: 400;
    }

    .search-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .search-btn {
        background: linear-gradient(135deg, #3bd17a, #00d4aa);
        border: none;
        border-radius: 12px;
        padding: 0.7rem 1.8rem;
        font-weight: 600;
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 209, 122, 0.3);
        font-size: 0.95rem;
        letter-spacing: 0.3px;
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(59, 209, 122, 0.5);
    }

    /* Table Container with Modern Design */
    .table-container {
        background: linear-gradient(145deg, #072d42, #22384e);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        border: 1px solid rgba(59, 209, 122, 0.1);
    }

    .table-header {
        padding: 1.8rem 2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
    }

    .table-title {
        color: #3bd17a;
        font-weight: 700;
        font-size: 1.4rem;
        letter-spacing: -0.3px;
        margin: 0;
    }

    /* Modern Table Styling */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table thead th {
        background: rgba(255, 255, 255, 0.05);
        color: #3bd17a;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 1.2rem 1.5rem;
        font-weight: 700;
        border-bottom: 2px solid rgba(59, 209, 122, 0.2);
    }

    .modern-table tbody td {
        padding: 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: #ddd;
        font-size: 0.95rem;
    }

    .modern-table tbody tr {
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover {
        background: rgba(59, 209, 122, 0.08);
        transform: scale(1.01);
    }

    /* User Info with Avatar */
    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3bd17a, #00d4aa);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 4px 15px rgba(59, 209, 122, 0.4);
    }

    .user-details h6 {
        margin: 0 0 0.3rem 0;
        font-weight: 600;
        color: #fff;
        font-size: 1rem;
    }

    .user-details small {
        color: #aaa;
        font-size: 0.8rem;
    }

    .text-info {
        color: #00d4aa !important;
    }

    .text-success {
        color: #3bd17a !important;
    }

    .text-warning {
        color: #ffc107 !important;
    }

    .text-muted {
        color: #999 !important;
    }

    .fw-semibold {
        font-weight: 600;
    }

    .font-monospace {
        font-family: 'Courier New', monospace;
    }

    /* Status Dropdown Modern */
    .status-dropdown select {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(59, 209, 122, 0.3);
        color: #fff;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .status-dropdown select:focus {
        outline: none;
        border-color: #3bd17a;
        box-shadow: 0 0 15px rgba(59, 209, 122, 0.4);
        background: rgba(255, 255, 255, 0.08);
    }

    .status-dropdown select option {
        background: #072d42;
        color: #fff;
    }

    /* Modern Buttons */
    .btn-outline-primary {
        color: #ffffff;
        background: rgba(59, 209, 122, 0.1);
        border: 1px solid rgba(59, 209, 122, 0.4);
        border-radius: 10px;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: rgba(59, 209, 122, 0.2);
        border-color: #3bd17a;
        transform: translateY(-2px);
    }

    .btn-outline-danger {
        color: #ff6b6b;
        background: rgba(255, 107, 107, 0.1);
        border: 1px solid rgba(255, 107, 107, 0.4);
        border-radius: 10px;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .btn-outline-danger:hover {
        background: rgba(255, 107, 107, 0.2);
        border-color: #ff6b6b;
        transform: translateY(-2px);
    }

    .btn-outline-info {
        color: #00d4aa;
        background: rgba(0, 212, 170, 0.1);
        border: 1px solid rgba(0, 212, 170, 0.4);
        border-radius: 8px;
        padding: 0.3rem 0.6rem;
        transition: all 0.3s ease;
    }

    .btn-outline-info:hover {
        background: rgba(0, 212, 170, 0.2);
        border-color: #00d4aa;
    }

    .btn-outline-secondary {
        color: #bbb;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 0.5rem 0.8rem;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #bbb;
        color: #fff;
    }

    /* Wallet Info Styling */
    .wallet-info {
        background: rgba(59, 209, 122, 0.05);
        padding: 0.8rem;
        border-radius: 10px;
        border: 1px solid rgba(59, 209, 122, 0.15);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #aaa;
    }

    .empty-state i {
        font-size: 4rem;
        color: #3bd17a;
        margin-bottom: 1.5rem;
        opacity: 0.6;
    }

    .empty-state h4 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #fff;
    }

    .empty-state p {
        font-size: 1rem;
        opacity: 0.7;
    }

    /* Pagination Modern */
    .pagination-container {
        margin-top: 2rem;
        background: linear-gradient(145deg, #072d42, #22384e);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    }

    .pagination .page-link {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(59, 209, 122, 0.3);
        color: #3bd17a;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        margin: 0 0.3rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover {
        background: rgba(59, 209, 122, 0.2);
        color: #3bd17a;
        transform: translateY(-2px);
    }

    .pagination .active .page-link {
        background: linear-gradient(135deg, #3bd17a, #00d4aa);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 4px 15px rgba(59, 209, 122, 0.4);
    }

    /* Status Badge for Moderators */
    .badge-success {
        background: linear-gradient(135deg, #3bd17a, #00d4aa);
        color: #fff;
    }

    .badge-danger {
        background: linear-gradient(135deg, #ff6b6b, #ff5252);
        color: #fff;
    }

    .badge-warning {
        background: linear-gradient(135deg, #ffc107, #ffb300);
        color: #fff;
    }

    .badge-secondary {
        background: linear-gradient(145deg, #072d42, #22384e);
        color: #bbb;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Action Buttons Group */
    .d-flex.gap-2 {
        gap: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.8rem;
        }

        .statistics-item h2 {
            font-size: 1.8rem;
        }

        .table-container {
            overflow-x: auto;
        }

        .modern-table {
            min-width: 1000px;
        }
    }

    /* Smooth Animations */
    * {
        transition: background 0.3s ease, border 0.3s ease, box-shadow 0.3s ease;
    }
</style>

<div class="main-panel">
    <main class="container-fluid py-4" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">

        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title" style="color: #3bd17a">User Management</h1>
            <p class="page-subtitle">Manage and monitor all user accounts</p>
        </div>

        <!-- Statistics -->
        <div class="row grid-margin">
            <div class="col-12">
                <div class="card card-statistics ">
                    <div class="card-body d-flex flex-wrap justify-content-between">

                        <div class="statistics-item col-lg-4">
                            <p><i class="fa fa-user me-2"></i>Total Users</p>
                            <h2>{{ number_format($users->total()) }}</h2>
                            <span class="badge badge-outline-success">
                                +{{ number_format($users->where('created_at', '>=', now()->startOfDay())->count()) }}
                                today
                                ·
                                {{ number_format($users->where('created_at', '>=', now()->subDays(7))->count()) }} last 7d
                            </span>
                        </div>

                        <div class="statistics-item col-lg-4">
                            <p><i class="fa fa-check-circle me-2"></i>Active Users</p>
                            <h2>{{ number_format($users->where('email_verified_at', '!=', null)->count()) }}</h2>
                            <span class="badge badge-outline-success">Verified</span>
                        </div>

                        <div class="statistics-item col-lg-4">
                            <p><i class="fa fa-clock me-2"></i>Pending Users</p>
                            <h2>{{ number_format($users->where('email_verified_at', null)->count()) }}</h2>
                            <span class="badge badge-outline-warning">Awaiting Verification</span>
                        </div>

                        <!-- <div class="statistics-item">
                            <p><i class="fa fa-sign-in-alt me-2"></i>Recent Logins</p>
                            <h2>{{ number_format($users->where('last_login_at', '>=', now()->subDays(7))->count()) }}</h2>
                            <span class="badge badge-outline-info">Last 7 Days</span>
                        </div> -->

                    </div>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div class="search-section">
            <form method="GET" action="{{ url('/user-details') }}" id="searchForm">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="search-input" id="searchInput"
                        placeholder="Search by name, email, phone, referral code, or referred by...">
                    <button class="search-btn" type="submit" id="searchBtn">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                    @if($q)
                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="clearSearch()" title="Clear Search">
                        <i class="fas fa-times"></i>
                    </button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Search Results Info -->
        @if($q)
        <div class="alert alert-info mb-3" style="background: rgba(0, 212, 170, 0.1); border: 1px solid rgba(0, 212, 170, 0.3); color: #00d4aa;">
            <i class="fas fa-search me-2"></i>
            <strong>Search Results:</strong> Found {{ $users->total() }} user(s) matching "{{ $q }}"
            <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="clearSearch()">
                <i class="fas fa-times me-1"></i>Clear Search
            </button>
        </div>
        @endif

        <!-- Table -->
        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title"><i class="fas fa-users me-2"></i>User Details</h3>
            </div>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>User Info</th>
                            <th>Contact Details</th>
                            <th>Referral Info</th>
                            <th>Status</th>
                            <th>Last Activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        @php
                        $status = $user->activationInfo->status ?? ($user->email_verified_at ? 'Active' : 'Pending');
                        $referredByText = optional($user->referrer)->name ?? ($user->referred_by ?? '—');
                        $lastLogin = $user->last_login_at ? \Illuminate\Support\Carbon::parse($user->last_login_at)->diffForHumans() : '—';
                        $statusLower = strtolower($status);
                        $userInitials = strtoupper(substr($user->name, 0, 2));
                        @endphp
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">{{ $userInitials }}</div>
                                    <div class="user-details">
                                        <h6>{{ $user->name }}</h6>
                                        <small>ID: {{ $user->id }}</small>
                                        @if($user->referral_code)
                                        <br><small class="text-info">Referral ID: {{ $user->referral_code }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $user->email }}</div>
                                    <small class="text-muted">{{ $user->phone ?? 'No phone' }}</small>
                                    @if($user->wallet_address)
                                    <div class="wallet-info mt-2">
                                        <small class="text-success">
                                            <i class="fas fa-wallet me-1"></i>
                                            <strong>Trust Wallet:</strong>
                                        </small>
                                        <br>
                                        <small class="text-info font-monospace" style="font-size: 0.75rem;">
                                            {{ $user->wallet_address }}
                                        </small>
                                        <button class="btn btn-sm btn-outline-info ms-2"
                                            onclick="copyToClipboard('{{ $user->wallet_address }}')"
                                            title="Copy Wallet Address">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    @else
                                    <br><small class="text-muted">
                                        <i class="fas fa-wallet me-1"></i>
                                        No wallet connected
                                    </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $referredByText }}</div>
                                    @if (isset($user->referrer))
                                    <small class="text-muted">{{ $user->referrer->email }}</small>
                                    <br><small class="text-info">Referred ID: {{ $user->referred_by }}</small>
                                    @else
                                    <small class="text-muted">Direct Registration</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if(Auth::user()->role === 'admin')
                                <form method="POST" action="{{ url('/admin/user-details/' . $user->id . '/status') }}">
                                    @csrf @method('PATCH')
                                    <div class="status-dropdown">
                                        <select name="status" onchange="this.form.submit()">
                                            <option value="Pending" {{ $statusLower === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Active" {{ $statusLower === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="Blocked" {{ $statusLower === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                            <option value="Rejected" {{ $statusLower === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                </form>
                                @else
                                <div class="status-display">
                                    <span class="badge badge-{{ $statusLower === 'active' ? 'success' : ($statusLower === 'blocked' ? 'danger' : ($statusLower === 'rejected' ? 'warning' : 'secondary')) }}">
                                        {{ ucfirst($statusLower) }}
                                    </span>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold">{{ $lastLogin }}</div>
                                    @if ($user->last_login_at)
                                    <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($user->last_login_at)->format('M d, Y') }}</small>
                                    @endif
                                    @if($user->pin_setup_completed_at)
                                    <br><small class="text-success">PIN Secured</small>
                                    @else
                                    <br><small class="text-warning">PIN Not Set</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if(Auth::user()->role === 'admin')
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-info"
                                        onclick="openReferralModal({{ $user->id }}, '{{ $user->name }}', {{ $user->referred_by ?? 'null' }})"
                                        title="Update Referral">
                                        <i class="fas fa-user-plus"></i>
                                    </button>

                                    <form method="POST" action="{{ url('/admin/user-details/' . $user->id . '/login') }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary"
                                            onclick="return confirm('Login as {{ $user->name }}?')"
                                            title="Login as User">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ url('/admin/user-details/' . $user->id . '/delete') }}"
                                        style="display: inline;" onsubmit="return confirmDelete('{{ $user->name }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <div class="text-muted">
                                    <i class="fas fa-eye"></i> View Only
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-users"></i>
                                <h4>No users found</h4>
                                <p>Try adjusting your search criteria or check back later.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
        <div class="pagination-container">
            <div class="d-flex justify-content-center">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </main>
</div>

<!-- Referral Update Modal -->
<div class="modal fade" id="referralModal" tabindex="-1" role="dialog" aria-labelledby="referralModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: linear-gradient(145deg, #072d42, #22384e); border: 1px solid rgba(59, 209, 122, 0.3); border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="referralModalLabel" style="color: #3bd17a; font-weight: 700;">
                    <i class="fas fa-user-plus me-2"></i>Update Referral
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="referralUpdateForm">
                    @csrf
                    <input type="hidden" id="userId" name="user_id">
                    
                    <div class="mb-3">
                        <label class="form-label" style="color: #3bd17a; font-weight: 600;">User</label>
                        <div class="form-control" id="userName" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(59, 209, 122, 0.3); color: #fff; border-radius: 10px; padding: 0.8rem;">
                            <!-- User name will be populated here -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="newReferrerId" class="form-label" style="color: #3bd17a; font-weight: 600;">New Referrer ID</label>
                        <input type="number" class="form-control" id="newReferrerId" name="new_referrer_id" 
                               placeholder="Enter user ID of new referrer" 
                               style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(59, 209, 122, 0.3); color: #fff; border-radius: 10px; padding: 0.8rem;">
                        <div class="form-text" style="color: #aaa;">Enter the user ID of the person who will be the new referrer</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="referrerSearch" class="form-label" style="color: #3bd17a; font-weight: 600;">Search Referrer</label>
                        <input type="text" class="form-control" id="referrerSearch" 
                               placeholder="Search by name or email..." 
                               style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(59, 209, 122, 0.3); color: #fff; border-radius: 10px; padding: 0.8rem;">
                        <div id="referrerResults" class="mt-2" style="max-height: 200px; overflow-y: auto; display: none;">
                            <!-- Search results will appear here -->
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 10px; padding: 0.6rem 1.5rem;">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="updateReferral()" style="background: linear-gradient(135deg, #3bd17a, #00d4aa); border: none; border-radius: 10px; padding: 0.6rem 1.5rem; font-weight: 600;">
                    <i class="fas fa-save me-2"></i>Update Referral
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toastify CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<!-- Toastify JS -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
    function confirmDelete(userName) {
        return confirm(`Are you sure you want to delete user "${userName}"?\n\nThis action cannot be undone and will permanently remove:\n- User account\n- All associated data\n- Referral relationships\n- Investment history\n\nType "DELETE" to confirm:`) &&
            prompt('Type "DELETE" to confirm:') === 'DELETE';
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
        @endif
    });

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 8px 30px rgba(0,0,0,0.4); border-radius: 12px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('Wallet address copied to clipboard!', 'success');
        }).catch(function(err) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showNotification('Wallet address copied to clipboard!', 'success');
        });
    }

    // Search functionality enhancements
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('searchForm').submit();
    }

    // Auto-submit search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchForm').submit();
        }
    });

    // Show loading state on search
    document.getElementById('searchForm').addEventListener('submit', function() {
        const searchBtn = document.getElementById('searchBtn');
        const originalText = searchBtn.innerHTML;
        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
        searchBtn.disabled = true;
        
        // Re-enable after 3 seconds (in case of slow response)
        setTimeout(() => {
            searchBtn.innerHTML = originalText;
            searchBtn.disabled = false;
        }, 3000);
    });

    // Referral update functionality
    let currentUserId = null;
    let searchTimeout = null;

    function openReferralModal(userId, userName, currentReferrerId) {
        currentUserId = userId;
        document.getElementById('userId').value = userId;
        document.getElementById('userName').textContent = userName;
        document.getElementById('newReferrerId').value = currentReferrerId || '';
        document.getElementById('referrerSearch').value = '';
        document.getElementById('referrerResults').style.display = 'none';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('referralModal'));
        modal.show();
    }

    // Search referrer functionality
    document.getElementById('referrerSearch').addEventListener('input', function() {
        const query = this.value.trim();
        const resultsDiv = document.getElementById('referrerResults');
        
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Debounce search
        searchTimeout = setTimeout(() => {
            searchReferrers(query);
        }, 300);
    });

    function searchReferrers(query) {
        fetch(`/admin/search-users?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                const resultsDiv = document.getElementById('referrerResults');
                
                if (data.users && data.users.length > 0) {
                    resultsDiv.innerHTML = data.users.map(user => `
                        <div class="search-result-item" onclick="selectReferrer(${user.id}, '${user.name}')" 
                             style="padding: 0.5rem; border: 1px solid rgba(59, 209, 122, 0.2); border-radius: 8px; margin-bottom: 0.5rem; cursor: pointer; background: rgba(255, 255, 255, 0.05);">
                            <div style="font-weight: 600; color: #fff;">${user.name}</div>
                            <div style="font-size: 0.8rem; color: #aaa;">ID: ${user.id} | ${user.email}</div>
                        </div>
                    `).join('');
                    resultsDiv.style.display = 'block';
                } else {
                    resultsDiv.innerHTML = '<div style="padding: 0.5rem; color: #aaa;">No users found</div>';
                    resultsDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                Toastify({
                    text: "Error searching users",
                    backgroundColor: "#ff6b6b"
                }).showToast();
            });
    }

    function selectReferrer(userId, userName) {
        document.getElementById('newReferrerId').value = userId;
        document.getElementById('referrerSearch').value = userName;
        document.getElementById('referrerResults').style.display = 'none';
    }

    function updateReferral() {
        const userId = document.getElementById('userId').value;
        const newReferrerId = document.getElementById('newReferrerId').value;

        if (!newReferrerId) {
            Toastify({
                text: "Please enter a referrer ID",
                backgroundColor: "#ff6b6b"
            }).showToast();
            return;
        }

        // Show loading state
        const updateBtn = document.querySelector('#referralModal .btn-primary');
        const originalText = updateBtn.innerHTML;
        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        updateBtn.disabled = true;

        fetch('/admin/update-referral', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userId,
                new_referrer_id: newReferrerId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Toastify({
                    text: data.message,
                    backgroundColor: "#3bd17a"
                }).showToast();
                
                // Close modal and reload page
                $('#referralModal').modal('hide');
                setTimeout(() => location.reload(), 1000);
            } else {
                Toastify({
                    text: data.message,
                    backgroundColor: "#ff6b6b"
                }).showToast();
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            Toastify({
                text: `Network error: ${error.message}`,
                backgroundColor: "#ff6b6b"
            }).showToast();
        })
        .finally(() => {
            // Restore button state
            updateBtn.innerHTML = originalText;
            updateBtn.disabled = false;
        });
    }
</script>
@endsection