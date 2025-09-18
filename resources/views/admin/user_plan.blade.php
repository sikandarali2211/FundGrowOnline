@extends('layouts.admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">User Plan Details</h4>
                    </div>
                    <div class="card-body">
                        @if($plan)
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>User Information</h5>
                                    <p><strong>Name:</strong> {{ $user->name }}</p>
                                    <p><strong>Email:</strong> {{ $user->email }}</p>
                                    <p><strong>User ID:</strong> {{ $user->id }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Plan Information</h5>
                                    <p><strong>Plan Name:</strong> {{ $plan->plan_name ?? 'N/A' }}</p>
                                    <p><strong>Amount:</strong> ${{ number_format($plan->amount ?? 0, 2) }}</p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge badge-{{ $plan->status === 'active' ? 'success' : ($plan->status === 'blocked' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($plan->status ?? 'Unknown') }}
                                        </span>
                                    </p>
                                    <p><strong>Created:</strong> {{ $plan->created_at ? $plan->created_at->format('M d, Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <h5>Update Plan Status</h5>
                                <form method="POST" action="{{ route('admin.user-plan.update-status', $plan->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="active" {{ $plan->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="blocked" {{ $plan->status === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                            <option value="rejected" {{ $plan->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <h5>No Plan Found</h5>
                                <p>This user doesn't have any plan associated with them.</p>
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.userdetails.index') }}" class="btn btn-secondary">Back to User List</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
