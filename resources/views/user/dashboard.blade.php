@extends('layouts.user')

@section('content')
<style>
    :root {
        --primary-green: #22c55e;
        --secondary-green: #16a34a;
        --accent-blue: #3b82f6;
        --light-blue: #dbeafe;
        --dark-blue: #1e40af;
        --orange: #f97316;
        --red: #ef4444;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --white: #ffffff;
        --black: #000000;
    }

    .dashboard-container {
        background: var(--white);
        min-height: 100vh;
        padding: 0;
    }

    /* Header Section */
    .dashboard-header {
        background: var(--white);
        border-bottom: 1px solid var(--gray-200);
        padding: 1rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .hamburger-menu {
        display: flex;
        flex-direction: column;
        gap: 3px;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s;
    }

    .hamburger-menu:hover {
        background-color: var(--gray-100);
    }

    .hamburger-line {
        width: 20px;
        height: 2px;
        background-color: var(--gray-600);
        border-radius: 1px;
    }

    .search-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background-color: var(--gray-100);
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        min-width: 300px;
    }

    .search-icon {
        color: var(--gray-400);
        font-size: 1.1rem;
    }

    .search-input {
        border: none;
        background: transparent;
        outline: none;
        color: var(--gray-700);
        font-size: 0.9rem;
        width: 100%;
    }

    .search-input::placeholder {
        color: var(--gray-400);
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .notification-icon {
        position: relative;
        padding: 0.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .notification-icon:hover {
        background-color: var(--gray-100);
    }

    .notification-badge {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        background-color: var(--red);
        color: var(--white);
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .profile-dropdown {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .profile-dropdown:hover {
        background-color: var(--gray-100);
    }

    .profile-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .dropdown-arrow {
        color: var(--gray-400);
        font-size: 0.8rem;
    }

    .menu-icon {
        padding: 0.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .menu-icon:hover {
        background-color: var(--gray-100);
    }

    /* Main Content */
    .main-content {
        padding: 2rem;
    }

    .dashboard-title {
        background: linear-gradient(135deg, var(--light-blue), var(--accent-blue));
        color: var(--dark-blue);
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: inline-block;
    }

    .title-divider {
        height: 2px;
        background: linear-gradient(90deg, var(--accent-blue), transparent);
        margin-bottom: 2rem;
    }

    /* Investment Details Card */
    .investment-card {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 1.5rem;
    }

    .table-container {
        overflow-x: auto;
    }

    .investment-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .investment-table thead {
        background-color: var(--gray-50);
    }

    .investment-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--gray-700);
        border-bottom: 1px solid var(--gray-200);
    }

    .investment-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--gray-100);
        color: var(--gray-600);
    }

    .investment-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-completed {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .action-button {
        padding: 0.5rem 1rem;
        border: 1px solid var(--gray-300);
        background: var(--white);
        color: var(--gray-700);
        border-radius: 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-button:hover {
        background-color: var(--gray-50);
        border-color: var(--gray-400);
    }

    .action-button.primary {
        background-color: var(--primary-green);
        color: var(--white);
        border-color: var(--primary-green);
    }

    .action-button.primary:hover {
        background-color: var(--secondary-green);
        border-color: var(--secondary-green);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--gray-500);
    }

    .empty-state-icon {
        font-size: 3rem;
        color: var(--gray-300);
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--gray-600);
        margin-bottom: 0.5rem;
    }

    .empty-state-description {
        font-size: 0.9rem;
        color: var(--gray-500);
    }

    /* Statistics Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .stat-icon.primary {
        background-color: #dcfce7;
        color: var(--primary-green);
    }

    .stat-icon.warning {
        background-color: #fef3c7;
        color: var(--orange);
    }

    .stat-icon.info {
        background-color: #dbeafe;
        color: var(--accent-blue);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
        margin-bottom: 0.25rem;
    }

    .stat-change {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .stat-change.positive {
        color: var(--primary-green);
    }

    .stat-change.negative {
        color: var(--red);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .dashboard-header {
            padding: 1rem;
        }

        .search-container {
            min-width: 200px;
        }

        .main-content {
            padding: 1rem;
        }

        .dashboard-title {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .investment-table {
            font-size: 0.8rem;
        }

        .investment-table th,
        .investment-table td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <div class="hamburger-menu">
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
            </div>
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search">
            </div>
        </div>
        <div class="header-right">
            <div class="notification-icon">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">0</span>
            </div>
            <div class="notification-icon">
                <i class="fas fa-envelope"></i>
                <span class="notification-badge">0</span>
            </div>
            <div class="profile-dropdown">
                <div class="profile-avatar">JD</div>
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </div>
            <div class="menu-icon">
                <i class="fas fa-ellipsis-v"></i>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="dashboard-title">Investment Dashboard</h1>
        <div class="title-divider"></div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Investment</span>
                    <div class="stat-icon primary">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <div class="stat-value">$12,450</div>
                <div class="stat-change positive">+12.5% from last month</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Active Plans</span>
                    <div class="stat-icon warning">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
                <div class="stat-value">8</div>
                <div class="stat-change positive">+2 new this week</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-title">Total Returns</span>
                    <div class="stat-icon info">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-value">$44,820</div>
                <div class="stat-change positive">+360% ROI</div>
            </div>
        </div>

        <!-- Investment Details Card -->
        <div class="investment-card">
            <h2 class="card-title">User Investment Details</h2>
            <div class="table-container">
                <table class="investment-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Invested Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="profile-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">JD</div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--gray-900);">John Doe</div>
                                        <div style="font-size: 0.8rem; color: var(--gray-500);">john@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--gray-900);">Builder Plan</div>
                                <div style="font-size: 0.8rem; color: var(--gray-500);">3.6x Return</div>
                            </td>
                            <td style="font-weight: 600; color: var(--gray-900);">$20.00</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td style="color: var(--gray-600);">Dec 15, 2024</td>
                            <td>
                                <button class="action-button">View</button>
                                <button class="action-button primary">Edit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="profile-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">AS</div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--gray-900);">Alice Smith</div>
                                        <div style="font-size: 0.8rem; color: var(--gray-500);">alice@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--gray-900);">Bloom Plan</div>
                                <div style="font-size: 0.8rem; color: var(--gray-500);">3.6x Return</div>
                            </td>
                            <td style="font-weight: 600; color: var(--gray-900);">$40.00</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td style="color: var(--gray-600);">Dec 14, 2024</td>
                            <td>
                                <button class="action-button">View</button>
                                <button class="action-button primary">Edit</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="profile-avatar" style="width: 32px; height: 32px; font-size: 0.8rem;">BJ</div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--gray-900);">Bob Johnson</div>
                                        <div style="font-size: 0.8rem; color: var(--gray-500);">bob@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--gray-900);">Champion Plan</div>
                                <div style="font-size: 0.8rem; color: var(--gray-500);">3.6x Return</div>
                            </td>
                            <td style="font-weight: 600; color: var(--gray-900);">$1,000.00</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                            <td style="color: var(--gray-600);">Dec 10, 2024</td>
                            <td>
                                <button class="action-button">View</button>
                                <button class="action-button primary">Edit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State (Hidden by default, shown when no data) -->
        <div class="investment-card" style="display: none;">
            <h2 class="card-title">User Investment Details</h2>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="empty-state-title">No user investments found</div>
                <div class="empty-state-description">Start by creating your first investment plan to see data here.</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add interactive functionality
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const profileDropdown = document.querySelector('.profile-dropdown');
    const notificationIcons = document.querySelectorAll('.notification-icon');
    
    // Hamburger menu toggle
    hamburgerMenu.addEventListener('click', function() {
        console.log('Hamburger menu clicked');
        // Add sidebar toggle functionality here
    });
    
    // Profile dropdown toggle
    profileDropdown.addEventListener('click', function() {
        console.log('Profile dropdown clicked');
        // Add dropdown functionality here
    });
    
    // Notification icons
    notificationIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            console.log('Notification clicked');
            // Add notification functionality here
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    searchInput.addEventListener('input', function(e) {
        console.log('Searching for:', e.target.value);
        // Add search functionality here
    });
    
    // Action buttons
    const actionButtons = document.querySelectorAll('.action-button');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log('Action button clicked:', this.textContent);
            // Add action functionality here
        });
    });
});
</script>
@endsection


