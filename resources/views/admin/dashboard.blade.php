@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="card">
        <h1>Admin Dashboard</h1>
        <p class="muted">Manage WhatsApp communication and customer data from one place.</p>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="muted">Contacts</div>
            <h3>{{ $totalContacts }}</h3>
            <p class="muted">Total customers in the contact table.</p>
        </div>
        <div class="stat">
            <div class="muted">Sent Messages</div>
            <h3>{{ $sentMessagesCount }}</h3>
            <p class="muted">Customers with a message sent using message_sent_at.</p>
        </div>
    </div>
@endsection