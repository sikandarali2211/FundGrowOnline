@extends('layouts.admin')

@section('content')
    <style>
        /* Page background */
        .content-wrapper {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            padding-top: 80px;
            /* space for fixed navbar */
            min-height: calc(100vh - 80px);
            color: #fff;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #3bd17a;
        }

        /* Card style */
        .card.card-statistics {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 15px;
            border: none;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            color: #fff;
            margin-bottom: 1.5rem;
        }

        /* Table Styling */
        .table-container {
            background: linear-gradient(145deg, #072d42, #22384e);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
            margin-bottom: 1.5rem;
        }

        /* Responsive Table */
        .modern-table {
            color: #fff;
        }

        .modern-table th,
        .modern-table td {
            vertical-align: middle;
        }

        .badge {
            border-radius: 12px;
            padding: 4px 8px;
        }

        /* Statistics Items */
        .statistics-item {
            flex: 1 1 200px;
            margin-bottom: 1rem;
        }

        /* Mobile responsiveness */
        @media (max-width: 991px) {
            .page-header {
                padding: 1rem;
                font-size: 1rem;
            }

            .statistics-item h2 {
                font-size: 1.2rem;
            }

            .statistics-item p {
                font-size: 0.9rem;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }

        .main-card {
            min-height: 20%;
            height: 30px;
        }

        .clickable-card {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            color: #fff;
        }

        .clickable-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 0 15px rgba(0, 255, 200, 0.4),
                0 0 30px rgba(0, 255, 200, 0.2);
            cursor: pointer;
        }

        .clickable-card i {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .clickable-card:hover i {
            transform: scale(1.2) rotate(5deg);
            color: #00ffd0 !important;
            /* neon glow icon */
        }

        .clickable-card h6,
        .clickable-card span {
            transition: color 0.3s ease;
        }

        .clickable-card:hover h6,
        .clickable-card:hover span {
            color: #00ffd0;
        }

        body.modal-open {
            overflow: hidden !important;
            padding-right: 0px !important;
            margin-right: 0px !important;
        }

        .modal {
            overflow-y: auto !important;
            /* modal content scrollable ho agar content bada ho */
        }

        .modal-open .modal {
            overflow-x: hidden !important;
            overflow-y: hidden !important;
            /* content chhota ho to scroll bilkul na aaye */
        }

        .toast-message {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #3bd17a;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease-in-out;
            z-index: 9999;
        }

        .toast-message.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    <!-- MAIN PANEL -->
    <div class="main-panel">
        <div class="content-wrapper">
            <!-- Header -->
            <div class="page-header">
                <h3 class="page-title" style="color: #3bd17a;">Dashboard</h3>
            </div>
            <div class="row mb-4">
                <!-- Left Side Main Balance Card -->
                <div class="col-md-6 col-lg-5">
                    <div class="main-card p-4 text-white position-relative d-flex justify-content-between align-items-center"
                        style="background: url('{{ asset('assets/images/bg-balance.png') }}') no-repeat center/cover; 
                             border-radius: 15px; 
                             min-height: 220px; ">
                        <!-- Logo Top Right -->
                        <img src="{{ asset('assets/images/favicon.png') }}" alt="Logo"
                            style="position: absolute; top: 5px; left: 15px; height: 60px;">
                        <!-- Left: Balance -->
                        <div style="margin-top:70px;">
                            <h5 style="color: #3bd17a;">Total Balance</h5>
                            <h2 style="color: #3bd17a" class="font-weight-bold mb-0">${{ $adminBalance['total_usd'] ?? '0.00' }}</h2>
                            <small style="color: #3bd17a;">USDT: ${{ $adminBalance['usdt'] ?? '0.00' }} | BNB: {{ $adminBalance['bnb'] ?? '0.0000' }}</small>
                        </div>

                        <!-- Right: Buttons - Only for Admin -->
                        @if(Auth::user()->role === 'admin')
                        <div class="d-flex flex-column">
                            <button class="btn btn-light btn-sm mb-2" style="border-radius: 20px; min-width: 120px;"
                                data-toggle="modal" data-target="#topUpModal">
                                <i class="fas fa-arrow-down mr-1"></i> Top Up
                            </button>
                            <button class="btn btn-light btn-sm" style="border-radius: 20px; min-width: 120px;">
                                <i class="fas fa-arrow-up mr-1"></i> Cash Out
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Top Up Modal -->
                <div class="modal fade" id="topUpModal" tabindex="-1" role="dialog" aria-labelledby="topUpModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="background: #072d42; color: #fff; border-radius: 15px;">

                            <!-- Header -->
                            <div class="modal-header border-0">
                                <h5 class="modal-title" id="topUpModalLabel" style="color:#3bd17a;">Top Up Wallet</h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body text-center">

                                <!-- QR Code -->
                                <div class="mb-3">
                                    <img src="{{ asset('assets/images/qr-sample.png') }}" alt="QR Code" class="img-fluid"
                                        style="max-width: 180px;">
                                </div>

                                <!-- Wallet Address + Copy -->
                                <div class="input-group mb-3">
                                    <input type="text" id="walletAddress" class="form-control text-center"
                                        value="0x1234567890ABCDEF1234567890ABCDEF12345678" readonly>
                                    <div class="input-group-append">
                                        <button id="copyBtn" class="btn btn-success" type="button"
                                            onclick="copyWalletAddress()">Copy</button>
                                    </div>
                                </div>

                                <!-- Instructions -->
                                <div class="p-3 rounded text-warning mt-3" style="background: rgba(255,255,255,0.1);">
                                    <strong>Important:</strong> Please send only <b>USDT (BEP20)</b> to this address.
                                    Sending any other token may result in permanent loss of funds.
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Side Wallet Cards -->
                <div class="col-md-6 col-lg-7">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('balance-wallet') }}" class="text-decoration-none">
                                <div class="card text-center p-3 shadow-sm h-100 clickable-card">
                                    <i class="fas fa-wallet fa-2x text-info mb-2"></i>
                                    <h6 class="mb-1">USDT Balance</h6>
                                    <span class="font-weight-bold">${{ $adminBalance['usdt'] ?? '0.00' }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('pool-wallet') }}" class="text-decoration-none">
                                <div class="card text-center p-3 shadow-sm h-100 clickable-card">
                                    <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                                    <h6 class="mb-1">BNB Balance</h6>
                                    <span class="font-weight-bold">{{ $adminBalance['bnb'] ?? '0.0000' }}</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('exchange') }}" class="text-decoration-none">
                                <div class="card text-center p-3 shadow-sm h-100 clickable-card">
                                    <i class="fas fa-exchange-alt fa-2x text-warning mb-2"></i>
                                    <h6 class="mb-1">Exchange</h6>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ url('pools') }}" class="text-decoration-none">
                                <div class="card text-center p-3 shadow-sm h-100 clickable-card">
                                    <i class="fas fa-layer-group fa-2x text-primary mb-2"></i>
                                    <h6 class="mb-1">Pools</h6>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Statistics -->
            <div class="row grid-margin">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body d-flex flex-wrap justify-content-between">
                            <div class="statistics-item">
                                <p><i class="fa fa-user me-2"></i> Total Users</p>
                                <h2>{{ number_format($totalUsers ?? 0) }}</h2>
                                <span class="badge badge-outline-success">
                                    +{{ number_format($newUsersToday ?? 0) }} today ·
                                    {{ number_format($newUsers7Days ?? 0) }} last 7d
                                </span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fa fa-clipboard-list me-2"></i> Plan Selections</p>
                                <h2>{{ number_format(\App\Models\PlanSelection::count()) }}</h2>
                                <span class="badge badge-outline-warning">
                                    {{ \App\Models\PlanSelection::where('status', 'pending')->count() }} pending
                                </span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-hourglass-half me-2"></i> Avg Time</p>
                                <h2>123.50</h2>
                                <span class="badge badge-outline-danger">30% decrease</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-cloud-download-alt me-2"></i> Downloads</p>
                                <h2>3500</h2>
                                <span class="badge badge-outline-success">12% increase</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-check-circle me-2"></i> Update</p>
                                <h2>7500</h2>
                                <span class="badge badge-outline-success">57% increase</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-chart-line me-2"></i> Sales</p>
                                <h2>9000</h2>
                                <span class="badge badge-outline-success">10% increase</span>
                            </div>

                            <div class="statistics-item">
                                <p><i class="fas fa-circle-notch me-2"></i> Pending</p>
                                <h2>7500</h2>
                                <span class="badge badge-outline-danger">16% decrease</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <h4 class="card-title text-white"><i class="fa fa-user me-2"></i> Total Users</h4>
                            <div style="height: 300px; position: relative;">
                                <canvas id="usersChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <h4 class="card-title text-white"><i class="fas fa-chart-line me-2"></i> Sales</h4>
                            <h2 class="mb-4 text-center" style="color: #3bd17a; font-size: 3rem;">${{ number_format(array_sum($salesChartData) ?? 0, 0) }}</h2>
                            <p class="text-center text-muted">Total Sales Amount</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Plan Selections -->
            <div class="row">
                <div class="col-12 grid-margin">
                    <div class="table-container">
                        <div class="p-3 d-flex justify-content-between align-items-center">
                            <h4 class="table-title"> <i class="fas fa-clipboard-list me-2"></i> Recent Plan Selections
                            </h4>
                            @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.plan-selections.index') }}" class="btn btn-sm btn-primary">View
                                All</a>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table modern-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $recentSelections = \App\Models\PlanSelection::with('user')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(5)
                                            ->get();
                                    @endphp
                                    @forelse($recentSelections as $selection)
                                        <tr>
                                            <td><strong>#{{ $selection->id }}</strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                                    <div>
                                                        <strong>{{ $selection->user->name ?? 'N/A' }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ $selection->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><strong>{{ $selection->plan_name }}</strong></td>
                                            <td><strong>${{ number_format($selection->plan_amount, 2) }}</strong></td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $selection->status_badge }}">{{ $selection->status_text }}</span>
                                            </td>
                                            <td>{{ $selection->created_at->format('M d, Y g:i A') }}</td>
                                            <td>
                                                @if(Auth::user()->role === 'admin')
                                                <a href="{{ route('admin.plan-selections.show', $selection->id) }}"
                                                    class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i>
                                                    View</a>
                                                @else
                                                <span class="text-muted">View Only</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-clipboard-list fa-3x mb-3"></i><br>
                                                No plan selections found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <script>
        function copyWalletAddress() {
            var copyText = document.getElementById("walletAddress");

            // Copy to clipboard
            navigator.clipboard.writeText(copyText.value).then(function() {

                // Change button text
                let btn = document.getElementById("copyBtn");
                btn.innerText = "Copied!";
                btn.classList.remove("btn-success");
                btn.classList.add("btn-info");

                // Reset back after 2s
                setTimeout(() => {
                    btn.innerText = "Copy";
                    btn.classList.remove("btn-info");
                    btn.classList.add("btn-success");
                }, 2000);

                // Show toast
                showToast("Wallet address copied!");
            });
        }

        // Simple toast function
        function showToast(message) {
            let toast = document.createElement("div");
            toast.className = "toast-message";
            toast.innerText = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add("show");
            }, 100);

            setTimeout(() => {
                toast.classList.remove("show");
                setTimeout(() => toast.remove(), 300);
            }, 2000);
        }


        $('#topUpModal').on('shown.bs.modal', function() {
            document.body.style.overflow = 'hidden';
            document.body.style.paddingRight = '0px';
            document.body.style.marginRight = '0px';
        });

        $('#topUpModal').on('hidden.bs.modal', function() {
            document.body.style.overflow = 'auto';
            document.body.style.paddingRight = '0px';
            document.body.style.marginRight = '0px';
        });
    </script>


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Users Pie Chart
        const usersCtx = document.getElementById('usersChart').getContext('2d');
        new Chart(usersCtx, {
            type: 'pie',
            data: {
                labels: ['New Users (Last 6 Days)', 'Existing Users'],
                datasets: [{
                    data: [
                        @json(array_sum($usersChartData)),
                        @json($totalUsers - array_sum($usersChartData))
                    ],
                    backgroundColor: [
                        '#3bd17a',
                        'rgba(59, 209, 122, 0.3)'
                    ],
                    borderColor: [
                        '#3bd17a',
                        'rgba(59, 209, 122, 0.5)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1.5,
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10,
                        left: 10,
                        right: 10
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#fff',
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} users (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
    </script>
@endsection
