@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
    <div class="mb-4 rounded-xl bg-white p-5 shadow-sm">
        <h2 class="m-0 text-xl font-semibold">Users</h2>
        <p class="text-sm text-gray-500">Activate accounts and manage roles.</p>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <table class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="border-b border-gray-200 p-2.5 text-left">Name</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Email</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Role</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Status</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td class="border-b border-gray-200 p-2.5">{{ $user->name }}</td>
                        <td class="border-b border-gray-200 p-2.5">{{ $user->email }}</td>
                        <td class="border-b border-gray-200 p-2.5">
                            @if(auth()->user()->isAdmin())
                                <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" onchange="this.form.submit()" {{ $user->id === auth()->id() ? 'disabled' : '' }} class="w-auto rounded-lg border border-gray-300 px-2 py-1.5">
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                    </select>
                                </form>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-600">{{ ucfirst($user->role) }}</span>
                            @endif
                        </td>
                        <td class="border-b border-gray-200 p-2.5">
                            @if($user->is_active)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-700">Active</span>
                            @else
                                <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs text-red-700">Inactive</span>
                            @endif
                        </td>
                        <td class="border-b border-gray-200 p-2.5">
                            @php
                                $isDisabled = $user->id === auth()->id() || (! auth()->user()->isAdmin() && $user->isAdmin());
                            @endphp
                            <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" {{ $isDisabled ? 'disabled' : '' }} class="w-auto rounded-lg px-3 py-1.5 text-white {{ $user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection