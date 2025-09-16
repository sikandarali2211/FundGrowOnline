@extends('layouts.user')

@section('content')
    <style>
        :root {
            --primary-green: #22c55e;
            --secondary-green: #16a34a;
            --accent-blue: #3b82f6;
            --dark-blue: #0f172a;
            --darker-blue: #0a1120;
            --card-bg: rgba(255, 255, 255, 0.08);
            --card-border: rgba(255, 255, 255, 0.15);
            --white: #ffffff;
        }

        body {
            background: linear-gradient(135deg, var(--darker-blue), var(--dark-blue));
            color: var(--white);
            font-family: 'Segoe UI', sans-serif;
        }

        .plans-container {
            min-height: 100vh;
            padding: 3rem 1rem;
        }

        /* Header */
        .plans-header {
            text-align: center;
            margin-bottom: 3rem;
            margin-top: 4rem;
        }

        .plans-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--white);
            background: linear-gradient(90deg, var(--accent-blue), var(--primary-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .plans-header p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            margin-top: 1rem;
        }

        .title-divider {
            height: 3px;
            width: 200px;
            margin: 1.5rem auto 0;
            background: linear-gradient(90deg, var(--accent-blue), var(--primary-green));
            border-radius: 3px;
        }

        /* Plan Cards */
        .plan-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 1rem;
            padding: 2rem;
            position: relative;
            transition: all 0.3s ease;
            backdrop-filter: blur(12px);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        }

        .plan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7), 0 0 25px rgba(59, 130, 246, 0.4);
            border-color: var(--accent-blue);
        }

        .plan-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 2px;
            background: linear-gradient(135deg, var(--accent-blue), var(--primary-green));
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0.5;
        }

        .plan-name {
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
            color: var(--white);
        }

        .plan-amount {
            font-size: 2.2rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--accent-blue);
            text-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }

        .plan-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .plan-detail {
            padding: 1rem;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--card-border);
            text-align: center;
        }

        .plan-detail-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.3rem;
        }

        .plan-detail-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-green);
        }

        .plan-total-return {
            text-align: center;
            padding: 1.2rem;
            border-radius: 12px;
            border: 1px solid var(--accent-blue);
            background: rgba(59, 130, 246, 0.15);
            margin-bottom: 1.5rem;
        }

        .plan-total-return-label {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 0.3rem;
        }

        .plan-total-return-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--accent-blue);
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        .plan-button {
            width: 100%;
            padding: 1rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            border: none;
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: var(--white);
            text-align: center;
            text-transform: uppercase;
            transition: 0.3s ease;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }

        .plan-button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.6);
        }

        /* Badges */
        .plan-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, var(--accent-blue), var(--primary-green));
            color: var(--white);
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        /* Grid */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1300px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .plan-details {
                grid-template-columns: 1fr;
            }

            .plans-header h1 {
                font-size: 2rem;
            }
        }

        .plan-button {
            position: relative;
            z-index: 2;
        }

        .plan-card::before {
            z-index: 1;
        }
    </style>
    <div class="main-panel">
        <div class="plans-container" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
            <div class="container">
                <div class="plans-header">
                    <h1>Investment Plans</h1>
                    <p>Choose your investment plan and start growing your wealth with our proven 3.6x return strategy</p>
                    <div class="title-divider"></div>
                </div>

                <div class="plans-grid">
                    <!-- Grower Plan -->
                    <div class="plan-card grower-plan">
                        <div class="plan-name">Grower</div>
                        <div class="plan-amount">$10</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">0%</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">0%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$0</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'grower', 'amount' => 10]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Builder Plan -->
                    <div class="plan-card builder-plan">
                        <div class="plan-badge">Popular</div>
                        <div class="plan-name">Builder</div>
                        <div class="plan-amount">$20</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$72</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'builder', 'amount' => 20]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Bloom Plan -->
                    <div class="plan-card premium-plan">
                        <div class="plan-name">Bloom</div>
                        <div class="plan-amount">$40</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$144</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'bloom', 'amount' => 40]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Multiplier Plan -->
                    <div class="plan-card premium-plan">
                        <div class="plan-name">Multiplier</div>
                        <div class="plan-amount">$60</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$216</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'multiplier', 'amount' => 60]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Accelerator Plan -->
                    <div class="plan-card premium-plan">
                        <div class="plan-name">Accelerator</div>
                        <div class="plan-amount">$100</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$360</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'accelerator', 'amount' => 100]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Contributor Plan -->
                    <div class="plan-card premium-plan">
                        <div class="plan-name">Contributor</div>
                        <div class="plan-amount">$200</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$720</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'contributor', 'amount' => 200]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Supporter Plan -->
                    <div class="plan-card premium-plan">
                        <div class="plan-name">Supporter</div>
                        <div class="plan-amount">$400</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$1,440</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'supporter', 'amount' => 400]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Catalyst Plan -->
                    <div class="plan-card premium-plan">
                        <div class="plan-name">Catalyst</div>
                        <div class="plan-amount">$600</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$2,160</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'catalyst', 'amount' => 600]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Champion Plan -->
                    <div class="plan-card elite-plan">
                        <div class="plan-badge popular-badge">Elite</div>
                        <div class="plan-name">Champion</div>
                        <div class="plan-amount">$1,000</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$3,600</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'champion', 'amount' => 1000]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Harvester Plan -->
                    <div class="plan-card elite-plan">
                        <div class="plan-name">Harvester</div>
                        <div class="plan-amount">$2,000</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$7,200</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'harvester', 'amount' => 2000]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Pioneer Plan -->
                    <div class="plan-card elite-plan">
                        <div class="plan-name">Pioneer</div>
                        <div class="plan-amount">$5,000</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$18,000</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'pioneer', 'amount' => 5000]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Visionary Plan -->
                    <div class="plan-card legendary-plan">
                        <div class="plan-badge">Legendary</div>
                        <div class="plan-name">Visionary</div>
                        <div class="plan-amount">$10,000</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$36,000</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'visionary', 'amount' => 10000]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Leader Plan -->
                    <div class="plan-card legendary-plan">
                        <div class="plan-name">Leader</div>
                        <div class="plan-amount">$20,000</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$72,000</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'leader', 'amount' => 20000]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Innovator Plan -->
                    <div class="plan-card legendary-plan">
                        <div class="plan-name">Innovator</div>
                        <div class="plan-amount">$50,000</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$180,000</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'innovator', 'amount' => 50000]) }}"
                            class="plan-button">Select Plan</a>
                    </div>

                    <!-- Master Plan -->
                    <div class="plan-card legendary-plan">
                        <div class="plan-badge">Master</div>
                        <div class="plan-name">Master</div>
                        <div class="plan-amount">$100,000</div>
                        <div class="plan-details">
                            <div class="plan-detail">
                                <div class="plan-detail-label">Return %</div>
                                <div class="plan-detail-value">3.6x</div>
                            </div>
                            <div class="plan-detail">
                                <div class="plan-detail-label">Total Return %</div>
                                <div class="plan-detail-value">360%</div>
                            </div>
                        </div>
                        <div class="plan-total-return">
                            <div class="plan-total-return-label">Total Return</div>
                            <div class="plan-total-return-value">$360,000</div>
                        </div>
                        <a href="{{ route('user.plan-selections.create', ['plan' => 'master', 'amount' => 100000]) }}"
                            class="plan-button">Select Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Plan selection is now handled by direct links to payment creation
            console.log('Investment plans loaded successfully');
        });
    </script>
@endsection
