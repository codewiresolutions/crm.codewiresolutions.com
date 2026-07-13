@extends('layouts.admin')

@section('title', 'Customer Groups')

@section('content')
    <div class="mb-4 flex items-center justify-between rounded-xl bg-white p-5 shadow-sm">
        <div>
            <h2 class="m-0 text-xl font-semibold">Customer Groups</h2>
            <p class="text-sm text-gray-500">Groups are created automatically when you send a WhatsApp message to multiple selected customers at once from the Customers page.</p>
        </div>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        @include('admin.partials.groups-table')
    </div>

    <div class="mt-4 rounded-xl bg-white p-5 shadow-sm">
        <h3 class="m-0 mb-1 text-lg font-semibold">Resend delay options</h3>
        <p class="mb-4 text-sm text-gray-500">These are the choices offered in the "Resend in..." dropdown above.</p>

        <table class="mb-4 w-full border-collapse">
            <thead>
                <tr>
                    <th class="border-b border-gray-200 p-2.5 text-left">Label</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Minutes</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resendIntervals as $interval)
                    <tr>
                        <td class="border-b border-gray-200 p-2.5">{{ $interval->label }}</td>
                        <td class="border-b border-gray-200 p-2.5">{{ $interval->minutes }}</td>
                        <td class="border-b border-gray-200 p-2.5">
                            <form action="{{ route('admin.resend-intervals.destroy', $interval) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Remove" class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-lg border-none bg-transparent p-0 text-red-600 hover:bg-red-50">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-4 text-center text-sm text-gray-500">No delay options yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <form action="{{ route('admin.resend-intervals.store') }}" method="POST" class="flex items-end gap-2">
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
