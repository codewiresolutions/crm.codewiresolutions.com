@extends('layouts.admin')

@section('title', 'WhatsApp Messages')

@section('content')
    <div class="mb-4 flex items-center justify-between rounded-xl bg-white p-5 shadow-sm">
        <div>
            <h2 class="m-0 text-xl font-semibold">WhatsApp Messages</h2>
            <p class="text-sm text-gray-500">Full incoming WhatsApp messages received via the connected account.</p>
        </div>
        <a href="{{ route('admin.whatsapp-inbox') }}" class="w-auto rounded-lg bg-blue-600 px-4 py-2.5 text-white no-underline hover:bg-blue-700">Refresh</a>
    </div>

    @if($fetchFailed)
        <div class="mb-4 rounded-xl bg-red-50 p-5 text-red-700 shadow-sm">
            Unable to fetch messages right now. Please try again later.
        </div>
    @endif

    <div class="rounded-xl bg-white p-5 shadow-sm">
        @if(empty($messages))
            <p class="text-sm text-gray-500">No messages found.</p>
        @else
            <input type="text" id="messageSearch" placeholder="Search messages..." onkeyup="filterMessages()" class="mb-4 w-full rounded-lg border border-gray-300 px-3 py-2.5">
            <table id="messagesTable" class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border-b border-gray-200 p-2.5 text-left">From</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Type</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Message</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Group</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Received At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $item)
                        <tr>
                            <td class="border-b border-gray-200 p-2.5">
                                <div class="font-medium">{{ $item['name'] ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500">{{ $item['from'] ?? '' }}</div>
                            </td>
                            <td class="border-b border-gray-200 p-2.5">{{ $item['type'] ?? '' }}</td>
                            <td class="border-b border-gray-200 p-2.5">{{ $item['message'] ?? '' }}</td>
                            <td class="border-b border-gray-200 p-2.5">
                                {{ !empty($item['isGroup']) ? ($item['groupName'] ?? 'Group') : '-' }}
                            </td>
                            <td class="border-b border-gray-200 p-2.5">
                                {{ !empty($item['timestamp']) ? \Illuminate\Support\Carbon::parse($item['timestamp'])->format('Y-m-d H:i:s') : '' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        function filterMessages() {
            var query = document.getElementById('messageSearch').value.trim().toLowerCase();
            var rows = document.querySelectorAll('#messagesTable tbody tr');
            rows.forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        }
    </script>
@endsection