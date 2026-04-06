<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard - Modern Store Management</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Top Navigation Bar -->
        <nav class="navbar">
            <div class="navbar-content">
                <!-- Left Section: Logo and Store Name -->
                <div class="navbar-left">
                    <div class="logo-section">
                        <div class="logo">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="store-info">
                            <h1 class="store-name">RetailHub</h1>
                            <p class="store-tagline">Point of Sale System</p>
                        </div>
                    </div>
                </div>

                <!-- Right Section: User Profile -->
                <div class="navbar-right">
                    <div class="notification-bell">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar">
                            <img src="https://via.placeholder.com/40" alt="User Avatar">
                        </div>
                        <div class="user-details">
                            <p class="user-name">John Doe</p>
                            <p class="user-role">Manager</p>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Container -->
        <div class="main-wrapper">
            <!-- Sidebar Menu -->
            <aside class="sidebar">
                <nav class="sidebar-nav">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="{{ route('pos') }}" class="nav-link" data-section="pos">
                                <span class="nav-icon">
                                    <i class="fas fa-cash-register"></i>
                                </span>
                                <span class="nav-label">POS Billing</span>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a href="{{ route('dashboard') }}" class="nav-link" data-section="sales">
                                <span class="nav-icon">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <span class="nav-label">Sales</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('products') }}" class="nav-link" data-section="products">
                                <span class="nav-icon">
                                    <i class="fas fa-box"></i>
                                </span>
                                <span class="nav-label">Products</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-section="customers">
                                <span class="nav-icon">
                                    <i class="fas fa-users"></i>
                                </span>
                                <span class="nav-label">Customers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-section="reports">
                                <span class="nav-icon">
                                    <i class="fas fa-file-alt"></i>
                                </span>
                                <span class="nav-label">Reports</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settings') }}" class="nav-link" data-section="settings">
                                <span class="nav-icon">
                                    <i class="fas fa-cog"></i>
                                </span>
                                <span class="nav-label">Settings</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="sidebar-footer">
                    <button class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </div>
            </aside>
