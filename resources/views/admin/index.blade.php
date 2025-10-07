@extends('layouts.admin')

@section('content')

@php
use Illuminate\Support\Facades\DB;

// ✅ Unique users per plan (sirf approved)
$planUserCounts = \App\Models\PlanSelection::select('plan_name', DB::raw('COUNT(DISTINCT user_id) as users'))
->where('status', 'approved')
->groupBy('plan_name')
->orderBy('users', 'desc')
->get();

$planLabels = $planUserCounts->pluck('plan_name');
$planUsers = $planUserCounts->pluck('users')->map(fn($v) => (int)$v); // integers

// (Optional) Agar total purchases (multiple buys count) bhi chahiye hon:
$planPurchaseCounts = \App\Models\PlanSelection::select('plan_name', DB::raw('COUNT(*) as purchases'))
->where('status', 'approved')
->groupBy('plan_name')
->orderBy('purchases', 'desc')
->get()
->keyBy('plan_name');

// Align purchases array with same label order (optional)
$planPurchases = $planLabels->map(function($label) use ($planPurchaseCounts) {
return (int) optional($planPurchaseCounts->get($label))->purchases ?? 0;
});
@endphp

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
        <div class="row mb-4 align-items-stretch">
            <!-- Left Side Main Balance Card -->
            <div class="col-md-6 col-lg-5 d-flex">
                <div class="main-card p-4 text-white position-relative d-flex justify-content-between align-items-center w-100 h-100"
                    style="background: url('{{ asset('assets/images/bg-balance.png') }}') no-repeat center/cover;
                   border-radius: 15px;
                   min-height: 220px;">
                    <!-- Logo Top Right -->
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="Logo"
                        style="position: absolute; top: 5px; left: 15px; height: 60px;">
                    <!-- Left: Balance -->
                    <div style="margin-top:70px;">
                        <h5 style="color: #3BD17A;">Total Balance</h5>
                        <h2 style="color: #3BD17A" class="font-weight-bold mb-0">
                            ${{ $adminBalance['total_usd'] ?? '0.00' }}
                        </h2>
                    </div>
                    <!-- Right: Buttons - Only for Admin -->
                    @if (Auth::user()->role === 'admin')
                    <div class="d-flex flex-column">
                        <!-- Example Buttons (Disabled in your case) -->
                    </div>
                    @endif
                </div>
            </div>
            <!-- Right Side Wallet Cards -->
            <div class="col-md-6 col-lg-7 d-flex">
                <div class="row w-100 h-100">
                    <div class="col-sm-6 mb-3 d-flex">
                        <a href="#" class="text-decoration-none w-100">
                            <div
                                class="card text-center p-3 shadow-sm h-100 w-100 d-flex flex-column align-items-center justify-content-center clickable-card">
                                <i class="fas fa-wallet fa-2x text-info mb-2"></i>
                                <h6 class="mb-1">USDT Balance</h6>
                                <span class="font-weight-bold">${{ $adminBalance['usdt'] ?? '0.00' }}</span>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3 d-flex">
                        <a href="#" class="text-decoration-none w-100">
                            <div class="card text-center p-3 shadow-sm h-100 w-100 d-flex flex-column align-items-center justify-content-center clickable-card">
                                <i class="fas fa-coins fa-2x text-warning mb-2"></i>
                                <h6 class="mb-1">BNB Balance</h6>
                                <span class="font-weight-bold">{{ $adminBalance['bnb'] ?? '0.0000' }}</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Top Up Modal -->
        <div class="modal fade" id="topUpModal" tabindex="-1" role="dialog" aria-labelledby="topUpModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="background: #072D42; color: #fff; border-radius: 15px;">
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
                            <p><i class="fas fa-check-double me-2"></i> Active Plans</p>
                            <h2>{{ number_format(\App\Models\PlanSelection::where('status', 'approved')->count()) }}</h2>
                            <span class="badge badge-outline-success">Approved</span>
                        </div>

                        <div class="statistics-item">
                            <p><i class="fas fa-hourglass-start me-2"></i> Plan Pending</p>
                            <h2>{{ number_format(\App\Models\PlanSelection::where('status', 'pending')->count()) }}</h2>
                            <span class="badge badge-outline-warning">Awaiting approval</span>
                        </div>

                        <div class="statistics-item">
                            <p><i class="fas fa-globe-americas me-2"></i> Global Pool</p>
                            <h2>${{ number_format(\App\Models\GlobalPool::getTotalAmount(), 0) }}</h2>
                            <span class="badge badge-outline-info">Total contributed</span>
                        </div>

                        <div class="statistics-item">
                            <p><i class="fas fa-clipboard-list me-2"></i> Plan Amount</p>
                            <h2>${{ number_format(\App\Models\PlanSelection::where('status','approved')->sum('plan_amount'), 0) }}</h2>
                            <span class="badge badge-outline-success">Approved total</span>
                        </div>

                        <!-- <div class="statistics-item">
                                <p><i class="fas fa-circle-notch me-2"></i> Pending</p>
                                <h2>7500</h2>
                                <span class="badge badge-outline-danger">16% decrease</span>
                            </div> -->
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
                                <h4 class="card-title text-white">
                                    <i class="fas fa-chart-bar me-2"></i> Total Approved Plan Purchases (by Plan)
                                </h4>
                                <div style="height: 360px; position: relative;">
                                    <canvas id="planTotalsChart"></canvas>
                                </div>
                               
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
                                            <i class="fas fa-user-circle fa-2x text-success me-2"></i>
                                            <div>
                                                <strong>{{ $selection->user->name ?? 'N/A' }}</strong><br>
                                                <small
                                                    class="text-white">{{ $selection->user->email ?? 'N/A' }}</small>
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
                                            class="btn btn-sm btn-outline-success"><i class="fas fa-eye"></i>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  const planCtx = document.getElementById('planTotalsChart').getContext('2d');

  const planLabels   = @json($planLabels);
  const planUsers    = @json($planUsers);     // ✅ unique users
  // (Optional) Agar purchases bhi saath dikhani hon:
  const planPurchases = @json($planPurchases);

  // Distinct colors per bar (dark theme friendly)
  const palette = [
    '#3bd17a', '#fdbb2d', '#8dc6ff', '#ff8b8b', '#b38bff',
    '#00d4ff', '#ffcf66', '#66ffcc', '#ff99cc', '#a8e6cf',
    '#ffd3b6', '#dcedc1', '#ffaaa5', '#a0c4ff', '#caffbf'
  ];
  const bgColors = planLabels.map((_, i) => palette[i % palette.length]);

  // ✅ Primary dataset: unique users per plan
  const datasets = [{
    label: 'Unique Users',
    data: planUsers,
    backgroundColor: bgColors,
    borderColor: bgColors,
    borderWidth: 2,
    borderRadius: 6,
    barThickness: 'flex',
    maxBarThickness: 52,
  }];

  // (Optional) Agar total purchases bhi dikhani hon, yeh second dataset add rakh do:
  // datasets.push({
  //   label: 'Total Purchases',
  //   data: planPurchases,
  //   backgroundColor: 'rgba(255,255,255,0.15)',
  //   borderColor: '#ffffff',
  //   borderWidth: 1.5,
  //   borderRadius: 6,
  //   barThickness: 'flex',
  //   maxBarThickness: 52,
  // });

  new Chart(planCtx, {
    type: 'bar',
    data: { labels: planLabels, datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: true, labels: { color: '#fff' } },
        tooltip: {
          callbacks: {
            label: (ctx) => {
              const v = ctx.parsed.y ?? 0;
              return ` ${ctx.dataset.label}: ${v} users`;
            }
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(255,255,255,0.08)' },
          ticks: { color: '#fff', font: { size: 12 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(255,255,255,0.08)' },
          ticks: {
            color: '#fff',
            font: { size: 12 },
            // users count hai — integers show karo:
            callback: (v) => Number.isInteger(v) ? v : ''
          },
          // optional: small counts ke liye stepSize 1
          // suggestedMax: Math.max(...planUsers) + 1,
          // ticks: { stepSize: 1, color: '#fff', font: { size: 12 } }
        }
      },
      animation: { duration: 800, easing: 'easeOutCubic' }
    }
  });
});
</script>


@endsection