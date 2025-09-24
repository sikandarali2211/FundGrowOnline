@extends('layouts.user')

@section('content')
@php
$planOrder = [
['name' => 'grower', 'label' => 'Grower', 'amount' => 1, 'return' => 0, 'duration' => 30],
[
'name' => 'builder',
'label' => 'Builder',
'amount' => 20,
'return' => 260,
'duration' => 30,
'badge' => 'Popular',
],
['name' => 'bloom', 'label' => 'Bloom', 'amount' => 40, 'return' => 260, 'duration' => 30],
['name' => 'multiplier', 'label' => 'Multiplier', 'amount' => 60, 'return' => 260, 'duration' => 30],
['name' => 'accelerator', 'label' => 'Accelerator', 'amount' => 100, 'return' => 260, 'duration' => 30],
['name' => 'contributor', 'label' => 'Contributor', 'amount' => 200, 'return' => 260, 'duration' => 30],
['name' => 'supporter', 'label' => 'Supporter', 'amount' => 400, 'return' => 260, 'duration' => 30],
['name' => 'catalyst', 'label' => 'Catalyst', 'amount' => 600, 'return' => 260, 'duration' => 30],
[
'name' => 'champion',
'label' => 'Champion',
'amount' => 1000,
'return' => 260,
'duration' => 30,
'badge' => 'Elite',
],
['name' => 'harvester', 'label' => 'Harvester', 'amount' => 2000, 'return' => 260, 'duration' => 30],
['name' => 'pioneer', 'label' => 'Pioneer', 'amount' => 5000, 'return' => 260, 'duration' => 30],
[
'name' => 'visionary',
'label' => 'Visionary',
'amount' => 10000,
'return' => 260,
'duration' => 30,
'badge' => 'Legendary',
],
['name' => 'leader', 'label' => 'Leader', 'amount' => 20000, 'return' => 260, 'duration' => 30],
['name' => 'innovator', 'label' => 'Innovator', 'amount' => 50000, 'return' => 260, 'duration' => 30],
[
'name' => 'master',
'label' => 'Master',
'amount' => 100000,
'return' => 260,
'duration' => 30,
'badge' => 'Master',
],
];

$userPlans = \App\Models\PlanSelection::where('user_id', auth()->id())->get();
$approvedPlans = $userPlans->where('status', 'approved')->pluck('plan_name')->toArray();
$pendingPlans = $userPlans->where('status', 'pending')->pluck('plan_name')->toArray();

$lastApprovedIndex = -1;
foreach ($planOrder as $i => $p) {
if (in_array($p['name'], $approvedPlans)) {
$lastApprovedIndex = $i;
}
}
@endphp

<style>
    body {
        background: #0f2027;
        font-family: 'Poppins', sans-serif;
        color: #e0e6ed;
    }

    .plans-header {
        text-align: center;
        margin: 40px 0;
    }

    .plans-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #29ff9a;
        text-shadow: 0 0 15px rgba(41, 255, 154, 0.6);
    }

    .plans-header p {
        color: #9ca3af;
        margin-top: 10px;
    }

    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 25px;
    }

    .plan-card {
        background: linear-gradient(180deg, rgba(20, 33, 61, 0.9), rgba(10, 20, 40, 0.95));
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
        position: relative;
        transition: transform 0.2s;
    }

    .plan-card:hover {
        transform: translateY(-6px);
    }

    .plan-amount {
        font-size: 1.8rem;
        font-weight: 700;
        color: #29ff9a;
    }

    .total-return {
        margin-top: 12px;
        background: rgba(0, 153, 255, 0.15);
        border: 1px solid #1e90ff;
        padding: 10px;
        border-radius: 12px;
        color: #1e90ff;
        font-weight: 600;
    }

    /* Default button */
    .btn-plan {
        border: none;
        font-weight: 600;
        padding: 10px;
        border-radius: 10px;
        width: 100%;
        margin-top: 15px;
        transition: all 0.3s;
    }

    /* Purchased (green) */
    .btn-purchased {
        background: linear-gradient(135deg, #29ff9a, #00cc66);
        color: #fff;
        box-shadow: 0 0 15px rgba(41, 255, 154, 0.8);
    }

    /* Pending (yellow) */
    .btn-pending {
        background: linear-gradient(135deg, #ffcc00, #e6b800);
        color: #fff;
        box-shadow: 0 0 15px rgba(255, 204, 0, 0.8);
    }

    /* Locked (red) */
    .btn-locked {
        background: linear-gradient(135deg, #ff4444, #cc0000);
        color: #fff;
        box-shadow: 0 0 15px rgba(255, 68, 68, 0.8);
    }

    /* Available (default green) */
    .btn-available {
        background: linear-gradient(135deg, #29ff9a, #00cc66);
        color: #fff;
    }

    /* Badge */
    .badge-plan {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #3bd17a;
        color: #fff;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 8px;
        font-weight: 600;
    }
</style>

<div class="main-panel" style=" background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
    <div class="container py-5">
        <div class="plans-header">
            <h1>Investment Plans</h1>
            <p>Choose your plan and unlock step by step after admin approval</p>
        </div>

        <div class="plans-grid">
            @foreach ($planOrder as $index => $plan)
            <div class="plan-card">
                @if (isset($plan['badge']))
                <div class="badge-plan">{{ $plan['badge'] }}</div>
                @endif

                <h5>{{ $plan['label'] }}</h5>
                <div class="plan-amount">${{ number_format($plan['amount']) }}</div>

                <div class="total-return">
                    Total Return: ${{ $plan['return'] > 0 ? $plan['amount'] * 3.6 : 0 }}
                </div>

                @php
                $btnText = 'Select Plan';
                $btnClass = 'btn-available';
                $disabled = false;

                if (in_array($plan['name'], $approvedPlans)) {
                $btnText = 'Purchased';
                $btnClass = 'btn-purchased';
                $disabled = true;
                } elseif (in_array($plan['name'], $pendingPlans)) {
                $btnText = 'Pending...';
                $btnClass = 'btn-pending';
                $disabled = true;
                } elseif ($index > $lastApprovedIndex + 1) {
                $btnText = 'Locked';
                $btnClass = 'btn-locked';
                $disabled = true;
                }
                @endphp

                <form method="GET" action="{{ route('user.plan-selections.create') }}">
                    <input type="hidden" name="plan" value="{{ $plan['name'] }}">
                    <input type="hidden" name="amount" value="{{ $plan['amount'] }}">
                    <input type="hidden" name="return" value="{{ $plan['return'] }}">
                    <input type="hidden" name="duration" value="{{ $plan['duration'] }}">
                    <button type="submit" class="btn-plan {{ $btnClass }}" {{ $disabled ? 'disabled' : '' }}>
                        {{ $btnText }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection