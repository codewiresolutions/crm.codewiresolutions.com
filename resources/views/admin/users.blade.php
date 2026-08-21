@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
    <div class="mb-4 rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <span class="bg-blue-50 text-blue-700 px-2.5 py-2 rounded-xl text-3xl flex items-center justify-center"><i class="ri-user-add-fill"></i></span>
        <div>
        <h2 class="m-0 text-xl font-semibold">Users</h2>
        <p class="text-sm text-gray-500">Activate accounts and manage roles.</p>
    </div>
</div>
    <div class="flex items-center gap-3">
        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">
            <i class="ri-user-add-line"></i> Invite User
        </button></div>
        </div>
        <div class="flex flex-cols-1 sm:flex-cols-2 lg:flex-cols-3 gap-4 my-6">
        <div class="flex gap-3 rounded-xl bg-slate-50/50 p-4 shadow-xs border border-slate-200 w-full max-w-50">
        <div class="flex items-center justify-center px-2.5 py-1 rounded-full bg-blue-100 text-blue-500 text-2xl shrink-0"><i class="ri-user-add-line"></i>
    </div>
    <div>
        <p class="text-xs font-medium">Total Users</p>
        <h4 class="text-xl font-bold m-0">0</h4>
    </div>
  </div>
  <div class="flex gap-3 rounded-xl bg-slate-50/50 p-4 shadow-xs border border-slate-200 w-full max-w-50">
        <div class="flex items-center justify-center bg-green-100 px-2.5 py-1 rounded-full text-green-600 text-2xl shrink-0"><i class="ri-checkbox-circle-fill"></i>
    </div>
    <div>
        <p class="text-xs font-medium">Active Users</p>
        <h4 class="text-xl font-bold m-0">0</h4>
    </div>
  </div>
  <div class="flex gap-3 rounded-xl bg-slate-50/50 p-4 shadow-xs border border-slate-200 w-full max-w-50">
        <div class="flex items-center justify-center bg-purple-100 px-2.5 py-1 rounded-full text-purple-400 text-2xl shrink-0"><i class="ri-user-unfollow-line"></i>
    </div>
    <div>
        <p class="text-xs font-medium">Inactive Users</p>
        <h4 class="text-xl font-bold m-0">0</h4>
    </div>
  </div>
  </div>
</div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <!-- <div class="rounded-lg bg-white shadow-sm"> -->
        <table class="w-full border-collapse">
            <thead class="text-sm bg-gray-100">
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
                                    <select name="role" onchange="this.form.submit()" {{ $user->id === auth()->id() ? 'disabled' : '' }} class="w-auto rounded-lg border border-gray-300 px-2 py-1.5 text-sm">
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
                                <span class=" inline-flex gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs text-emerald-700"><i class="ri-check-line"></i>Active</span>
                            @else
                                <span class="inline-flex gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs text-red-700"><i class="ri-close-fill"></i>Inactive</span>
                            @endif
                        </td>
                        <td class="border-b border-gray-200 p-2.5">
                            @php
                                $isDisabled = $user->id === auth()->id() || (! auth()->user()->isAdmin() && $user->isAdmin());
                            @endphp
                            <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" {{ $isDisabled ? 'disabled' : '' }} class="w-auto rounded-lg px-3 py-1.5 text-white text-sm {{ $user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- </div> -->
    </div>
@endsection