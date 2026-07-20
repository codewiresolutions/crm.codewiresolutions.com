@extends('layouts.admin')

@section('title', 'Menu Permissions')

@section('content')
    <div class="mb-4 rounded-xl bg-white p-5 shadow-sm">
        <h2 class="m-0 text-xl font-semibold">Menu Permissions</h2>
        <p class="text-sm text-gray-500">Choose which sidebar menus are visible to Manager and User roles. Admins always see everything.</p>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <form action="{{ route('admin.permissions.update') }}" method="POST">
            @csrf
            @method('PUT')
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border-b border-gray-200 p-2.5 text-left">Menu</th>
                        @foreach($roles as $role)
                            <th class="border-b border-gray-200 p-2.5 text-left">{{ ucfirst($role) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($menuKeys as $menuKey)
                        <tr>
                            <td class="border-b border-gray-200 p-2.5">{{ ucwords(str_replace('-', ' ', $menuKey)) }}</td>
                            @foreach($roles as $role)
                                @php
                                    $isVisible = optional($permissions->get($role, collect())->firstWhere('menu_key', $menuKey))->is_visible ?? false;
                                @endphp
                                <td class="border-b border-gray-200 p-2.5">
                                    <input type="checkbox" name="visible[{{ $role }}][{{ $menuKey }}]" value="1" {{ $isVisible ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="mt-4 w-auto rounded-lg bg-blue-600 px-4 py-2.5 text-white hover:bg-blue-700">Save Permissions</button>
        </form>
    </div>
@endsection