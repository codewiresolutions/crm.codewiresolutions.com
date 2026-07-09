@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus class="mb-3 w-full rounded-lg border border-gray-300 px-3 py-2.5">

        <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" required class="mb-3 w-full rounded-lg border border-gray-300 px-3 py-2.5">

        <label class="mb-4 flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" class="w-auto rounded border-gray-300">
            Remember me
        </label>

        <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2.5 text-white hover:bg-blue-700">Login</button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-medium text-blue-600 no-underline hover:underline">Register</a>
    </p>
@endsection