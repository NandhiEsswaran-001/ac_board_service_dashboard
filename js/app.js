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

    // ---- Payment Status -> Partial Amount toggle ----
    function togglePartialAmount(selectEl) {
        if (!selectEl) return;
        const form = selectEl.closest('form');
        const partialWrap = form ? form.querySelector('[data-partial-amount]') : null;
        if (!partialWrap) return;
        const input = partialWrap.querySelector('input');
        if (selectEl.value === 'Partial') {
            partialWrap.style.display = '';
            if (input) input.setAttribute('required', 'required');
        } else {
            partialWrap.style.display = 'none';
            if (input) input.removeAttribute('required');
        }
    }

    document.querySelectorAll('select[name="payment_status"]').forEach(function (selectEl) {
        togglePartialAmount(selectEl);
        selectEl.addEventListener('change', function () {
            togglePartialAmount(selectEl);
        });
    });

    // ---- Table sorting on number column (#) ----
    document.querySelectorAll('table').forEach(function (table) {
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        if (!thead || !tbody) return;
        const th = thead.querySelector('th:first-child');
        if (!th) return;

        th.classList.add('sortable-th');
        th.setAttribute('role', 'button');
        th.setAttribute('aria-sort', 'descending');
        th.dataset.sortOrder = 'desc';

        const indicator = document.createElement('span');
        indicator.className = 'sort-indicator';
        indicator.textContent = '▼';
        th.appendChild(indicator);

        function parseNumber(cell) {
            const text = (cell && cell.textContent) ? cell.textContent : '';
            const num = parseInt(text.replace(/[^0-9-]/g, ''), 10);
            return isNaN(num) ? 0 : num;
        }

        function sortRows(order) {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            rows.sort(function (a, b) {
                const aNum = parseNumber(a.querySelector('td:first-child'));
                const bNum = parseNumber(b.querySelector('td:first-child'));
                return order === 'asc' ? aNum - bNum : bNum - aNum;
            });
            rows.forEach(function (row) { tbody.appendChild(row); });
        }

        th.addEventListener('click', function () {
            const newOrder = th.dataset.sortOrder === 'asc' ? 'desc' : 'asc';
            th.dataset.sortOrder = newOrder;
            th.setAttribute('aria-sort', newOrder === 'asc' ? 'ascending' : 'descending');
            indicator.textContent = newOrder === 'asc' ? '▲' : '▼';
            sortRows(newOrder);
        });
    });

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

    // ---- Public mobile menu ----
    const publicMenuBtn = document.getElementById('publicMenuBtn');
    const publicNav = document.getElementById('publicNav');
    const publicNavOverlay = document.getElementById('publicNavOverlay');

    function openPublicNav() {
        if (!publicMenuBtn || !publicNav) return;
        publicMenuBtn.classList.add('is-open');
        publicMenuBtn.setAttribute('aria-expanded', 'true');
        publicNav.classList.add('open');
        if (publicNavOverlay) publicNavOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closePublicNav() {
        if (!publicMenuBtn || !publicNav) return;
        publicMenuBtn.classList.remove('is-open');
        publicMenuBtn.setAttribute('aria-expanded', 'false');
        publicNav.classList.remove('open');
        if (publicNavOverlay) publicNavOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (publicMenuBtn && publicNav) {
        publicMenuBtn.addEventListener('click', function () {
            if (publicNav.classList.contains('open')) {
                closePublicNav();
            } else {
                openPublicNav();
            }
        });

        publicNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 900) closePublicNav();
            });
        });
    }

    if (publicNavOverlay) {
        publicNavOverlay.addEventListener('click', closePublicNav);
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closePublicNav();
        }
    });

    // ---- Hero Slider ----
    const heroSlider = document.getElementById('heroSlider');
    const heroSlides = document.querySelectorAll('.hero-slide');
    const heroDots = document.querySelectorAll('.hero-dot');
    const heroPrev = document.getElementById('heroPrev');
    const heroNext = document.getElementById('heroNext');

    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        if (!heroSlides.length) return;
        heroSlides.forEach(slide => slide.classList.remove('active'));
        heroDots.forEach(dot => dot.classList.remove('active'));

        heroSlides[index].classList.add('active');
        if (heroDots[index]) {
            heroDots[index].classList.add('active');
        }
        currentSlide = index;
    }

    function nextSlide() {
        const nextIndex = (currentSlide + 1) % heroSlides.length;
        showSlide(nextIndex);
    }

    function prevSlide() {
        const prevIndex = (currentSlide - 1 + heroSlides.length) % heroSlides.length;
        showSlide(prevIndex);
    }

    function startAutoSlide() {
        stopAutoSlide();
        slideInterval = setInterval(nextSlide, 3000);
    }

    function stopAutoSlide() {
        clearInterval(slideInterval);
    }

    if (heroSlider) {
        // Navigation button events
        if (heroPrev) {
            heroPrev.addEventListener('click', function() {
                prevSlide();
                startAutoSlide();
            });
        }

        if (heroNext) {
            heroNext.addEventListener('click', function() {
                nextSlide();
                startAutoSlide();
            });
        }

        // Dot navigation
        heroDots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                showSlide(index);
                startAutoSlide();
            });
        });

        // Pause on hover
        heroSlider.addEventListener('mouseenter', stopAutoSlide);
        heroSlider.addEventListener('mouseleave', startAutoSlide);

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                stopAutoSlide();
            } else {
                startAutoSlide();
            }
        });

        // Start auto sliding
        startAutoSlide();
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
