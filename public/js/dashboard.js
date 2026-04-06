/* ============================================
   POS Dashboard - JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    initializeSidebar();
    initializeMobileMenu();
    initializeUserProfile();
    initializeNotifications();
    initializeGlobalCurrency();
});

/* ============================================
   Global Currency Handler
   ============================================ */

const GlobalCurrency = {
    currencies: {
        'USD': { symbol: '$', rate: 1 },
        'PKR': { symbol: 'Rs', rate: 280 }
    },
    current: localStorage.getItem('pos_currency') || 'USD',

    init() {
        this.updateGlobalUI();
        this.attachListeners();
    },

    attachListeners() {
        const selector = document.getElementById('globalCurrencySelect');
        if (selector) {
            selector.value = this.current;
            selector.addEventListener('change', (e) => this.switch(e.target.value));
        }
    },

    switch(code) {
        if (!this.currencies[code]) return;
        this.current = code;
        localStorage.setItem('pos_currency', code);
        
        // Notify POS if it's active
        if (window.pos && typeof window.pos.syncCurrencyUI === 'function') {
            window.pos.syncCurrencyUI();
        } else {
            this.updateGlobalUI();
        }
    },

    format(amount, isBase = true) {
        const config = this.currencies[this.current];
        const rate = isBase ? config.rate : 1;
        const converted = amount * rate;
        return `${config.symbol}${converted.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    },

    updateGlobalUI() {
        // Update selector value if it exists
        const selector = document.getElementById('globalCurrencySelect');
        if (selector) {
            selector.value = this.current;
        }

        // Update all elements with data-base-price
        document.querySelectorAll('[data-base-price]').forEach(el => {
            const basePrice = parseFloat(el.dataset.basePrice);
            if (!isNaN(basePrice)) {
                el.textContent = this.format(basePrice);
            }
        });
    }
};

function initializeGlobalCurrency() {
    GlobalCurrency.init();
}

/* ============================================
   Sidebar Navigation
   ============================================ */

function initializeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;

    // Handle active state
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.parentElement.classList.add('active');
        } else {
            link.parentElement.classList.remove('active');
        }
    });
}

function initializeMobileMenu() {
    const toggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.querySelector('.sidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
}

function initializeUserProfile() {
    // Placeholder for profile menu logic
}

function initializeNotifications() {
    // Placeholder for notifications logic
}
