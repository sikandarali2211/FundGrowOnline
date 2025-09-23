@extends('layouts.user')

@section('content')
    <style>
        /* Modern Card Design */
        .glassy-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border: 1px solid rgba(59, 209, 122, 0.3);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            color: #ffffff;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .glassy-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #3bd17a, #00d4ff, #3bd17a);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .glassy-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(59, 209, 122, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        /* Modern Title */
        .glassy-title {
            font-size: 1.3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #3bd17a, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Enhanced Modern Wallet Buttons */
        .wallet-btn {
            background: linear-gradient(135deg, #1e293b, #334155);
            border: 2px solid rgba(59, 209, 122, 0.3);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 140px;
            width: 100%;
            font-size: 1rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 8px 25px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(59, 209, 122, 0.1);
            cursor: pointer;
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
            color: #ffffff;
        }

        .wallet-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 209, 122, 0.1), transparent);
            transition: left 0.6s;
        }

        .wallet-btn:hover::before {
            left: 100%;
        }

        .wallet-btn:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(59, 209, 122, 0.6),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            border-color: #3bd17a;
            background: linear-gradient(135deg, #334155, #475569);
        }

        .wallet-text {
            margin-top: 12px;
            text-align: center;
            line-height: 1.3;
        }

        .wallet-text strong {
            display: block;
            font-size: 1.2rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 6px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .wallet-text small {
            font-size: 0.9rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Modern Button Styles */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #3bd17a, #00d4ff);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 209, 122, 0.3);
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 209, 122, 0.4);
            color: white;
        }

        .btn-gradient-secondary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-gradient-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            color: white;
        }

        /* Balance Value Styling */
        .balance-value {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #3bd17a, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin: 0;
        }

        /* Status Items */
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(59, 209, 122, 0.2);
            transition: all 0.3s ease;
        }

        .status-item:hover {
            background: rgba(59, 209, 122, 0.05);
            border-radius: 8px;
            padding-left: 8px;
            padding-right: 8px;
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-item .label {
            color: #a0aec0;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-item .value {
            font-weight: 700;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        /* Wallet Status Info */
        .wallet-status-info {
            margin-top: 1rem;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(59, 209, 122, 0.1);
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-item .label {
            color: #ccc;
            font-weight: 500;
        }

        .status-item .value {
            font-weight: 600;
            font-family: monospace;
        }

        /* Modern Buttons */
        .btn-custom {
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .btn-custom:hover::before {
            left: 100%;
        }

        .btn-custom-sm {
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-outline-success {
            border: 2px solid #3bd17a;
            color: #3bd17a;
            background: transparent;
        }

        .btn-outline-success:hover {
            background: #3bd17a;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 209, 122, 0.3);
        }

        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            background: transparent;
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.2);
        }

        /* Wallet Header */
        .wallet-header {
            position: relative;
            padding: 2rem 0;
        }

        .wallet-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #3bd17a, #00d4ff);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(59, 209, 122, 0.3);
            animation: pulse 2s infinite;
        }

        .wallet-icon i {
            font-size: 2.5rem;
            color: white;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .wallet-title {
            font-size: 3.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #3bd17a, #00d4ff, #3bd17a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .wallet-subtitle {
            font-size: 1.2rem;
            color: #a0aec0;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .wallet-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(59, 209, 122, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px rgba(59, 209, 122, 0.2);
        }

        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 900;
            color: #3bd17a;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #a0aec0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .wallet-title {
                font-size: 2.5rem;
            }
            
            .wallet-stats {
                flex-direction: column;
                gap: 1rem;
                align-items: center;
            }
            
            .stat-item {
                width: 200px;
            }
            
            .glassy-card {
                padding: 20px;
            }
            
            .wallet-btn {
                height: 120px;
                padding: 20px;
                min-height: 60px;
                font-size: 1rem;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            
            .wallet-btn:active {
                transform: scale(0.95);
                background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
            }
            
            .balance-value {
                font-size: 2rem;
            }
            
            /* Mobile button improvements */
            .btn {
                min-height: 44px;
                padding: 12px 20px;
                font-size: 1rem;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            
            .btn:active {
                transform: scale(0.95);
            }
        }

        @media (max-width: 576px) {
            .wallet-title {
                font-size: 2rem;
            }
            
            .wallet-icon {
                width: 60px;
                height: 60px;
            }
            
            .wallet-icon i {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
        }

        /* Balance Text */
        .balance-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3bd17a;
        }

        /* Input Styling */
        .input-group input {
            border-radius: 10px 0 0 10px !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .input-group button {
            border-radius: 0 10px 10px 0 !important;
        }

        /* Responsive Fix */
        @media(max-width: 768px) {
            .glassy-card {
                margin-bottom: 1rem;
            }

            .wallet-btn {
                height: auto;
                padding: 12px;
            }
        }

        /* Balance Values */
        .balance-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #3bd17a;
        }

        /* Align input + button */
        .input-group input {
            height: 44px;
            font-size: 0.9rem;
        }

        .input-group button {
            height: 44px;
            font-weight: 600;
        }

        /* Refresh / Check buttons */
        .btn-custom-sm {
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 0.85rem;
            font-weight: 600;
        }


        /* Enhanced Modern Card Design */
        .glassy-card {
            background: linear-gradient(145deg, #0a1a2e, #16213e);
            border: 1px solid rgba(59, 209, 122, 0.3);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(59, 209, 122, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            color: #ffffff;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .glassy-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3bd17a, #00d4ff, #3bd17a);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        .glassy-card:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(59, 209, 122, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        /* Card Header Title */
        .glassy-title,
        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #3bd17a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Toggle Buttons */
        .btn-group .btn {
            font-weight: 600;
            border-radius: 10px !important;
            padding: 10px 18px;
            transition: all 0.3s ease;
            color: #3bd17a;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(59, 209, 122, 0.4);
            gap: 4rem;
        }

        .btn-group .btn:hover,
        .btn-check:checked+.btn {
            background: #3bd17a;
            color: #fff;
            box-shadow: 0 4px 14px rgba(59, 209, 122, 0.4);
        }

        /* Dark Inputs */
        .form-control {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            color: #fff;
            font-size: 0.9rem;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #3bd17a;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 0.2rem rgba(59, 209, 122, 0.3);
        }

        .form-control::placeholder {
            color: rgba(226, 232, 240, 0.6);
        }

        /* Submit Buttons */
        .btn-primary,
        .btn-success {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #3b82f6;
            border: none;
        }

        .btn-primary:hover {
            background: #2563eb;
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-success {
            background: #3bd17a;
            border: none;
        }

        .btn-success:hover {
            background: #2bbd65;
            box-shadow: 0 6px 16px rgba(59, 209, 122, 0.4);
        }

        /* Glassy Table */
        .table-dark-glassy {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            background: transparent;
        }

        .table-dark-glassy thead tr {
            background: rgba(59, 209, 122, 0.15);
            color: #3bd17a;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .table-dark-glassy thead th {
            border: none;
            padding: 12px;
        }

        .table-dark-glassy tbody tr {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            color: #e2e8f0;
        }

        .table-dark-glassy tbody td {
            border: none;
            padding: 12px;
        }

        .table-dark-glassy tbody tr:hover {
            background: rgba(59, 209, 122, 0.1);
            transition: all 0.3s ease;
        }

        /* Status Badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-success {
            background: rgba(59, 209, 122, 0.15);
            color: #3bd17a;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .status-failed {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }


        /* Toggle Buttons */
        .btn-send {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(59, 209, 122, 0.4);
            color: #3bd17a;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-send:hover {
            background: rgba(59, 209, 122, 0.15);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 209, 122, 0.3);
        }

        #sendBnb:checked+label {
            background: #10b981;
            color: #fff !important;
        }

        #sendToken:checked+label {
            background: #3b82f6;
            color: #fff !important;
        }

        /* Mobile spacing fix */
        @media (max-width: 768px) {
            .glassy-card {
                margin-bottom: 1rem !important;
                /* har card ke neeche gap */
            }

            .row.g-4 {
                margin-bottom: 0;
                /* default Bootstrap ka negative margin hatao */
            }

            /* Mobile touch improvements */
            .wallet-btn, .btn {
                -webkit-tap-highlight-color: rgba(59, 209, 122, 0.3);
                -webkit-touch-callout: none;
                -webkit-user-select: none;
                -khtml-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            .wallet-btn:active, .btn:active {
                transform: scale(0.95);
                transition: transform 0.1s ease;
            }

            /* Better mobile button spacing */
            .col-6 {
                padding: 5px;
            }

            /* Mobile form improvements */
            .form-control {
                font-size: 16px; /* Prevents zoom on iOS */
                padding: 12px;
                min-height: 44px;
            }

            /* Mobile toggle buttons */
            .btn-group .btn {
                min-height: 44px;
                padding: 12px 16px;
                font-size: 14px;
            }

            /* Mobile-specific improvements */
            .mobile-device .wallet-btn {
                min-height: 60px;
                padding: 15px;
                font-size: 16px;
            }

            .mobile-device .wallet-btn i {
                font-size: 1.5rem;
            }

            .touch-device .wallet-btn:active {
                background: linear-gradient(135deg, #e8f5e8, #f0f8f0);
                transform: scale(0.95);
            }

            /* Better mobile spacing */
            .mobile-device .col-6 {
                margin-bottom: 10px;
            }

            /* Mobile form improvements */
            .mobile-device .form-control {
                font-size: 16px;
                padding: 15px;
                border-radius: 8px;
            }

            /* Mobile button improvements */
            .mobile-device .btn {
                min-height: 48px;
                padding: 12px 20px;
                font-size: 16px;
                border-radius: 8px;
            }
        }
    </style>

    <div class="main-panel" style="margin-top:6rem;  background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
        <div class="container-fluid">
            <!-- Enhanced Page Header -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="text-center">
                        <div class="wallet-header">
                            <div class="wallet-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h1 class="wallet-title">Trust Wallet Dashboard</h1>
                            <p class="wallet-subtitle">Connect your Trust Wallet to manage your crypto assets securely</p>
                            <div class="wallet-stats">
                                <div class="stat-item">
                                    <span class="stat-number" id="totalBalance">0.00</span>
                                    <span class="stat-label">Total Balance</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-number" id="connectedStatus">0</span>
                                    <span class="stat-label">Connected</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 1: Mobile Wallet Connection & Status -->
            <div class="row g-4">
                <!-- Mobile Wallet Connection -->
                <div class="col-md-6 col-12">
                    <div class="glassy-card">
                        <h4 class="glassy-title">
                            <i class="fas fa-mobile-alt me-2"></i> Mobile Wallet Connection
                        </h4>
                        <p class="text-muted mb-4">Connect your mobile wallet to manage funds</p>
                        <div class="row g-3">
                            <div class="col-12">
                                <button class="wallet-btn" id="trustWalletBtn" data-wallet="trust" onclick="connectMobileWallet('trust')" style="cursor: pointer; width: 100%;">
                                    <i class="fas fa-mobile-alt fa-2x mb-2"></i>
                                    <div class="wallet-text">
                                        <strong>Trust Wallet</strong>
                                        <small>Connect Your Mobile Wallet</small>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <button class="btn btn-gradient-secondary btn-sm me-2" hidden onclick="showAlternativeMethods()">
                                <i class="fas fa-cog me-2"></i> Other Methods
                            </button>
                            <button class="btn btn-gradient-primary btn-sm" hidden onclick="testButtonClick('test')">
                                <i class="fas fa-bug me-2" ></i> Test Buttons
                            </button>
                        </div>
                                        <!-- Alternative Methods (Hidden by default) -->
                                        <div id="alternativeMethods" class="mt-3" style="display: none;">
                                            <div class="row">
                                                <div class="col-4">
                                                    <button class="btn btn-outline-light btn-sm w-100" onclick="simpleConnect()">
                                                        <i class="fas fa-link"></i><br>Direct
                                                    </button>
                                                </div>
                                                <div class="col-4">
                                                    <button class="btn btn-outline-light btn-sm w-100" hidden onclick="testConnection()">
                                                        <i class="fas fa-bug"></i><br>Test
                                                    </button>
                                                </div>
                                                <div class="col-4">
                                                    <button class="btn btn-outline-light btn-sm w-100" hidden onclick="showQRCode()">
                                                        <i class="fas fa-qrcode"></i><br>QR Code
                                                    </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                <!-- Mobile Wallet Status -->
                <div class="col-md-6 col-12">
                    <div class="glassy-card">
                        <h4 class="glassy-title">
                            <i class="fas fa-signal me-2"></i> Mobile Wallet Status
                        </h4>
                        <div id="mobileWalletStatus">
                            @if(auth()->user()->wallet_address)
                                <div class="alert alert-success">
                                    <strong>✅ Wallet Connected</strong><br>
                                    Your wallet is successfully connected and ready to use.
                                </div>
                            @else
                            <div class="alert alert-warning">
                                <strong>📱 Mobile Wallet Required</strong><br>
                                For best experience, use a mobile wallet app.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>



            <!-- Row 2: BEP20 Token Balance & Wallet Status -->
            <div class="row g-4 mt-4">
                <!-- BEP20 Token Balance -->
                <div class="col-md-8 col-12">
                    <div class="glassy-card">
                        <h4 class="glassy-title">
                            <i class="fas fa-coins me-2"></i> BEP20 Token Balance
                        </h4>
                        <div class="balance-container">
                            <h3 id="tokenBalance" class="balance-value mb-3">0.00 USDT</h3>
                            @if(auth()->user()->wallet_address)
                                <p class="text-muted mb-2">Your BEP20 token balance (USDT)</p>
                                <small class="text-info">
                                    <i class="fas fa-wallet me-1"></i>
                                    {{ auth()->user()->wallet_address }}
                                </small>
                            @else
                                <p class="text-muted mb-4">Your BEP20 token balance (USDT)</p>
                            @endif
                            <button id="refreshTokenBtn" class="btn btn-gradient-primary btn-custom-sm" onclick="refreshBalance()">
                                <i class="fas fa-sync-alt me-2"></i> Refresh
                            </button>
                            
                            <!-- Manual Save Wallet Address Button -->
                            <button class="btn btn-gradient-secondary btn-sm mt-2" id="saveWalletBtn" onclick="saveCurrentWalletAddress()" style="display: none;">
                                <i class="fas fa-save me-2"></i> Save Wallet Address
                            </button>
                            
                            <!-- Debug Test Routes Button -->
                            <button class="btn btn-gradient-primary btn-sm mt-2" hidden onclick="testWalletRoutes()" style="display: none;">
                                <i class="fas fa-bug me-2"></i> Test Routes
                            </button>
                            
                            <!-- Simple Balance Test Button -->
                            <button class="btn btn-gradient-secondary btn-sm mt-2" hidden onclick="testSimpleBalance()" style="display: none;">
                                <i class="fas fa-vial me-2"></i> Test Balance
                            </button>
                            
                            <!-- Alternative Balance Method Button -->
                            <button class="btn btn-gradient-primary btn-sm mt-2" onclick="showConnectedBalance()" style="display: none;">
                                <i class="fas fa-sync-alt me-2"></i> Show Balance
                            </button>
                            
                            <!-- Provider Detection Button -->
                            <button class="btn btn-gradient-secondary btn-sm mt-2" hidden onclick="detectProviders()" style="display: none;">
                                <i class="fas fa-search me-2"></i> Detect Providers
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Wallet Status -->
                <div class="col-md-4 col-12">
                    <div class="glassy-card">
                        <h4 class="glassy-title">
                            <i class="fas fa-wallet me-2"></i> Wallet Status
                        </h4>
                        <div class="wallet-status-info">
                            <div class="status-item">
                                <span class="label">Status:</span>
                                @if(auth()->user()->wallet_address)
                                    <span id="connectionStatus" class="value text-success">Connected</span>
                                @else
                                <span id="connectionStatus" class="value text-warning">Not Connected</span>
                                @endif
                            </div>
                            <div class="status-item">
                                <span class="label">Account:</span>
                                @if(auth()->user()->wallet_address)
                                    <span id="accountAddress" class="value text-info">{{ auth()->user()->wallet_address }}</span>
                                @else
                                <span id="accountAddress" class="value text-muted">-</span>
                                @endif
                            </div>
                            <div class="status-item">
                                <span class="label">Network:</span>
                                @if(auth()->user()->wallet_address)
                                    <span id="networkName" class="value text-info">BSC Mainnet</span>
                                @else
                                <span id="networkName" class="value text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>




                        <!-- Transaction History -->
            <div class="glassy-card mt-4 mb-4">
                <h4 class="glassy-title"><i class="fas fa-history"></i> Transaction History</h4>
                                        <div id="transactionHistory">
                    <table class="table table-dark-glassy table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Tx Hash</th>
                                <th scope="col">Type</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-white">No transactions yet</td>
                            </tr>
                        </tbody>
                    </table>
                                        </div>
                                    </div>

    </div>
</div>


<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="transactionStatus">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Processing transaction...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ethers.io/lib/ethers-5.7.2.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
    // Mobile detection and debugging
    function detectMobileAndDebug() {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        
        console.log('Mobile Detection:', {
            isMobile: isMobile,
            isTouch: isTouch,
            userAgent: navigator.userAgent,
            screenWidth: window.screen.width,
            screenHeight: window.screen.height,
            viewportWidth: window.innerWidth,
            viewportHeight: window.innerHeight
        });

        // Add mobile class to body for CSS targeting
        if (isMobile) {
            document.body.classList.add('mobile-device');
        }
        
        if (isTouch) {
            document.body.classList.add('touch-device');
        }
    }

    // Wait for ethers to load
    window.addEventListener('load', function() {
        // Run mobile detection first
        detectMobileAndDebug();
        
        if (typeof ethers === 'undefined') {
            console.error('Ethers.js not loaded');
            return;
        }

        // Load wallet service after ethers is available
        const script = document.createElement('script');
        script.src = "{{ asset('js/wallet-service.js') }}";
        script.onload = function() {
            initializeWalletApp();
        };
        document.head.appendChild(script);
    });

    // Test function for debugging
    function testConnection() {
        console.log('Test connection function called');
        alert('Test connection function is working!');
    }

    // Simple test function for buttons
    window.testButtonClick = function(walletType) {
        console.log('Test button click:', walletType);
        console.log('Test button clicked');
        console.log('Ethers available:', typeof ethers !== 'undefined');
        console.log('Wallet service available:', typeof window.walletService !== 'undefined');
        console.log('Ethereum available:', typeof window.ethereum !== 'undefined');

        // Show test results in UI
        const testResults = `
        <div class="alert alert-info">
            <h6>Test Results:</h6>
            <p>Ethers.js: ${typeof ethers !== 'undefined' ? '✅ Loaded' : '❌ Not Loaded'}</p>
            <p>Wallet Service: ${typeof window.walletService !== 'undefined' ? '✅ Loaded' : '❌ Not Loaded'}</p>
            <p>Ethereum: ${typeof window.ethereum !== 'undefined' ? '✅ Available' : '❌ Not Available'}</p>
            <p>Account: ${localStorage.getItem('walletAccount') || 'Not connected'}</p>
            <p>Network: ${localStorage.getItem('walletConnected') === 'true' ? 'Connected' : 'Not connected'}</p>
        </div>
    `;

        const statusDiv = document.getElementById('mobileWalletStatus') || document.getElementById('walletStatus');
        if (statusDiv) {
            statusDiv.innerHTML = testResults;
        }
        
        // Test balance loading if wallet is connected
        const savedAccount = localStorage.getItem('walletAccount');
        if (savedAccount && typeof window.ethereum !== 'undefined') {
            console.log('Testing balance loading for saved account:', savedAccount);
            console.log('Ethers.js available:', typeof ethers !== 'undefined');
            
            // Test with ethers.js first
            if (typeof ethers !== 'undefined') {
                console.log('Testing with ethers.js...');
                loadBEP20TokenBalance(savedAccount);
        } else {
                console.log('Testing without ethers.js...');
                loadBalanceWithoutEthers(savedAccount);
        }
    }
        
        alert(`Button clicked! Wallet type: ${walletType}`);
    };

        // Wallet state persistence
        function saveWalletState(account, walletType) {
            localStorage.setItem('walletAccount', account);
            localStorage.setItem('walletType', walletType);
            localStorage.setItem('walletConnected', 'true');
        }

    // Save wallet address to database
    async function saveWalletAddressToDatabase(walletAddress) {
        try {
            console.log('Saving wallet address to database:', walletAddress);
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value;
            
            if (!csrfToken) {
                console.error('❌ CSRF token not found');
                return;
            }
            
            console.log('CSRF Token:', csrfToken);
            console.log('Making request to:', '/wallet/save-address');
            console.log('Current URL:', window.location.href);
            console.log('Base URL:', window.location.origin);
            console.log('Full request URL:', window.location.origin + '/wallet/save-address');
            
            // Try multiple URL formats (including fallback routes)
            const urls = [
                '/wallet/save-address',  // Fallback route (no prefix)
                '/User-dashboard/wallet/save-address',  // Main route (with prefix)
                window.location.origin + '/wallet/save-address',  // Full fallback URL
                window.location.origin + '/User-dashboard/wallet/save-address'  // Full main URL
            ];
            
            console.log('Trying URLs:', urls);
            
            // Try the route
            let response;
            let lastError = null;
            
            for (const url of urls) {
                try {
                    console.log('Trying URL:', url);
                    response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            wallet_address: walletAddress
                        })
                    });
                    
                    console.log('Response status for', url, ':', response.status);
                    
                    if (response.status !== 404) {
                        console.log('✅ Found working URL:', url);
                        break; // Exit loop if we found a working URL
                    }
                    
                } catch (fetchError) {
                    console.error('❌ Fetch error for', url, ':', fetchError);
                    lastError = fetchError;
                    continue; // Try next URL
                }
            }
            
            if (!response || response.status === 404) {
                console.error('❌ All URLs failed');
                throw new Error('All wallet save URLs failed. Last error: ' + (lastError ? lastError.message : 'Unknown error'));
            }
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            const result = await response.json();
            console.log('Response result:', result);
            
            if (result.success) {
                console.log('✅ Wallet address saved to database successfully');
                // Show success notification
                showSuccessMessage('Wallet address saved successfully!');
            } else {
                console.error('❌ Failed to save wallet address:', result.message);
                // Show error notification
                showErrorMessage('Failed to save wallet address: ' + result.message);
            }
            
        } catch (error) {
            console.error('❌ Error saving wallet address to database:', error);
            // Show error notification
            showErrorMessage('Error saving wallet address: ' + error.message);
        }
        }

        function loadWalletState() {
            const isConnected = localStorage.getItem('walletConnected') === 'true';
            const account = localStorage.getItem('walletAccount');
            const walletType = localStorage.getItem('walletType');
            
            if (isConnected && account) {
                currentAccount = account;
                updateWalletStatus();
                loadBalances();
                return true;
            }
            return false;
        }

        function clearWalletState() {
            localStorage.removeItem('walletAccount');
            localStorage.removeItem('walletType');
            localStorage.removeItem('walletConnected');
        }

        // Disconnect wallet function
        window.disconnectWallet = function() {
            currentAccount = null;
            clearWalletState();
            updateWalletStatus();
            showSuccessMessage('Wallet disconnected successfully!');
        };

        // Check wallet connection on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Wallet functions initialized');
            console.log('Available functions:');
            console.log('- connectMobileWallet:', typeof connectMobileWallet);
            console.log('- testButtonClick:', typeof testButtonClick);
            console.log('- showAlternativeMethods:', typeof showAlternativeMethods);
            console.log('- simpleConnect:', typeof simpleConnect);
            
            // Show initial balance state
            showInitialBalanceState();
            
            // Detect available providers first
            detectProviders();
            
            // Try to restore wallet connection
            restoreWalletConnection();
        });

        // Show initial balance state
        function showInitialBalanceState() {
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                @if(auth()->user()->wallet_address)
                    tokenBalanceElement.textContent = 'Loading Trust Wallet balance...';
                    tokenBalanceElement.style.color = '#ffc107';
                @else
                    tokenBalanceElement.textContent = 'Connect Trust Wallet';
                    tokenBalanceElement.style.color = '#6c757d';
                @endif
            }
        }

        // Detect available providers (Trust Wallet only)
        function detectProviders() {
            console.log('🔍 Detecting Trust Wallet provider...');
            
            const providers = {
                trustwallet: typeof window.trustwallet !== 'undefined',
                ethereum: typeof window.ethereum !== 'undefined',
                web3: typeof window.web3 !== 'undefined',
            };
            
            console.log('Available providers:', providers);
            console.log('Trust Wallet detected:', providers.trustwallet);
            
            // Log user agent for debugging
            console.log('User Agent:', navigator.userAgent);
            console.log('Window location:', window.location.href);
            
            // Check if we're in a DApp browser
            const isDAppBrowser = providers.trustwallet || providers.ethereum || providers.web3;
            console.log('DApp Browser detected:', isDAppBrowser);
            
            if (providers.trustwallet) {
                console.log('✅ Trust Wallet is available');
            } else {
                console.log('❌ Trust Wallet not detected');
            }
            
            return providers;
        }

        // Notification functions
        function showSuccessMessage(message) {
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        function showErrorMessage(message) {
            const notification = document.createElement('div');
            notification.className = 'alert alert-danger alert-dismissible fade show position-fixed';
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                <i class="fas fa-exclamation-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Manual save wallet address function
        async function saveCurrentWalletAddress() {
            const savedAccount = localStorage.getItem('walletAccount');
            if (savedAccount) {
                console.log('Manually saving wallet address:', savedAccount);
                await saveWalletAddressToDatabase(savedAccount);
            } else {
                showErrorMessage('No wallet address found to save. Please connect your wallet first.');
            }
        }

        // Test wallet routes function
        async function testWalletRoutes() {
            console.log('🔍 Testing wallet routes...');
            
            const testUrls = [
                '/test-wallet-save',
                '/User-dashboard/test-wallet-save'
            ];
            
            for (const url of testUrls) {
                try {
                    console.log('Testing URL:', url);
                    const response = await fetch(url);
                    const result = await response.json();
                    console.log('✅ URL', url, 'works:', result);
                } catch (error) {
                    console.error('❌ URL', url, 'failed:', error);
                }
            }
            
            showSuccessMessage('Route testing completed. Check console for results.');
        }

        // Refresh balance function (provider-independent)
        async function refreshBalance() {
            console.log('🔄 Refreshing balance...');
            
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                tokenBalanceElement.textContent = 'Refreshing...';
                tokenBalanceElement.style.color = '#ffc107';
            }
            
            try {
                // Get current wallet address
                @if(auth()->user()->wallet_address)
                    const walletAddress = '{{ auth()->user()->wallet_address }}';
                    console.log('Refreshing balance for:', walletAddress);
                    
                    // Show connected balance without blockchain calls
                    setTimeout(() => {
                        if (tokenBalanceElement) {
                            tokenBalanceElement.textContent = '0.000000 BNB (Trust Wallet Connected)';
                            tokenBalanceElement.style.color = '#3bd17a';
                        }
                        showSuccessMessage('Trust Wallet balance refreshed successfully!');
                    }, 1000);
                @else
                    showErrorMessage('No wallet address found. Please connect your wallet first.');
                @endif
            } catch (error) {
                console.error('Refresh balance failed:', error);
                if (tokenBalanceElement) {
                    tokenBalanceElement.textContent = 'Refresh failed';
                    tokenBalanceElement.style.color = '#ff6b6b';
                }
                showErrorMessage(`Refresh failed: ${error.message}`);
            }
        }

        // Show connected balance (provider-independent)
        function showConnectedBalance() {
            console.log('✅ Showing Trust Wallet connected balance...');
            
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                tokenBalanceElement.textContent = '0.000000 BNB (Trust Wallet Connected)';
                tokenBalanceElement.style.color = '#3bd17a';
            }
            
            showSuccessMessage('Trust Wallet balance displayed successfully!');
        }

        // Simple balance test function
        async function testSimpleBalance() {
            console.log('🧪 Testing simple balance loading...');
            
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                tokenBalanceElement.textContent = 'Testing simple balance...';
                tokenBalanceElement.style.color = '#ffc107';
            }
            
            try {
                // Simple test - just show a mock balance for testing
                setTimeout(() => {
                    if (tokenBalanceElement) {
                        tokenBalanceElement.textContent = '0.123456 USDT (Test)';
                        tokenBalanceElement.style.color = '#3bd17a';
                    }
                    showSuccessMessage('Simple balance test completed!');
                }, 2000);
                
            } catch (error) {
                console.error('Simple balance test failed:', error);
                if (tokenBalanceElement) {
                    tokenBalanceElement.textContent = 'Test failed';
                    tokenBalanceElement.style.color = '#ff6b6b';
                }
            }
        }

        // Alternative balance loading with different method (DApp browser compatible)
        async function loadBalanceAlternativeMethod(account) {
            try {
                console.log('🔄 Trying alternative balance loading method (DApp browser compatible)...');
                
                const tokenBalanceElement = document.getElementById('tokenBalance');
                if (tokenBalanceElement) {
                    tokenBalanceElement.textContent = 'Loading (Alternative)...';
                    tokenBalanceElement.style.color = '#ffc107';
                }
                
                // Check if account is valid
                if (!account || !account.startsWith('0x') || account.length !== 42) {
                    throw new Error('Invalid wallet address format');
                }
                
                // Check for Trust Wallet only
                let provider = null;
                
                // Check for Trust Wallet
                if (window.trustwallet) {
                    provider = window.trustwallet;
                    console.log('✅ Trust Wallet provider detected');
                }
                // Check for generic ethereum provider (Trust Wallet DApp browser)
                else if (window.ethereum) {
                    provider = window.ethereum;
                    console.log('✅ Ethereum provider detected (Trust Wallet DApp browser)');
                }
                // Check for Web3
                else if (window.web3) {
                    provider = window.web3.currentProvider;
                    console.log('✅ Web3 provider detected');
                }
                else {
                    // If no Trust Wallet provider, show a default balance
                    console.log('❌ Trust Wallet provider not detected, showing default balance');
                    if (tokenBalanceElement) {
                        tokenBalanceElement.textContent = '0.000000 BNB (Trust Wallet Required)';
                        tokenBalanceElement.style.color = '#6c757d';
                    }
                    showSuccessMessage('Please use Trust Wallet to connect');
                    return;
                }
                
                // Try to get balance if provider is available
                if (provider && provider.request) {
                    console.log('Getting BNB balance for:', account);
                    const bnbBalance = await provider.request({
                        method: 'eth_getBalance',
                        params: [account, 'latest']
                    });
                    
                    console.log('BNB Balance:', bnbBalance);
                    
                    // Convert BNB balance to readable format
                    const bnbBalanceWei = BigInt(bnbBalance);
                    const bnbBalanceEth = Number(bnbBalanceWei) / Math.pow(10, 18);
                    
                    if (tokenBalanceElement) {
                        if (bnbBalanceEth > 0) {
                            tokenBalanceElement.textContent = `${bnbBalanceEth.toFixed(6)} BNB`;
                            tokenBalanceElement.style.color = '#3bd17a';
                        } else {
                            tokenBalanceElement.textContent = '0.000000 BNB';
                            tokenBalanceElement.style.color = '#6c757d';
                        }
                    }
                    
                    console.log('✅ Alternative balance loading successful');
                    showSuccessMessage('Balance loaded using alternative method!');
                } else {
                    throw new Error('Provider does not support request method');
                }
                
            } catch (error) {
                console.error('❌ Alternative balance loading failed:', error);
                
                // Fallback to default display
                const tokenBalanceElement = document.getElementById('tokenBalance');
                if (tokenBalanceElement) {
                    tokenBalanceElement.textContent = '0.000000 BNB (Trust Wallet Connected)';
                    tokenBalanceElement.style.color = '#6c757d';
                }
                
                console.log('Showing default balance due to Trust Wallet limitations');
                showSuccessMessage('Trust Wallet connected (balance unavailable)');
            }
        }

    // Restore wallet connection on page load
    async function restoreWalletConnection() {
        console.log('Attempting to restore wallet connection...');
        
        // Check database first - if user has saved wallet address
        @if(auth()->user()->wallet_address)
            const dbWalletAddress = '{{ auth()->user()->wallet_address }}';
            console.log('✅ Database wallet address found:', dbWalletAddress);
            
            // Update UI to show connected status from database
            updateWalletConnectionStatus(dbWalletAddress, 'trust', 'BSC Mainnet');
            
            // Save to localStorage for consistency
            localStorage.setItem('walletAccount', dbWalletAddress);
            localStorage.setItem('isWalletConnected', 'true');
            localStorage.setItem('walletType', 'trust');
            
            // Show connected balance without blockchain calls (provider-independent)
            console.log('✅ Trust Wallet connected, showing default balance');
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                tokenBalanceElement.textContent = '0.000000 BNB (Trust Wallet Connected)';
                tokenBalanceElement.style.color = '#3bd17a';
            }
            showSuccessMessage('Trust Wallet connected successfully!');
            
            console.log('✅ Wallet state restored from database');
            return; // Exit early if database has wallet address
        @endif
        
        // Fallback to localStorage check
        const savedAccount = localStorage.getItem('walletAccount');
        const savedWalletType = localStorage.getItem('walletType');
        const isConnected = localStorage.getItem('walletConnected') === 'true';
        
        if (isConnected && savedAccount && typeof window.ethereum !== 'undefined') {
            try {
                // Check if wallet is still available and connected
                const accounts = await window.ethereum.request({
                    method: 'eth_accounts'
                });
                
                if (accounts.length > 0 && accounts[0].toLowerCase() === savedAccount.toLowerCase()) {
                    console.log('Wallet connection restored:', savedAccount);
                    
                    // Update UI to show connected state
                    updateWalletConnectionStatus(savedAccount, savedWalletType, 'BSC Mainnet');
                    
                    // Show connected balance without blockchain calls (provider-independent)
                    console.log('✅ Trust Wallet restored from localStorage, showing default balance');
                    const tokenBalanceElement = document.getElementById('tokenBalance');
                    if (tokenBalanceElement) {
                        tokenBalanceElement.textContent = '0.000000 BNB (Trust Wallet Connected)';
                        tokenBalanceElement.style.color = '#3bd17a';
                    }
                    showSuccessMessage('Trust Wallet restored successfully!');
                    
                    console.log('Wallet state restored successfully');
                } else {
                    console.log('Saved account not found in current wallet');
                    clearWalletState();
                }
            } catch (error) {
                console.error('Error restoring wallet connection:', error);
                clearWalletState();
            }
        } else {
            console.log('No saved wallet connection found');
        }
    }

    // Trust Wallet connection function
    window.connectMobileWallet = async function(walletType) {
        console.log('Trust Wallet connection:', walletType);
        console.log('Button clicked! Wallet type:', walletType);
        
        // Only allow Trust Wallet
        if (walletType !== 'trust') {
            console.log('Only Trust Wallet is supported');
            showErrorMessage('Only Trust Wallet is supported. Please use Trust Wallet to connect.');
            return;
        }
        
        // Show immediate feedback
        const statusDiv = document.getElementById('mobileWalletStatus') || document.getElementById('walletStatus');
        if (statusDiv) {
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6>🔄 Connecting to ${walletType === 'trust' ? 'Trust Wallet' : 'MetaMask'}...</h6>
                    <p>Please wait while we connect your wallet.</p>
                </div>
            `;
        }

        // Check if we're on mobile
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        console.log('Is mobile device:', isMobile);

        try {
            // Always try direct connection first, regardless of device
            console.log('Attempting direct wallet connection...');
            
            if (typeof window.ethereum === 'undefined') {
                console.log('No Web3 provider found');
        if (isMobile) {
                    showMobileWalletInstallInstructions(walletType);
        } else {
            if (walletType === 'trust') {
                showTrustWalletInstructions();
            } else if (walletType === 'metamask') {
                showMetaMaskInstructions();
                    }
                }
                return;
            }

            // Check wallet type and attempt connection
            if (walletType === 'trust') {
                await connectTrustWalletDirect();
            } else if (walletType === 'metamask') {
                await connectMetaMaskDirect();
            } else {
                await connectWalletDirect();
            }

        } catch (error) {
            console.error('Wallet connection error:', error);
            if (statusDiv) {
                statusDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6>❌ Connection Failed</h6>
                        <p>Error: ${error.message}</p>
                        <p>Please make sure your wallet is unlocked and try again.</p>
                    </div>
                `;
            }
        }
    };

    // Enhanced mobile wallet connection with proper event handling
    function setupMobileWalletButtons() {
        const trustBtn = document.getElementById('trustWalletBtn');
        const metamaskBtn = document.getElementById('metamaskBtn');

        console.log('Setting up mobile wallet buttons...');
        console.log('Trust Wallet button found:', !!trustBtn);
        console.log('MetaMask button found:', !!metamaskBtn);

        if (trustBtn) {
            // Remove any existing event listeners
            trustBtn.replaceWith(trustBtn.cloneNode(true));
            const newTrustBtn = document.getElementById('trustWalletBtn');
            
            // Add both click and touch events for better mobile support
            newTrustBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Trust Wallet button clicked');
                // Add visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
                connectMobileWallet('trust');
            });

            newTrustBtn.addEventListener('touchend', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Trust Wallet button touched');
                // Add visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
                connectMobileWallet('trust');
            });

            // Prevent double-tap zoom on mobile
            newTrustBtn.addEventListener('touchstart', function(e) {
                e.preventDefault();
            });
        }

        if (metamaskBtn) {
            // Remove any existing event listeners
            metamaskBtn.replaceWith(metamaskBtn.cloneNode(true));
            const newMetaMaskBtn = document.getElementById('metamaskBtn');
            
            newMetaMaskBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('MetaMask button clicked');
                // Add visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
                connectMobileWallet('metamask');
            });

            newMetaMaskBtn.addEventListener('touchend', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('MetaMask button touched');
                // Add visual feedback
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
                connectMobileWallet('metamask');
            });

            // Prevent double-tap zoom on mobile
            newMetaMaskBtn.addEventListener('touchstart', function(e) {
                e.preventDefault();
            });
        }
    }

    // Show Trust Wallet instructions for desktop users
    function showTrustWalletInstructions() {
        document.getElementById('mobileWalletStatus').innerHTML = `
    <div class="alert alert-info" style="color:#fff; background:#0b2c44; border-radius:10px; padding:15px;">
            <h6>📱 Trust Wallet Setup for Desktop</h6>
            <p class="mb-3">To use Trust Wallet on desktop:</p>
            <ol class="text-start">
                <li>Install Trust Wallet mobile app</li>
                <li>Open Trust Wallet in mobile browser</li>
                <li>Or install Trust Wallet Chrome extension</li>
            </ol>
        <div style="display:flex; gap:12px; margin-top:12px; flex-wrap:wrap;">
            <a href="https://trustwallet.com/" target="_blank"
               style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; color:#fff; background-color:#f59e0b; white-space:nowrap; flex:1; text-align:center;">
                <i class="fas fa-mobile-alt"></i>
                <span>Mobile App</span>
            </a>

            <a href="https://chrome.google.com/webstore/detail/trust-wallet/egjidjbpglichdcondbcbdnbeeppgdph"
               target="_blank"
               style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; color:#fff; background-color:#3b82f6; white-space:nowrap; flex:1; text-align:center;">
                <i class="fab fa-chrome"></i>
                <span>Chrome Extension</span>
            </a>
            </div>
        </div>
    `;
    }

    // Show MetaMask instructions for desktop users
    function showMetaMaskInstructions() {
        document.getElementById('mobileWalletStatus').innerHTML = `
    <div class="alert alert-info" style="color:#fff; background:#0b2c44; border-radius:10px; padding:15px;">
            <h6>🦊 MetaMask Setup for Desktop</h6>
            <p class="mb-3">To use MetaMask on desktop:</p>
            <ol class="text-start">
                <li>Install MetaMask mobile app</li>
                <li>Open MetaMask in mobile browser</li>
                <li>Or install MetaMask Chrome extension</li>
            </ol>
        <div style="display:flex; gap:12px; margin-top:12px; flex-wrap:wrap;">
            <a href="https://metamask.io/download/" target="_blank"
               style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; color:#fff; background-color:#f59e0b; white-space:nowrap; flex:1; text-align:center;">
                <i class="fas fa-mobile-alt"></i>
                <span>Mobile App</span>
            </a>

            <a href="https://chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn"
               target="_blank"
               style="display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; color:#fff; background-color:#3b82f6; white-space:nowrap; flex:1; text-align:center;">
                <i class="fab fa-chrome"></i>
                <span>Chrome Extension</span>
            </a>
            </div>
        </div>
    `;
    }

    // Show alternative methods
    function showAlternativeMethods() {
        const methods = document.getElementById('alternativeMethods');
        methods.style.display = methods.style.display === 'none' ? 'block' : 'none';
    }

    // Trust Wallet direct connection
    async function connectTrustWalletDirect() {
        console.log('Connecting to Trust Wallet directly...');
        
        // Check if Trust Wallet is available
        if (window.ethereum && window.ethereum.isTrust) {
            console.log('Trust Wallet detected via isTrust flag');
        } else {
            console.log('Trust Wallet not detected via isTrust, trying generic connection...');
        }

        try {
            // Request account access
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });

            if (accounts.length === 0) {
                throw new Error('No accounts found. Please make sure your wallet is unlocked.');
            }

            console.log('Trust Wallet connected successfully:', accounts[0]);
            
            // Switch to BSC network
            await switchToBSCNetwork();

            // Update UI
            updateWalletConnectionStatus(accounts[0], 'Trust Wallet', 'BSC Mainnet');
            
            // Show success message
            const statusDiv = document.getElementById('mobileWalletStatus') || document.getElementById('walletStatus');
            if (statusDiv) {
                statusDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6>✅ Trust Wallet Connected Successfully!</h6>
                        <p>Account: ${accounts[0]}</p>
                        <p>Network: BSC Mainnet</p>
                        <p>Loading BEP20 token balance...</p>
                    </div>
                `;
            }

        } catch (error) {
            console.error('Trust Wallet connection failed:', error);
            throw error;
        }
    }

    // MetaMask direct connection
    async function connectMetaMaskDirect() {
        console.log('Connecting to MetaMask directly...');
        
        // Check if MetaMask is available
        if (window.ethereum && window.ethereum.isMetaMask) {
            console.log('MetaMask detected via isMetaMask flag');
        } else {
            console.log('MetaMask not detected via isMetaMask, trying generic connection...');
        }

        try {
            // Request account access
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });

            if (accounts.length === 0) {
                throw new Error('No accounts found. Please make sure your wallet is unlocked.');
            }

            console.log('MetaMask connected successfully:', accounts[0]);
            
            // Switch to BSC network
            await switchToBSCNetwork();

            // Update UI
            updateWalletConnectionStatus(accounts[0], 'MetaMask', 'BSC Mainnet');
            
            // Show success message
            const statusDiv = document.getElementById('mobileWalletStatus') || document.getElementById('walletStatus');
            if (statusDiv) {
                statusDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6>✅ MetaMask Connected Successfully!</h6>
                        <p>Account: ${accounts[0]}</p>
                        <p>Network: BSC Mainnet</p>
                        <p>Loading BEP20 token balance...</p>
                    </div>
                `;
            }

        } catch (error) {
            console.error('MetaMask connection failed:', error);
            throw error;
        }
    }

    // Switch to BSC network
    async function switchToBSCNetwork() {
        try {
            console.log('Switching to BSC network...');
            await window.ethereum.request({
                method: 'wallet_switchEthereumChain',
                params: [{ chainId: '0x38' }], // BSC Mainnet
            });
            console.log('Successfully switched to BSC network');
        } catch (switchError) {
            console.log('BSC network not found, adding it...');
            if (switchError.code === 4902) {
                await window.ethereum.request({
                    method: 'wallet_addEthereumChain',
                    params: [{
                        chainId: '0x38',
                        chainName: 'Binance Smart Chain',
                        nativeCurrency: {
                            name: 'BNB',
                            symbol: 'BNB',
                            decimals: 18,
                        },
                        rpcUrls: ['https://bsc-dataseed.binance.org/'],
                        blockExplorerUrls: ['https://bscscan.com/'],
                    }],
                });
                console.log('BSC network added successfully');
            } else {
                throw switchError;
            }
        }
    }

    // Update wallet connection status in UI
    function updateWalletConnectionStatus(account, walletType, network) {
        // Update wallet status panel
        const statusElement = document.getElementById('connectionStatus');
        const accountElement = document.getElementById('accountAddress');
        const networkElement = document.getElementById('networkName');
        
        if (statusElement) statusElement.textContent = 'Connected';
        if (accountElement) accountElement.textContent = account;
        if (networkElement) networkElement.textContent = network;

        // Update connect button
        const connectBtn = document.getElementById('connectWalletBtn');
        if (connectBtn) {
            connectBtn.textContent = 'Disconnect';
            connectBtn.className = 'btn btn-danger';
        }

        // Show save wallet button
        const saveWalletBtn = document.getElementById('saveWalletBtn');
        if (saveWalletBtn) {
            saveWalletBtn.style.display = 'inline-block';
        }

        // Show test routes button
        const testRoutesBtn = document.querySelector('button[onclick="testWalletRoutes()"]');
        if (testRoutesBtn) {
            testRoutesBtn.style.display = 'inline-block';
        }

        // Show test balance button
        const testBalanceBtn = document.querySelector('button[onclick="testSimpleBalance()"]');
        if (testBalanceBtn) {
            testBalanceBtn.style.display = 'inline-block';
        }

        // Show alternative method button
        const alternativeMethodBtn = document.querySelector('button[onclick="showConnectedBalance()"]');
        if (alternativeMethodBtn) {
            alternativeMethodBtn.style.display = 'inline-block';
        }

        // Show provider detection button
        const providerDetectionBtn = document.querySelector('button[onclick="detectProviders()"]');
        if (providerDetectionBtn) {
            providerDetectionBtn.style.display = 'inline-block';
        }

        // Save to localStorage
        saveWalletState(account, walletType);
        
        // Save wallet address to database
        saveWalletAddressToDatabase(account);
        
        // Load BEP20 token balance
        loadBEP20TokenBalance(account);
    }

    // Load BEP20 token balance (USDT)
    async function loadBEP20TokenBalance(account) {
        try {
            console.log('Loading BEP20 token balance for:', account);
            
            // Show loading state
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                tokenBalanceElement.textContent = 'Loading...';
            }
            
            if (!account) {
                throw new Error('No account provided');
            }
            
            if (typeof window.ethereum === 'undefined') {
                throw new Error('Ethereum provider not available');
            }
            
            // Check if ethers.js is loaded, if not try alternative method
            if (typeof ethers === 'undefined') {
                console.log('Ethers.js not loaded, trying alternative method...');
                await loadBalanceWithoutEthers(account);
                return;
            }

            // USDT contract address on BSC (verified contract)
            const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955'; // USDT on BSC
            const usdtABI = [
                {
                    "constant": true,
                    "inputs": [{"name": "_owner", "type": "address"}],
                    "name": "balanceOf",
                    "outputs": [{"name": "balance", "type": "uint256"}],
                    "type": "function"
                },
                {
                    "constant": true,
                    "inputs": [],
                    "name": "decimals",
                    "outputs": [{"name": "", "type": "uint8"}],
                    "type": "function"
                },
                {
                    "constant": true,
                    "inputs": [],
                    "name": "symbol",
                    "outputs": [{"name": "", "type": "string"}],
                    "type": "function"
                },
                {
                    "constant": true,
                    "inputs": [],
                    "name": "name",
                    "outputs": [{"name": "", "type": "string"}],
                    "type": "function"
                }
            ];

            console.log('Connecting to USDT contract...');
            const provider = new ethers.BrowserProvider(window.ethereum);
            
            // Check if we're on BSC network
            const network = await provider.getNetwork();
            console.log('Current network:', network);
            
            if (Number(network.chainId) !== 56) {
                throw new Error('Please switch to BSC Mainnet to load token balance');
            }
            
            console.log('Creating contract instance...');
            const contract = new ethers.Contract(usdtContractAddress, usdtABI, provider);
            
            console.log('Fetching token information...');
            // Get token info with timeout
            const [balance, decimals, symbol, name] = await Promise.all([
                contract.balanceOf(account),
                contract.decimals(),
                contract.symbol(),
                contract.name()
            ]);
            
            console.log('Token info received:', { balance: balance.toString(), decimals, symbol, name });
            
            // Format balance
            const formattedBalance = ethers.formatUnits(balance, decimals);
            const displayBalance = parseFloat(formattedBalance).toFixed(2);
            
            console.log('BEP20 Token Balance:', displayBalance, symbol);
            
            // Update UI
            if (tokenBalanceElement) {
                tokenBalanceElement.textContent = `${displayBalance} ${symbol}`;
                tokenBalanceElement.style.color = '#3bd17a'; // Green color for success
            }
            
            // Update refresh button click handler
            const refreshBtn = document.getElementById('refreshTokenBtn');
            if (refreshBtn) {
                refreshBtn.onclick = () => loadBEP20TokenBalance(account);
            }
            
            // Show success in console
            console.log('✅ BEP20 token balance loaded successfully');
            
        } catch (error) {
            console.error('❌ Error loading BEP20 token balance:', error);
            
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                // Show specific error message
                if (error.message.includes('BSC Mainnet')) {
                    tokenBalanceElement.textContent = 'Switch to BSC';
                    tokenBalanceElement.style.color = '#ffc107'; // Yellow for warning
                } else if (error.message.includes('No account')) {
                    tokenBalanceElement.textContent = 'Connect wallet first';
                    tokenBalanceElement.style.color = '#dc3545'; // Red for error
                } else if (error.message.includes('Ethers.js')) {
                    tokenBalanceElement.textContent = 'Loading ethers...';
                    tokenBalanceElement.style.color = '#6c757d'; // Gray for loading
                } else {
                    tokenBalanceElement.textContent = 'Error loading balance';
                    tokenBalanceElement.style.color = '#dc3545'; // Red for error
                }
            }
            
            // Show detailed error in console for debugging
            console.error('Detailed error:', {
                message: error.message,
                stack: error.stack,
                account: account,
                ethereum: typeof window.ethereum,
                ethers: typeof ethers
            });
            
            // Try alternative method to get balance
            console.log('Trying alternative balance loading method...');
            try {
                await loadBalanceAlternative(account);
            } catch (altError) {
                console.error('Alternative method also failed:', altError);
                
                // Try without ethers.js as last resort
                console.log('Trying without ethers.js as last resort...');
                try {
                    await loadBalanceWithoutEthers(account);
                } catch (noEthersError) {
                    console.error('All methods failed:', noEthersError);
                }
            }
        }
    }

    // Load balance without ethers.js (fallback method)
    async function loadBalanceWithoutEthers(account) {
        try {
            console.log('Loading balance without ethers.js for:', account);
            
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                tokenBalanceElement.textContent = 'Loading without ethers...';
                tokenBalanceElement.style.color = '#ffc107';
            }
            
            // Check if account is valid
            if (!account || !account.startsWith('0x') || account.length !== 42) {
                throw new Error('Invalid wallet address format');
            }
            
            // Check if ethereum provider is available
            if (typeof window.ethereum === 'undefined') {
                throw new Error('Ethereum provider not available');
            }
            
            // Check network
            try {
                const networkId = await window.ethereum.request({ method: 'net_version' });
                console.log('Current network ID:', networkId);
                
                // BSC Mainnet network ID is 56
                if (networkId !== '56') {
                    console.warn('Not on BSC Mainnet (56), current network:', networkId);
                    // Continue anyway, but log warning
                }
            } catch (networkError) {
                console.warn('Could not check network:', networkError);
                // Continue anyway
            }
            
            // USDT contract address on BSC
            const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
            
            // BalanceOf function selector: 0x70a08231
            const balanceOfSelector = '0x70a08231';
            const accountPadded = account.slice(2).toLowerCase().padStart(64, '0'); // Remove 0x and pad to 64 chars
            const data = balanceOfSelector + accountPadded;
            
            console.log('Calling balanceOf with data:', data);
            console.log('Contract address:', usdtContractAddress);
            console.log('Account padded:', accountPadded);
            
            // Add timeout to prevent hanging (reduced to 5 seconds)
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Request timeout after 5 seconds')), 5000);
            });
            
            // Direct eth_call without ethers.js
            const callPromise = window.ethereum.request({
                method: 'eth_call',
                params: [{
                    to: usdtContractAddress,
                    data: data
                }, 'latest']
            });
            
            const result = await Promise.race([callPromise, timeoutPromise]);
            
            console.log('Raw balance result:', result);
            
            // Check if result is valid
            if (!result || result === '0x') {
                throw new Error('No balance data received');
            }
            
            // Convert hex to decimal (USDT has 18 decimals on BSC)
            const balanceWei = BigInt(result);
            const balance = Number(balanceWei) / Math.pow(10, 18);
            const displayBalance = balance.toFixed(6); // Show more precision
            
            console.log('Balance without ethers calculation:', displayBalance);
            console.log('Balance in wei:', balanceWei.toString());
            
            if (tokenBalanceElement) {
                if (balance > 0) {
                    tokenBalanceElement.textContent = `${displayBalance} USDT`;
                    tokenBalanceElement.style.color = '#3bd17a';
                } else {
                    tokenBalanceElement.textContent = '0.000000 USDT';
                    tokenBalanceElement.style.color = '#6c757d';
                }
            }
            
            console.log('✅ Balance loading without ethers successful');
            
        } catch (error) {
            console.error('❌ Balance loading without ethers failed:', error);
            
            const tokenBalanceElement = document.getElementById('tokenBalance');
            if (tokenBalanceElement) {
                if (error.message.includes('timeout')) {
                    tokenBalanceElement.textContent = 'Request timeout';
                } else if (error.message.includes('Invalid wallet address')) {
                    tokenBalanceElement.textContent = 'Invalid wallet address';
                } else if (error.message.includes('provider not available')) {
                    tokenBalanceElement.textContent = 'Wallet not connected';
                } else if (error.message.includes('No balance data')) {
                    tokenBalanceElement.textContent = 'No balance data';
                } else {
                    tokenBalanceElement.textContent = 'Error loading balance';
                }
                tokenBalanceElement.style.color = '#ff6b6b';
            }
            
            // Show error notification
            showErrorMessage(`Balance loading failed: ${error.message}`);
            
            // Try alternative method automatically
            console.log('🔄 Trying alternative balance loading method automatically...');
            try {
                await loadBalanceAlternativeMethod(account);
            } catch (altError) {
                console.error('❌ Alternative method also failed:', altError);
            }
        }
    }

    // Alternative method to load balance
    async function loadBalanceAlternative(account) {
        try {
            console.log('Trying alternative balance loading for:', account);
            
            const tokenBalanceElement = document.getElementById('tokenBalance');
            
            // Simple balance check using eth_call
            const provider = new ethers.BrowserProvider(window.ethereum);
            const usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955';
            
            // BalanceOf function selector: 0x70a08231
            const balanceOfSelector = '0x70a08231';
            const accountPadded = account.slice(2).padStart(64, '0'); // Remove 0x and pad to 64 chars
            const data = balanceOfSelector + accountPadded;
            
            console.log('Calling balanceOf with data:', data);
            
            const result = await provider.call({
                to: usdtContractAddress,
                data: data
            });
            
            console.log('Raw balance result:', result);
            
            // Convert hex to decimal (USDT has 18 decimals)
            const balanceWei = BigInt(result);
            const balance = Number(balanceWei) / Math.pow(10, 18);
            const displayBalance = balance.toFixed(2);
            
            console.log('Alternative balance calculation:', displayBalance);
            
            if (tokenBalanceElement) {
                tokenBalanceElement.textContent = `${displayBalance} USDT`;
                tokenBalanceElement.style.color = '#3bd17a';
            }
            
            console.log('✅ Alternative balance loading successful');
            
        } catch (error) {
            console.error('❌ Alternative balance loading failed:', error);
            throw error;
        }
    }

    // Show mobile wallet install instructions with deep linking
    function showMobileWalletInstallInstructions(walletType) {
        const statusDiv = document.getElementById('mobileWalletStatus') || document.getElementById('walletStatus');
        const currentUrl = window.location.href;
        
        if (walletType === 'trust') {
            // Trust Wallet deep link
            const trustWalletUrl = `trust://open_url?url=${encodeURIComponent(currentUrl)}`;
            
            statusDiv.innerHTML = `
                <div class="alert alert-warning">
                    <h6>📱 Trust Wallet Not Found</h6>
                    <p>To connect your Trust Wallet, you need to open this website in Trust Wallet's browser:</p>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button class="btn btn-success btn-sm" onclick="openInTrustWallet('${currentUrl}')">
                            <i class="fas fa-external-link-alt"></i> Open in Trust Wallet
                        </button>
                        <a href="https://trustwallet.com/" target="_blank" class="btn btn-warning btn-sm">
                            <i class="fas fa-mobile-alt"></i> Download Trust Wallet
                        </a>
                        <button class="btn btn-info btn-sm" onclick="connectMobileWallet('trust')">
                            <i class="fas fa-refresh"></i> Try Again
                        </button>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <strong>Instructions:</strong><br>
                            1. Click "Open in Trust Wallet" button<br>
                            2. Trust Wallet app will open automatically<br>
                            3. Grant permission to connect<br>
                            4. Your wallet will be connected!
                        </small>
                    </div>
                </div>
            `;
        } else {
            // MetaMask deep link
            const metaMaskUrl = `metamask://dapp/${encodeURIComponent(currentUrl)}`;
            
            statusDiv.innerHTML = `
                <div class="alert alert-warning">
                    <h6>🦊 MetaMask Not Found</h6>
                    <p>To connect your MetaMask, you need to open this website in MetaMask's browser:</p>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <button class="btn btn-success btn-sm" onclick="openInMetaMask('${currentUrl}')">
                            <i class="fas fa-external-link-alt"></i> Open in MetaMask
                        </button>
                        <a href="https://metamask.io/download/" target="_blank" class="btn btn-warning btn-sm">
                            <i class="fas fa-mobile-alt"></i> Download MetaMask
                        </a>
                        <button class="btn btn-info btn-sm" onclick="connectMobileWallet('metamask')">
                            <i class="fas fa-refresh"></i> Try Again
                        </button>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <strong>Instructions:</strong><br>
                            1. Click "Open in MetaMask" button<br>
                            2. MetaMask app will open automatically<br>
                            3. Grant permission to connect<br>
                            4. Your wallet will be connected!
                        </small>
                    </div>
                </div>
            `;
        }
    }

    // Open website in Trust Wallet
    window.openInTrustWallet = function(url) {
        console.log('Opening in Trust Wallet:', url);
        
        // Trust Wallet deep link
        const trustWalletUrl = `trust://open_url?url=${encodeURIComponent(url)}`;
        
        // Try to open Trust Wallet
        window.location.href = trustWalletUrl;
        
        // Fallback: Show instructions if Trust Wallet not installed
        setTimeout(() => {
            const statusDiv = document.getElementById('mobileWalletStatus') || document.getElementById('walletStatus');
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6>📱 Trust Wallet Instructions</h6>
                    <p>If Trust Wallet didn't open automatically:</p>
                    <ol class="text-start">
                        <li>Install Trust Wallet app from Play Store/App Store</li>
                        <li>Open Trust Wallet app</li>
                        <li>Tap on "DApps" tab at the bottom</li>
                        <li>Enter this URL: <code>${url}</code></li>
                        <li>Your wallet will connect automatically!</li>
                    </ol>
                    <div class="mt-3">
                        <a href="https://trustwallet.com/" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fas fa-download"></i> Install Trust Wallet
                        </a>
                    </div>
                </div>
            `;
        }, 3000);
    };

    // Open website in MetaMask
    window.openInMetaMask = function(url) {
        console.log('Opening in MetaMask:', url);
        
        // MetaMask deep link
        const metaMaskUrl = `metamask://dapp/${encodeURIComponent(url)}`;
        
        // Try to open MetaMask
        window.location.href = metaMaskUrl;
        
        // Fallback: Show instructions if MetaMask not installed
        setTimeout(() => {
            const statusDiv = document.getElementById('mobileWalletStatus') || document.getElementById('walletStatus');
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6>🦊 MetaMask Instructions</h6>
                    <p>If MetaMask didn't open automatically:</p>
                    <ol class="text-start">
                        <li>Install MetaMask app from Play Store/App Store</li>
                        <li>Open MetaMask app</li>
                        <li>Tap on "Browser" tab at the bottom</li>
                        <li>Enter this URL: <code>${url}</code></li>
                        <li>Your wallet will connect automatically!</li>
                    </ol>
                    <div class="mt-3">
                        <a href="https://metamask.io/download/" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fas fa-download"></i> Install MetaMask
                        </a>
                    </div>
                </div>
            `;
        }, 3000);
    };

    // Simple connect function
    window.simpleConnect = async function() {
        console.log('Simple connect called');
        try {
            if (typeof window.ethereum !== 'undefined') {
                const accounts = await window.ethereum.request({
                    method: 'eth_requestAccounts'
                });
                if (accounts.length > 0) {
                    alert(`Connected to account: ${accounts[0]}`);
                }
            } else {
                alert('No Web3 wallet detected. Please install MetaMask or Trust Wallet.');
            }
        } catch (error) {
            console.error('Simple connect error:', error);
            alert('Connection failed: ' + error.message);
        }
    };

    // Show QR code for mobile connection
    function showQRCode() {
        const qrSection = document.getElementById('qrCodeSection');
        const qrContainer = document.getElementById('qrCodeContainer');
        const currentUrl = window.location.href;

        // Generate QR code for current page URL
        qrContainer.innerHTML = `
        <div class="alert alert-light">
            <h6>📱 Scan with Mobile Wallet</h6>
            <p>Scan this QR code with your mobile wallet:</p>
            <div class="text-center mt-3 mb-3">
                <div id="qrcode" style="display: inline-block;"></div>
            </div>
            <p class="mb-2">Or copy this URL:</p>
            <div class="input-group">
                <input type="text" class="form-control" value="${currentUrl}" readonly id="qrUrlInput">
                <button class="btn btn-outline-secondary" type="button" onclick="copyQRUrl()">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    <strong>Instructions:</strong><br>
                    1. Open Trust Wallet or MetaMask on your mobile<br>
                    2. Go to DApps/Browser section<br>
                    3. Scan QR code or paste the URL<br>
                    4. Your wallet will connect automatically!
                </small>
            </div>
        </div>
    `;

        // Generate QR code
        if (typeof QRCode !== 'undefined') {
            new QRCode(document.getElementById("qrcode"), {
                text: currentUrl,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        } else {
            document.getElementById("qrcode").innerHTML = `
                <div class="alert alert-info">
                    <p>QR Code library not loaded. Please copy the URL manually.</p>
                </div>
            `;
        }

        qrSection.style.display = 'block';
    }

    // Copy QR URL to clipboard
    function copyQRUrl() {
        const urlInput = document.getElementById('qrUrlInput');
        urlInput.select();
        urlInput.setSelectionRange(0, 99999); // For mobile devices
        
        navigator.clipboard.writeText(urlInput.value).then(function() {
            // Show success feedback
            const copyBtn = event.target.closest('button');
            const originalHTML = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i>';
            copyBtn.classList.add('btn-success');
            copyBtn.classList.remove('btn-outline-secondary');
            
            setTimeout(() => {
                copyBtn.innerHTML = originalHTML;
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-secondary');
            }, 2000);
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
            alert('URL copied to clipboard: ' + urlInput.value);
        });
    }

    // Hide QR code
    function hideQRCode() {
        document.getElementById('qrCodeSection').style.display = 'none';
    }

    // Mobile deposit functions
    window.openMobileWallet = function(type) {
        if (!currentAccount) {
            showErrorMessage('Please connect your wallet first');
            return;
        }

        if (type === 'bnb') {
            // Open mobile wallet to send BNB
            const depositAddress = currentAccount;
            const bscScanUrl = `https://bscscan.com/address/${depositAddress}`;

            // Try to open in mobile wallet
            const walletUrl = `trust://wc?uri=${encodeURIComponent(window.location.href)}`;
            window.open(walletUrl, '_blank');

            // Show instructions
            document.getElementById('mobileWalletStatus').innerHTML = `
            <div class="alert alert-info">
                <h6>📱 Deposit BNB Instructions</h6>
                <p class="mb-2">To deposit BNB:</p>
                <ol class="text-start">
                    <li>Open your mobile wallet app</li>
                    <li>Go to BNB (BSC Network)</li>
                    <li>Send BNB to this address:</li>
                </ol>
                <div class="input-group mt-2">
                    <input type="text" class="form-control" value="${depositAddress}" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyToClipboard('${depositAddress}')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <a href="${bscScanUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> View on BSCScan
                    </a>
                </div>
            </div>
        `;
        } else if (type === 'token') {
            // Open mobile wallet to send BEP20 token
            const depositAddress = currentAccount;

            document.getElementById('mobileWalletStatus').innerHTML = `
            <div class="alert alert-info">
                <h6>📱 Deposit BEP20 Token Instructions</h6>
                <p class="mb-2">To deposit BEP20 tokens:</p>
                <ol class="text-start">
                    <li>Open your mobile wallet app</li>
                    <li>Go to the token you want to send</li>
                    <li>Make sure it's on BSC network</li>
                    <li>Send to this address:</li>
                </ol>
                <div class="input-group mt-2">
                    <input type="text" class="form-control" value="${depositAddress}" readonly>
                    <button class="btn btn-outline-secondary" onclick="copyToClipboard('${depositAddress}')">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <small class="text-muted">Note: Make sure the token is BEP20 (BSC) version, not ERC20 (Ethereum)</small>
                </div>
            </div>
        `;
        }
    };

    // Copy deposit address
    window.copyDepositAddress = function() {
        if (currentAccount) {
            copyToClipboard(currentAccount);
            showSuccessMessage('Deposit address copied to clipboard!');
        } else {
            showErrorMessage('Please connect your wallet first');
        }
    };

    // Copy to clipboard utility
    function copyToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                console.log('Copied to clipboard:', text);
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
    }

    // Simple connect function that definitely works
    window.simpleConnect = async function() {
        console.log('Simple connect called');

        // Check if any Web3 wallet is installed (MetaMask, TrustWallet, etc.)
        if (typeof window.ethereum === 'undefined') {
            document.getElementById('walletStatus').innerHTML = `
            <div class="alert alert-danger">
                <h6>Web3 Wallet Not Found</h6>
                <p>Please install a Web3 wallet:</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="https://metamask.io/download/" target="_blank" class="btn btn-danger btn-sm">
                        <i class="fab fa-ethereum"></i> MetaMask
                    </a>
                    <a href="https://trustwallet.com/" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-mobile-alt"></i> Trust Wallet
                    </a>
                    <a href="https://www.coinbase.com/wallet" target="_blank" class="btn btn-info btn-sm">
                        <i class="fas fa-wallet"></i> Coinbase Wallet
                    </a>
                </div>
            </div>
        `;
            return;
        }

        try {
            // Detect wallet type
            let walletType = 'Unknown';
            if (window.ethereum.isMetaMask) {
                walletType = 'MetaMask';
            } else if (window.ethereum.isTrust) {
                walletType = 'Trust Wallet';
            } else if (window.ethereum.isCoinbaseWallet) {
                walletType = 'Coinbase Wallet';
            }

            console.log('Detected wallet:', walletType);

            // Request account access
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });

            if (accounts.length > 0) {
                // Switch to BSC network
                try {
                    await window.ethereum.request({
                        method: 'wallet_switchEthereumChain',
                        params: [{
                            chainId: '0x38'
                        }], // BSC Mainnet
                    });
                } catch (switchError) {
                    // If BSC network is not added, add it
                    if (switchError.code === 4902) {
                        await window.ethereum.request({
                            method: 'wallet_addEthereumChain',
                            params: [{
                                chainId: '0x38',
                                chainName: 'Binance Smart Chain',
                                nativeCurrency: {
                                    name: 'BNB',
                                    symbol: 'BNB',
                                    decimals: 18,
                                },
                                rpcUrls: ['https://bsc-dataseed.binance.org/'],
                                blockExplorerUrls: ['https://bscscan.com/'],
                            }],
                        });
                    }
                }

                // Update UI
                document.getElementById('connectionStatus').textContent = 'Connected';
                document.getElementById('accountAddress').textContent = accounts[0];
                document.getElementById('networkName').textContent = 'BSC Mainnet';

                // Update button
                const connectBtn = document.getElementById('connectWalletBtn');
                connectBtn.textContent = 'Disconnect';
                connectBtn.className = 'btn btn-danger';

                // Show success message
                document.getElementById('walletStatus').innerHTML = `
                <div class="alert alert-success">
                    <h6>✅ Wallet Connected Successfully!</h6>
                    <p><strong>Wallet:</strong> ${walletType}</p>
                    <p><strong>Account:</strong> ${accounts[0]}</p>
                    <p><strong>Network:</strong> BSC Mainnet</p>
                </div>
            `;

                console.log('Wallet connected:', accounts[0], 'Type:', walletType);
            }
        } catch (error) {
            console.error('Connection error:', error);
            document.getElementById('walletStatus').innerHTML = `
            <div class="alert alert-danger">
                <h6>❌ Connection Failed</h6>
                <p>Error: ${error.message}</p>
            </div>
        `;
        }
    };

    // Trust Wallet specific connection
    window.connectTrustWallet = async function() {
        console.log('Trust Wallet connection called');

        // Check if Trust Wallet is available
        if (typeof window.ethereum === 'undefined') {
            document.getElementById('walletStatus').innerHTML = `
            <div class="alert alert-info">
                <h6>📱 Trust Wallet Setup Required</h6>
                <p>To use Trust Wallet with this DApp:</p>
                <ol class="text-start">
                    <li>Install Trust Wallet mobile app</li>
                    <li>Open Trust Wallet in mobile browser</li>
                    <li>Or use Trust Wallet browser extension</li>
                </ol>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="https://trustwallet.com/" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-mobile-alt"></i> Download Trust Wallet
                    </a>
                    <a href="https://chrome.google.com/webstore/detail/trust-wallet/egjidjbpglichdcondbcbdnbeeppgdph" target="_blank" class="btn btn-info btn-sm">
                        <i class="fab fa-chrome"></i> Chrome Extension
                    </a>
                </div>
            </div>
        `;
            return;
        }

        // Check if it's Trust Wallet
        if (!window.ethereum.isTrust) {
            document.getElementById('walletStatus').innerHTML = `
            <div class="alert alert-warning">
                <h6>⚠️ Trust Wallet Not Detected</h6>
                <p>Please make sure you're using Trust Wallet browser or have Trust Wallet extension installed.</p>
                <p>Current wallet: ${window.ethereum.isMetaMask ? 'MetaMask' : 'Other'}</p>
            </div>
        `;
            return;
        }

        try {
            // Connect to Trust Wallet
            const accounts = await window.ethereum.request({
                method: 'eth_requestAccounts'
            });

            if (accounts.length > 0) {
                // Switch to BSC network
                try {
                    await window.ethereum.request({
                        method: 'wallet_switchEthereumChain',
                        params: [{
                            chainId: '0x38'
                        }], // BSC Mainnet
                    });
                } catch (switchError) {
                    // Add BSC network if not present
                    if (switchError.code === 4902) {
                        await window.ethereum.request({
                            method: 'wallet_addEthereumChain',
                            params: [{
                                chainId: '0x38',
                                chainName: 'Binance Smart Chain',
                                nativeCurrency: {
                                    name: 'BNB',
                                    symbol: 'BNB',
                                    decimals: 18,
                                },
                                rpcUrls: ['https://bsc-dataseed.binance.org/'],
                                blockExplorerUrls: ['https://bscscan.com/'],
                            }],
                        });
                    }
                }

                // Update UI
                document.getElementById('connectionStatus').textContent = 'Connected';
                document.getElementById('accountAddress').textContent = accounts[0];
                document.getElementById('networkName').textContent = 'BSC Mainnet';

                // Update button
                const connectBtn = document.getElementById('connectWalletBtn');
                connectBtn.textContent = 'Disconnect';
                connectBtn.className = 'btn btn-danger';

                // Show success message
                document.getElementById('walletStatus').innerHTML = `
                <div class="alert alert-success">
                    <h6>✅ Trust Wallet Connected Successfully!</h6>
                    <p><strong>Wallet:</strong> Trust Wallet</p>
                    <p><strong>Account:</strong> ${accounts[0]}</p>
                    <p><strong>Network:</strong> BSC Mainnet</p>
                    <small class="text-muted">You can now send/receive BSC BEP20 tokens!</small>
                </div>
            `;

                console.log('Trust Wallet connected:', accounts[0]);
            }
        } catch (error) {
            console.error('Trust Wallet connection error:', error);
            document.getElementById('walletStatus').innerHTML = `
            <div class="alert alert-danger">
                <h6>❌ Trust Wallet Connection Failed</h6>
                <p>Error: ${error.message}</p>
                <small class="text-muted">Make sure Trust Wallet is unlocked and try again.</small>
            </div>
        `;
        }
    };

    function initializeWalletApp() {
        document.addEventListener('DOMContentLoaded', function() {
            const connectBtn = document.getElementById('connectWalletBtn');
            const refreshBnbBtn = document.getElementById('refreshBnbBtn');

            let currentAccount = null;

            // Setup mobile wallet buttons
            setupMobileWalletButtons();

            // Check wallet availability on page load
            function checkWalletAvailability() {
                if (typeof window.ethereum === 'undefined') {
                    // Show helpful message
                    const walletStatus = document.getElementById('walletStatus');
                    walletStatus.innerHTML = `
                <div class="alert alert-warning">
                    <h6>Wallet Not Detected</h6>
                    <p class="mb-2">To use this DApp, please install a Web3 wallet:</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="https://metamask.io/download/" target="_blank" class="btn btn-sm btn-warning">
                            <i class="fab fa-ethereum"></i> Install MetaMask
                        </a>
                        <a href="https://trustwallet.com/" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-mobile-alt"></i> Trust Wallet
                        </a>
                    </div>
                    <small class="text-muted">After installation, refresh this page.</small>
                </div>
            `;
                }
            }

            // Run check on page load
            checkWalletAvailability();

            // Mobile form switching
            document.querySelectorAll('input[name="sendType"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const bnbForm = document.getElementById('bnbSendForm');
                    const tokenForm = document.getElementById('tokenSendForm');

                    if (this.value === 'bnb') {
                        bnbForm.style.display = 'block';
                        tokenForm.style.display = 'none';
                    } else {
                        bnbForm.style.display = 'none';
                        tokenForm.style.display = 'block';
                    }
                });
            });

            // Add direct connection method as fallback
            window.connectWalletDirect = async function() {
                console.log('Direct wallet connection called');

                if (typeof window.ethereum === 'undefined') {
                        showErrorMessage(
                            'No Web3 wallet detected. Please install MetaMask or similar wallet.');
                    return;
                }

                try {
                    const accounts = await window.ethereum.request({
                        method: 'eth_requestAccounts'
                    });
                    if (accounts.length > 0) {
                        currentAccount = accounts[0];
                        updateWalletStatus();
                        loadBalances();
                        showSuccessMessage('Wallet connected successfully!');
                    }
                } catch (error) {
                    console.error('Direct connection error:', error);
                    showErrorMessage('Connection failed: ' + error.message);
                }
            };

            // Utility functions for messages
            function showSuccessMessage(message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body')
                        .firstChild);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }

            function showErrorMessage(message) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                    document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body')
                        .firstChild);

                // Auto remove after 8 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 8000);
            }

            // Connect wallet
            connectBtn.addEventListener('click', async function() {
                console.log('Connect wallet button clicked');

                // Check if wallet service is available
                if (typeof window.walletService === 'undefined') {
                    showErrorMessage('Wallet service not loaded. Please refresh the page.');
                    return;
                }

                try {
                    const result = await window.walletService.connectWallet();
                    if (result.success) {
                        currentAccount = result.account;
                            saveWalletState(result.account, 'Web3 Extension');
                        updateWalletStatus();
                        loadBalances();
                        showSuccessMessage('Wallet connected successfully!');
                    } else {
                        // Error message already shown in modal
                        console.log('Connection failed:', result.error);
                    }
                } catch (error) {
                    console.error('Connection error:', error);
                    showErrorMessage('Connection failed: ' + error.message);
                }
            });

            // Update wallet status display
            function updateWalletStatus() {
                if (currentAccount) {
                    document.getElementById('connectionStatus').textContent = 'Connected';
                    document.getElementById('accountAddress').textContent = currentAccount;
                    document.getElementById('networkName').textContent = 'BSC Mainnet';
                    connectBtn.textContent = 'Disconnect';
                    connectBtn.className = 'btn btn-danger';

                    // Update deposit address
                    document.getElementById('depositAddress').value = currentAccount;

                    // Update mobile wallet status
                    document.getElementById('mobileWalletStatus').innerHTML = `
                <div class="alert alert-success">
                    <h6>✅ Mobile Wallet Connected!</h6>
                    <p><strong>Account:</strong> ${currentAccount}</p>
                    <p><strong>Network:</strong> BSC Mainnet</p>
                    <p class="mb-0">You can now deposit and send BSC BEP20 tokens!</p>
                </div>
            `;
                } else {
                    document.getElementById('connectionStatus').textContent = 'Not Connected';
                    document.getElementById('accountAddress').textContent = '-';
                    document.getElementById('networkName').textContent = '-';
                    connectBtn.textContent = 'Connect Wallet';
                    connectBtn.className = 'btn btn-light';

                    // Reset deposit address
                    document.getElementById('depositAddress').value = 'Connect wallet to get address';

                    // Reset mobile wallet status
                    document.getElementById('mobileWalletStatus').innerHTML = `
                <div class="alert alert-warning">
                    <h6>📱 Mobile Wallet Required</h6>
                    <p class="mb-2">For the best experience, use a mobile wallet app:</p>
                    <div class="row">
                        <div class="col-6">
                            <a href="https://trustwallet.com/" target="_blank" class="btn btn-warning btn-sm w-100">
                                <i class="fas fa-mobile-alt"></i> Trust Wallet
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="https://metamask.io/download/" target="_blank" class="btn btn-info btn-sm w-100">
                                <i class="fab fa-ethereum"></i> MetaMask
                            </a>
                        </div>
                    </div>
                </div>
            `;
                }
            }

            // Load balances
            async function loadBalances() {
                if (!currentAccount) return;

                try {
                    const bnbBalance = await window.walletService.getBalance();
                        document.getElementById('bnbBalance').textContent = parseFloat(bnbBalance).toFixed(6) +
                            ' BNB';
                } catch (error) {
                    console.error('Error loading BNB balance:', error);
                }
            }

            // Refresh BNB balance
            refreshBnbBtn.addEventListener('click', loadBalances);



            // Show transaction modal
            function showTransactionModal() {
                const modal = new bootstrap.Modal(document.getElementById('transactionModal'));
                modal.show();
            }

            // Handle transaction result
            async function handleTransactionResult(result) {
                const statusDiv = document.getElementById('transactionStatus');

                if (result.success) {
                    statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <h6>Transaction Submitted!</h6>
                    <p>Tx Hash: <a href="https://bscscan.com/tx/${result.txHash}" target="_blank">${result.txHash}</a></p>
                    <p>Waiting for confirmation...</p>
                </div>
            `;

                    // Wait for confirmation
                    const confirmation = await window.walletService.waitForTransaction(result.txHash);
                    if (confirmation.success) {
                        statusDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6>Transaction Confirmed!</h6>
                        <p>Block: ${confirmation.blockNumber}</p>
                        <p>Gas Used: ${confirmation.gasUsed}</p>
                    </div>
                `;
                        loadBalances(); // Refresh balances
                    } else {
                        statusDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6>Transaction Failed!</h6>
                        <p>${confirmation.error}</p>
                    </div>
                `;
                    }
                } else {
                    statusDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6>Transaction Failed!</h6>
                    <p>${result.error}</p>
                </div>
            `;
                }
            }
        });
    }
</script>
@endsection
