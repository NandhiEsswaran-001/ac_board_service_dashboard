// AC Service Management - App JS

// Auto-dismiss alerts after 4 seconds
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // Confirm delete actions (supports optional double-confirm)
    document.querySelectorAll('.confirm-action').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.dataset.confirm || 'Are you sure?';
            if (!confirm(msg)) {
                e.preventDefault();
                return;
            }
            const msg2 = el.dataset.confirm2;
            if (msg2 && !confirm(msg2)) {
                e.preventDefault();
            }
        });
    });

    const searchInput = document.getElementById('tableSearch');
    const statusFilter = document.getElementById('statusFilter');

    function applyTableFilters() {
        const term = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const statusVal = statusFilter ? statusFilter.value.toLowerCase().trim() : '';

        document.querySelectorAll('tbody tr').forEach(function (row) {
            let match = true;

            if (term) {
                const text = row.textContent.toLowerCase();
                match = text.includes(term);
            }

            if (match && statusVal) {
                let rowStatus = row.dataset.status ? row.dataset.status.toLowerCase().trim() : '';
                if (!rowStatus) {
                    const badges = row.querySelectorAll('.badge');
                    if (badges.length) {
                        rowStatus = badges[badges.length - 1].textContent.toLowerCase().trim();
                    }
                }
                match = rowStatus === statusVal;
            }

            row.style.display = match ? '' : 'none';
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyTableFilters);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', applyTableFilters);
    }

    // ---- Mobile Hamburger Menu ----
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar && sidebar.classList.add('open');
        overlay && overlay.classList.add('active');
        hamburgerBtn && hamburgerBtn.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar && sidebar.classList.remove('open');
        overlay && overlay.classList.remove('active');
        hamburgerBtn && hamburgerBtn.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function () {
            if (sidebar && sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar on nav link click (mobile)
    if (sidebar) {
        sidebar.querySelectorAll('.nav-item').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 900) closeSidebar();
            });
        });
    }

    // ---- Password visibility toggle ----
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-toggle-password');
            const input = targetId ? document.getElementById(targetId) : null;
            if (!input) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.classList.toggle('is-visible', isHidden);
            const label = isHidden ? 'Hide password' : 'Show password';
            btn.setAttribute('aria-label', label);
            btn.textContent = isHidden ? 'Hide' : 'Show';
        });
    });
});

// Open WhatsApp Desktop/App (prefilled, user still presses Send)
function openWhatsApp(phone, message) {
    const clean = (phone || '').replace(/[^0-9]/g, '');
    const number = clean.length === 10 ? '91' + clean : clean;
    const text = encodeURIComponent(message || '');
    const url = 'whatsapp://send?phone=' + number + '&text=' + text;
    // Prefer opening the native app (desktop/mobile) instead of WhatsApp Web
    window.location.href = url;
}
