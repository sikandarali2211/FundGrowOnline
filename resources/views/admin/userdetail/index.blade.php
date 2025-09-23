@extends('layouts.admin')
@section('content')
    <style>
        /* ===============================
                               USER MANAGEMENT PAGE STYLING
                            =============================== */

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

            color: #fff;
            margin: 0;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            margin-top: 4rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
        }

        .page-title {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: .3rem;
        }

        .page-subtitle {
            font-size: .95rem;
            opacity: .8;
        }

        .card.card-statistics {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 15px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
        }


        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(145deg, #072d42, #22384e);
            ;
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: 1.5rem;
            border: none;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
            background: rgba(255, 255, 255, 0.08);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #3bd17a;
            text-shadow: 0 0 8px rgba(59, 209, 122, 0.6);
        }

        .stat-label {
            margin-top: .3rem;
            font-size: .9rem;
            color: #bbb;
        }

        /* Search Section */
        .search-section {
            margin-bottom: 2rem;
        }

        .search-container {
            display: flex;
            align-items: center;
            background: linear-gradient(145deg, #072d42, #22384e);
            ;
            border-radius: 12px;
            padding: .6rem 1rem;
        }

        .search-icon {
            color: #3bd17a;
            margin-right: .6rem;
        }

        .search-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: .95rem;
        }

        .search-btn {
            background: linear-gradient(90deg, #3bd17a, #00d4aa);
            border: none;
            border-radius: 8px;
            padding: .5rem 1rem;
            font-weight: 600;
            color: #fff;
            transition: 0.3s;
        }

        .search-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 12px rgba(59, 209, 122, 0.6);
        }

        /* Table Container */
        .table-container {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            overflow: hidden;
        }

        .table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .table-title {
            color: #3bd17a;
            font-weight: 600;
        }

        /* Modern Table */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modern-table thead th {
            background: rgba(255, 255, 255, 0.08);
            color: #3bd17a;
            text-transform: uppercase;
            font-size: .75rem;
            letter-spacing: 0.8px;
            padding: 1rem;
        }

        .modern-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #ddd;
        }

        .modern-table tbody tr:hover {
            background: rgba(0, 212, 170, 0.08);
            transition: 0.25s;
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3bd17a, #00d4aa);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: .8rem;
        }

        .user-details h6 {
            margin: 0;
            font-weight: 600;
            color: #3bd17a;
        }

        .user-details small {
            color: #aaa;
        }

        /* Status Dropdown */
        .status-dropdown select {
            background: linear-gradient(145deg, #072d42, #22384e);
            ;
            border: 1px solid rgba(59, 209, 122, 0.4);
            color: #e0e0e0;
            border-radius: 6px;
            padding: .4rem .6rem;
            font-size: .9rem;
        }

        .status-dropdown select:focus {
            outline: none;
            border-color: #3bd17a;
            box-shadow: 0 0 8px rgba(59, 209, 122, 0.6);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #aaa;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #3bd17a;
            margin-bottom: .8rem;
        }

        /* Pagination */
        .pagination-container {
            margin-top: 2rem;
            background: linear-gradient(145deg, #072d42, #22384e);
            ;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
        }

        .pagination .page-link {
            background: transparent;
            border: 1px solid rgba(59, 209, 122, 0.4);
            color: #3bd17a;
            border-radius: 6px;
        }

        .pagination .page-link:hover {
            background: #3bd17a;
            color: #0d1b2a;
        }

        .pagination .active .page-link {
            background: #3bd17a;
            color: #0d1b2a;
            border-color: #3bd17a;
        }
    </style>
    <div class="main-panel">
        <main class="container-fluid py-4" style=" background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">

            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title" style="color: #3bd17a">User Management</h1>
            </div>


            <!-- Statistics -->
            <div class="row grid-margin">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body custom-glassy-background d-flex flex-wrap justify-content-between">

                            <div class="statistics-item">
                                <p><i class="fa fa-user me-2"></i>Total Users</p>
                                <h2>{{ number_format($users->total()) }}</h2>
                                <span class="badge badge-outline-success">
                                    +{{ number_format($users->where('created_at', '>=', now()->startOfDay())->count()) }}
                                    today
                                    ·
                                    {{ number_format($users->where('created_at', '>=', now()->subDays(7))->count()) }} last
                                    7d
                                </span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fa fa-check-circle me-2"></i>Active Users</p>
                                <h2>{{ number_format($users->where('email_verified_at', '!=', null)->count()) }}</h2>
                                <span class="badge badge-outline-success">Verified</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fa fa-clock me-2"></i>Pending Users</p>
                                <h2>{{ number_format($users->where('email_verified_at', null)->count()) }}</h2>
                                <span class="badge badge-outline-warning">Awaiting Verification</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fa fa-sign-in-alt me-2"></i>Recent Logins</p>
                                <h2>{{ number_format($users->where('last_login_at', '>=', now()->subDays(7))->count()) }}
                                </h2>
                                <span class="badge badge-outline-info">Last 7 Days</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <!-- Search -->
            <div class="search-section">
                <form method="GET" action="{{ url('/admin/user-details') }}">
                    <div class="search-container">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="q" value="{{ $q ?? '' }}" class="search-input"
                            placeholder="Search by name, email, phone, or referral...">
                        <button class="search-btn" type="submit">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>

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
                                    $status =
                                        $user->activationInfo->status ??
                                        ($user->email_verified_at ? 'Active' : 'Pending');
                                    $referredByText = optional($user->referrer)->name ?? ($user->referred_by ?? '—');
                                    $lastLogin = $user->last_login_at
                                        ? \Illuminate\Support\Carbon::parse($user->last_login_at)->diffForHumans()
                                        : '—';
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
                                                <br>
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
                                        <form method="POST"
                                            action="{{ url('/admin/user-details/' . $user->id . '/status') }}">
                                            @csrf @method('PATCH')
                                            <div class="status-dropdown">
                                                <select name="status" onchange="this.form.submit()">
                                                    <option value="Pending"
                                                        {{ $statusLower === 'pending' ? 'selected' : '' }}>
                                                        Pending</option>
                                                    <option value="Active"
                                                        {{ $statusLower === 'active' ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="Blocked"
                                                        {{ $statusLower === 'blocked' ? 'selected' : '' }}>
                                                        Blocked</option>
                                                    <option value="Rejected"
                                                        {{ $statusLower === 'rejected' ? 'selected' : '' }}>Rejected
                                                    </option>
                                                </select>
                                            </div>
                                        </form>
                                        @else
                                        <!-- Moderator can only view status, not change it -->
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
                                                <small
                                                    class="text-muted">{{ \Illuminate\Support\Carbon::parse($user->last_login_at)->format('M d, Y') }}</small>
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
                                            <!-- User Login Button -->
                                            <form method="POST" action="{{ url('/admin/user-details/' . $user->id . '/login') }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary" 
                                                        onclick="return confirm('Login as {{ $user->name }}?')"
                                                        title="Login as User">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Delete Button -->
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
                                        <!-- Moderator can only view, no actions -->
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

    <script>
        // Delete confirmation function
        function confirmDelete(userName) {
            return confirm(`Are you sure you want to delete user "${userName}"?\n\nThis action cannot be undone and will permanently remove:\n- User account\n- All associated data\n- Referral relationships\n- Investment history\n\nType "DELETE" to confirm:`) && 
                   prompt('Type "DELETE" to confirm:') === 'DELETE';
        }

        // Auto-refresh status updates
        document.addEventListener('DOMContentLoaded', function() {
            // Add success message handling
            @if(session('success'))
                showNotification('{{ session('success') }}', 'success');
            @endif
            
            @if(session('error'))
                showNotification('{{ session('error') }}', 'error');
            @endif
        });

        // Notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Copy wallet address to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                showNotification('Wallet address copied to clipboard!', 'success');
            }).catch(function(err) {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showNotification('Wallet address copied to clipboard!', 'success');
            });
        }
    </script>
@endsection
