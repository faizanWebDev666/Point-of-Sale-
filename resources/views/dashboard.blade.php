<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard - Modern Store Management</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                        <a href="{{ route('pos') }}">
                        <button class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            New Sale
                        </button>
                        </a>
                    </div>
                </div>

                <!-- Summary Cards Section -->
                <div class="cards-grid">
                    <!-- Total Sales Card -->
                    <div class="summary-card">
                        <div class="card-header">
                            <h3 class="card-title">Total Revenue</h3>
                            <i class="fas fa-dollar-sign card-icon"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-value" id="totalRevenue" data-base-price="{{ $stats['total_revenue'] }}">{{ $stats['total_revenue'] }}</div>
                            <div class="card-meta">
                                <span class="meta-badge {{ $stats['revenue_growth'] >= 0 ? 'positive' : 'negative' }}">
                                    <i class="fas fa-arrow-{{ $stats['revenue_growth'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($stats['revenue_growth']) }}%
                                </span>
                                <span class="meta-text">vs. yesterday</span>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Orders Card -->
                    <div class="summary-card">
                        <div class="card-header">
                            <h3 class="card-title">Today's Orders</h3>
                            <i class="fas fa-shopping-cart card-icon"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-value">{{ $stats['today_orders'] }}</div>
                            <div class="card-meta">
                                <span class="meta-badge {{ $stats['orders_growth'] >= 0 ? 'positive' : 'negative' }}">
                                    <i class="fas fa-arrow-{{ $stats['orders_growth'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($stats['orders_growth']) }}%
                                </span>
                                <span class="meta-text">vs. yesterday</span>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Alerts Card -->
                    <div class="summary-card">
                        <div class="card-header">
                            <h3 class="card-title">Low Stock</h3>
                            <i class="fas fa-exclamation-triangle card-icon"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-value {{ $stats['low_stock_count'] > 0 ? 'text-danger' : '' }}">{{ $stats['low_stock_count'] }}</div>
                            <div class="card-meta">
                                <span class="meta-text">Items needing restock</span>
                            </div>
                        </div>
                    </div>

                    <!-- Avg Order Value Card -->
                    <div class="summary-card">
                        <div class="card-header">
                            <h3 class="card-title">Avg. Order</h3>
                            <i class="fas fa-chart-pie card-icon"></i>
                        </div>
                        <div class="card-body">
                            <div class="card-value" data-base-price="{{ $stats['avg_order_value'] }}">{{ $stats['avg_order_value'] }}</div>
                            <div class="card-meta">
                                <span class="meta-text">Per transaction</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); margin-top: 1.5rem; gap: 1.5rem;">
                    <div class="table-card" style="padding: 1.5rem; display: flex; flex-direction: column;">
                        <h3 style="margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 700; color: #1e293b;">Sales Revenue (Last 7 Days)</h3>
                        <div style="position: relative; height: 300px; width: 100%; flex: 1;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    <div class="table-card" style="padding: 1.5rem; display: flex; flex-direction: column;">
                        <h3 style="margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 700; color: #1e293b;">Payment Methods</h3>
                        <div style="position: relative; height: 300px; width: 100%; flex: 1;">
                            <canvas id="paymentChart"></canvas>
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
                            @foreach($recentTransactions as $trx)
                            <div class="transaction-item">
                                <div class="transaction-info">
                                    <p class="transaction-customer">{{ $trx->customer_name ?? 'Walk-in Customer' }}</p>
                                    <p class="transaction-desc">#{{ $trx->transaction_id }}</p>
                                </div>
                                <div class="transaction-amount" data-base-price="{{ $trx->total_amount }}">{{ $trx->total_amount }}</div>
                                <span class="transaction-status {{ strtolower($trx->status) }}">{{ $trx->status }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="info-card">
                        <div class="info-card-header">
                            <h3>Top Products</h3>
                            <a href="#" class="view-all-link">View All →</a>
                        </div>
                        <div class="products-list">
                            @foreach($topProducts as $product)
                            <div class="product-item">
                                <div class="product-info">
                                    <p class="product-name">{{ $product['name'] }}</p>
                                    <p class="product-sku">SKU: {{ $product['sku'] }}</p>
                                </div>
                                <div class="product-sales">{{ $product['total_qty'] }} units</div>
                                <span class="product-badge" data-base-price="{{ $product['price'] }}">{{ $product['price'] }}</span>
                            </div>
                            @endforeach
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
    <script>
        let salesChart, paymentChart;
        
        function initCharts() {
            const salesCtx = document.getElementById('salesChart');
            const paymentCtx = document.getElementById('paymentChart');
            
            if (!salesCtx && !paymentCtx) return;

            // Use GlobalCurrency for formatting and rates
            const currentCode = localStorage.getItem('pos_currency') || 'USD';
            const currencies = {
                'USD': { symbol: '$', rate: 1 },
                'PKR': { symbol: 'Rs', rate: 280 }
            };
            const config = currencies[currentCode];
            const rate = config.rate;
            const symbol = config.symbol;

            // Convert base sales data to current currency
            const baseSalesData = {!! json_encode($charts['sales_data']) !!};
            const currentSalesData = baseSalesData.map(v => v * rate);

            if (salesCtx) {
                if (salesChart) salesChart.destroy();
                salesChart = new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($charts['sales_labels']) !!},
                        datasets: [{
                            label: 'Revenue',
                            data: currentSalesData,
                            borderColor: '#800000',
                            backgroundColor: 'rgba(128, 0, 0, 0.05)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#800000',
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                padding: 12,
                                backgroundColor: '#1e293b',
                                titleFont: { size: 13 },
                                bodyFont: { size: 13 },
                                callbacks: {
                                    label: function(context) {
                                        return ' Revenue: ' + symbol + context.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2 });
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: {
                                    font: { size: 11 },
                                    callback: function(value) {
                                        return symbol + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 } }
                            }
                        }
                    }
                });
            }

            if (paymentCtx) {
                if (paymentChart) paymentChart.destroy();
                paymentChart = new Chart(paymentCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($charts['payment_labels']) !!},
                        datasets: [{
                            data: {!! json_encode($charts['payment_data']) !!},
                            backgroundColor: ['#800000', '#2c3e50', '#27ae60', '#f39c12'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 25,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: { size: 12, weight: '600' },
                                    color: '#475569'
                                }
                            },
                            tooltip: {
                                padding: 12,
                                backgroundColor: '#1e293b'
                            }
                        },
                        cutout: '70%'
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            initCharts();
            
            // Listen for currency changes from Settings page (via storage event)
            window.addEventListener('storage', (e) => {
                if (e.key === 'pos_currency') {
                    initCharts();
                }
            });

            // Handle manual selector if it exists on this page
            const selector = document.getElementById('globalCurrencySelect');
            if (selector) {
                selector.addEventListener('change', () => {
                    setTimeout(initCharts, 50);
                });
            }
        });
    </script>
</body>
</html>
