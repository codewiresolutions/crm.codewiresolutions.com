@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required autofocus class="mb-3 w-full rounded-lg border border-gray-300 px-3 py-2.5">

        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required class="mb-3 w-full rounded-lg border border-gray-300 px-3 py-2.5">

        <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" required class="mb-3 w-full rounded-lg border border-gray-300 px-3 py-2.5">

        <label class="mb-1 block text-sm font-medium text-gray-700">Confirm Password</label>
        <input type="password" name="password_confirmation" required class="mb-4 w-full rounded-lg border border-gray-300 px-3 py-2.5">

        <button type="submit" class="w-full rounded-lg bg-blue-600 px-3 py-2.5 text-white hover:bg-blue-700">Register</button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-blue-600 no-underline hover:underline">Login</a>
    </p>
@endsection