@extends('layouts.user')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Withdrawal</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Withdrawal Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Request Withdrawal</h5>
                </div>
                <div class="card-body">
                    <!-- Pool Commission Balance -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-wallet"></i> Available Balance</h6>
                        <h4 class="mb-0">${{ number_format($poolCommission, 2) }}</h4>
                        <small>Pool Commission Balance</small>
                    </div>

                    @if($poolCommission > 0)
                    <form id="withdrawalForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">Amount (USDT)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" 
                                           min="1" max="{{ $poolCommission }}" step="0.01" required>
                                    <small class="text-muted">Minimum: $1.00, Maximum: ${{ number_format($poolCommission, 2) }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="wallet_type">Wallet Type</label>
                                    <select class="form-control" id="wallet_type" name="wallet_type" required>
                                        <option value="">Select Wallet Type</option>
                                        <option value="trust">Trust Wallet</option>
                                        <option value="metamask">MetaMask</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="wallet_address">Wallet Address</label>
                            <input type="text" class="form-control" id="wallet_address" name="wallet_address" 
                                   placeholder="0x..." minlength="42" maxlength="42" required>
                            <small class="text-muted">Enter your BEP-20 compatible wallet address</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Withdrawal Request
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        You don't have any pool commission balance to withdraw.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Withdrawals -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Withdrawals</h5>
                </div>
                <div class="card-body">
                    @if($withdrawals->count() > 0)
                        @foreach($withdrawals as $withdrawal)
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded">
                            <div>
                                <h6 class="mb-1">${{ number_format($withdrawal->amount, 2) }}</h6>
                                <small class="text-muted">{{ $withdrawal->created_at->format('M d, Y') }}</small>
                            </div>
                            <span class="badge badge-{{ $withdrawal->status_badge }}">
                                {{ $withdrawal->status_text }}
                            </span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">No withdrawal requests yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;
    
    fetch('{{ route("user.withdrawal.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Withdrawal request submitted successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
@endsection
