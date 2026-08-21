<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css" integrity="sha512-ApSLB1Pd3/bZN8fWB/RG9YhN/7bd9Hkf3AGaE2mPfebjrxagjuBtx2GcgdqIlJkUzwylBo61r9Xa9NmgBI0swA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"rel="stylesheet"/>
    <title>@yield('title', 'Admin')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('styles')
</head>
<body class="m-0 bg-slate-100 font-sans text-gray-900 antialiased">
<div class="flex min-h-screen">
    <aside class="w-60 bg-slate-900 px-4 py-6 text-white">
            
        <h2 class="mb-4 px-3 py-2 text-xl font-semibold"><i class="ri-chat-3-line mr-2 bg-gray-700 px-2 py-2 rounded-lg text-white"></i>CRM</h2>
        <nav class="flex flex-col gap-2">
            @if(auth()->user()->canViewMenu('dashboard'))
                <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><span class="mr-2 text-lg"><i class="ri-dashboard-line"></i></span>Dashboard</a>
            @endif
            @if(auth()->user()->canViewMenu('whatsapp'))
                <div class="px-2.5 py-1.5 text-gray-400 font-bold text-xs">MESSAGES</div>
                <a href="{{ route('admin.whatsapp') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ (request()->routeIs('admin.whatsapp') || request()->routeIs('admin.whatsapp.*')) ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-whatsapp-line mr-2 text-lg"></i>WhatsApp integration</a>
            @endif
            @if(auth()->user()->canViewMenu('whatsapp-inbox'))
                <a href="{{ route('admin.whatsapp-inbox') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.whatsapp-inbox*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-discuss-line mr-2 text-lg"></i>WhatsApp Messages</a>
            @endif
            @if(auth()->user()->canViewMenu('customers'))
               <div class="px-2.5 py-1.5 text-gray-400 font-bold text-xs">CUSTOMERS</div>
                <a href="{{ route('admin.customers.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.customers.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-group-line mr-2 text-lg"></i>Customers</a>
                <a href="{{ route('admin.groups.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.groups.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-team-line mr-2 text-lg"></i>Customer Groups</a>
            @endif
            @if(auth()->user()->canViewMenu('csv'))
                <a href="{{ route('admin.csv.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.csv.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-file-add-line mr-2 text-lg"></i>CSV Import</a>
            @endif
            @if(auth()->user()->hasRole('admin', 'manager'))
                <div class="px-2.5 py-1.5 text-gray-400 font-bold text-xs">ADMIN</div>
                <a href="{{ route('admin.users.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-user-add-line mr-2 text-lg"></i>Users</a>
            @endif
            @if(auth()->user()->hasRole('admin', 'manager'))
                <a href="{{ route('admin.permissions.edit') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.permissions.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-lock-2-line mr-2 text-lg"></i>Permissions</a>
            @endif
        </nav>
    </aside>
    <div class="flex flex-1 flex-col">
        <header class="flex items-center justify-end gap-3 border-b border-gray-200 bg-white px-6 py-3">
            <div class="relative flex-1">
            <div class="absolute inset-y-0 flex items-center pl-3 text-gray-400">
            <i class="ri-search-line text-lg"></i>
            </div>
            <input type="text" placeholder="Search anything..." class="w-full max-w-100 rounded-lg border border-gray-300 py-2 pl-10 pr-3 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-500">
            </div>
            <div class="flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                <!-- <span class="rounded-sm bg-gray-100 px-3 py-0.5 text-xs text-gray-600">{{ ucfirst(auth()->user()->role) }}</span> -->
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-auto rounded-lg bg-gray-800 px-4 py-1.5 text-sm text-white hover:bg-gray-900">Logout</button>
            </form>
        </header>
        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-emerald-700">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-700">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@yield('scripts')
</body>
</html>
