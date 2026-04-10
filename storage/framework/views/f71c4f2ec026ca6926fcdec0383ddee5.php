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
        :root {
            --color-primary: #37AA05;
            --color-primary-dark: #1a5c00;
            --color-primary-light: #5fc42e;
            --color-neutral-900: #111827;
            --color-neutral-800: #1f2937;
            --color-neutral-700: #374151;
            --color-neutral-600: #4b5563;
            --color-neutral-300: #d1d5db;
            --color-neutral-100: #f3f4f6;
            --color-neutral-50: #f9fafb;
            --color-white: #ffffff;
            --color-accent: #d4af37;
        }

        * {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", sans-serif;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--color-neutral-300);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--color-neutral-600);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--color-neutral-300) transparent;
        }

        /* Sidebar - Modern Glassmorphism */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            background: linear-gradient(135deg, rgba(26, 92, 0, 0.95) 0%, rgba(17, 24, 39, 0.93) 100%);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            z-index: 50;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 260px;
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
            backdrop-filter: blur(4px);
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar.collapsed .sidebar-logo {
            padding: 1rem;
            justify-content: center;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: white;
            transition: opacity 0.3s ease-in-out, width 0.3s ease-in-out;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        .toggle-icon {
            font-size: 1rem;
            color: var(--color-primary-light);
            transition: transform 0.3s ease-in-out;
            flex-shrink: 0;
        }

        .sidebar.collapsed #manager-approval-badge-dot {
            display: block !important;
        }

        #manager-approval-badge-dot {
            display: none;
        }
        
        .sidebar.collapsed #manager-approval-badge-dot.hidden {
            display: block !important;
        }

        #expandIcon {
            display: none;
            font-size: 1.25rem;
            color: var(--color-primary-light);
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
            gap: 0.25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            margin: 0 0.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.875rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(95, 196, 46, 0.2), transparent);
            opacity: 0;
            border-radius: 0.75rem;
            transition: opacity 0.2s ease;
            pointer-events: none;
            z-index: -1;
        }

        .nav-link:hover {
            background: rgba(95, 196, 46, 0.12);
            color: white;
            border-color: rgba(95, 196, 46, 0.3);
        }

        .nav-link:hover::before {
            opacity: 1;
        }

        .nav-link span {
            transition: opacity 0.3s ease-in-out, width 0.3s ease-in-out;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-link.active {
            background: rgba(95, 196, 46, 0.2) !important;
            color: white !important;
            border-color: rgba(95, 196, 46, 0.4) !important;
            font-weight: 600 !important;
        }

        .nav-link.active::before {
            opacity: 1 !important;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.875rem;
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
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .user-info > * {
            transition: opacity 0.3s ease-in-out, height 0.3s ease-in-out;
            overflow: hidden;
        }

        .user-info p:first-child {
            font-weight: 600;
            color: white;
            font-size: 0.95rem;
        }

        .user-info p:last-child {
            color: var(--color-primary-light);
            font-size: 0.8rem;
            margin-top: 0.25rem;
            text-transform: capitalize;
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
            justify-content: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            background: linear-gradient(135deg, var(--color-accent) 0%, #c9a626 100%);
            border: none;
            border-radius: 0.75rem;
            color: var(--color-neutral-900);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
            width: calc(100% - 1rem);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #e6c200 0%, #d9a300 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.35);
        }

        .logout-btn:active {
            transform: translateY(0);
        }

        .sidebar.collapsed .logout-btn {
            padding: 0.875rem 0.5rem;
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
            margin-left: 260px;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.sidebar-collapsed {
            margin-left: 80px;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 260px !important;
            }

            .sidebar.collapsed {
                width: 260px !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .main-content.sidebar-collapsed {
                margin-left: 0 !important;
            }
        }

        /* Close Button */
        .vnb-floating-modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            color: var(--color-neutral-700);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 60;
            transition: all 0.2s ease;
        }

        .vnb-floating-modal-close:hover {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: scale(1.05);
        }

        .vnb-floating-modal-close:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        /* Animations */
        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes pulse-subtle {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Card Glass Effect */
        .card-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.08);
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(55, 170, 5, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(55, 170, 5, 0.35);
        }

        .btn-secondary {
            background: rgba(55, 170, 5, 0.1);
            color: var(--color-primary);
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            border: 1px solid rgba(55, 170, 5, 0.2);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background: rgba(55, 170, 5, 0.15);
            border-color: rgba(55, 170, 5, 0.4);
        }

        /* Table Styles - Modern Design */
        .table-container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 0.75rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.08);
            overflow: hidden;
        }

        .table-modern {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .table-modern thead {
            background: linear-gradient(135deg, rgba(55, 170, 5, 0.15) 0%, rgba(95, 196, 46, 0.1) 100%);
            border-bottom: 2px solid rgba(55, 170, 5, 0.2);
        }

        .table-modern thead th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--color-primary-dark);
            letter-spacing: 0.5px;
        }

        .table-modern tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: rgba(55, 170, 5, 0.08);
        }

        .table-modern tbody tr:last-child {
            border-bottom: none;
        }

        .table-modern tbody td {
            padding: 0.875rem 1rem;
            color: var(--color-neutral-800);
        }

        /* Badge Status */
        .badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-success {
            background: rgba(34, 197, 94, 0.15);
            color: #16a34a;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .badge-warning {
            background: rgba(202, 138, 4, 0.15);
            color: #a16207;
            border: 1px solid rgba(202, 138, 4, 0.3);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-info {
            background: rgba(59, 130, 246, 0.15);
            color: #1d4ed8;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .badge-primary {
            background: rgba(55, 170, 5, 0.15);
            color: var(--color-primary-dark);
            border: 1px solid rgba(55, 170, 5, 0.3);
        }

        /* Table Actions */
        .table-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .table-action-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            border: 1px solid rgba(55, 170, 5, 0.3);
            background: transparent;
            color: var(--color-primary);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .table-action-btn:hover {
            background: rgba(55, 170, 5, 0.1);
            border-color: var(--color-primary);
        }
    </style>

</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 flex flex-col min-h-screen text-gray-900">
    <?php $role = Auth::user()->getRoleNames()->first(); ?>
    
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay hidden"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <!-- Logo - Toggle Button -->
        <div class="sidebar-logo" id="sidebarToggle" title="Click to collapse/expand sidebar">
            <span class="logo-text">VnB Platform</span>
            <i class="fas fa-chevron-left toggle-icon"></i>
        </div>

        <!-- User Info -->
        <div class="user-info">
            <p class="font-semibold text-sm"><?php echo e(Auth::user()->name); ?></p>
            <p class="text-xs capitalize" style="color: #D0EC98;"><?php echo e(str_replace('_',' ',$role)); ?></p>
        </div>

        <!-- Navigation Links -->
        <nav>
            <a href="/" class="nav-link <?php echo e(request()->is('/') ? 'active' : ''); ?>" title="Dashboard">
                <i class="fas fa-chart-line w-5 flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>

            <?php if(in_array($role, ['admin','pcx_manager'])): ?>
            <a href="/intercomm" class="nav-link <?php echo e(request()->is('intercomm*') ? 'active' : ''); ?>" title="Manage Intercomm">
                <i class="fas fa-users-cog w-5 flex-shrink-0"></i>
                <span>Manage Intercomm</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','pcx_manager','intercomm'])): ?>
            <a href="/vnb-framework" class="nav-link <?php echo e(request()->is('vnb-framework*') ? 'active' : ''); ?>" title="V&B Framework">
                <i class="fas fa-layer-group w-5 flex-shrink-0"></i>
                <span>V&B Framework</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','intercomm','pcx_manager'])): ?>
            <a href="/managers" class="nav-link <?php echo e(request()->is('managers*') ? 'active' : ''); ?>" title="Manager">
                <i class="fas fa-user-tie w-5 flex-shrink-0"></i>
                <span>Manager</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','intercomm','pcx_manager'])): ?>
            <a href="/employees" class="nav-link <?php echo e(request()->is('employees*') ? 'active' : ''); ?>" title="Employee">
                <i class="fas fa-user-graduate w-5 flex-shrink-0"></i>
                <span>Employee</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['manager'])): ?>
            <a href="/manager/employees" class="nav-link <?php echo e(request()->is('manager/employees*') ? 'active' : ''); ?>" title="Employee">
                <i class="fas fa-user-graduate w-5 flex-shrink-0"></i>
                <span>Employee</span>
            </a>
            <a href="/manager/approval-requests" class="nav-link <?php echo e(request()->is('manager/approval-requests*') ? 'active' : ''); ?>" title="Approval Request">
                <div style="position: relative; display: inline-block;">
                    <i class="fas fa-clipboard-check w-5 flex-shrink-0"></i>
                    <span id="manager-approval-badge-dot" class="absolute w-2 h-2 rounded-full bg-red-600" style="display: none; top: -4px; right: -4px; z-index: 10;"></span>
                </div>
                <span>Approval Request</span>
                <span id="manager-approval-badge" class="ml-auto w-5 h-5 rounded-full text-xs font-bold flex items-center justify-center hidden" style="background-color: white; color: white; min-width: unset; font-size: 10px;">0</span>
            </a>
            <a href="/my-account/profile" class="nav-link <?php echo e(request()->is('my-account/profile*') ? 'active' : ''); ?>" title="Akun Saya">
                <i class="fas fa-user-circle w-5 flex-shrink-0"></i>
                <span>Akun Saya</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','employee'])): ?>
            <a href="/vnb-plans" class="nav-link <?php echo e(request()->is('vnb-plans*') ? 'active' : ''); ?>" title="Planning">
                <i class="fas fa-clipboard-list w-5 flex-shrink-0"></i>
                <span>Planning</span>
            </a>
            <a href="/vnb-activity" class="nav-link <?php echo e(request()->is('vnb-activity*') ? 'active' : ''); ?>" title="Aktivitas">
                <i class="fas fa-tasks w-5 flex-shrink-0"></i>
                <span>Aktivitas</span>
            </a>
            <a href="/my-account/profile" class="nav-link <?php echo e(request()->is('my-account/profile*') ? 'active' : ''); ?>" title="Akun Saya">
                <i class="fas fa-user-circle w-5 flex-shrink-0"></i>
                <span>Akun Saya</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin'])): ?>
            <a href="/review-activity" class="nav-link <?php echo e(request()->is('review-activity*') ? 'active' : ''); ?>" title="Review">
                <i class="fas fa-eye w-5 flex-shrink-0"></i>
                <span>Review</span>
            </a>
            <?php endif; ?>

            <?php if(in_array($role, ['admin','intercomm','pcx_manager'])): ?>
            <a href="/master-data" class="nav-link <?php echo e(request()->is('master-data*') ? 'active' : ''); ?>" title="Master Data">
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
    <main id="mainContent" class="main-content px-6 py-8 sm:px-8 lg:px-12 flex-grow max-w-full">
        <?php if(session('success')): ?>
        <div class="mb-6 px-4 py-3 rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 shadow-sm animate-slide-in">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-600"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="bg-white/70 backdrop-filter backdrop-blur-xl border-t border-gray-200/50 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-6">
            <p class="text-gray-500 text-center text-xs font-medium">© 2026 Wismilak VnB Platform • Powered by Modern Tech Stack</p>
        </div>
    </footer>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] hidden flex items-center justify-center animate-fade-in">
        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden animate-slide-in">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 px-6 py-4 border-b border-yellow-100">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <h3 id="confirmTitle" class="text-lg font-bold text-gray-900">Konfirmasi</h3>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-5">
                <p id="confirmMessage" class="text-gray-700 text-sm leading-relaxed"></p>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
                <button id="confirmCancel" class="px-4 py-2.5 rounded-lg bg-white border border-gray-300 text-gray-700 font-medium text-sm hover:bg-gray-100 transition-all duration-200 cursor-pointer">
                    Batal
                </button>
                <button id="confirmOK" class="px-4 py-2.5 rounded-lg bg-gradient-to-r from-yellow-500 to-amber-500 text-white font-medium text-sm hover:shadow-lg hover:scale-105 transition-all duration-200 cursor-pointer">
                    <i class="fas fa-check mr-2"></i>Lanjutkan
                </button>
            </div>
        </div>
    </div>

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

    // Styled Confirmation Modal
    function showConfirm(message, title = 'Konfirmasi') {
        return new Promise((resolve) => {
            const modal = document.getElementById('confirmModal');
            const titleEl = document.getElementById('confirmTitle');
            const messageEl = document.getElementById('confirmMessage');
            const okBtn = document.getElementById('confirmOK');
            const cancelBtn = document.getElementById('confirmCancel');

            // Set content
            titleEl.textContent = title;
            messageEl.textContent = message;

            // Show modal
            modal.classList.remove('hidden');

            // Handle OK button
            const handleOK = () => {
                cleanup();
                resolve(true);
            };

            // Handle Cancel button
            const handleCancel = () => {
                cleanup();
                resolve(false);
            };

            // Handle Escape key
            const handleEscape = (e) => {
                if (e.key === 'Escape') {
                    handleCancel();
                }
            };

            // Cleanup function
            const cleanup = () => {
                modal.classList.add('hidden');
                okBtn.removeEventListener('click', handleOK);
                cancelBtn.removeEventListener('click', handleCancel);
                document.removeEventListener('keydown', handleEscape);
            };

            // Add event listeners
            okBtn.addEventListener('click', handleOK);
            cancelBtn.addEventListener('click', handleCancel);
            document.addEventListener('keydown', handleEscape);

            // Focus OK button for keyboard accessibility
            okBtn.focus();
        });
    }

    // Sidebar Collapse/Expand Logic
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');

    // Function to handle badge dot visibility based on sidebar state
    function updateBadgeDotVisibility() {
        const badgeDot = document.getElementById('manager-approval-badge-dot');
        if (!badgeDot) {
            console.warn('Badge dot element not found');
            return;
        }
        
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) {
            console.warn('Sidebar element not found');
            return;
        }
        
        const isSidebarCollapsed = sidebar.classList.contains('collapsed');
        const badgeElement = document.getElementById('manager-approval-badge');
        const hasRequests = badgeElement && !badgeElement.classList.contains('hidden');
        
        console.log('===Badge Dot Debug===');
        console.log('Sidebar collapsed:', isSidebarCollapsed);
        console.log('Has requests:', hasRequests);
        console.log('Badge element:', badgeElement);
        console.log('Badge text:', badgeElement?.textContent);
        
        // Show dot only if sidebar collapsed AND has requests
        if (isSidebarCollapsed && hasRequests) {
            badgeDot.style.display = 'block';
            console.log('Badge dot SHOWN');
        } else {
            badgeDot.style.display = 'none';
            console.log('Badge dot HIDDEN');
        }
    }

    // Toggle sidebar on logo click
    sidebarToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        if (window.innerWidth >= 1024) {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            updateBadgeDotVisibility();
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
        const badgeDot = document.getElementById('manager-approval-badge-dot');
        
        if (!badge) {
            console.warn('manager-approval-badge element not found');
            return;
        }

        try {
            const res = await apiGet('/api/manager/approval-requests');
            console.log('Approval requests response:', res);
            
            if (!(res && res.success === true && res.data)) {
                console.warn('Approval requests failed or no data:', res);
                badge.classList.add('hidden');
                if (badgeDot) badgeDot.classList.add('hidden');
                return;
            }

            const myApprovals = res.data?.my_approvals || [];
            const monitoring = res.data?.monitoring || [];
            const hasAnyRequests = myApprovals.length > 0 || monitoring.length > 0;
            
            console.log('My approvals:', myApprovals.length, 'Monitoring:', monitoring.length);
            
            if (hasAnyRequests) {
                // Always show: approvals count (or 0 if only monitoring)
                const count = myApprovals.length;
                badge.textContent = count > 99 ? '99+' : String(count);
                
                // Dynamic background color: white if 0, red if >0
                if (count === 0) {
                    badge.style.backgroundColor = 'white';
                    badge.style.color = 'white';
                } else {
                    badge.style.backgroundColor = '#dc2626';
                    badge.style.color = 'white';
                }
                badge.classList.remove('hidden');
                
                // Update badge dot visibility
                updateBadgeDotVisibility();
            } else {
                badge.classList.add('hidden');
                if (badgeDot) badgeDot.classList.add('hidden');
            }
        } catch (e) {
            console.error('Error hydrating manager approval badge:', e);
        }
    }

    // Initial hydration
    hydrateManagerApprovalBadge();
    
    // Update badge dot visibility on init
    updateBadgeDotVisibility();
    
    // Polling interval - update badge every 5 seconds
    setInterval(hydrateManagerApprovalBadge, 5000);
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/layouts/app.blade.php ENDPATH**/ ?>