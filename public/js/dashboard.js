/* ============================================
   POS Dashboard - JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    initializeSidebar();
    initializeMobileMenu();
    initializeUserProfile();
    initializeNotifications();
});

/* ============================================
   Sidebar Navigation
   ============================================ */



/* ============================================
   Mobile Menu Toggle
   ============================================ */

function initializeMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function () {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('mobile-active');

            // Update icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('mobile-active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close mobile menu when a nav link is clicked
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function () {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar.classList.contains('mobile-active')) {
                    sidebar.classList.remove('mobile-active');
                    const icon = mobileMenuToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    }
}

/* ============================================
   User Profile Dropdown
   ============================================ */

function initializeUserProfile() {
    const userProfile = document.querySelector('.user-profile');

    if (userProfile) {
        userProfile.addEventListener('click', function (e) {
            e.preventDefault();
            console.log('User profile clicked');
            // You can add a dropdown menu here
            // Example: showUserMenu();
        });
    }
}

/* ============================================
   Notifications
   ============================================ */

function initializeNotifications() {
    const notificationBell = document.querySelector('.notification-bell');

    if (notificationBell) {
        notificationBell.addEventListener('click', function (e) {
            e.preventDefault();
            console.log('Notifications clicked');
            // You can add a notification panel here
            // Example: showNotificationPanel();
        });
    }
}

/* ============================================
   Card Interactions
   ============================================ */

// Animate cards on page load
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.summary-card, .info-card');

    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.5s ease-out';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

/* ============================================
   Button Events
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    const newSaleBtn = document.querySelector('.btn-primary');
    const selectDateBtn = document.querySelector('.btn-secondary');

    if (newSaleBtn) {
        newSaleBtn.addEventListener('click', function () {
            handleNewSale();
        });
    }

    if (selectDateBtn) {
        selectDateBtn.addEventListener('click', function () {
            handleSelectDate();
        });
    }
});

function handleNewSale() {
    console.log('New Sale button clicked');
    // TODO: Implement new sale modal/page
    // Example: showNewSaleDialog();
}

function handleSelectDate() {
    console.log('Select Date button clicked');
    // TODO: Implement date picker
    // Example: showDatePicker();
}

/* ============================================
   Logout Functionality
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    const logoutBtn = document.querySelector('.logout-btn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            handleLogout();
        });
    }
});

function handleLogout() {
    if (confirm('Are you sure you want to logout?')) {
        console.log('Logging out...');
        // TODO: Implement logout
        // For Laravel: window.location.href = '/logout';
    }
}

/* ============================================
   View All Links
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    const viewAllLinks = document.querySelectorAll('.view-all-link');

    viewAllLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const section = this.closest('.info-card').querySelector('h3').textContent;
            console.log('Viewing all:', section);
            // TODO: Navigate to full view
        });
    });
});

/* ============================================
   Responsive Behavior
   ============================================ */

let currentDevice = 'desktop';

function detectDevice() {
    const width = window.innerWidth;
    if (width <= 480) {
        return 'mobile';
    } else if (width <= 768) {
        return 'tablet';
    } else {
        return 'desktop';
    }
}

window.addEventListener('resize', function () {
    const newDevice = detectDevice();
    if (newDevice !== currentDevice) {
        currentDevice = newDevice;
        console.log('Device changed to:', currentDevice);
        // Adjust layout if needed
    }
});

// Initialize on load
currentDevice = detectDevice();

/* ============================================
   Utility Functions
   ============================================ */

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(value);
}

// Format date
function formatDate(date) {
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    }).format(date);
}

// Format time
function formatTime(date) {
    return new Intl.DateTimeFormat('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    }).format(date);
}

/* ============================================
   Real-time Updates (WebSocket Example)
   ============================================ */

/*
   Note: Uncomment and configure based on your backend setup
   
function initializeRealtimeUpdates() {
    const ws = new WebSocket('wss://your-server.com/dashboard-updates');

    ws.onopen = function () {
        console.log('WebSocket connected');
    };

    ws.onmessage = function (event) {
        const data = JSON.parse(event.data);
        console.log('Received update:', data);
        
        // Update dashboard cards based on data
        // Example: updateSalesCard(data.sales);
    };

    ws.onerror = function (error) {
        console.error('WebSocket error:', error);
    };

    ws.onclose = function () {
        console.log('WebSocket disconnected');
        // Attempt to reconnect
        setTimeout(initializeRealtimeUpdates, 5000);
    };
}

// Call on demand
// initializeRealtimeUpdates();
*/

/* ============================================
   Analytics Tracking
   ============================================ */

function trackEvent(eventName, eventData = {}) {
    console.log(`Tracking event: ${eventName}`, eventData);
    // TODO: Integrate with your analytics service (Google Analytics, Mixpanel, etc.)
}

// Track page view
document.addEventListener('DOMContentLoaded', function () {
    trackEvent('page_view', {
        page: 'dashboard',
        timestamp: new Date().toISOString(),
    });
});

/* ============================================
   Error Handling & Logging
   ============================================ */

window.addEventListener('error', function (event) {
    console.error('Global error:', event.error);
    // TODO: Send error to error tracking service
});

window.addEventListener('unhandledrejection', function (event) {
    console.error('Unhandled promise rejection:', event.reason);
    // TODO: Send error to error tracking service
});

/* ============================================
   Performance Monitoring
   ============================================ */

if (window.PerformanceObserver) {
    const observer = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
            console.log(`Performance ${entry.name}: ${entry.duration}ms`);
        }
    });

    observer.observe({ entryTypes: ['navigation', 'resource', 'paint'] });
}

/* ============================================
   Export for Testing
   ============================================ */

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatCurrency,
        formatDate,
        formatTime,
        trackEvent,
    };
}
