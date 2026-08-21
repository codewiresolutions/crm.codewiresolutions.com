@extends('layouts.admin')

@section('title', 'Customer Groups')

@section('content')
    <div class="mb-4 flex items-start justify-between rounded-xl bg-white p-6 shadow-sm">
        <div class="flex items-start gap-4">
            <span class="bg-blue-100 mt-1.5 text-blue-600 px-3.5 py-3 rounded-xl text-3xl flex items-center justify-center"><i class="ri-group-fill"></i></span>
            <div>
            <h2 class="m-0 text-lg font-bold text-gray-900">Customer Groups</h2>
            <p class="text-sm mt-1 text-gray-500 w-full max-w-120">Groups are created automatically when you send a WhatsApp message to multiple selected customers at once from the Customers page.</p>
            <div class="mt-3 flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600">
                    <i class="ri-history-line text-xs"></i> 0 Groups
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600">
                    <i class="ri-group-line text-xs"></i> Auto Generated
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-600">
                    <i class="ri-shield-flash-line text-xs"></i> Active System
                </span>
        </div>
        </div></div>
        <div class="flex items-center gap-3">
        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
            <i class="ri-add-line text-sm"></i> New Group
        </button></div>
    </div>
    
    <div class="rounded-xl bg-white px-5 shadow-sm text-sm ">
        @include('admin.partials.groups-table')
    </div>

     <div class="mt-4 rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-start gap-3">
            <span class="flex-items-center justify-center mt-1 bg-blue-50 text-blue-700 rounded-lg px-3 py-2"><i class="ri-time-line  text-xl"></i></span>
        <div>
        <h3 class="m-0 font-semibold text-gray-900">Resend Delay Options</h3>
        <p class="mb-4 text-sm text-gray-500">These are the choices offered in the "Resend in..." dropdown above.</p></div></div>
       <div class="w-full overflow-x-auto">
        <table class="w-full border-collapse text-left text-xs text-gray-600">
            <thead class="bg-gray-50/50 uppercase tracking-wider text-[11px] text-gray-400">
                <tr class="border-b border-gray-100 font-semibold">
                    <th class="py-3 px-6">Label</th>
                    <th class="py-3 px-6">Minutes</th>
                    <th class="py-3 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($resendIntervals as $interval)
                    @php
                        $labelLower = strtolower($interval->label);
                        $iconColor = str_contains($labelLower, 'hour') ? 'text-amber-500' : (str_contains($labelLower, 'day') ? 'text-purple-500' : 'text-blue-500');
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="py-3 px-6 font-medium text-gray-900">
                            <div class="flex items-center gap-2">
                                <i class="ri-history-line {{ $iconColor }} text-sm"></i>
                                <span>{{ $interval->label }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-6 text-gray-600">
                            {{ $interval->minutes }}
                        </td>
                        <td class="py-3 px-6 text-right">
                            <form action="{{ route('admin.resend-intervals.destroy', $interval) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Remove" class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-rose-500 hover:bg-rose-50">
                                    <i class="ri-delete-bin-line text-sm"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-6 px-6 text-center text-gray-400">
                            No resend delay options configured yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
        <form action="{{ route('admin.resend-intervals.store') }}" method="POST" class="flex items-end gap-2 mt-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Label</label>
                <input type="text" name="label" placeholder="e.g. 45 minutes" required class="mt-1 rounded-lg border border-gray-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Minutes</label>
                <input type="number" name="minutes" min="1" placeholder="e.g. 45" required class="mt-1 w-32 rounded-lg border border-gray-300 px-3 py-2">
            </div>
            <button type="submit" class="w-auto rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">+ Add</button>
        </form>
    </div>
@endsection
