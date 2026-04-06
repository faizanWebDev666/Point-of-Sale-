<x-header/>
            <!-- Main Content -->
            <main class="main-content">
                <div class="content-header">
                    <div class="header-left">
                        <h2 class="page-title">Settings</h2>
                        <p class="page-subtitle">Configure your POS system preferences.</p>
                    </div>
                </div>

                <div class="settings-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                    <!-- Currency Settings Card -->
                    <div class="table-card" style="padding: 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                            <div style="width: 40px; height: 40px; background: rgba(128, 0, 0, 0.1); color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.1rem; font-weight: 700;">Currency Configuration</h3>
                                <p style="font-size: 0.8rem; color: #64748b;">Set your default store currency.</p>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.5rem; text-transform: uppercase;">Default Currency</label>
                            <select id="globalCurrencySelect" class="input-field" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; font-weight: 600; cursor: pointer;">
                                <option value="USD">USD ($) - US Dollar</option>
                                <option value="PKR">PKR (Rs) - Pakistani Rupee</option>
                            </select>
                            <p style="margin-top: 0.75rem; font-size: 0.75rem; color: #64748b; line-height: 1.4;">
                                <i class="fas fa-info-circle" style="color: var(--primary-color); margin-right: 0.25rem;"></i>
                                Changing this will update all prices across the Dashboard, Products, and POS system.
                            </p>
                        </div>

                        <button onclick="GlobalCurrency.switch(document.getElementById('globalCurrencySelect').value); alert('Settings saved successfully!');" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
                            Save Changes
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
