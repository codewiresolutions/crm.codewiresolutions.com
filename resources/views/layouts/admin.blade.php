<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f5f7fb; color: #222; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #1f2937; color: #fff; padding: 24px 16px; }
        .sidebar a { display: block; color: #d1d5db; text-decoration: none; padding: 10px 12px; margin-bottom: 8px; border-radius: 8px; }
        .sidebar a.active, .sidebar a:hover { background: #374151; color: #fff; }
        .content { flex: 1; padding: 24px; }
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 16px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-top: 20px; }
        .stat { background: #fff; padding: 16px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        input, textarea, select, button { width: 100%; padding: 10px; margin-top: 8px; border-radius: 8px; border: 1px solid #d1d5db; }
        button { background: #2563eb; color: #fff; border: none; cursor: pointer; }
        .muted { color: #6b7280; font-size: 13px; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .alert-success { background: #ecfdf3; color: #047857; }
        .alert-error { background: #fef2f2; color: #b91c1c; }

        /* Icon action buttons */
        .actions { display: flex; align-items: center; gap: 6px; white-space: nowrap; }
        .icon-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            background: transparent;
            transition: background 0.15s ease, transform 0.1s ease;
        }
        .icon-btn:hover { transform: translateY(-1px); }
        .icon-btn svg { width: 18px; height: 18px; pointer-events: none; }

        .icon-btn.edit { color: #2563eb; }
        .icon-btn.edit:hover { background: #eff6ff; }

        .icon-btn.whatsapp { color: #16a34a; }
        .icon-btn.whatsapp:hover { background: #ecfdf3; }

        .icon-btn.delete { color: #dc2626; }
        .icon-btn.delete:hover { background: #fef2f2; }

        @yield('styles')
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <h2 style="padding: 10px 12px;">CRM</h2>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.whatsapp') }}" class="{{ request()->routeIs('admin.whatsapp*') ? 'active' : '' }}">WhatsApp integration </a>
        <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">Customers</a>
    </aside>
    <main class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>
@yield('scripts')
</body>
</html>
