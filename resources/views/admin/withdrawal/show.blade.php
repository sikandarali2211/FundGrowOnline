@extends('layouts.admin')

@section('content')
<div class="content-wrapper" style="background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); padding-top:80px; min-height:calc(100vh - 80px); color:#fff;">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <h3 class="page-title" style="color:#3bd17a;">Withdrawal #{{ $withdrawal->id }}</h3>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(145deg, #072d42, #22384e); border:none; border-radius:12px; color:#fff;">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-2">User</h6>
                        <p class="mb-1"><strong>{{ $withdrawal->user->name ?? 'N/A' }}</strong></p>
                        <p class="text-muted">{{ $withdrawal->user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-2">Details</h6>
                        <p class="mb-1">Amount: <strong>${{ number_format($withdrawal->amount, 2) }}</strong></p>
                        <p class="mb-1">Wallet: <code>{{ $withdrawal->wallet_address }}</code></p>
                        <p class="mb-1">Wallet Type: {{ ucfirst($withdrawal->wallet_type) }}</p>
                        <p class="mb-1">Source: {{ str_replace('_',' ', ucfirst($withdrawal->withdrawal_source ?? 'pool_commission')) }}</p>
                        <p class="mb-1">Status: <span class="badge badge-{{ $withdrawal->status_badge }}">{{ $withdrawal->status_text }}</span></p>
                        @if($withdrawal->transaction_hash)
                        <p class="mb-0">Tx Hash: <code>{{ $withdrawal->transaction_hash }}</code></p>
                        @endif
                    </div>
                </div>

                <hr style="border-color: rgba(255,255,255,.1)">

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


