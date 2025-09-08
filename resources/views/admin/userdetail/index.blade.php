@extends('layouts.admin')

@section('content')
<main class="container-fluid py-4">
    <div class="row">
        <div class="col-12">

            <div class="card card-statistics">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <h5 class="mb-2 mb-md-0">User Details</h5>

                    <form method="GET" class="d-flex" action="{{ route('admin.userdetails.index') }}">
                        <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control me-2"
                            placeholder="Search name, email, phone, referral...">
                        <button class="btn btn-primary">Search</button>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-hover align-middle">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th style="min-width: 220px;">Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Referred By</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                @php
                                // Resolve status (ActivationInfo.status -> else email_verified_at -> Pending)
                                $status = $user->activationInfo->status
                                ?? ($user->email_verified_at ? 'Active' : 'Pending');

                                // Resolve "referred by" → try to show referrer name, else the code/string
                                $referredByText = $user->referrerUser->name
                                ?? $user->referred_by
                                ?? '—';

                                // Last login (if you track it as last_login_at)
                                $lastLogin = $user->last_login_at
                                ? \Illuminate\Support\Carbon::parse($user->last_login_at)->diffForHumans()
                                : '—';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <small class="text-muted">ID: {{ $user->id }}</small>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '—' }}</td>
                                    <td>
                                        {{ $referredByText }}
                                        @if(isset($user->referrer))
                                        <small class="text-muted d-block">({{ $user->referrer->email }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(strtolower($status) === 'active')
                                        <span class="badge bg-success">Active</span>
                                        @elseif(strtolower($status) === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @else
                                        <span class="badge bg-secondary">{{ $status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $lastLogin }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No users found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($users->hasPages())
                <div class="card-footer">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
                @endif
            </div>

        </div>
    </div>
</main>
@endsection