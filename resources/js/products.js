document.addEventListener('DOMContentLoaded', function() {
    const addProductBtn = document.getElementById('addProductBtn');
    const productModal = document.getElementById('productModal');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const addProductForm = document.getElementById('addProductForm');
    const productSearch = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const productsTableBody = document.querySelector('.products-table tbody');
    const rows = productsTableBody.querySelectorAll('tr');

    // Modal logic
    function openModal() {
        productModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function hideModal() {
        productModal.classList.remove('active');
        document.body.style.overflow = 'auto';
        addProductForm.reset();
    }

    if (addProductBtn) {
        addProductBtn.addEventListener('click', openModal);
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

    // Form submission
    if (addProductForm) {
        addProductForm.addEventListener('submit', function(e) {
            // Let the form submit normally to the server
            // e.preventDefault();
            // alert('Product saved successfully!');
            // hideModal();
        });
    }

    // Search and Filter logic
    function filterProducts() {
        const searchTerm = productSearch.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();

        rows.forEach(row => {
            const productName = row.querySelector('.product-name-main').textContent.toLowerCase();
            const productSKU = row.querySelector('.product-sku-sub').textContent.toLowerCase();
            const productCategory = row.querySelector('.category-badge').textContent.toLowerCase();

            const matchesSearch = productName.includes(searchTerm) || productSKU.includes(searchTerm);
            const matchesCategory = selectedCategory === '' || productCategory === selectedCategory;

            if (matchesSearch && matchesCategory) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (productSearch) {
        productSearch.addEventListener('input', filterProducts);
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
    }

    // Delete button logic
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this product?')) {
                const row = this.closest('tr');
                row.remove();
                alert('Product deleted successfully!');
            }
        });
    });

    // Edit button logic
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            const name = row.querySelector('.product-name-main').textContent;
            const sku = row.querySelector('.product-sku-sub').textContent.replace('SKU: ', '');
            const category = row.querySelector('.category-badge').textContent.toLowerCase();
            const price = row.querySelector('.price-value').textContent.replace('$', '');
            const stock = row.querySelector('.stock-count').textContent;

            // Fill modal with existing data (simulating edit)
            document.getElementById('productName').value = name;
            document.getElementById('productSKU').value = sku;
            document.getElementById('category').value = category;
            document.getElementById('price').value = price;
            document.getElementById('stock').value = stock;

            document.querySelector('.modal-header h3').textContent = 'Edit Product';
            openModal();
        });
    });
});