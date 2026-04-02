// Professional POS System JavaScript
class POSSystem {
    constructor() {
        this.cart = [];
        this.subtotal = 0;
        this.discount = { type: 'percentage', value: 0 };
        this.taxRate = 0.05;
        this.billNumber = this.generateBillNumber();
        this.checkoutPaymentMethod = 'cash';
        
        // Configuration
        this.currency = '$';
        
        this.init();
    }

    init() {
        this.cacheElements();
        this.attachEventListeners();
        this.updateBillNumber();
        this.refreshStockAlerts();
        this.setupKeyboardShortcuts();
        this.renderCart();
    }

    cacheElements() {
        // Core Layout
        this.productGrid = document.getElementById('productGrid');
        this.cartItemsList = document.getElementById('cartItems');
        this.productSearch = document.getElementById('productSearch');
        this.toastContainer = document.getElementById('toastContainer');
        
        // Modals
        this.checkoutModal = document.getElementById('checkoutModal');
        this.moreOptionsModal = document.getElementById('moreOptionsModal');
        this.infoModal = document.getElementById('infoModal');
        this.discountModal = document.getElementById('discountModal');
        
        // Checkout Elements
        this.checkoutForm = document.getElementById('checkoutForm');
        this.checkoutTotal = document.getElementById('checkoutTotal');
        this.checkoutAmountPaid = document.getElementById('checkoutAmountPaid');
        this.checkoutChange = document.getElementById('checkoutChange');
        this.checkoutSubmitBtn = document.getElementById('checkoutSubmitBtn');
        
        // Summary Elements
        this.summaryElements = {
            subtotal: document.querySelector('[data-value="subtotal"]'),
            discount: document.querySelector('[data-value="discount"]'),
            tax: document.querySelector('[data-value="tax"]'),
            total: document.querySelector('[data-value="total"]'),
        };

        // Action Buttons
        this.completeSaleBtn = document.getElementById('completeSaleBtn');
        this.clearCartBtn = document.getElementById('clearCartBtn');
    }

    attachEventListeners() {
        // Product Interaction
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', () => this.addToCart(card));
        });

        // Search & Filter
        this.productSearch.addEventListener('input', (e) => this.filterProducts(e.target.value));
        
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.addEventListener('click', () => this.filterByCategory(tab));
        });

        // Cart Actions
        this.clearCartBtn.addEventListener('click', () => this.confirmClearCart());
        
        // Checkout
        this.completeSaleBtn.addEventListener('click', () => this.openCheckout());
        this.checkoutForm.addEventListener('submit', (e) => this.handleCheckoutSubmit(e));
        this.checkoutAmountPaid.addEventListener('input', () => this.calculateCheckoutChange());

        // More Options
        document.getElementById('toggleMoreOptionsBtn').addEventListener('click', () => this.openModal(this.moreOptionsModal));
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // F2: Checkout
            if (e.key === 'F2') {
                e.preventDefault();
                if (!this.completeSaleBtn.disabled) this.openCheckout();
            }
            // F4: Search focus
            if (e.key === 'F4') {
                e.preventDefault();
                this.productSearch.focus();
            }
            // Escape: Close modals
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });

        // Barcode scanner support (Enter key in search)
        this.productSearch.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.handleBarcodeScan(this.productSearch.value);
            }
        });
    }

    async handleBarcodeScan(sku) {
        if (!sku) return;
        
        try {
            const res = await fetch(`/api/pos/search-products?q=${sku}`);
            const products = await res.json();
            
            // If exact SKU match found
            const exactMatch = products.find(p => p.sku.toLowerCase() === sku.toLowerCase());
            if (exactMatch) {
                this.addToCartFromData(exactMatch);
                this.productSearch.value = '';
                this.showToast(`Scanned: ${exactMatch.name}`, 'success');
            } else if (products.length === 1) {
                this.addToCartFromData(products[0]);
                this.productSearch.value = '';
                this.showToast(`Scanned: ${products[0].name}`, 'success');
            } else {
                this.showToast('Product not found', 'warning');
            }
        } catch (e) {
            console.error('Scan error:', e);
        }
    }

    addToCartFromData(p) {
        const id = p.id.toString();
        const name = p.name;
        const price = parseFloat(p.price);
        const stock = parseInt(p.stock);

        if (stock <= 0) {
            this.showToast('Product out of stock!', 'danger');
            return;
        }

        const existing = this.cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty >= stock) {
                this.showToast('Maximum stock reached', 'warning');
                return;
            }
            existing.qty++;
        } else {
            this.cart.push({ id, name, price, qty: 1, stock });
        }

        this.renderCart();
    }

    // --- Product Logic ---

    addToCart(card) {
        const id = card.dataset.id;
        const name = card.dataset.name;
        const price = parseFloat(card.dataset.price);
        const stock = parseInt(card.dataset.stock);

        if (stock <= 0) {
            this.showToast('Product out of stock!', 'danger');
            return;
        }

        const existing = this.cart.find(item => item.id === id);
        if (existing) {
            if (existing.qty >= stock) {
                this.showToast('Maximum stock reached', 'warning');
                return;
            }
            existing.qty++;
        } else {
            this.cart.push({ id, name, price, qty: 1, stock });
        }

        this.renderCart();
        this.showToast(`Added ${name} to cart`);
    }

    updateQty(id, delta) {
        const item = this.cart.find(it => it.id === id);
        if (!item) return;

        const newQty = item.qty + delta;
        if (newQty <= 0) {
            this.removeFromCart(id);
        } else if (newQty > item.stock) {
            this.showToast('Not enough stock', 'warning');
        } else {
            item.qty = newQty;
            this.renderCart();
        }
    }

    removeFromCart(id) {
        this.cart = this.cart.filter(item => item.id !== id);
        this.renderCart();
    }

    confirmClearCart() {
        if (this.cart.length === 0) return;
        if (confirm('Are you sure you want to clear the cart?')) {
            this.cart = [];
            this.discount = { type: 'percentage', value: 0 };
            this.renderCart();
            this.showToast('Cart cleared', 'info');
        }
    }

    // --- Filtering Logic ---

    filterProducts(query) {
        const q = query.toLowerCase();
        
        // If query is small, just filter in-memory
        if (q.length < 3) {
            this.localFilter(q);
            return;
        }

        // Debounce API search
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => this.searchProducts(q), 300);
    }

    localFilter(q) {
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const sku = card.dataset.sku.toLowerCase();
            card.style.display = (name.includes(q) || sku.includes(q)) ? 'flex' : 'none';
        });
    }

    async searchProducts(q) {
        const category = document.querySelector('.category-tab.active')?.dataset.category || 'all';
        try {
            const res = await fetch(`/api/pos/search-products?q=${q}&category=${category}`);
            const products = await res.json();
            this.renderProductGrid(products);
        } catch (e) {
            console.error('Search error:', e);
        }
    }

    renderProductGrid(products) {
        this.productGrid.innerHTML = products.map(p => {
            let icon = 'fa-box';
            const cat = p.category ? p.category.toLowerCase() : '';
            if(cat === 'electronics') icon = 'fa-laptop';
            else if(cat === 'clothing') icon = 'fa-tshirt';
            else if(cat === 'grocery') icon = 'fa-apple-alt';
            else if(cat === 'beauty' || cat === 'beauty & health') icon = 'fa-pump-medical';

            return `
                <div class="product-card" 
                     data-id="${p.id}" 
                     data-name="${p.name}" 
                     data-price="${p.price}" 
                     data-sku="${p.sku}" 
                     data-category="${cat}" 
                     data-stock="${p.stock}"
                     onclick="pos.addToCart(this)">
                    <div class="product-img">
                        <i class="fas ${icon}"></i>
                    </div>
                    <div class="product-info">
                        <div class="product-name">${p.name}</div>
                        <div class="product-price">${this.currency}${parseFloat(p.price).toFixed(2)}</div>
                        <div class="product-stock">Stock: ${p.stock}</div>
                    </div>
                    ${p.stock <= 5 && p.stock > 0 ? '<span class="stock-warning" title="Low Stock"></span>' : ''}
                    ${p.stock == 0 ? '<span class="stock-danger" title="Out of Stock"></span>' : ''}
                </div>
            `;
        }).join('');
    }

    async filterByCategory(tab) {
        document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        const category = tab.dataset.category;
        const query = this.productSearch.value;

        // Fetch filtered products from API
        try {
            const res = await fetch(`/api/pos/search-products?q=${query}&category=${category}`);
            const products = await res.json();
            this.renderProductGrid(products);
        } catch (e) {
            this.localFilter(query); // Fallback to local filter if API fails
        }
    }

    // --- UI Rendering ---

    renderCart() {
        if (this.cart.length === 0) {
            this.cartItemsList.innerHTML = `
                <div style="text-align:center; padding: 4rem 2rem; color: var(--text-light);">
                    <i class="fas fa-shopping-basket" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                    <p>Cart is empty</p>
                </div>`;
            this.completeSaleBtn.disabled = true;
            this.updateSummary(0);
            return;
        }

        this.subtotal = 0;
        this.cartItemsList.innerHTML = this.cart.map(item => {
            const total = item.price * item.qty;
            this.subtotal += total;
            return `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">${this.currency}${item.price.toFixed(2)}</div>
                    </div>
                    <div class="cart-item-controls">
                        <div class="cart-item-qty">
                            <button class="qty-btn" onclick="pos.updateQty('${item.id}', -1)">−</button>
                            <span>${item.qty}</span>
                            <button class="qty-btn" onclick="pos.updateQty('${item.id}', 1)">+</button>
                        </div>
                    </div>
                    <div class="cart-item-total">${this.currency}${total.toFixed(2)}</div>
                    <button class="remove-item-btn" onclick="pos.removeFromCart('${item.id}')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }).join('');

        this.completeSaleBtn.disabled = false;
        this.updateSummary();
    }

    updateSummary() {
        let discAmt = 0;
        if (this.discount.type === 'percentage') {
            discAmt = (this.subtotal * this.discount.value) / 100;
        } else {
            discAmt = this.discount.value;
        }

        const taxable = Math.max(0, this.subtotal - discAmt);
        const tax = taxable * this.taxRate;
        const total = taxable + tax;

        this.summaryElements.subtotal.textContent = `${this.currency}${this.subtotal.toFixed(2)}`;
        this.summaryElements.tax.textContent = `${this.currency}${tax.toFixed(2)}`;
        this.summaryElements.total.textContent = `${this.currency}${total.toFixed(2)}`;

        const discRow = document.getElementById('discountRow');
        if (discAmt > 0) {
            discRow.style.display = 'flex';
            this.summaryElements.discount.textContent = `-${this.currency}${discAmt.toFixed(2)}`;
        } else {
            discRow.style.display = 'none';
        }
    }

    // --- Checkout Logic ---

    openCheckout() {
        const total = parseFloat(this.summaryElements.total.textContent.replace(this.currency, ''));
        this.checkoutTotal.textContent = `${this.currency}${total.toFixed(2)}`;
        this.checkoutAmountPaid.value = '';
        this.calculateCheckoutChange();
        this.openModal(this.checkoutModal);
        
        // Focus amount paid field for quick entry
        setTimeout(() => this.checkoutAmountPaid.focus(), 100);
    }

    selectCheckoutPaymentMethod(el) {
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
        el.classList.add('active');
        this.checkoutPaymentMethod = el.dataset.method;
        
        const cashSection = document.getElementById('checkoutCashSection');
        if (this.checkoutPaymentMethod === 'cash') {
            cashSection.style.display = 'block';
        } else {
            cashSection.style.display = 'none';
            this.checkoutAmountPaid.value = this.summaryElements.total.textContent.replace(this.currency, '');
            this.calculateCheckoutChange();
        }
    }

    calculateCheckoutChange() {
        const total = parseFloat(this.checkoutTotal.textContent.replace(this.currency, ''));
        const paid = parseFloat(this.checkoutAmountPaid.value) || 0;
        const change = paid - total;

        const changeEl = this.checkoutChange;
        if (paid < total && this.checkoutPaymentMethod === 'cash') {
            changeEl.textContent = 'Short: ' + this.currency + (total - paid).toFixed(2);
            changeEl.style.color = 'var(--danger-color)';
            this.checkoutSubmitBtn.disabled = true;
        } else {
            changeEl.textContent = this.currency + Math.max(0, change).toFixed(2);
            changeEl.style.color = 'var(--success-color)';
            this.checkoutSubmitBtn.disabled = false;
        }
    }

    async handleCheckoutSubmit(e) {
        e.preventDefault();
        
        const total = parseFloat(this.checkoutTotal.textContent.replace(this.currency, ''));
        const paid = parseFloat(this.checkoutAmountPaid.value) || total;

        const saleData = {
            items: this.cart,
            subtotal: this.subtotal,
            discount_amount: this.calculateDiscountAmount(),
            tax_amount: parseFloat(this.summaryElements.tax.textContent.replace(this.currency, '')),
            total_amount: total,
            payment_method: this.checkoutPaymentMethod,
            amount_paid: paid,
            customer_name: document.getElementById('checkoutCustomerName').value || 'Walk-in Customer',
            customer_phone: document.getElementById('checkoutCustomerPhone').value || null,
        };

        try {
            this.checkoutSubmitBtn.disabled = true;
            this.checkoutSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            const response = await fetch('/api/pos/save-sale', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(saleData),
            });

            const result = await response.json();

            if (result.success) {
                this.showToast('Sale completed!', 'success');
                this.generateReceipt(saleData, result.transaction_id);
                this.resetPOS();
                this.closeAllModals();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            this.showToast(error.message, 'danger');
            this.checkoutSubmitBtn.disabled = false;
            this.checkoutSubmitBtn.innerHTML = 'Confirm & Print';
        }
    }

    // --- Options Logic ---

    toggleDiscountSection() {
        this.closeModal(this.moreOptionsModal);
        this.openModal(this.discountModal);
    }

    applyDiscount() {
        const type = document.getElementById('discountType').value;
        const val = parseFloat(document.getElementById('discountValue').value) || 0;

        if (type === 'percentage' && val > 100) {
            this.showToast('Invalid percentage', 'warning');
            return;
        }

        this.discount = { type, value: val };
        this.updateSummary();
        this.closeModal(this.discountModal);
        this.showToast('Discount applied');
    }

    async showTransactionHistory() {
        this.showInfoModal('Loading history...', 'Recent Transactions');
        try {
            const res = await fetch('/api/pos/transaction-history?limit=10');
            const data = await res.json();
            
            let html = `
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead style="background: var(--bg-gray); text-align: left;">
                        <tr>
                            <th style="padding: 0.75rem;">ID</th>
                            <th style="padding: 0.75rem;">Customer</th>
                            <th style="padding: 0.75rem;">Total</th>
                            <th style="padding: 0.75rem;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.data.map(trx => `
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.75rem;">${trx.transaction_id.split('-').pop()}</td>
                                <td style="padding: 0.75rem;">${trx.customer_name}</td>
                                <td style="padding: 0.75rem; font-weight: 600;">${this.currency}${trx.total_amount.toFixed(2)}</td>
                                <td style="padding: 0.75rem; color: var(--text-light);">${new Date(trx.created_at).toLocaleDateString()}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            this.showInfoModal(html, 'Recent Transactions');
        } catch (e) {
            this.showInfoModal('Error loading history', 'Error');
        }
    }

    async showDailySummary() {
        this.showInfoModal('Loading summary...', 'Daily Summary');
        try {
            const res = await fetch('/api/pos/daily-summary');
            const data = await res.json();
            
            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="background: var(--primary-color); color: white; padding: 1.25rem; border-radius: 12px;">
                        <div style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase; font-weight: 600;">Total Sales</div>
                        <div style="font-size: 1.5rem; font-weight: 800;">${this.currency}${data.total_sales.toFixed(2)}</div>
                    </div>
                    <div style="background: var(--bg-gray); padding: 1.25rem; border-radius: 12px;">
                        <div style="font-size: 0.75rem; color: var(--text-light); text-transform: uppercase; font-weight: 600;">Transactions</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-dark);">${data.transaction_count}</div>
                    </div>
                </div>
                <h4 style="font-size: 0.875rem; margin-bottom: 1rem; color: var(--text-medium);">Payment Breakdown</h4>
                ${data.payment_breakdown.map(p => `
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--bg-light-gray); border-radius: 8px; margin-bottom: 0.5rem;">
                        <span style="font-weight: 600; text-transform: uppercase;">${p.payment_method}</span>
                        <span>${p.count} sales - <strong>${this.currency}${p.total.toFixed(2)}</strong></span>
                    </div>
                `).join('')}
            `;
            this.showInfoModal(html, 'Daily Summary');
        } catch (e) {
            this.showInfoModal('Error loading summary', 'Error');
        }
    }

    async showStockAlerts() {
        this.showInfoModal('Checking stock...', 'Stock Alerts');
        try {
            const res = await fetch('/api/pos/stock-alerts');
            const data = await res.json();
            
            let html = '';
            if (data.out_of_stock.length > 0) {
                html += '<h4 style="color: var(--danger-color); margin-bottom: 0.75rem;">Out of Stock</h4>';
                html += data.out_of_stock.map(p => `<div style="padding: 0.5rem; border-bottom: 1px solid var(--border-color);">${p.name} (SKU: ${p.sku})</div>`).join('');
            }
            if (data.low_stock.length > 0) {
                html += '<h4 style="color: var(--warning-color); margin: 1.5rem 0 0.75rem;">Low Stock</h4>';
                html += data.low_stock.map(p => `<div style="padding: 0.5rem; border-bottom: 1px solid var(--border-color);">${p.name} - <strong>${p.stock} remaining</strong></div>`).join('');
            }
            if (!html) html = '<p style="color: var(--success-color);">All items well-stocked!</p>';
            
            this.showInfoModal(html, 'Stock Alerts');
        } catch (e) {
            this.showInfoModal('Error checking stock', 'Error');
        }
    }

    // --- Utility Methods ---

    showToast(msg, type = 'dark') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        let icon = 'info-circle';
        if (type === 'success') icon = 'check-circle';
        if (type === 'danger') icon = 'exclamation-circle';
        if (type === 'warning') icon = 'exclamation-triangle';
        
        toast.innerHTML = `<i class="fas fa-${icon}"></i> ${msg}`;
        this.toastContainer.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    showInfoModal(content, title = 'Information') {
        document.getElementById('infoModalTitle').textContent = title;
        document.getElementById('infoModalContent').innerHTML = content;
        this.openModal(this.infoModal);
    }

    openModal(modal) {
        modal.classList.add('active');
    }

    closeModal(modal) {
        modal.classList.remove('active');
    }

    closeAllModals() {
        document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
    }

    resetPOS() {
        this.cart = [];
        this.discount = { type: 'percentage', value: 0 };
        this.billNumber = this.generateBillNumber();
        this.updateBillNumber();
        this.renderCart();
        this.refreshStockAlerts();
        
        // Reset inputs
        document.getElementById('checkoutCustomerName').value = '';
        document.getElementById('checkoutCustomerPhone').value = '';
        document.getElementById('discountValue').value = '';
    }

    generateBillNumber() {
        const date = new Date().toISOString().slice(0,10).replace(/-/g,'');
        const rand = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        return `INV-${date}-${rand}`;
    }

    updateBillNumber() {
        document.getElementById('billNumber').textContent = 'Bill #' + this.billNumber;
    }

    calculateDiscountAmount() {
        if (this.discount.type === 'percentage') {
            return (this.subtotal * this.discount.value) / 100;
        }
        return this.discount.value;
    }

    async refreshStockAlerts() {
        try {
            const res = await fetch('/api/pos/stock-alerts');
            const data = await res.json();
            const container = document.getElementById('stockAlerts');
            const outEl = document.getElementById('outOfStockAlert');
            const lowEl = document.getElementById('lowStockAlert');
            
            let hasAlert = false;
            if (data.out_of_stock.length > 0) {
                outEl.innerHTML = `<i class="fas fa-times-circle"></i> ${data.out_of_stock.length} out of stock`;
                outEl.style.display = 'flex';
                hasAlert = true;
            } else {
                outEl.style.display = 'none';
            }
            
            if (data.low_stock.length > 0) {
                lowEl.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${data.low_stock.length} low stock`;
                lowEl.style.display = 'flex';
                hasAlert = true;
            } else {
                lowEl.style.display = 'none';
            }
            
            container.style.display = hasAlert ? 'flex' : 'none';
        } catch (e) {}
    }

    generateReceipt(sale, trxId) {
        const receiptEl = document.getElementById('receipt');
        const now = new Date().toLocaleString();
        
        let itemsHtml = sale.items.map(item => `
            <tr>
                <td style="padding: 5px 0;">${item.name}<br><small>${item.qty} x ${this.currency}${item.price.toFixed(2)}</small></td>
                <td style="text-align: right; vertical-align: bottom;">${this.currency}${(item.price * item.qty).toFixed(2)}</td>
            </tr>
        `).join('');

        receiptEl.innerHTML = `
            <div style="font-family: 'Courier New', monospace; width: 300px; margin: 0 auto; padding: 20px; background: white; color: black;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">RetailHub</h2>
                    <p style="margin: 5px 0; font-size: 0.8rem;">Professional POS Terminal</p>
                </div>
                
                <div style="font-size: 0.8rem; margin-bottom: 15px;">
                    <div>TRX: ${trxId}</div>
                    <div>Date: ${now}</div>
                    <div>Cust: ${sale.customer_name}</div>
                </div>
                
                <table style="width: 100%; border-top: 1px dashed #000; border-bottom: 1px dashed #000; font-size: 0.8rem; margin-bottom: 15px;">
                    ${itemsHtml}
                </table>
                
                <div style="font-size: 0.8rem;">
                    <div style="display: flex; justify-content: space-between;"><span>Subtotal:</span><span>${this.currency}${sale.subtotal.toFixed(2)}</span></div>
                    ${sale.discount_amount > 0 ? `<div style="display: flex; justify-content: space-between;"><span>Discount:</span><span>-${this.currency}${sale.discount_amount.toFixed(2)}</span></div>` : ''}
                    <div style="display: flex; justify-content: space-between;"><span>Tax (5%):</span><span>${this.currency}${sale.tax_amount.toFixed(2)}</span></div>
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1rem; margin-top: 5px; border-top: 1px solid #000; padding-top: 5px;">
                        <span>TOTAL:</span><span>${this.currency}${sale.total_amount.toFixed(2)}</span>
                    </div>
                </div>
                
                <div style="margin-top: 15px; font-size: 0.8rem; text-align: center;">
                    <div>Payment: ${sale.payment_method.toUpperCase()}</div>
                    ${sale.payment_method === 'cash' ? `<div>Change: ${this.currency}${(sale.amount_paid - sale.total_amount).toFixed(2)}</div>` : ''}
                </div>
                
                <div style="text-align: center; margin-top: 30px; font-size: 0.8rem;">
                    <p>Thank you for your business!</p>
                </div>
            </div>
        `;

        window.print();
    }
}

// Initialize
let pos;
document.addEventListener('DOMContentLoaded', () => {
    pos = new POSSystem();
});
