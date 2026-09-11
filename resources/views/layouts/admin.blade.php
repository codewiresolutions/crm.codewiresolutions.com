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
<div class="flex min-h-screen relative overflow-x-hidden">

    <!-- Mobile Backdrop Overlay -->
    <div id="sidebarBackdrop" 
         class="fixed inset-0 z-40 hidden bg-gray-900/50 transition-opacity duration-300 lg:hidden"></div>

    <!-- Responsive Sidebar -->
    <aside id="sidebar" 
       class="fixed inset-y-0 left-0 z-50 w-60 h-screen overflow-y-auto -translate-x-full flex-col transform bg-slate-900 px-4 py-6 text-white transition-transform duration-300 ease-in-out lg:static lg:h-auto lg:translate-x-0 shrink-0 [::-webkit-scrollbar]:w-1.5 [::-webkit-scrollbar-thumb]:bg-slate-700 [::-webkit-scrollbar-thumb]:rounded-full [::-webkit-scrollbar-track]:bg-slate-900">
        
        <div class="mb-4 flex items-center justify-between px-3 py-2 shrink-0">
            <h2 class="flex items-center text-xl font-semibold">
                <i class="ri-chat-3-line mr-2 rounded-lg bg-gray-700 px-2.5 py-1.5 text-white"></i>CRM
            </h2>
            <button id="closeBtn" type="button" class="text-gray-400 hover:text-white lg:hidden">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        <nav class="flex flex-col gap-2 pb-6">
            @if(auth()->user()->canViewMenu('dashboard'))
                <a href="{{ route('admin.dashboard') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><span class="mr-2 text-lg"><i class="ri-dashboard-line"></i></span>Dashboard</a>
            @endif
            @if(auth()->user()->canViewMenu('whatsapp'))
                <div class="px-2.5 py-1.5 text-xs font-bold text-gray-400">MESSAGES</div>
                <a href="{{ route('admin.whatsapp') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ (request()->routeIs('admin.whatsapp') || request()->routeIs('admin.whatsapp.*')) ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-whatsapp-line mr-2 text-lg"></i>WhatsApp integration</a>
            @endif
            @if(auth()->user()->canViewMenu('whatsapp-inbox'))
                <a href="{{ route('admin.whatsapp-inbox') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.whatsapp-inbox*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-discuss-line mr-2 text-lg"></i>WhatsApp Messages</a>
            @endif
            @if(auth()->user()->canViewMenu('customers'))
               <div class="px-2.5 py-1.5 text-xs font-bold text-gray-400">CUSTOMERS</div>
                <a href="{{ route('admin.customers.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.customers.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-group-line mr-2 text-lg"></i>Customers</a>
                <a href="{{ route('admin.groups.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.groups.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-team-line mr-2 text-lg"></i>Customer Groups</a>
            @endif
            @if(auth()->user()->canViewMenu('csv'))
                <a href="{{ route('admin.csv.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.csv.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-file-add-line mr-2 text-lg"></i>CSV Import</a>
            @endif
            @if(auth()->user()->canViewMenu('items'))
                <div class="px-2.5 py-1.5 text-xs font-bold text-gray-400">ITEMS</div>
                <a href="{{ route('admin.items.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.items.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-box-3-line mr-2 text-lg"></i>Items</a>
            @endif
            @if(auth()->user()->hasRole('admin', 'manager'))
                <div class="px-2.5 py-1.5 text-xs font-bold text-gray-400">ADMIN</div>
                <a href="{{ route('admin.users.index') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-user-add-line mr-2 text-lg"></i>Users</a>
            @endif
            @if(auth()->user()->hasRole('admin', 'manager'))
                <a href="{{ route('admin.permissions.edit') }}" class="rounded-lg px-3 py-2 text-gray-300 no-underline {{ request()->routeIs('admin.permissions.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-700 hover:text-white' }}"><i class="ri-lock-2-line mr-2 text-lg"></i>Permissions</a>
            @endif
            
        </nav>
        <div class="mt-auto border-t border-slate-700 pt-3 md:hidden">

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-gray-300 transition hover:bg-gray-700 hover:text-white">

                    <i class="ri-logout-box-r-line text-lg"></i>

                    <span>Logout</span>

                </button>
            </form>

        </div>
    </aside>
    <div class="flex flex-1 flex-col min-w-0">
        
        <!-- Header Section -->
        <header class="flex items-center justify-between gap-3 border-b border-gray-200 bg-white px-6 py-3">
            <button id="menuBtn"
                    type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900 lg:hidden">
                <i class="ri-menu-line text-2xl"></i>
            </button>

            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 flex items-center pl-3 text-gray-400">
                    <i class="ri-search-line text-lg"></i>
                </div>
                <input type="text" placeholder="Search anything..." class="w-full rounded-lg border border-gray-300 py-2 pl-10 pr-3 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-gray-500">
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <div class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="hidden text-sm font-medium text-gray-700 sm:inline">{{ auth()->user()->name }}</span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="hidden md:block">
                    @csrf
                    <button type="submit" class="w-auto rounded-lg bg-gray-800 px-4 py-1.5 text-sm text-white hover:bg-gray-900">Logout</button>
                </form>
            </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuBtn = document.getElementById('menuBtn');
        const closeBtn = document.getElementById('closeBtn');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (!menuBtn || !sidebar || !backdrop) return;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            backdrop.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }

        menuBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        backdrop.addEventListener('click', closeSidebar);
    });
</script>

@yield('scripts')

</body>
</html>
