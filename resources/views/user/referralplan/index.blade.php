@extends('layouts.user')

@section('content')
<style>
    :root {
        --card-bg: rgba(255, 255, 255, 0.08);
        --card-border: rgba(255, 255, 255, 0.15);
        --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        --primary: #38bdf8;
        --secondary: #6366f1;
        --accent: #10b981;
    }

    body {
        background: linear-gradient(135deg, #0d1b2a, #1b263b, #243b55);
        color: #e2e8f0;
        font-family: 'Segoe UI', sans-serif;
    }

    .referral-plan-container {
        padding: 2rem 1rem;
    }

    .plan-header {
        text-align: center;
        margin-bottom: 3rem;
        margin-top: 4rem;
    }

    .plan-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #29ff9a;
        text-shadow: 0 0 15px rgba(41, 255, 154, 0.6);
    }

    .plan-header .subtitle {
        color: #94a3b8;
        font-size: 1.1rem;
    }

    .plan-description {
        background: linear-gradient(145deg, #072d42, #22384e);
        ;
        border: 1px solid var(--card-border);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 3rem;
        backdrop-filter: blur(12px);
        box-shadow: var(--card-shadow);
    }

    .plan-description p {
        color: #cbd5e1;
        font-size: 1rem;
        line-height: 1.6;
    }

    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
    }

    .plan-card {
        background: linear-gradient(180deg, rgba(20, 33, 61, 0.9), rgba(10, 20, 40, 0.95));
        border: 1px solid var(--card-border);
        border-radius: 20px;
        padding: 1.5rem;
        min-height: 280px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        backdrop-filter: blur(12px);
        position: relative;
    }

    .plan-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.7);
        border-color: var(--primary);
    }

    .plan-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        border-radius: 20px 20px 0 0;
    }

    .plan-name {
        font-size: 1.2rem;
        font-weight: 800;
        color: #f1f5f9;
        text-align: center;
        margin-bottom: 1rem;
    }

    .plan-entry,
    .plan-return {
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        margin-bottom: 0.8rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .plan-entry-label,
    .plan-return-label {
        font-size: 0.85rem;
        color: #94a3b8;
    }

    .plan-entry-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary);
    }

    .plan-return-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--accent);
    }

    .plan-total {
        text-align: center;
        padding: 1rem;
        border-radius: 15px;
        background: linear-gradient(135deg, #2563eb, #1e40af);
        margin-top: auto;
    }

    .plan-total-label {
        color: #f1f5f9;
        font-size: 0.85rem;
    }

    .plan-total-value {
        font-size: 1.4rem;
        font-weight: 900;
        color: white;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
    }

    .plan-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.3rem 0.7rem;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 20px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    }

    .game-changer {
        border: 1px solid rgba(239, 68, 68, 0.5);
    }

    .game-changer .plan-name {
        color: #00ff7f;
    }

    .game-changer .plan-total {
        background: linear-gradient(135deg, #50bb09, #6de229);
    }

    .special-badge {
        background: linear-gradient(135deg, #76ee31, #3eb806);
    }

    .total-earning-section {
        background: linear-gradient(145deg, #072d42, #22384e);
        border: 1px solid var(--card-border);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        margin-top: 3rem;
        backdrop-filter: blur(12px);
        box-shadow: var(--card-shadow);
    }

    .total-earning-title {
        font-size: 1.6rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .total-earning-amount {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--primary);
        text-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
        margin: 1rem 0;
    }

    .total-earning-subtitle {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .plan-header h1 {
            font-size: 2rem;
        }

        .plan-card {
            padding: 1.2rem;
        }

        .plan-name {
            font-size: 1rem;
        }

        .plan-total-value {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 480px) {
        .plans-grid {
            grid-template-columns: 1fr;
        }

        .plan-header h1 {
            font-size: 1.6rem;
        }

        .plan-description {
            padding: 1.2rem;
        }
    }
</style>

<div class="main-panel" style=" background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
    <div class="referral-plan-container">
        <div class="container-fluid">
            <div class="plan-header">
                <h1>OUR REFERRAL PLAN</h1>
                <div class="subtitle">GROW TOGETHER, EARN TOGETHER</div>
            </div>

            <div class="plan-description">
                <p>At Fund Grow Online, we believe in rewarding the power of community. Our referral system ensures that
                    as you help others join and grow, you also benefit from attractive bonuses.</p>
            </div>

            <div class="plans-grid">
                <!-- Grower -->
                <div class="plan-card">
                    <div class="plan-name">GROWER</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$10</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">100%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$10</div>
                    </div>
                </div>

                <!-- Builder -->
                <div class="plan-card">
                    <div class="plan-badge">Popular</div>
                    <div class="plan-name">BUILDER</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$20</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$6</div>
                    </div>
                </div>

                <!-- Bloom -->
                <div class="plan-card">
                    <div class="plan-name">BLOOM</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$40</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$12</div>
                    </div>
                </div>

                <!-- Multiplier -->
                <div class="plan-card">
                    <div class="plan-name">MULTIPLIER</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$60</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$18</div>
                    </div>
                </div>

                <!-- Accelerator -->
                <div class="plan-card">
                    <div class="plan-name">ACCELERATOR</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$100</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$30</div>
                    </div>
                </div>

                <!-- Contributor -->
                <div class="plan-card">
                    <div class="plan-name">CONTRIBUTOR</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$200</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$60</div>
                    </div>
                </div>

                <!-- Supporter -->
                <div class="plan-card">
                    <div class="plan-name">SUPPORTER</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$400</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$120</div>
                    </div>
                </div>

                <!-- Catalyst -->
                <div class="plan-card">
                    <div class="plan-name">CATALYST</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$600</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$180</div>
                    </div>
                </div>

                <!-- Champion -->
                <div class="plan-card">
                    <div class="plan-name">CHAMPION</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$1,000</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$300</div>
                    </div>
                </div>

                <!-- Harvester -->
                <div class="plan-card">
                    <div class="plan-name">HARVESTER</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$2,000</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$600</div>
                    </div>
                </div>

                <!-- Pioneer -->
                <div class="plan-card">
                    <div class="plan-name">PIONEER</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$5,000</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$1,500</div>
                    </div>
                </div>

                <!-- Visionary -->
                <div class="plan-card">
                    <div class="plan-name">VISIONARY</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$10,000</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$3,000</div>
                    </div>
                </div>

                <!-- Leader -->
                <div class="plan-card">
                    <div class="plan-name">LEADER</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$20,000</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$6,000</div>
                    </div>
                </div>

                <!-- Innovator -->
                <div class="plan-card">
                    <div class="plan-name">INNOVATOR</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$50,000</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$15,000</div>
                    </div>
                </div>

                <!-- Game Changer -->
                <div class="plan-card game-changer">
                    <div class="plan-badge special-badge">GAME CHANGER</div>
                    <div class="plan-name">GAME CHANGER</div>
                    <div class="plan-entry"><span class="plan-entry-label">POOLS ENTRY</span><span
                            class="plan-entry-value">$100,000</span></div>
                    <div class="plan-return"><span class="plan-return-label">RETURN %</span><span
                            class="plan-return-value">30%</span></div>
                    <div class="plan-total">
                        <div class="plan-total-label">TOTAL RETURN</div>
                        <div class="plan-total-value">$30,000</div>
                    </div>
                </div>
            </div>

            <div class="total-earning-section">
                <div class="total-earning-title">TOTAL REFERRAL EARNING</div>
                <div class="total-earning-amount">$56,836</div>
                <div class="total-earning-subtitle">Maximum potential earnings from all referral plans</div>
            </div>
        </div>
    </div>
</div>
@endsection