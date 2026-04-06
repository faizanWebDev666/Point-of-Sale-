document.addEventListener('DOMContentLoaded', function() {
    const addProductBtn = document.getElementById('addProductBtn');
    const productModal = document.getElementById('productModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const productForm = document.getElementById('productForm');
    const productSearch = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.querySelector('.modal-header h3');
    const currencySymbolEls = document.querySelectorAll('.current-currency-symbol');

    // Modal logic
    function openModal(mode = 'add', data = {}) {
        // Update currency symbols in modal
        if (typeof GlobalCurrency !== 'undefined') {
            const symbol = GlobalCurrency.currencies[GlobalCurrency.current].symbol;
            currencySymbolEls.forEach(el => el.textContent = symbol);
        }

        if (mode === 'edit') {
            modalTitle.textContent = 'Edit Product';
            productForm.action = `/products/${data.id}`;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            // Fill data
            document.getElementById('productName').value = data.name;
            document.getElementById('productSKU').value = data.sku;
            document.getElementById('category').value = data.category;
            
            // Convert base price to current currency for editing
            let displayPrice = data.price;
            if (typeof GlobalCurrency !== 'undefined') {
                const rate = GlobalCurrency.currencies[GlobalCurrency.current].rate;
                displayPrice = (parseFloat(data.price) * rate).toFixed(2);
            }
            document.getElementById('price').value = displayPrice;

            document.getElementById('stock').value = data.stock;
            document.getElementById('minStock').value = data.min_stock;
            document.getElementById('description').value = data.description || '';
        } else {
            modalTitle.textContent = 'Add New Product';
            productForm.action = '/products';
            methodField.innerHTML = '';
            productForm.reset();
        }
        
        productModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Handle form submission with currency conversion
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            if (typeof GlobalCurrency !== 'undefined' && GlobalCurrency.current !== 'USD') {
                const enteredPrice = parseFloat(document.getElementById('price').value);
                const rate = GlobalCurrency.currencies[GlobalCurrency.current].rate;
                const basePrice = enteredPrice / rate;
                
                // Update the price input with the base (USD) value before submitting
                document.getElementById('price').value = basePrice.toFixed(2);
            }
        });
    }

    function hideModal() {
        productModal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    if (addProductBtn) {
        addProductBtn.addEventListener('click', () => openModal('add'));
    }

    if (closeModal) {
        closeModal.addEventListener('click', hideModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideModal);
    }

    // Close modal on outside click
    window.addEventListener('click', (e) => {
        if (e.target === productModal) {
            hideModal();
        }
    });

    // Search and Filter logic
    function filterProducts() {
        const searchTerm = productSearch.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();
        const rows = document.querySelectorAll('.products-table tbody tr');

        rows.forEach(row => {
            const nameEl = row.querySelector('.product-name-main');
            if (!nameEl) return; // Skip "No products found" row

            const productName = nameEl.textContent.toLowerCase();
            const productSKU = row.querySelector('.product-sku-sub').textContent.toLowerCase();
            const productCategory = row.querySelector('.category-badge').textContent.toLowerCase();

            const matchesSearch = productName.includes(searchTerm) || productSKU.includes(searchTerm);
            const matchesCategory = selectedCategory === '' || productCategory === selectedCategory;

            row.style.display = (matchesSearch && matchesCategory) ? '' : 'none';
        });
    }

    if (productSearch) {
        productSearch.addEventListener('input', filterProducts);
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
    }

    // Edit button logic
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.edit-btn');
        if (editBtn) {
            const data = {
                id: editBtn.dataset.id,
                name: editBtn.dataset.name,
                sku: editBtn.dataset.sku,
                category: editBtn.dataset.category,
                price: editBtn.dataset.price,
                stock: editBtn.dataset.stock,
                min_stock: editBtn.dataset.min_stock,
                description: editBtn.dataset.description
            };
            openModal('edit', data);
        }
    });
});
