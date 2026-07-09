<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CRM')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="m-0 flex min-h-screen items-center justify-center bg-slate-50 font-sans text-gray-900 antialiased">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-sm">
        <h2 class="m-0 mb-6 text-center text-xl font-semibold">CRM</h2>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="m-0 list-disc pl-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
