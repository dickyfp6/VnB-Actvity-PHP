<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VnB Platform - Wismilak')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
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

        /* Sidebar - Modern Glassmorphism */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            background: linear-gradient(135deg, rgba(26, 92, 0, 0.95) 0%, rgba(17, 24, 39, 0.93) 100%);
            backdrop-filter: blur(14px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            z-index: 50;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 220px;
        }

        .sidebar.collapsed {
            width: 70px;
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

        .sidebar.collapsed #sync-pending-badge-dot {
            display: block !important;
        }

        #sync-pending-badge-dot {
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
            padding: 0.75rem 0;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            flex: 1;
            min-height: 0;
            overflow-y: auto;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 0.875rem;
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

        .nav-section-title {
            margin: 0.75rem 1rem 0.25rem;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.09em;
            color: rgba(208, 236, 152, 0.9);
        }

        .sidebar.collapsed .nav-section-title {
            display: none;
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
            padding: 0.75rem 1rem;
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

        .top-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
            position: sticky;
            top: 0.75rem;
            z-index: 35;
            pointer-events: none;
        }

        .top-navbar-left {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
            min-width: 0;
            padding-top: 0.15rem;
        }

        .top-navbar-title {
            font-size: 1.15rem;
            line-height: 1.1;
            font-weight: 800;
            color: var(--color-neutral-900);
            letter-spacing: -0.02em;
        }

        .top-navbar-subtitle {
            font-size: 0.78rem;
            color: var(--color-neutral-600);
            max-width: 52rem;
        }

        .top-navbar-right {
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
            flex-shrink: 0;
            pointer-events: auto;
        }

        .top-profile-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.625rem;
            border: 1px solid rgba(17, 24, 39, 0.08);
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(12px);
            color: var(--color-neutral-900);
            border-radius: 999px;
            padding: 0.5rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(17, 24, 39, 0.06);
        }

        .top-profile-btn .meta {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.1;
        }

        .top-profile-btn .meta .name {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--color-neutral-900);
        }

        .top-profile-btn .meta small {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--color-neutral-600);
        }

        .top-profile-btn .avatar {
            width: 1.8rem;
            height: 1.8rem;
            border-radius: 999px;
            background: linear-gradient(135deg, #37AA05 0%, #1f7a0b 100%);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .top-profile-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            min-width: 240px;
            background: white;
            border: 1px solid rgba(17, 24, 39, 0.08);
            border-radius: 0.9rem;
            box-shadow: 0 20px 40px rgba(17, 24, 39, 0.14);
            padding: 0.55rem;
            z-index: 70;
        }

        .top-navbar-shell {
            position: relative;
            min-height: 4.5rem;
            padding-right: 0.25rem;
        }

        .top-profile-menu-item {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            color: var(--color-neutral-800);
            padding: 0.6rem 0.65rem;
            border-radius: 0.6rem;
            font-size: 0.82rem;
            font-weight: 600;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .top-profile-menu-item:hover {
            background: rgba(55, 170, 5, 0.09);
            color: var(--color-primary-dark);
        }

        .top-role-select {
            width: 100%;
            border: 1.5px solid #37AA05;
            border-radius: 0.5rem;
            padding: 0.42rem 0.65rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-primary-dark);
            background: linear-gradient(135deg, #f0fdf4 0%, #f9fafb 100%);
            cursor: pointer;
            transition: all 0.2s ease;
            min-height: 34px;
            line-height: 1.1;
        }

        .top-role-select:hover {
            background: linear-gradient(135deg, #dcfce7 0%, #f3f4f6 100%);
            border-color: var(--color-primary-light);
            box-shadow: 0 4px 12px rgba(55, 170, 5, 0.15);
        }

        .top-role-select:focus {
            outline: none;
            background: linear-gradient(135deg, #dcfce7 0%, #f3f4f6 100%);
            border-color: var(--color-primary-light);
            box-shadow: 0 0 0 3px rgba(55, 170, 5, 0.1);
        }

        .top-role-select option {
            background: white;
            color: var(--color-neutral-800);
            padding: 0.6rem;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: 220px;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content.sidebar-collapsed {
            margin-left: 70px;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 220px !important;
            }

            .sidebar.collapsed {
                width: 220px !important;
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
            border-radius: 999px; /* Pill shape for cuteness */
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
            border-radius: 999px;
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
            table-layout: auto; /* let column widths follow content */
        }

        .table-modern thead {
            background: linear-gradient(135deg, rgba(55, 170, 5, 0.15) 0%, rgba(95, 196, 46, 0.1) 100%);
            border-bottom: 2px solid rgba(55, 170, 5, 0.2);
        }

        .table-modern thead th {
            padding: 1rem;
            text-align: left; /* left-align headers */
            white-space: nowrap; /* prevent headers from wrapping to 2 lines */
            font-weight: 600;
            color: var(--color-primary-dark);
            letter-spacing: 0.5px;
        }

        .table-modern thead th.table-interactive-header {
            position: relative;
            cursor: pointer;
            user-select: none;
            padding-right: 1.75rem;
            transition: color 0.15s ease, text-decoration-color 0.15s ease;
        }

        .table-modern thead th.table-interactive-header:hover {
            text-decoration: underline;
            text-decoration-thickness: 1.5px;
            text-underline-offset: 0.2em;
        }

        .table-modern thead th.table-interactive-header[data-sort-state="asc"]::after {
            content: '▲';
            color: #144600;
        }

        .table-modern thead th.table-interactive-header[data-sort-state="desc"]::after {
            content: '▼';
            color: #144600;
        }

        .table-modern thead th.table-interactive-header[data-filter-active="true"] {
            color: #144600;
        }

        .table-modern thead th.table-interactive-header::before {
            content: attr(data-tooltip);
            position: absolute;
            left: 50%;
            top: calc(100% + 0.45rem);
            transform: translateX(-50%) translateY(0.25rem);
            z-index: 25;
            min-width: 12rem;
            max-width: 18rem;
            padding: 0.45rem 0.65rem;
            border-radius: 0.5rem;
            background: rgba(17, 24, 39, 0.96);
            color: #fff;
            font-size: 0.72rem;
            line-height: 1.25;
            white-space: normal;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s ease, transform 0.15s ease;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
        }

        .table-modern thead th.table-interactive-header:hover::before {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .table-filter-modal-panel {
            max-height: min(72vh, 44rem);
        }

        .table-filter-option {
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .table-filter-option:hover {
            background: rgba(55, 170, 5, 0.08);
            border-color: rgba(55, 170, 5, 0.35);
        }

        .table-modern tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: rgba(55, 170, 5, 0.08); /* Soft green hover */
        }

        .table-modern tbody tr:last-child {
            border-bottom: none;
        }

        .table-modern tbody td {
            padding: 0.875rem 1rem;
            color: var(--color-neutral-800);
        }

        /* Center any button inside table cells without affecting other cell content */
        .table-modern td:has(> button),
        .table-modern th:has(> button) {
            text-align: center;
        }

        .table-modern td:has(> button) > button,
        .table-modern th:has(> button) > button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        /* Modern table scrollbar: thin, subtle, and only visible on hover */
        .table-scroll-host,
        .overflow-x-auto:has(> table.table-modern),
        .overflow-auto:has(> table.table-modern),
        .overflow-x-auto:has(table.table-modern),
        .overflow-auto:has(table.table-modern) {
            scrollbar-width: thin;
            scrollbar-color: transparent transparent;
        }

        .table-scroll-host::-webkit-scrollbar,
        .overflow-x-auto:has(> table.table-modern)::-webkit-scrollbar,
        .overflow-auto:has(> table.table-modern)::-webkit-scrollbar,
        .overflow-x-auto:has(table.table-modern)::-webkit-scrollbar,
        .overflow-auto:has(table.table-modern)::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .table-scroll-host::-webkit-scrollbar-track,
        .overflow-x-auto:has(> table.table-modern)::-webkit-scrollbar-track,
        .overflow-auto:has(> table.table-modern)::-webkit-scrollbar-track,
        .overflow-x-auto:has(table.table-modern)::-webkit-scrollbar-track,
        .overflow-auto:has(table.table-modern)::-webkit-scrollbar-track {
            background: transparent;
        }

        .table-scroll-host::-webkit-scrollbar-thumb,
        .overflow-x-auto:has(> table.table-modern)::-webkit-scrollbar-thumb,
        .overflow-auto:has(> table.table-modern)::-webkit-scrollbar-thumb,
        .overflow-x-auto:has(table.table-modern)::-webkit-scrollbar-thumb,
        .overflow-auto:has(table.table-modern)::-webkit-scrollbar-thumb {
            background: transparent;
            border-radius: 999px;
            transition: background 0.2s ease;
        }

        .table-scroll-host:hover,
        .overflow-x-auto:has(> table.table-modern):hover,
        .overflow-auto:has(> table.table-modern):hover,
        .overflow-x-auto:has(table.table-modern):hover,
        .overflow-auto:has(table.table-modern):hover {
            scrollbar-color: rgba(20, 70, 0, 0.35) transparent;
        }

        .table-scroll-host:hover::-webkit-scrollbar-thumb,
        .overflow-x-auto:has(> table.table-modern):hover::-webkit-scrollbar-thumb,
        .overflow-auto:has(> table.table-modern):hover::-webkit-scrollbar-thumb,
        .overflow-x-auto:has(table.table-modern):hover::-webkit-scrollbar-thumb,
        .overflow-auto:has(table.table-modern):hover::-webkit-scrollbar-thumb {
            background: rgba(20, 70, 0, 0.28);
        }

        .table-scroll-host:hover::-webkit-scrollbar-thumb:hover,
        .overflow-x-auto:has(> table.table-modern):hover::-webkit-scrollbar-thumb:hover,
        .overflow-auto:has(> table.table-modern):hover::-webkit-scrollbar-thumb:hover,
        .overflow-x-auto:has(table.table-modern):hover::-webkit-scrollbar-thumb:hover,
        .overflow-auto:has(table.table-modern):hover::-webkit-scrollbar-thumb:hover {
            background: rgba(20, 70, 0, 0.4);
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
            border-radius: 999px;
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
<body class="bg-gradient-to-br from-gray-50 via-white to-gray-100 flex flex-col min-h-screen text-gray-900 font-sans">
    @php
        $user = Auth::user();
        $activeRole = \App\Support\ActiveRoleContext::current(request(), $user);
        $availableRoles = \App\Support\ActiveRoleContext::availableRoles($user);
        if ($user?->email === 'dev@vnb.id') {
            $availableRoles = array_values(array_unique(array_merge($availableRoles, [
                'direktur_utama',
                'pcx_manager',
                'intercomm',
                'manager',
                'employee',
            ])));
        }
    @endphp
    
    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay hidden"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <!-- Logo - Toggle Button -->
        <div class="sidebar-logo" id="sidebarToggle" title="Click to collapse/expand sidebar">
            <span class="logo-text">VnB Platform</span>
            <i class="fas fa-bars toggle-icon"></i>
        </div>

        <!-- Navigation Links -->
        <nav>
            <!-- BERANDA Section -->
            <p class="nav-section-title">BERANDA</p>
            <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}" title="Dashboard">
                <i class="fas fa-chart-line w-5 flex-shrink-0"></i>
                <span>Dashboard</span>
            </a>

            @if(in_array($activeRole, ['intercomm', 'pcx_manager']))
            <a href="/sinkronisasi" class="nav-link {{ request()->is('sinkronisasi*') ? 'active' : '' }}" title="Sinkronisasi Data">
                <div style="position: relative; display: inline-block;">
                    <i class="fas fa-sync-alt w-5 flex-shrink-0"></i>
                    <span id="sync-pending-badge-dot" class="absolute w-2 h-2 rounded-full bg-red-600" style="display: none; top: 0; right: 0; z-index: 10;"></span>
                </div>
                <span>Sinkronisasi Data</span>
                <span id="sync-pending-badge" class="ml-auto w-5 h-5 rounded-full text-xs font-bold flex items-center justify-center hidden" style="background-color: #dc2626; color: white; min-width: unset; font-size: 10px;"><i class="fas fa-sync-alt"></i></span>
            </a>
            @endif

            @if(in_array($activeRole, ['intercomm', 'pcx_manager', 'manager']))
            <a href="/employees" class="nav-link {{ request()->is('employees*') ? 'active' : '' }}" title="Manage Employee">
                <i class="fas fa-user-graduate w-5 flex-shrink-0"></i>
                <span>Employees</span>
            </a>
            @endif

            @if($activeRole === 'pcx_manager')
            <a href="/intercomm" class="nav-link {{ request()->is('intercomm*') ? 'active' : '' }}" title="Manage Intercomm">
                <i class="fas fa-users-cog w-5 flex-shrink-0"></i>
                <span>Intercomm</span>
            </a>
            @endif

            @if(in_array($activeRole, ['intercomm', 'pcx_manager']))
            <a href="/managers" class="nav-link {{ request()->is('managers*') ? 'active' : '' }}" title="Manage Manager">
                <i class="fas fa-user-tie w-5 flex-shrink-0"></i>
                <span>Managers</span>
            </a>
            {{-- Master Data hidden temporarily --}}
            {{-- <a href="/master-data" class="nav-link {{ request()->is('master-data*') ? 'active' : '' }}" title="Master Data">
                <i class="fas fa-database w-5 flex-shrink-0"></i>
                <span>Master Data</span>
            </a> --}}
            @endif

            <!-- STAR Section -->
            <p class="nav-section-title">STAR</p>
            <a href="/star/schema" class="nav-link {{ request()->is('star/schema*') ? 'active' : '' }}" title="STAR Schema">
                <i class="fas fa-layer-group w-5 flex-shrink-0"></i>
                <span>Schema</span>
            </a>
            <a href="/star/recognition" class="nav-link {{ request()->is('star/recognition*') ? 'active' : '' }}" title="Recognition">
                <i class="fas fa-trophy w-5 flex-shrink-0"></i>
                <span>Recognition</span>
            </a>
            <a href="/star/achievements" class="nav-link {{ request()->is('star/achievements*') ? 'active' : '' }}" title="Achievements">
                <i class="fas fa-star w-5 flex-shrink-0"></i>
                <span>Achievements</span>
            </a>

            @if(in_array($activeRole, ['pcx_manager', 'intercomm', 'direktur_utama']))
            <a href="/star/star-approval" class="nav-link {{ request()->is('star/star-approval*') ? 'active' : '' }}" title="Approval">
                <i class="fas fa-clipboard-check w-5 flex-shrink-0"></i>
                <span>Approval</span>
            </a>
            @endif

            <!-- VNB ACTIVITY Section -->
            <p class="nav-section-title">VNB ACTIVITY</p>

            @if(in_array($activeRole, ['intercomm', 'pcx_manager']))
            <a href="/vnb/framework" class="nav-link {{ request()->is('vnb/framework*') ? 'active' : '' }}" title="V&B Framework">
                <i class="fas fa-layer-group w-5 flex-shrink-0"></i>
                <span>Framework</span>
            </a>
            @endif

            @if($activeRole === 'employee')
            <a href="/vnb/plan" class="nav-link {{ request()->is('vnb/plan*') ? 'active' : '' }}" title="Planning">
                <i class="fas fa-clipboard-list w-5 flex-shrink-0"></i>
                <span>Plans</span>
            </a>
            <a href="/vnb/activity" class="nav-link {{ request()->is('vnb/activity*') ? 'active' : '' }}" title="VnB Activity">
                <i class="fas fa-tasks w-5 flex-shrink-0"></i>
                <span>Activity</span>
            </a>
            @endif

            @if(in_array($activeRole, ['intercomm', 'pcx_manager']))
            <a href="/vnb/participants" class="nav-link {{ request()->is('vnb/participants*') ? 'active' : '' }}" title="Participants">
                <i class="fas fa-users w-5 flex-shrink-0"></i>
                <span>Participants</span>
            </a>
            @endif

            @if($activeRole === 'manager')
            <a href="/vnb/vnb-approval" class="nav-link {{ request()->is('vnb/vnb-approval*') ? 'active' : '' }}" title="Activity Approval">
                <div style="position: relative; display: inline-block;">
                    <i class="fas fa-clipboard-check w-5 flex-shrink-0"></i>
                    <span id="manager-approval-badge-dot" class="absolute w-2 h-2 rounded-full bg-red-600" style="display: none; top: -4px; right: -4px; z-index: 10;"></span>
                </div>
                <span>Approval</span>
                <span id="manager-approval-badge" class="ml-auto w-5 h-5 rounded-full text-xs font-bold flex items-center justify-center hidden" style="background-color: white; color: white; min-width: unset; font-size: 10px;">0</span>
            </a>
            @endif

            <!-- Sidebar ends here; profile access is available from the top menu -->
        </nav>

    </div>

    <!-- Main Content -->
    <main id="mainContent" class="main-content px-4 py-4 sm:px-6 lg:px-8 flex-grow max-w-full">
        <div class="top-navbar-shell">
            @php
                $roleLabels = [
                    'direktur_utama' => 'Direktur Utama',
                    'pcx_manager' => 'PCX Manager',
                    'intercomm' => 'Intercomm',
                    'manager' => 'Manager',
                    'employee' => 'Employee',
                ];
                $topbarTitle = trim($__env->yieldContent('page_title'));
                if ($topbarTitle === '') {
                    $topbarTitle = trim(strip_tags($__env->yieldContent('title')));
                }
                $topbarSubtitle = trim($__env->yieldContent('page_subtitle'));
            @endphp
            <div class="top-navbar">
                <div class="top-navbar-left">
                    <div class="top-navbar-title">{{ $topbarTitle ?: 'VnB Platform' }}</div>
                    @if($topbarSubtitle !== '')
                    <div class="top-navbar-subtitle">{{ $topbarSubtitle }}</div>
                    @endif
                </div>

                <div class="top-navbar-right">
                    <div id="topProfileMenuWrapper" class="relative">
                        <button id="topProfileMenuBtn" class="top-profile-btn" type="button" aria-haspopup="menu" aria-expanded="false">
                            <span class="avatar"><i class="fas fa-user"></i></span>
                            <span class="meta hidden sm:inline">
                                <span class="name">{{ Auth::user()->name }}</span>
                                <small>{{ $roleLabels[$activeRole ?? ''] ?? ucwords(str_replace('_', ' ', $activeRole ?? '-')) }}</small>
                            </span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <div id="topProfileMenu" class="top-profile-menu hidden">
                            <div class="px-2 pb-2 mb-2 border-b border-gray-100">
                                <p class="text-xs font-bold text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="mt-0.5 text-xs font-semibold text-emerald-700">
                                    {{ $roleLabels[$activeRole ?? ''] ?? ucwords(str_replace('_', ' ', $activeRole ?? '-')) }}
                                </p>
                            </div>

                            @if(count($availableRoles) > 1)
                            <form action="{{ route('switch-role') }}" method="POST" class="px-2 py-1">
                                @csrf
                                <label class="text-[11px] text-gray-500 font-semibold block mb-1">Switch Account</label>
                                <select name="role" onchange="this.form.submit()" class="top-role-select">
                                    @foreach($availableRoles as $roleOption)
                                    <option value="{{ $roleOption }}" {{ $activeRole === $roleOption ? 'selected' : '' }}>
                                        {{ $roleLabels[$roleOption] ?? ucwords(str_replace('_', ' ', $roleOption)) }}
                                    </option>
                                    @endforeach
                                </select>
                            </form>
                            @endif

                            <a href="/profile/details" class="top-profile-menu-item">
                                <i class="fas fa-user-circle"></i>
                                <span>Profil Saya</span>
                            </a>

                            <form action="{{ route('logout') }}" method="POST" class="pt-1 mt-1 border-t border-gray-100">
                                @csrf
                                <button type="submit" class="top-profile-menu-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 shadow-sm animate-slide-in">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif
        @yield('content')
    </main>

    <footer class="bg-white/70 backdrop-filter backdrop-blur-xl border-t border-gray-200/50 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-6">
            <p class="text-gray-500 text-center text-xs font-medium">© 2026 Wismilak VnB Platform • Powered by Modern Tech Stack</p>
        </div>
    </footer>

    <!-- Confirmation Modal -->
    <div id="tableFilterModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[59] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 id="tableFilterTitle" class="text-lg font-bold text-gray-900">Filter Kolom</h3>
                        <p class="text-xs text-gray-500 mt-1">Pilih nilai yang ingin ditampilkan.</p>
                    </div>
                    <button type="button" id="tableFilterClose" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
                </div>
            </div>
            <div class="px-6 py-4 table-filter-modal-panel overflow-y-auto">
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Search</label>
                    <input id="tableFilterSearch" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Cari nilai...">
                </div>
                <div id="tableFilterOptions" class="space-y-2"></div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between gap-3 bg-gray-50">
                <button type="button" id="tableFilterClear" class="text-sm font-semibold text-gray-600 hover:text-gray-900 underline underline-offset-2">Hapus Filter</button>
                <div class="flex items-center gap-2">
                    <button type="button" id="tableFilterCancel" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm hover:bg-gray-100">Batal</button>
                    <button type="button" id="tableFilterApply" class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background-color: #144600;">Terapkan</button>
                </div>
            </div>
        </div>
    </div>

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

    function escapeHtml(value) {
        return (value ?? '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalizeFilterValue(value) {
        return (value ?? '')
            .toString()
            .trim()
            .toLowerCase();
    }

    function setFilterButtonState(buttonIdOrIds, active) {
        const buttonIds = Array.isArray(buttonIdOrIds) ? buttonIdOrIds : [buttonIdOrIds];

        buttonIds.forEach((buttonId) => {
            const button = document.getElementById(buttonId);
            if (!button) {
                return;
            }

            button.style.backgroundColor = active ? '#4B5563' : '';
            button.style.borderRadius = active ? '4px' : '';

            const icon = button.querySelector('svg');
            if (icon) {
                icon.style.color = active ? 'white' : '';
            }
        });
    }

    function renderFilterOptions(containerId, values, column, selectedValue = null, options = {}) {
        const container = document.getElementById(containerId);
        if (!container) {
            return;
        }

        const toJsSingleQuoted = (value) => {
            return `'${(value ?? '')
                .toString()
                .replace(/\\/g, '\\\\')
                .replace(/'/g, "\\'")}'`;
        };

        const settings = {
            allLabel: 'Semua',
            emptyLabel: 'Tidak ada opsi',
            onSelect: 'setColumnFilter',
            ...options,
        };

        const uniqueValues = [...new Set((values || [])
            .map((value) => (value ?? '').toString().trim())
            .filter(Boolean))]
            .sort((left, right) => left.localeCompare(right, 'id', { sensitivity: 'base' }));

        const selectedKey = normalizeFilterValue(selectedValue);
        const columnKey = toJsSingleQuoted(column);
        const allButtonClass = 'table-filter-option flex items-center gap-2 w-full text-left px-3 py-2 text-sm rounded-lg border border-transparent';
        const selectedClass = 'bg-green-50 border-green-200 font-semibold text-green-800';
        const columnArg = toJsSingleQuoted(column);

        const items = [
            `<div class="mb-3"><button type="button" onclick="${settings.onSelect}(${columnArg}, '')" class="${allButtonClass}${!selectedKey ? ` ${selectedClass}` : ''}"><input type="checkbox" class="pointer-events-none" ${!selectedKey ? 'checked' : ''}><span>${escapeHtml(settings.allLabel)}</span></button></div>`,
        ];

        if (!uniqueValues.length) {
            items.push(`<div class="px-3 py-1.5 text-sm text-gray-400">${escapeHtml(settings.emptyLabel)}</div>`);
        } else {
            uniqueValues.forEach((value) => {
                const valueKey = normalizeFilterValue(value);
                const isSelected = selectedKey && valueKey === selectedKey;
                const valueArg = toJsSingleQuoted(value);
                items.push(
                    `<button type="button" data-filter-value="${escapeHtml(valueKey)}" onclick="${settings.onSelect}(${columnArg}, ${valueArg})" class="${allButtonClass}${isSelected ? ` ${selectedClass}` : ''}"><input type="checkbox" class="pointer-events-none" ${isSelected ? 'checked' : ''}><span>${escapeHtml(value)}</span></button>`
                );
            });
        }

        container.innerHTML = `<div class="space-y-2" data-filter-column="${escapeHtml(column)}" data-filter-selected="${escapeHtml(selectedKey)}">`
            + items.join('')
            + `</div>`;

        const searchBox = container.previousElementSibling?.querySelector?.('#tableFilterSearch');
        if (searchBox) {
            searchBox.value = '';
        }
    }

    function filterFilterOptions(containerId, query) {
        const container = document.getElementById(containerId);
        if (!container) {
            return;
        }

        const normalized = normalizeFilterValue(query);
        container.querySelectorAll('[data-filter-value]').forEach((item) => {
            const label = normalizeFilterValue(item.textContent);
            item.classList.toggle('hidden', normalized && !label.includes(normalized));
        });
    }

    window.escapeHtml = window.escapeHtml || escapeHtml;
    window.normalizeFilterValue = window.normalizeFilterValue || normalizeFilterValue;
    window.setFilterButtonState = window.setFilterButtonState || setFilterButtonState;
    window.renderFilterOptions = window.renderFilterOptions || renderFilterOptions;
    window.filterFilterOptions = window.filterFilterOptions || filterFilterOptions;

    const tableEnhancers = new Map();
    window.tableEnhancers = tableEnhancers;
    const tableFilterModal = document.getElementById('tableFilterModal');
    const tableFilterTitle = document.getElementById('tableFilterTitle');
    const tableFilterSearch = document.getElementById('tableFilterSearch');
    const tableFilterOptions = document.getElementById('tableFilterOptions');
    const tableFilterApply = document.getElementById('tableFilterApply');
    const tableFilterClear = document.getElementById('tableFilterClear');
    const tableFilterClose = document.getElementById('tableFilterClose');
    const tableFilterCancel = document.getElementById('tableFilterCancel');

    let activeTableFilter = null;

    function getTableBodyRows(table) {
        return Array.from(table.querySelectorAll('tbody tr')).filter((row) => !row.closest('#tableFilterModal'));
    }

    function compareTableValues(leftCell, rightCell) {
        const leftText = normalizeFilterValue(leftCell);
        const rightText = normalizeFilterValue(rightCell);

        if (leftText === rightText) return 0;

        if (isNaN(leftText) && isNaN(rightText)) {
            const leftDate = Date.parse(leftCell);
            const rightDate = Date.parse(rightCell);
            if (!Number.isNaN(leftDate) && !Number.isNaN(rightDate)) {
                return leftDate - rightDate;
            }
        }

        return leftText.localeCompare(rightText, 'id', { numeric: true, sensitivity: 'base' });
    }

    function getTableCellText(row, header, columnIndex) {
        const key = normalizeFilterValue(header?.dataset.columnKey || header?.dataset.sortKey || header?.dataset.filterKey || '');
        if (key) {
            const keyedCell = row.querySelector(`[data-column-key="${key}"]`);
            if (keyedCell) {
                return keyedCell.textContent || '';
            }
        }

        return row.children[columnIndex]?.textContent || '';
    }

    function updateTableHeaderState(table, state) {
        const headers = Array.from(table.querySelectorAll('thead th'));
        headers.forEach((header, index) => {
            const sortable = header.dataset.sortable !== 'false';
            const filtered = Boolean(state.filters[index] && state.filters[index].size);
            if (sortable && !header.dataset.tooltip) {
                header.dataset.tooltip = 'Klik sekali: sort. Klik dua kali: filter.';
            }
            header.removeAttribute('title');
            header.classList.toggle('table-interactive-header', sortable);
            header.dataset.sortState = state.sortColumn === index ? state.sortDirection : '';
            header.dataset.filterActive = filtered ? 'true' : 'false';
        });
    }

    function applyTableEnhancer(table) {
        const state = tableEnhancers.get(table);
        if (!state) {
            return;
        }

        const rows = getTableBodyRows(table);
        rows.forEach((row, index) => {
            if (!row.dataset.tableOriginalOrder) {
                row.dataset.tableOriginalOrder = String(index);
            }
        });

        let visibleRows = rows.filter((row) => {
            return Object.entries(state.filters).every(([columnIndex, selectedValues]) => {
                if (!selectedValues || !selectedValues.size) {
                    return true;
                }
                const header = table.querySelectorAll('thead th')[Number(columnIndex)];
                const cellText = normalizeFilterValue(getTableCellText(row, header, Number(columnIndex)));
                return selectedValues.has(cellText);
            });
        });

        if (state.sortColumn !== null && state.sortDirection) {
            visibleRows.sort((leftRow, rightRow) => {
                const header = table.querySelectorAll('thead th')[state.sortColumn];
                const leftCell = getTableCellText(leftRow, header, state.sortColumn);
                const rightCell = getTableCellText(rightRow, header, state.sortColumn);
                const comparison = compareTableValues(leftCell, rightCell);
                return state.sortDirection === 'asc' ? comparison : -comparison;
            });
        } else {
            visibleRows.sort((leftRow, rightRow) => Number(leftRow.dataset.tableOriginalOrder || 0) - Number(rightRow.dataset.tableOriginalOrder || 0));
        }

        const tbody = table.tBodies[0];
        if (tbody) {
            if (state.observer) {
                state.observer.disconnect();
            }

            visibleRows.forEach((row) => {
                row.style.display = '';
                tbody.appendChild(row);
            });

            rows.forEach((row) => {
                if (!visibleRows.includes(row)) {
                    row.style.display = 'none';
                    tbody.appendChild(row);
                }
            });

            if (state.observer) {
                state.observer.observe(tbody, { childList: true, subtree: false });
            }
        }

        updateTableHeaderState(table, state);
    }

    function scheduleApplyTableEnhancer(table) {
        const state = tableEnhancers.get(table);
        if (!state) {
            return;
        }

        if (state.applyTimer) {
            clearTimeout(state.applyTimer);
        }

        state.applyTimer = setTimeout(() => applyTableEnhancer(table), 0);
    }

    function setTableSort(table, columnIndex) {
        const state = tableEnhancers.get(table);
        if (!state) {
            return;
        }

        if (state.sortColumn === columnIndex) {
            if (state.sortDirection === 'asc') {
                state.sortDirection = 'desc';
            } else if (state.sortDirection === 'desc') {
                state.sortDirection = null;
                state.sortColumn = null;
            } else {
                state.sortDirection = 'asc';
            }
        } else {
            state.sortColumn = columnIndex;
            state.sortDirection = 'asc';
        }

        scheduleApplyTableEnhancer(table);
    }

    function openTableFilterModal(table, columnIndex) {
        const state = tableEnhancers.get(table);
        if (!state) {
            return;
        }

        const header = table.querySelectorAll('thead th')[columnIndex];
        const label = header ? header.textContent.replace(/↕|▲|▼/g, '').trim() : `Kolom ${columnIndex + 1}`;
        const rows = getTableBodyRows(table);

        const valueMap = new Map();
        rows.forEach((row) => {
            const rawText = getTableCellText(row, header, columnIndex);
            const normalized = normalizeFilterValue(rawText);
            if (normalized && !valueMap.has(normalized)) {
                valueMap.set(normalized, rawText.trim());
            }
        });

        const uniqueValues = Array.from(valueMap.entries()).sort((a, b) => a[1].localeCompare(b[1], 'id', { sensitivity: 'base' }));

        activeTableFilter = { table, columnIndex };
        tableFilterTitle.textContent = `Filter: ${label}`;
        tableFilterSearch.value = '';

        const selectedValues = state.filters[columnIndex] || new Set();
        if (!uniqueValues.length) {
            tableFilterOptions.innerHTML = '<div class="text-sm text-gray-400">Belum ada opsi untuk kolom ini.</div>';
        } else {
            tableFilterOptions.innerHTML = uniqueValues.map(([normalized, display]) => `
                <label class="table-filter-option flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-2 text-sm cursor-pointer">
                    <input type="checkbox" class="table-filter-checkbox accent-green-700" value="${escapeHtml(normalized)}" ${selectedValues.has(normalized) ? 'checked' : ''}>
                    <span class="flex-1">${escapeHtml(display)}</span>
                </label>
            `).join('');
        }

        tableFilterModal.classList.remove('hidden');
    }

    function closeTableFilterModal() {
        if (tableFilterModal) {
            tableFilterModal.classList.add('hidden');
        }
        activeTableFilter = null;
    }

    function commitTableFilterSelection() {
        if (!activeTableFilter) {
            return;
        }

        const { table, columnIndex } = activeTableFilter;
        const state = tableEnhancers.get(table);
        if (!state) {
            return;
        }

        const checked = Array.from(tableFilterOptions.querySelectorAll('.table-filter-checkbox:checked')).map((checkbox) => normalizeFilterValue(checkbox.value));
        state.filters[columnIndex] = new Set(checked);
        scheduleApplyTableEnhancer(table);
    }

    function clearActiveTableFilter() {
        if (!activeTableFilter) {
            return;
        }

        const { table, columnIndex } = activeTableFilter;
        const state = tableEnhancers.get(table);
        if (!state) {
            return;
        }

        delete state.filters[columnIndex];
        closeTableFilterModal();
        scheduleApplyTableEnhancer(table);
    }

    function initTableEnhancer(table) {
        if (tableEnhancers.has(table)) {
            return;
        }

        const headers = Array.from(table.querySelectorAll('thead th'));
        if (!headers.length) {
            return;
        }

        const state = {
            sortColumn: null,
            sortDirection: null,
            filters: {},
            clickTimers: {},
            observer: null,
            applyTimer: null,
        };

        tableEnhancers.set(table, state);

        headers.forEach((header) => {
            if (header.dataset.sortable === 'false') {
                return;
            }

            header.classList.add('table-interactive-header');
            if (!header.dataset.tooltip) {
                header.dataset.tooltip = 'Klik sekali untuk sortir kolom dan klik 2 kali untuk filter kolom.';
            }
            header.removeAttribute('title');
        });

        const handleHeaderInteraction = (event, isDoubleClick = false) => {
            const header = event.target.closest('thead th');
            if (!header || !table.contains(header)) {
                return;
            }

            if (header.dataset.sortable === 'false') {
                return;
            }

            if (event.target.closest('button, a, input, label, select, textarea')) {
                return;
            }

            const headerIndex = Array.from(table.querySelectorAll('thead th')).indexOf(header);
            if (headerIndex < 0) {
                return;
            }

            if (isDoubleClick) {
                if (state.clickTimers[headerIndex]) {
                    clearTimeout(state.clickTimers[headerIndex]);
                    state.clickTimers[headerIndex] = null;
                }
                openTableFilterModal(table, headerIndex);
                return;
            }

            if (state.clickTimers[headerIndex]) {
                clearTimeout(state.clickTimers[headerIndex]);
            }

            state.clickTimers[headerIndex] = setTimeout(() => {
                setTableSort(table, headerIndex);
                state.clickTimers[headerIndex] = null;
            }, 250);
        };

        table.addEventListener('click', (event) => handleHeaderInteraction(event, false));
        table.addEventListener('dblclick', (event) => handleHeaderInteraction(event, true));

        state.observer = new MutationObserver(() => scheduleApplyTableEnhancer(table));
        table.querySelectorAll('tbody').forEach((tbody) => {
            state.observer.observe(tbody, { childList: true, subtree: false });
        });

        scheduleApplyTableEnhancer(table);
    }

    function initInteractiveTables() {
        document.querySelectorAll('table').forEach((table) => {
            if (table.closest('#tableFilterModal')) {
                return;
            }
            initTableEnhancer(table);
        });
    }

    window.resetTableEnhancer = function(table) {
        const state = tableEnhancers.get(table);
        if (state) {
            state.filters = {};
            state.sortColumn = null;
            state.sortDirection = null;
            scheduleApplyTableEnhancer(table);
        }
    };

    tableFilterSearch?.addEventListener('input', (event) => {
        window.filterFilterOptions('tableFilterOptions', event.target.value);
    });

    tableFilterApply?.addEventListener('click', () => {
        commitTableFilterSelection();
        closeTableFilterModal();
    });

    tableFilterClear?.addEventListener('click', clearActiveTableFilter);
    tableFilterClose?.addEventListener('click', closeTableFilterModal);
    tableFilterCancel?.addEventListener('click', closeTableFilterModal);
    tableFilterModal?.addEventListener('click', (event) => {
        if (event.target === tableFilterModal) {
            closeTableFilterModal();
        }
    });

    if (document.readyState === 'loading') {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initInteractiveTables);
        } else {
            initInteractiveTables();
        }
    } else {
        initInteractiveTables();
    }

    const tableEnhancerObserver = new MutationObserver(() => initInteractiveTables());
    tableEnhancerObserver.observe(document.body, { childList: true, subtree: true });

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
    const topProfileMenuBtn = document.getElementById('topProfileMenuBtn');
    const topProfileMenu = document.getElementById('topProfileMenu');
    const topProfileMenuWrapper = document.getElementById('topProfileMenuWrapper');

    if (topProfileMenuBtn && topProfileMenu && topProfileMenuWrapper) {
        topProfileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            topProfileMenu.classList.toggle('hidden');
            topProfileMenuBtn.setAttribute('aria-expanded', String(!topProfileMenu.classList.contains('hidden')));
        });

        document.addEventListener('click', (e) => {
            if (!topProfileMenuWrapper.contains(e.target)) {
                topProfileMenu.classList.add('hidden');
                topProfileMenuBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Function to handle badge dot visibility based on sidebar state
    function updateBadgeDotVisibility() {
        const badgeDot = document.getElementById('manager-approval-badge-dot');
        if (!badgeDot) {
            return;
        }
        
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) {
            return;
        }
        
        const isSidebarCollapsed = sidebar.classList.contains('collapsed');
        const badgeElement = document.getElementById('manager-approval-badge');
        const hasRequests = badgeElement && !badgeElement.classList.contains('hidden');

        // Show dot only if sidebar collapsed AND has requests
        if (isSidebarCollapsed && hasRequests) {
            badgeDot.style.display = 'block';
        } else {
            badgeDot.style.display = 'none';
        }

        const syncBadgeDot = document.getElementById('sync-pending-badge-dot');
        const syncBadge = document.getElementById('sync-pending-badge');
        const hasSyncPending = syncBadge && !syncBadge.classList.contains('hidden');
        if (syncBadgeDot) {
            syncBadgeDot.style.display = (isSidebarCollapsed && hasSyncPending) ? 'block' : 'none';
        }
    }

    function setSyncPendingBadge(pendingTotal) {
        const badge = document.getElementById('sync-pending-badge');
        const count = Number(pendingTotal || 0);
        if (!badge) {
            return;
        }

        if (count > 0) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }

        updateBadgeDotVisibility();
    }

    window.setSyncPendingBadge = setSyncPendingBadge;

    async function hydrateSyncPendingBadge() {
        const badge = document.getElementById('sync-pending-badge');
        if (!badge) {
            return;
        }

        try {
            const res = await apiGet('/api/beranda/hris');
            if (!(res && res.success === true && res.data && res.data.summary)) {
                setSyncPendingBadge(0);
                return;
            }

            setSyncPendingBadge(res.data.summary.pending_total || 0);
        } catch (e) {
            setSyncPendingBadge(0);
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
    hydrateSyncPendingBadge();
    
    // Update badge dot visibility on init
    updateBadgeDotVisibility();
    
    // Polling interval - update badge every 5 seconds
    setInterval(hydrateManagerApprovalBadge, 5000);
    setInterval(hydrateSyncPendingBadge, 10000);
    </script>
    @stack('scripts')
</body>
</html>
