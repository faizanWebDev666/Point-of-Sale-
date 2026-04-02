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
                            <a href="#" class="nav-link" data-section="settings">
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

            <!-- Main Content Area -->
            <main class="main-content">
                <!-- Header Section -->
                <div class="content-header">
                    <div class="header-left">
                        <h2 class="page-title">Dashboard</h2>
                        <p class="page-subtitle">Welcome back! Here's your store's performance today.</p>
                    </div>
                    <div class="header-right">
                        <button class="btn btn-secondary">
                            <i class="fas fa-calendar-alt"></i>
                            Select Date
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            New Sale
                        </button>
                    </div>
                </div>

                <!-- Summary Cards Section -->
                <div class="cards-grid">
                    <!-- Total Sales Card -->
                    <div class="summary-card">
                        <div class="card-header">
                            <h3 class="card-title">Total Sales</h3>
                            <i class="fas fa-dollar-sign card-icon"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-value">$12,450.50</div>
                            <div class="card-meta">
                                <span class="meta-badge positive">
                                    <i class="fas fa-arrow-up"></i> 12.5%
                                </span>
                                <span class="meta-text">vs. yesterday</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small>Updated 5 minutes ago</small>
                        </div>
                    </div>

                    <!-- Today's Orders Card -->
                    <div class="summary-card">
                        <div class="card-header">
                            <h3 class="card-title">Today's Orders</h3>
                            <i class="fas fa-shopping-bag card-icon"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-value">247</div>
                            <div class="card-meta">
                                <span class="meta-badge positive">
                                    <i class="fas fa-arrow-up"></i> 8.2%
                                </span>
                                <span class="meta-text">vs. yesterday</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small>Updated 2 minutes ago</small>
                        </div>
                    </div>

                    <!-- Low Stock Alerts Card -->
                    <div class="summary-card alert-card">
                        <div class="card-header">
                            <h3 class="card-title">Low Stock Alerts</h3>
                            <i class="fas fa-exclamation-triangle card-icon"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-value alert-value">5</div>
                            <div class="card-meta">
                                <span class="meta-badge negative">
                                    <i class="fas fa-arrow-down"></i> 2 critical
                                </span>
                                <span class="meta-text">Requires attention</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="footer-link">View Details →</a>
                        </div>
                    </div>

                    <!-- Revenue Card -->
                    <div class="summary-card">
                        <div class="card-header">
                            <h3 class="card-title">Today's Revenue</h3>
                            <i class="fas fa-chart-pie card-icon"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-value">$9,856.25</div>
                            <div class="card-meta">
                                <span class="meta-badge positive">
                                    <i class="fas fa-arrow-up"></i> 5.3%
                                </span>
                                <span class="meta-text">Net after costs</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small>Updated 3 minutes ago</small>
                        </div>
                    </div>
                </div>

                <!-- Additional Info Section -->
                <div class="info-section">
                    <!-- Recent Transactions -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <h3>Recent Transactions</h3>
                            <a href="#" class="view-all-link">View All →</a>
                        </div>
                        <div class="transactions-list">
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <p class="transaction-customer">Sarah Johnson</p>
                                    <p class="transaction-desc">Order #12047</p>
                                </div>
                                <div class="transaction-amount">$245.99</div>
                                <span class="transaction-status completed">Completed</span>
                            </div>
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <p class="transaction-customer">Mike Wilson</p>
                                    <p class="transaction-desc">Order #12046</p>
                                </div>
                                <div class="transaction-amount">$189.50</div>
                                <span class="transaction-status completed">Completed</span>
                            </div>
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <p class="transaction-customer">Emma Davis</p>
                                    <p class="transaction-desc">Order #12045</p>
                                </div>
                                <div class="transaction-amount">$325.00</div>
                                <span class="transaction-status pending">Pending</span>
                            </div>
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <h3>Top Products</h3>
                            <a href="#" class="view-all-link">View All →</a>
                        </div>
                        <div class="products-list">
                            <div class="product-item">
                                <div class="product-info">
                                    <p class="product-name">Wireless Headphones</p>
                                    <p class="product-sku">SKU: WH-2024</p>
                                </div>
                                <div class="product-sales">45 units</div>
                                <span class="product-badge">$89.99</span>
                            </div>
                            <div class="product-item">
                                <div class="product-info">
                                    <p class="product-name">USB-C Cable</p>
                                    <p class="product-sku">SKU: UC-1234</p>
                                </div>
                                <div class="product-sales">112 units</div>
                                <span class="product-badge">$12.99</span>
                            </div>
                            <div class="product-item">
                                <div class="product-info">
                                    <p class="product-name">Phone Case Premium</p>
                                    <p class="product-sku">SKU: PC-5678</p>
                                </div>
                                <div class="product-sales">78 units</div>
                                <span class="product-badge">$24.99</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
