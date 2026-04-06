<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - POS System</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Top Navigation Bar -->
        <nav class="navbar">
            <div class="navbar-content">
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
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}" class="nav-link" data-section="sales">
                                <span class="nav-icon">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <span class="nav-label">Sales</span>
                            </a>
                        </li>
                        <li class="nav-item active">
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
                <div class="content-header">
                    <div class="header-left">
                        <h2 class="page-title">Product Management</h2>
                        <p class="page-subtitle">View, add, edit, and delete your store's products.</p>
                    </div>
                    <div class="header-right">
                        <button class="btn btn-primary" id="addProductBtn">
                            <i class="fas fa-plus"></i>
                            Add New Product
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div style="padding: 1rem; background-color: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 1rem;">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="padding: 1rem; background-color: #f8d7da; color: #721c24; border-radius: 8px; margin-bottom: 1rem;">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Filters & Search Bar -->
                <div class="filters-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="productSearch" placeholder="Search products by name or SKU...">
                    </div>
                    <div class="filter-box">
                        <select id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="electronics">Electronics</option>
                            <option value="clothing">Clothing</option>
                            <option value="grocery">Grocery</option>
                            <option value="beauty">Beauty & Health</option>
                        </select>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Stock Quantity</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="product-cell">
                                                <div class="product-img-mini">
                                                    @php
                                                        $icon = 'fa-box';
                                                        if(strtolower($product->category) == 'electronics') $icon = 'fa-laptop';
                                                        elseif(strtolower($product->category) == 'clothing') $icon = 'fa-tshirt';
                                                        elseif(strtolower($product->category) == 'grocery') $icon = 'fa-apple-alt';
                                                        elseif(strtolower($product->category) == 'beauty') $icon = 'fa-pump-medical';
                                                    @endphp
                                                    <i class="fas {{ $icon }}"></i>
                                                </div>
                                                <div class="product-info-mini">
                                                    <p class="product-name-main">{{ $product->name }}</p>
                                                    <p class="product-sku-sub">SKU: {{ $product->sku }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="category-badge">{{ ucfirst($product->category) }}</span></td>
                                        <td>
                                            <div class="stock-status {{ $product->stock <= $product->min_stock ? 'low-stock' : 'in-stock' }}">
                                                <span class="stock-count">{{ $product->stock }}</span>
                                                <span class="stock-label">units</span>
                                            </div>
                                        </td>
                                        <td><span class="price-value" data-base-price="{{ $product->price }}">${{ number_format($product->price, 2) }}</span></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn edit-btn" 
                                                        data-id="{{ $product->id }}" 
                                                        data-name="{{ $product->name }}" 
                                                        data-sku="{{ $product->sku }}" 
                                                        data-category="{{ $product->category }}" 
                                                        data-price="{{ $product->price }}" 
                                                        data-stock="{{ $product->stock }}" 
                                                        data-min_stock="{{ $product->min_stock }}" 
                                                        data-description="{{ $product->description }}" 
                                                        title="Edit Product">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn delete-btn" title="Delete Product">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-light);">
                                            No products found. Click "Add New Product" to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-pagination">
                        <p class="pagination-info">Showing {{ $products->count() }} products</p>
                        <div class="pagination-btns">
                            <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-chevron-left"></i></button>
                            <button class="btn btn-secondary btn-sm" disabled><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal-overlay" id="productModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Add New Product</h3>
                <button class="close-modal" id="closeModal">&times;</button>
            </div>
            <form id="productForm" action="{{ route('products.store') }}" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="productName">Product Name</label>
                            <input type="text" id="productName" name="name" placeholder="Enter product name" required>
                        </div>
                        <div class="form-group">
                            <label for="productSKU">SKU</label>
                            <input type="text" id="productSKU" name="sku" placeholder="Enter SKU (e.g. LAP-001)" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <option value="" disabled selected>Select category</option>
                                <option value="electronics">Electronics</option>
                                <option value="clothing">Clothing</option>
                                <option value="grocery">Grocery</option>
                                <option value="beauty">Beauty & Health</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Price (<span class="current-currency-symbol">$</span>)</label>
                            <input type="number" id="price" name="price" step="0.01" placeholder="0.00" required>
                            <input type="hidden" name="base_price" id="base_price">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock">Stock Quantity</label>
                            <input type="number" id="stock" name="stock" placeholder="Enter quantity" required>
                        </div>
                        <div class="form-group">
                            <label for="minStock">Min Stock Alert</label>
                            <input type="number" id="minStock" name="min_stock" placeholder="Enter min stock alert" value="5">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Enter product description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/products.js') }}"></script>
</body>
</html>