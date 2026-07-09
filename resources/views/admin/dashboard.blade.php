@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="mb-4 rounded-xl bg-white p-5 shadow-sm">
        <h1 class="m-0 text-2xl font-semibold">Admin Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">Manage WhatsApp communication and customer data from one place.</p>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Contacts</div>
            <h3 class="my-1 text-2xl font-semibold">{{ $totalContacts }}</h3>
            <p class="text-sm text-gray-500">Total customers in the contact table.</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <div class="text-sm text-gray-500">Sent Messages</div>
            <h3 class="my-1 text-2xl font-semibold">{{ $sentMessagesCount }}</h3>
            <p class="text-sm text-gray-500">Customers with a message sent using message_sent_at.</p>
        </div>
    </div>
@endsection