<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Professional POS - RetailHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@21.0.8/build/css/intlTelInput.css">
</head>
<body>
    <!-- POS Header -->
    <header class="pos-header">
        <div class="logo-section">
            <div class="logo-box">
                <i class="fas fa-bolt"></i>
            </div>
            <a href="{{ route('dashboard') }}" style="text-decoration: none; color: inherit;">
            <div class="store-info">
                <h1 class="store-name">RetailHub</h1>
                <p class="store-subtitle">Smart POS Terminal</p>
            </div>
            </a>
        </div>
        
        <div class="search-bar" style="max-width: 400px; margin: 0 1rem;">
            <i class="fas fa-search"></i>
            <input type="text" id="productSearch" placeholder="Search products (F4)..." autocomplete="off">
        </div>

        <div class="bill-info">
            <p class="bill-number" id="billNumber">Bill #...</p>
            <p class="bill-date" id="billDate">{{ date('D, M d, Y | h:i A') }}</p>
        </div>
    </header>

    <!-- Main Container -->
    <div class="pos-container">
        <!-- Left Panel: Product Selection -->
        <section class="product-panel">
            <!-- Stock Alerts -->
            <div id="stockAlerts" class="stock-alerts" style="display: none;">
                <div class="alert-item alert-danger" id="outOfStockAlert"></div>
                <div class="alert-item alert-warning" id="lowStockAlert"></div>
            </div>

            <!-- Category Tabs -->
            <div class="category-tabs">
                <button class="category-tab active" data-category="all">All Items</button>
                @foreach($categories as $category)
                    <button class="category-tab" data-category="{{ strtolower($category) }}">{{ ucfirst($category) }}</button>
                @endforeach
            </div>

            <!-- Product Grid -->
            <div class="product-grid" id="productGrid">
                @forelse($products as $product)
                    <div class="product-card" 
                         data-id="{{ $product->id }}" 
                         data-name="{{ $product->name }}" 
                         data-price="{{ $product->price }}" 
                         data-sku="{{ $product->sku }}" 
                         data-category="{{ strtolower($product->category) }}" 
                         data-stock="{{ $product->stock }}"
                         onclick="pos.addToCart(this)">
                        <div class="product-img">
                            @php
                                $icon = 'fa-box';
                                if(strtolower($product->category) == 'electronics') $icon = 'fa-laptop';
                                elseif(strtolower($product->category) == 'clothing') $icon = 'fa-tshirt';
                                elseif(strtolower($product->category) == 'grocery') $icon = 'fa-apple-alt';
                                elseif(strtolower($product->category) == 'beauty' || strtolower($product->category) == 'beauty & health') $icon = 'fa-pump-medical';
                            @endphp
                            <i class="fas {{ $icon }}"></i>
                        </div>
                        <div class="product-info">
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-price">${{ number_format($product->price, 2) }}</div>
                            <div class="product-stock">Stock: {{ $product->stock }}</div>
                        </div>
                        @if($product->stock <= 5 && $product->stock > 0)
                            <span class="stock-warning" title="Low Stock"></span>
                        @elseif($product->stock == 0)
                            <span class="stock-danger" title="Out of Stock"></span>
                        @endif
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; color: var(--text-light);">
                        <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                        <p>No products available.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Right Panel: Cart -->
        <aside class="cart-panel">
            <div class="cart-header">
                <h2><i class="fas fa-shopping-cart"></i> Cart</h2>
                <button id="clearCartBtn" class="remove-item-btn" title="Clear All">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>

            <div class="cart-items" id="cartItems">
                <!-- Cart items loaded via JS -->
                <div style="text-align:center; padding: 4rem 2rem; color: var(--text-light);">
                    <i class="fas fa-shopping-basket" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p>Cart is empty</p>
                </div>
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span data-value="subtotal">$0.00</span>
                </div>
                <div class="summary-row" id="discountRow" style="display: none; color: var(--success-color);">
                    <span>Discount</span>
                    <span data-value="discount">-$0.00</span>
                </div>
                <div class="summary-row">
                    <span id="taxLabel">Tax (0%)</span>
                    <span data-value="tax">$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span data-value="total">$0.00</span>
                </div>
            </div>

            <div class="pos-footer">
                <button id="completeSaleBtn" class="complete-sale-btn" disabled>
                    <span>Checkout (F2)</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
                <button id="toggleMoreOptionsBtn" class="more-options-btn" title="More Options">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </aside>
    </div>

    <!-- Modals -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Complete Sale</h3>
                <button class="close-btn" onclick="pos.closeModal(document.getElementById('checkoutModal'))">&times;</button>
            </div>
            <form id="checkoutForm">
                <div class="modal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem; display: block;">Customer Name</label>
                            <input type="text" id="checkoutCustomerName" placeholder="Walk-in Customer" class="input-field">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem; display: block;">Phone Number</label>
                            <input type="tel" id="checkoutCustomerPhone" class="input-field" list="phoneHistory">
                            <datalist id="phoneHistory">
                                <!-- History loaded via JS -->
                            </datalist>
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.75rem; display: block;">Payment Method</label>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem;">
                            <div class="payment-method active" data-method="cash" onclick="pos.selectCheckoutPaymentMethod(this)">
                                <i class="fas fa-money-bill-wave"></i>
                                <span style="font-size: 0.7rem; font-weight: 600;">Cash</span>
                            </div>
                            <div class="payment-method" data-method="card" onclick="pos.selectCheckoutPaymentMethod(this)">
                                <i class="fas fa-credit-card"></i>
                                <span style="font-size: 0.7rem; font-weight: 600;">Card</span>
                            </div>
                            <div class="payment-method" data-method="upi" onclick="pos.selectCheckoutPaymentMethod(this)">
                                <i class="fas fa-mobile-alt"></i>
                                <span style="font-size: 0.7rem; font-weight: 600;">UPI</span>
                            </div>
                            <div class="payment-method" data-method="wallet" onclick="pos.selectCheckoutPaymentMethod(this)">
                                <i class="fas fa-wallet"></i>
                                <span style="font-size: 0.7rem; font-weight: 600;">Wallet</span>
                            </div>
                        </div>
                    </div>

                    <div id="checkoutCashSection">
                        <div style="background: var(--bg-gray); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span style="color: var(--text-medium); font-size: 0.875rem;">Total Amount</span>
                                <span id="checkoutTotal" style="font-weight: 700; color: var(--text-dark);">$0.00</span>
                            </div>
                            <input type="number" id="checkoutAmountPaid" step="0.01" placeholder="Enter amount paid..." class="input-field" style="margin-bottom: 0.5rem; font-size: 1.125rem; font-weight: 700; text-align: center;">
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem; border-top: 1px solid var(--border-color);">
                                <span style="color: var(--text-medium); font-size: 0.875rem;">Change Due</span>
                                <span id="checkoutChange" style="font-weight: 800; color: var(--success-color); font-size: 1.125rem;">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" class="btn-secondary" style="flex: 1;" onclick="pos.closeModal(document.getElementById('checkoutModal'))">Cancel</button>
                        <button type="submit" id="checkoutSubmitBtn" class="complete-sale-btn" style="flex: 2;">Confirm & Print</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- More Options Modal -->
    <div id="moreOptionsModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>POS Options</h3>
                <button class="close-btn" onclick="pos.closeModal(document.getElementById('moreOptionsModal'))">&times;</button>
            </div>
            <div class="modal-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <button class="modal-btn" onclick="pos.showTransactionHistory()">
                    <i class="fas fa-history"></i>
                    <span>History</span>
                </button>
                <button class="modal-btn" onclick="pos.showDailySummary()">
                    <i class="fas fa-chart-line"></i>
                    <span>Summary</span>
                </button>
                <button class="modal-btn" onclick="pos.showStockAlerts()">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Stock</span>
                </button>
                <button class="modal-btn" onclick="pos.toggleDiscountSection()">
                    <i class="fas fa-tag"></i>
                    <span>Discount</span>
                </button>
                <button class="modal-btn" onclick="pos.toggleTaxSection()">
                    <i class="fas fa-percentage"></i>
                    <span>Tax</span>
                </button>
                <a href="{{ route('dashboard') }}" class="modal-btn" style="text-decoration: none; grid-column: 1 / -1; justify-content: center; background: var(--bg-gray);">
                    <i class="fas fa-home"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    <!-- History/Summary Modal -->
    <div id="infoModal" class="modal">
        <div class="modal-content" style="max-width: 700px; max-height: 90vh;">
            <div class="modal-header">
                <h3 id="infoModalTitle">Information</h3>
                <button class="close-btn" onclick="pos.closeModal(document.getElementById('infoModal'))">&times;</button>
            </div>
            <div class="modal-body" id="infoModalContent" style="overflow-y: auto;">
                <!-- Dynamic content -->
            </div>
        </div>
    </div>

    <!-- Discount Modal -->
    <div id="discountModal" class="modal">
        <div class="modal-content" style="max-width: 350px;">
            <div class="modal-header">
                <h3>Apply Discount</h3>
                <button class="close-btn" onclick="pos.closeModal(document.getElementById('discountModal'))">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 1rem;">
                    <label id="discountFixedLabel" style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem; display: block;">Fixed Amount Discount (USD)</label>
                    <input type="number" id="discountFixed" placeholder="Enter fixed amount" class="input-field" value="0">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem; display: block;">Percentage Discount (%)</label>
                    <input type="number" id="discountPercent" placeholder="Enter percentage" class="input-field" value="0">
                </div>
                <button onclick="pos.applyDiscount()" class="complete-sale-btn" style="width: 100%;">Apply Discounts</button>
            </div>
        </div>
    </div>

    <!-- Tax Modal -->
    <div id="taxModal" class="modal">
        <div class="modal-content" style="max-width: 350px;">
            <div class="modal-header">
                <h3>Adjust Tax</h3>
                <button class="close-btn" onclick="pos.closeModal(document.getElementById('taxModal'))">&times;</button>
            </div>
            <div class="modal-body">
                <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-light); text-transform: uppercase; margin-bottom: 0.5rem; display: block;">Tax Rate (%)</label>
                <input type="number" id="taxValue" placeholder="Enter tax percentage" class="input-field" value="0">
                <button onclick="pos.applyTax()" class="complete-sale-btn" style="width: 100%;">Apply Tax</button>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Receipt Container (Hidden) -->
    <div id="receipt" class="receipt-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@21.0.8/build/js/intlTelInput.min.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/pos.js') }}"></script>
</body>
</html>