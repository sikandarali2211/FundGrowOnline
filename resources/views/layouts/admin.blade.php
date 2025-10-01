<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fund Grow Online</title>

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('assets/dashboard/vendors/iconfonts/font-awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/vendors/css/vendor.bundle.addons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/style.css') }}">
    <link rel="shortcut icon" href="http://www.urbanui.com/" />

    <style>
        /* BODY */
        body {
            background: linear-gradient(135deg, #041a2f, #072d42 60%);
            font-family: 'Poppins', sans-serif;
            color: #e0e0e0;
        }

        /* NAVBAR */
        .navbar {
            background: rgba(7, 45, 66, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(59, 209, 122, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
        }

        .navbar .nav-link,
        .navbar .fas {
            color: #3bd17a !important;
            transition: 0.3s;
        }

        .navbar .nav-link:hover,
        .navbar .fas:hover {
            color: #00d4aa !important;
            transform: scale(1.1);
        }

        /* SIDEBAR */
        .sidebar {
            background: rgba(7, 45, 66, 0.95);
            backdrop-filter: blur(12px);
            border-right: 1px solid rgba(59, 209, 122, 0.2);
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.6);
            transition: all 0.3s ease;
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            z-index: 1030;
        }

        .sidebar .nav-link {
            border-radius: 10px;
            padding: 12px 15px;
            color: #a5f2d5 !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background: linear-gradient(90deg, rgba(59, 209, 122, 0.2), rgba(0, 212, 170, 0.3));
            color: #3bd17a !important;
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(90deg, #3bd17a, #00d4aa);
            color: #0d1b2a !important;
            font-weight: 600;
            box-shadow: 0 0 12px rgba(59, 209, 122, 0.7);
        }

        /* PAGE CONTENT */
        .page-body-wrapper {
            margin-left: 250px;
            transition: all 0.3s ease;
        }

        /* RESPONSIVE MOBILE */
        @media (max-width: 991px) {
            .sidebar {
                left: -250px;
                /* Hide by default */
            }

            .sidebar.show {
                left: 0;
                /* Slide in */
            }

            .page-body-wrapper {
                margin-left: 0;
            }

            /* Show mobile toggle button */
            .navbar-toggler.d-lg-none {
                display: block !important;
            }
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <!-- Navbar -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row default-layout-navbar">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo" href="/">
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="FundGrow Online"
                        style="height: 100px; width: auto; max-width: 500px;" />
                </a>
                <a class="navbar-brand brand-logo-mini" href="javascript:void(0);" id="mobileSidebarToggle">
                    <img src="{{ asset('assets/images/favicon.png') }}" alt="FundGrow Online"
                        style="height: 100px; width: auto; max-width: 500px;" />
                </a>
            </div>

            <div class="navbar-menu-wrapper d-flex align-items-stretch" style="background-color: #072d42;">
                <!-- Search -->
                <ul class="navbar-nav">
                    <li class="nav-item nav-search d-none d-md-flex">
                        <div class="nav-link">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i style="color: #3bd17a;" class="fas fa-search"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control custom-input" placeholder="Search"
                                    aria-label="Search">
                            </div>
                        </div>
                    </li>
                </ul>

                <!-- Profile -->
                <!-- Profile -->
                <ul class="navbar-nav navbar-nav-right ml-auto">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            <img src="{{ Auth::user()->profile_picture
                                ? asset('storage/' . Auth::user()->profile_picture)
                                : asset('assets/images/default-avatar.png') }}"
                                alt="profile" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ route('admin.setting.index') }}">
                                <i style="color: #3bd17a;" class="fas fa-cog text-primary"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item">
                                <i style="color: #3bd17a;" class="fas fa-power-off text-primary"></i>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="cmn--btn btn--white btn-pill"
                                        onclick="return confirm('Are you sure you want to logout?')">Logout</button>
                                </form>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav flex-column">

                <!-- Logo -->
                <li class="nav-item text-center my-4">
                    <a href="/">
                        <img src="{{ asset('assets/images/favicon.png') }}" alt="FundGrow Logo"
                            style="height: 80px; width: auto; max-width: 100%;">
                    </a>
                </li>

                <hr style="border-color: rgba(59, 209, 122, 0.3); margin: 0 15px;">

                <!-- Profile Section -->
                <li class="nav-item nav-profile my-3">
                    <div class="nav-link d-flex align-items-center px-3 py-2">
                        <!-- Profile Image -->
                        <div class="profile-image" style="margin-right: 1rem;">
                            <img src="{{ Auth::user()->profile_picture
                                ? asset('storage/' . Auth::user()->profile_picture)
                                : asset('assets/images/default-avatar.png') }}"
                                alt="Profile"
                                style="width: 50px; height: 50px; border-radius: 50%; border: 2px solid #3bd17a; object-fit: cover;">
                        </div>
                        <!-- Profile Name -->
                        <div class="profile-name">
                            <p class="mb-0"
                                style="color: #3bd17a; font-weight: 600; font-size: 15px; white-space: normal; word-break: break-word; line-height: 1.3;">
                                Welcome, {{ Auth::user()->full_name ?? Auth::user()->name }}
                            </p>
                        </div>
                    </div>
                </li>

                <hr style="border-color: rgba(59, 209, 122, 0.3); margin: 0 15px;">

                <!-- Menu Items -->
                <li class="nav-item mt-3">
                    <a class="nav-link d-flex align-items-center px-3 py-2" href="{{ route('admin.index') }}">
                        <i class="fa fa-home menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Dashboard</span>
                    </a>
                </li>
                <!-- User Details - Visible to Admin and Moderator -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.userdetails.index') }}">
                        <i class="fa fa-users menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">User Details</span>
                    </a>
                </li>
                
                @if(Auth::user()->role === 'admin')
                <!-- Role Management - Only for Admin -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.role.index') }}">
                        <i class="fa fa-users-cog menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Role Management</span>
                    </a>
                </li>
                    <!-- Role Management - Only for Admin -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.transactionlog.index') }}">
                        <i class="fa fa-users-cog menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Transcation Log</span>
                    </a>
                </li>
                <!-- Investment Plans - Only for Admin -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.investmentplans.index') }}">
                        <i class="fa fa-chart-line menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Investment Plans</span>
                    </a>
                </li>
                <!-- Plan Selections - Only for Admin -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.plan-selections.index') }}">
                        <i class="fa fa-clipboard-list menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Plan Selections</span>
                        @php
                            $pendingCount = \App\Models\PlanSelection::where('status', 'pending')->count();
                        @endphp
                        @if ($pendingCount > 0)
                            <span class="badge badge-warning badge-pill ml-auto">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
                <!-- Withdrawal Requests - Only for Admin -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.withdrawals.index') }}">
                        <i class="fa fa-money-bill-wave menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Withdrawals</span>
                        @php
                            $pendingWithdrawals = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
                        @endphp
                        @if ($pendingWithdrawals > 0)
                            <span class="badge badge-warning badge-pill ml-auto">{{ $pendingWithdrawals }}</span>
                        @endif
                    </a>
                </li>
                <!-- Automated Withdrawal System - Only for Admin -->
                <!-- <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="/auto-transfer">
                        <i class="fa fa-robot menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Auto Transfer</span>
                        <span class="badge badge-success badge-pill ml-auto">NEW</span>
                    </a>
                </li> -->
                <!-- Global Pool Management - Only for Admin -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.global-pool.index') }}">
                        <i class="fa fa-globe menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Global Pool</span>
                        <span class="badge badge-warning badge-pill ml-auto">10%</span>
                    </a>
                </li>
                <!-- Wallet Connect - Only for Admin -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2" href="{{ route('admin.wallet.index') }}">
                        <i class="fa fa-wallet menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Wallet Connect</span>
                    </a>
                </li>
                <!-- Payments - Only for Admin -->
                <li class="nav-item" hidden>
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.payments.index') }}">
                        <i class="fa fa-credit-card menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Payments</span>
                    </a>
                </li>
                <!-- Settings - Only for Admin -->
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center px-3 py-2"
                        href="{{ route('admin.setting.index') }}">
                        <i class="fa fa-cog menu-icon mr-2" style="color: #3bd17a;"></i>
                        <span class="menu-title" style="color: #3bd17a;">Setting</span>
                    </a>
                </li>
                @endif
            </ul>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid page-body-wrapper">
            @yield('content')
        </div>
    </div>


    <script src="{{ asset('assets/dashboard/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/dashboard/vendors/js/vendor.bundle.addons.js') }}"></script>

    <script src="{{ asset('assets/dashboard/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/misc.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/settings.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/todolist.js') }}"></script>

    <!-- Ethers.js for Web3 functionality - Using alternative CDN -->
    <script src="https://cdn.jsdelivr.net/npm/ethers@5.7.2/dist/ethers.umd.min.js"></script>
    <!-- Wallet Service -->
    <script src="{{ asset('js/wallet-service.js') }}"></script>
    <!-- <script src="js/dashboard.js"></script> -->

    <script>
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const sidebar = document.getElementById('sidebar');

        mobileSidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });

        // Optional: close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 991 && sidebar.classList.contains('show') && !sidebar.contains(e.target) && !
                mobileSidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>

</html>
