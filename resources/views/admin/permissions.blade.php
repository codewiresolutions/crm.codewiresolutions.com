@extends('layouts.admin')

@section('title', 'Menu Permissions')

@section('content')
    <div class="mb-4 rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="bg-blue-50 text-blue-700 rounded-xl px-3 py-2 text-3xl">
                <i class="ri-shield-check-line"></i>
            </span>
        <div>
        <h2 class="m-0 text-xl text-slate-800 font-semibold">Menu Permissions</h2>
        <p class="text-sm text-gray-500 w-full max-w-120">Choose which sidebar menus are visible to Manager and User roles. Admins always see everything.</p>
       </div></div>
        <div class="flex flex-cols-1 sm:flex-cols-2 lg:flex-cols-3 gap-4 my-6">
        <div class="flex gap-3 rounded-xl shadow-xs p-4 w-full max-w-50 border border-gray-100 hover:bg-gray-50/50">
        <div class="flex items-center justify-center bg-blue-50 px-2.5 rounded-xl py-1 text-blue-500 text-2xl shrink-0"><i class="ri-function-line"></i>
    </div>
    <div>
        <p class="text-xs font-medium">Total Menu</p>
        <h4 class="text-xl font-bold m-0">0</h4>
    </div>
  </div>
  <div class="flex gap-3 rounded-xl p-4 shadow-xs w-full max-w-50 border border-gray-100 hover:bg-gray-50/50">
        <div class="flex items-center justify-center  bg-green-50 px-2.5 rounded-xl py-1 text-green-600 text-2xl shrink-0"><i class="ri-group-line"></i>
    </div>
    <div>
        <p class="text-xs font-medium">Roles</p>
        <h4 class="text-xl font-bold m-0">0</h4>
    </div>
  </div>
  <div class="flex gap-3 rounded-xl p-4 shadow-xs w-full max-w-50 border border-gray-100 hover:bg-gray-50/50">
        <div class="flex items-center justify-center bg-purple-50 px-2.5 py-1 rounded-xl text-purple-800 text-2xl shrink-0"><i class="ri-shield-check-line"></i>
    </div>
    <div>
        <p class="text-xs font-medium">Security</p>
        <h4 class="text-lg font-bold m-0 text-green-700">Enabled</h4>
    </div>
  </div>
  </div>

    </div>
    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <form action="{{ route('admin.permissions.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="w-full overflow-x-auto">
                <table class="w-full border-collapse text-left text-xs whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50/60 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                            <th class="py-3 px-4 rounded-l-xl">Menu</th>
                            @foreach($roles as $role)
                                <th class="py-3 px-4 {{ $loop->last ? 'rounded-r-xl' : '' }}">{{ ucfirst($role) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($menuKeys as $menuKey)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <!-- Menu Name with Icon -->
                                <td class="py-3.5 px-4 font-medium text-gray-800">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-sm">
                                            @if(str_contains($menuKey, 'dashboard'))
                                                <i class="ri-home-4-line"></i>
                                            @elseif(str_contains($menuKey, 'whatsapp'))
                                                <i class="ri-whatsapp-line text-emerald-600"></i>
                                            @elseif(str_contains($menuKey, 'customer'))
                                                <i class="ri-user-3-line"></i>
                                            @elseif(str_contains($menuKey, 'csv') || str_contains($menuKey, 'import'))
                                                <i class="ri-file-upload-line"></i>
                                            @else
                                                <i class="ri-menu-line"></i>
                                            @endif
                                        </div>
                                        <span>{{ ucwords(str_replace('-', ' ', $menuKey)) }}</span>
                                    </div>
                                </td>

                                <!-- Role Checkboxes -->
                                @foreach($roles as $role)
                                    @php
                                        $isVisible = optional($permissions->get($role, collect())->firstWhere('menu_key', $menuKey))->is_visible ?? false;
                                    @endphp
                                    <td class="py-3.5 px-4">
                                        <input type="checkbox" 
                                               name="visible[{{ $role }}][{{ $menuKey }}]" 
                                               value="1" 
                                               {{ $isVisible ? 'checked' : '' }} 
                                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
</div>
            <div class="mt-6">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
                    <i class="ri-save-3-line text-sm"></i> Save Permissions
                </button>
            </div>
        </form>
    </div>
@endsection