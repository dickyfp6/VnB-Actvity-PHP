<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'VnB Platform - Wismilak'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Sidebar Transitions */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            background: #144600;
            color: white;
            z-index: 50;
            overflow-y: auto;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            transition: width 0.3s ease-in-out;
            width: 256px;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 1;
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar-overlay.hidden {
            display: none;
            opacity: 0;
        }

        /* Logo Button */
        .sidebar-logo {
            cursor: pointer;
            user-select: none;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(55, 170, 5, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: padding 0.3s ease-in-out;
        }

        .sidebar.collapsed .sidebar-logo {
            padding: 1rem;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
            transition: opacity 0.3s ease-in-out, width 0.3s ease-in-out;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        #expandIcon {
            display: none;
            font-size: 1.5rem;
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar.collapsed #expandIcon {
            display: block;
        }

        /* Navigation */
        .sidebar nav {
            padding: 1.5rem 0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            margin: 0 0.5rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s ease, color 0.2s ease;
            text-decoration: none;
            color: white;
            font-size: 0.875rem;
        }

        .nav-link:hover {
            background-color: #37AA05;
        }

        .nav-link span {
            transition: opacity 0.3s ease-in-out, width 0.3s ease-in-out;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.75rem;
            margin: 0.25rem auto;
        }

        .sidebar.collapsed .nav-link span {
            opacity: 0;
            width: 0;
            display: none;
        }

        /* User Info */
        .user-info {
            padding: 1rem;
            border-bottom: 1px solid rgba(55, 170, 5, 0.2);
            border-top: 1px solid rgba(55, 170, 5, 0.2);
        }

        .user-info > * {
            transition: opacity 0.3s ease-in-out, height 0.3s ease-in-out;
            overflow: hidden;
        }

        .sidebar.collapsed .user-info {
            padding: 0.5rem;
            text-align: center;
        }

        .sidebar.collapsed .user-info > * {
            opacity: 0;
            height: 0;
            display: none;
        }

        /* Logout Button */
        .logout-btn {
            position: absolute;
            bottom: 1rem;
            left: 0.5rem;
            right: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: #C4A901;
            border: none;
            border-radius: 0.5rem;
            color: white;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            transition: background-color 0.2s ease;
            width: calc(100% - 1rem);
        }

        .logout-btn:hover {
            background: #9A7D00;
        }

        .sidebar.collapsed .logout-btn {
            padding: 0.75rem 0.25rem;
        }

        .logout-btn span {
            transition: opacity 0.3s ease-in-out, width 0.3s ease-in-out;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar.collapsed .logout-btn span {
            opacity: 0;
            width: 0;
            display: none;
        }

        /* Main Content */
        .main-content {
            margin-left: 256px;
            transition: margin-left 0.3s ease-in-out;
        }

        .main-content.sidebar-collapsed {
            margin-left: 80px;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 256px !important;
            }

            .sidebar.collapsed {
                width: 256px !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .main-content.sidebar-collapsed {
                margin-left: 0 !important;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }

        *:hover {
            scrollbar-color: #9ca3af transparent;
        }

        .vnb-floating-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 9999px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #111827;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 60;
        }

        .vnb-floating-modal-close:hover {
            background: #f9fafb;
        }

        .vnb-floating-modal-close:focus-visible {
            outline: 2px solid #144600;
            outline-offset: 2px;
        }

        @media (max-width: 640px) {
            .vnb-floating-modal-close {
                top: 0.75rem;
                right: 0.75rem;
            }
        }
    </style>

</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php $role = Auth::user()->getRoleNames()->first(); ?>
    
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay hidden"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <!-- Logo - Toggle Button -->
        <div class="sidebar-logo" id="sidebarToggle" title="Click to collapse/expand">
            <span class="logo-text">VnB</span>
            <i class="fas fa-chevron-right" id="expandIcon"></i>
        </div>

        <!-- User Info -->
        <div class="user-info">
            <p class="font-semibold text-sm"><?php echo e(Auth::user()->name); ?></p>
            <p class="text-xs capitalize" style="color: #D0EC98;"><?php echo e(str_replace('_',' ',$role)); ?></p>
        </div>

        <!-- Navigation Links -->
        <nav>
            <a href="/" class="nav-link" title="Dashboard">
                <i class="fas fa-chart-line w-5 flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>

            <?php if(in_array($role, ['admin','pcx_manager'])): ?>
            <a href="/intercomm" class="nav-link" title="Manage Intercomm">
                <i class="fas fa-users-cog w-5 flex-shrink-0"></i>
                <span>Manage Intercomm</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','pcx_manager','intercomm'])): ?>
            <a href="/vnb-framework" class="nav-link" title="V&B Framework">
                <i class="fas fa-layer-group w-5 flex-shrink-0"></i>
                <span>V&B Framework</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','intercomm','pcx_manager'])): ?>
            <a href="/managers" class="nav-link" title="Manager">
                <i class="fas fa-user-tie w-5 flex-shrink-0"></i>
                <span>Manager</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','intercomm','pcx_manager'])): ?>
            <a href="/employees" class="nav-link" title="New Hire">
                <i class="fas fa-user-graduate w-5 flex-shrink-0"></i>
                <span>New Hire</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['manager'])): ?>
            <a href="/manager/new-hires" class="nav-link" title="New Hire">
                <i class="fas fa-user-graduate w-5 flex-shrink-0"></i>
                <span>New Hire</span>
            </a>
            <a href="/manager/approval-requests" class="nav-link" title="Approval Request">
                <i class="fas fa-file-check w-5 flex-shrink-0"></i>
                <span>Approval Request</span>
                <span id="manager-approval-badge" class="ml-auto px-2 py-0.5 rounded-full text-xs bg-red-600 text-white hidden">0</span>
            </a>
            <a href="/my-account/profile" class="nav-link" title="Akun Saya">
                <i class="fas fa-user-circle w-5 flex-shrink-0"></i>
                <span>Akun Saya</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','new_hire'])): ?>
            <a href="/vnb-plans" class="nav-link" title="Planning">
                <i class="fas fa-clipboard-list w-5 flex-shrink-0"></i>
                <span>Planning</span>
            </a>
            <a href="/vnb-activity" class="nav-link" title="Aktivitas">
                <i class="fas fa-tasks w-5 flex-shrink-0"></i>
                <span>Aktivitas</span>
            </a>
            <a href="/my-account/profile" class="nav-link" title="Akun Saya">
                <i class="fas fa-user-circle w-5 flex-shrink-0"></i>
                <span>Akun Saya</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin'])): ?>
            <a href="/review-activity" class="nav-link" title="Review">
                <i class="fas fa-file-check w-5 flex-shrink-0"></i>
                <span>Review</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','intercomm','pcx_manager'])): ?>
            <a href="/master-data" class="nav-link" title="Master Data">
                <i class="fas fa-database w-5 flex-shrink-0"></i>
                <span>Master Data</span>
            </a>
            <?php endif; ?>
        </nav>

        <!-- Logout Button -->
        <form action="<?php echo e(route('logout')); ?>" method="POST" class="logout-form">
            <?php echo csrf_field(); ?>
            <button type="submit" class="logout-btn" title="Logout">
                <i class="fas fa-sign-out-alt w-5 flex-shrink-0"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

    <!-- Main Content -->
    <main id="mainContent" class="main-content px-4 py-12 sm:px-6 lg:px-8 flex-grow max-w-full">
        <?php if(session('success')): ?>
        <div class="mb-4 px-4 py-3 rounded" style="background-color: #D0EC98; color: #144600;"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4">
            <p class="text-gray-400 text-center text-xs">© 2026 Wismilak VnB Platform</p>
        </div>
    </footer>

    <script>
    // Global CSRF helper for fetch calls
    window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    async function apiGet(url) {
        try {
            const r = await fetch(url, { 
                credentials: 'include',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.csrfToken } 
            });
            if (!r.ok) {
                console.error(`API Error: ${r.status} ${r.statusText} from ${url}`);
                return { success: false, error: `HTTP ${r.status}` };
            }
            const contentType = r.headers.get('content-type');
            if (!contentType?.includes('application/json')) {
                console.error(`Invalid content-type from ${url}:`, contentType);
                const text = await r.text();
                console.error('Response body:', text.substring(0, 500));
                return { success: false, error: 'Invalid response type' };
            }
            return await r.json();
        } catch (e) {
            console.error(`API fetch error on ${url}:`, e);
            return { success: false, error: e.message };
        }
    }
    async function apiPost(url, data, method = 'POST') {
        const r = await fetch(url, {
            method, 
            credentials: 'include',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
            body: JSON.stringify(data)
        });
        return r.json();
    }
    function showAlert(msg, type = 'success') {
        const div = document.createElement('div');
        div.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded shadow-lg text-white ${type === 'success' ? 'text-white' : 'text-white'}`;
        div.style.backgroundColor = type === 'success' ? '#37AA05' : '#d32f2f';
        div.textContent = msg;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3500);
    }

    // Sidebar Collapse/Expand Logic
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Initialize sidebar state from localStorage
    const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isSidebarCollapsed && window.innerWidth >= 1024) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('sidebar-collapsed');
    }

    // Toggle sidebar on logo click
    sidebarToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        if (window.innerWidth >= 1024) {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    });

    // Close sidebar overlay on click
    sidebarOverlay.addEventListener('click', () => {
        sidebarOverlay.classList.add('hidden');
    });

    // Close sidebar when clicking on main content (mobile)
    mainContent.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
            sidebarOverlay.classList.add('hidden');
        }
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebarOverlay.classList.add('hidden');
        }
    });

    // Close sidebar when clicking nav links (mobile)
    const navLinks = sidebar.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                sidebarOverlay.classList.add('hidden');
            }
        });
    });

    function closeModalOverlay(overlay) {
        if (!overlay) return;

        const existingCloseBtn = overlay.querySelector('button[onclick*="closeModal"], button[onclick*="close-modal"], button[onclick*="close"]');
        if (existingCloseBtn && !existingCloseBtn.classList.contains('vnb-floating-modal-close')) {
            existingCloseBtn.click();
            return;
        }

        overlay.classList.add('hidden');
    }

    function setupFloatingModalCloseButtons() {
        const overlays = document.querySelectorAll('div.fixed.inset-0.bg-black.bg-opacity-50');
        overlays.forEach((overlay) => {
            if (overlay.querySelector('.vnb-floating-modal-close')) {
                return;
            }

            overlay.classList.add('vnb-popup-overlay');

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'vnb-floating-modal-close';
            closeBtn.setAttribute('aria-label', 'Tutup popup');
            closeBtn.innerHTML = '<i class="fas fa-times" aria-hidden="true"></i>';
            closeBtn.addEventListener('click', () => closeModalOverlay(overlay));

            overlay.appendChild(closeBtn);
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        const visibleOverlays = Array.from(document.querySelectorAll('div.fixed.inset-0.bg-black.bg-opacity-50'))
            .filter(el => !el.classList.contains('hidden'));

        const activeOverlay = visibleOverlays[visibleOverlays.length - 1];
        if (activeOverlay) {
            closeModalOverlay(activeOverlay);
        }
    });

    setupFloatingModalCloseButtons();

    async function hydrateManagerApprovalBadge() {
        const badge = document.getElementById('manager-approval-badge');
        if (!badge) return;

        const res = await apiGet('/api/manager/approval-summary');
        if (!(res && res.success === true && res.data)) {
            badge.classList.add('hidden');
            return;
        }

        const total = Number(res.data.total_count || 0);
        if (total > 0) {
            badge.textContent = total > 99 ? '99+' : String(total);
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    hydrateManagerApprovalBadge();
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/layouts/app.blade.php ENDPATH**/ ?>