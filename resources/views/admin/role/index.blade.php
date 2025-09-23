@extends('layouts.admin')

@section('content')
<style>
    /* Modern Card Design */
    .role-card {
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
        margin-bottom: 2rem;
    }

    .role-card::before {
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

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .role-card:hover {
        transform: translateY(-4px);
        box-shadow: 
            0 25px 50px rgba(0, 0, 0, 0.5),
            0 0 0 1px rgba(59, 209, 122, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }

    /* Role Title */
    .role-title {
        font-size: 1.4rem;
        font-weight: 800;
        background: linear-gradient(135deg, #3bd17a, #00d4ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 12px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
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

    .btn-gradient-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border: none;
        color: white;
        font-weight: 600;
        padding: 12px 24px;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .btn-gradient-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
        color: white;
    }

    /* Table Styling */
    .table-modern {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #3bd17a, #00d4ff);
        color: white;
        border: none;
        font-weight: 700;
        padding: 1rem;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .table-modern tbody td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .table-modern tbody tr:hover {
        background: rgba(59, 209, 122, 0.1);
        transition: all 0.3s ease;
    }

    /* Role Badge */
    .role-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .role-badge.admin {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .role-badge.user {
        background: linear-gradient(135deg, #3bd17a, #00d4ff);
        color: white;
    }

    .role-badge.moderator {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .role-badge.manager {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    /* Status Indicators */
    .status-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    .status-active {
        background: #3bd17a;
        box-shadow: 0 0 10px rgba(59, 209, 122, 0.5);
    }

    .status-inactive {
        background: #ef4444;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    /* Form Styling */
    .form-control-modern {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(59, 209, 122, 0.3);
        border-radius: 12px;
        color: white;
        padding: 12px 16px;
        transition: all 0.3s ease;
    }

    .form-control-modern:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #3bd17a;
        box-shadow: 0 0 0 0.2rem rgba(59, 209, 122, 0.25);
        color: white;
    }

    .form-control-modern::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    /* Alert Styling */
    .alert-modern {
        border: none;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 2rem;
        backdrop-filter: blur(20px);
    }

    .alert-success-modern {
        background: rgba(59, 209, 122, 0.2);
        border-left: 4px solid #3bd17a;
        color: #3bd17a;
    }

    .alert-danger-modern {
        background: rgba(239, 68, 68, 0.2);
        border-left: 4px solid #ef4444;
        color: #ef4444;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .role-card {
            padding: 20px;
            margin-bottom: 1rem;
        }
        
        .table-modern {
            font-size: 0.9rem;
        }
        
        .btn-gradient-primary,
        .btn-gradient-secondary,
        .btn-gradient-danger {
            padding: 10px 16px;
            font-size: 0.9rem;
        }
    }
</style>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="role-card">
                <h1 class="role-title">
                    <i class="fas fa-users-cog me-3"></i>
                    User Role Management
                </h1>
                <p class="text-muted mb-0">Manage user roles and permissions for your platform</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success-modern alert-modern">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger-modern alert-modern">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Role Assignment Form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="role-card">
                <h3 class="role-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Assign Role to User
                </h3>
                
                <form method="POST" action="{{ route('admin.role.assign') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="user_id" class="form-label text-white">
                                <i class="fas fa-user me-2"></i>Select User
                            </label>
                            <select class="form-control-modern form-select" id="user_id" name="user_id" required>
                                <option value="">Choose a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        @if(old('user_id') == $user->id) selected @endif>
                                        {{ $user->name }} ({{ $user->email }})
                                        @if($user->role)
                                            - Current: {{ ucfirst($user->role) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="role" class="form-label text-white">
                                <i class="fas fa-user-tag me-2"></i>Select Role
                            </label>
                            <select class="form-control-modern form-select" id="role" name="role" required>
                                <option value="">Choose a role...</option>
                                <option value="admin" @if(old('role') == 'admin') selected @endif>
                                    <i class="fas fa-crown me-2"></i>Admin
                                </option>
                                <option value="manager" @if(old('role') == 'manager') selected @endif>
                                    <i class="fas fa-user-tie me-2"></i>Manager
                                </option>
                                <option value="moderator" @if(old('role') == 'moderator') selected @endif>
                                    <i class="fas fa-user-shield me-2"></i>Moderator
                                </option>
                                <option value="user" @if(old('role') == 'user') selected @endif>
                                    <i class="fas fa-user me-2"></i>User
                                </option>
                            </select>
                            @error('role')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="fas fa-user-plus me-2"></i>
                                Assign Role
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Users with Roles Table -->
    <div class="row">
        <div class="col-12">
            <div class="role-card">
                <h3 class="role-title">
                    <i class="fas fa-list me-2"></i>
                    Users with Assigned Roles
                </h3>
                
                @if($usersWithRoles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>User</th>
                                    <th><i class="fas fa-envelope me-2"></i>Email</th>
                                    <th><i class="fas fa-user-tag me-2"></i>Current Role</th>
                                    <th><i class="fas fa-calendar me-2"></i>Role Assigned</th>
                                    <th><i class="fas fa-calendar me-2"></i>Email Verified</th>
                                    <th><i class="fas fa-cogs me-2"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usersWithRoles as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-white">{{ $user->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-white">{{ $user->email }}</span>
                                            @if($user->email_verified_at)
                                                <br><small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Verified
                                                </small>
                                            @else
                                                <br><small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Not Verified
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->role)
                                                <span class="role-badge {{ $user->role }}">
                                                    @switch($user->role)
                                                        @case('admin')
                                                            <i class="fas fa-crown me-1"></i>Admin
                                                            @break
                                                        @case('manager')
                                                            <i class="fas fa-user-tie me-1"></i>Manager
                                                            @break
                                                        @case('moderator')
                                                            <i class="fas fa-user-shield me-1"></i>Moderator
                                                            @break
                                                        @default
                                                            <i class="fas fa-user me-1"></i>User
                                                    @endswitch
                                                </span>
                                            @else
                                                <span class="role-badge user">
                                                    <i class="fas fa-user me-1"></i>No Role
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-white">
                                                {{ $user->role_updated_at ? $user->role_updated_at->format('M d, Y') : 'N/A' }}
                                            </span>
                                            @if($user->role_updated_at)
                                                <br><small class="text-muted">
                                                    {{ $user->role_updated_at->diffForHumans() }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->email_verified_at)
                                                <span class="status-indicator status-active"></span>
                                                <span class="text-success">Verified</span>
                                            @else
                                                <span class="status-indicator status-inactive"></span>
                                                <span class="text-warning">Not Verified</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- Change Role Button -->
                                                <button type="button" class="btn btn-gradient-secondary btn-sm" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#changeRoleModal{{ $user->id }}"
                                                        title="Change Role">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <!-- Remove Role Button -->
                                                <form method="POST" action="{{ route('admin.role.remove', $user->id) }}" 
                                                      style="display: inline;"
                                                      onsubmit="return confirm('Are you sure you want to remove the role from {{ $user->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-gradient-danger btn-sm" 
                                                            title="Remove Role">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Change Role Modal -->
                                    <div class="modal fade" id="changeRoleModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content" style="background: linear-gradient(145deg, #0a1a2e, #16213e); border: 1px solid rgba(59, 209, 122, 0.3);">
                                                <div class="modal-header">
                                                    <h5 class="modal-title text-white">
                                                        <i class="fas fa-edit me-2"></i>
                                                        Change Role for {{ $user->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" action="{{ route('admin.role.update', $user->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="new_role{{ $user->id }}" class="form-label text-white">New Role</label>
                                                            <select class="form-control-modern form-select" id="new_role{{ $user->id }}" name="role" required>
                                                                <option value="">Choose a role...</option>
                                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                                                    <i class="fas fa-crown me-2"></i>Admin
                                                                </option>
                                                                <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>
                                                                    <i class="fas fa-user-tie me-2"></i>Manager
                                                                </option>
                                                                <option value="moderator" {{ $user->role === 'moderator' ? 'selected' : '' }}>
                                                                    <i class="fas fa-user-shield me-2"></i>Moderator
                                                                </option>
                                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>
                                                                    <i class="fas fa-user me-2"></i>User
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-gradient-primary">
                                                            <i class="fas fa-save me-2"></i>Update Role
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No users with assigned roles found</h5>
                        <p class="text-muted">Assign roles to users using the form above</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-modern');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);

    // Form validation
    const roleForm = document.querySelector('form[action*="admin.role.assign"]');
    if (roleForm) {
        roleForm.addEventListener('submit', function(e) {
            const userId = document.getElementById('user_id').value;
            const role = document.getElementById('role').value;
            
            if (!userId || !role) {
                e.preventDefault();
                alert('Please select both user and role');
                return false;
            }
        });
    }
});
</script>
@endsection

