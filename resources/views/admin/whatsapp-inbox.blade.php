@extends('layouts.admin')

@section('title', 'WhatsApp Messages')

@section('content')
    <div class="mb-4 md:flex items-center justify-between rounded-xl bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-600 text-white text-3xl">
                    <i class="ri-whatsapp-line"></i>
                </div>
                </div>
            <div class="pl-4">
            <h2 class="m-0 text-xl font-semibold">WhatsApp Messages</h2>
            <p class="text-sm text-gray-500">
                Full incoming WhatsApp messages received via the connected account.
                @if(!is_null($count) || !is_null($totalStored))
                    <span class="text-gray-400">
                        &middot; Showing {{ $count ?? count($messages) }} of {{ $totalStored ?? count($messages) }} stored
                    </span>
                @endif
            </p>
        </div>
</div>
        <a href="{{ route('admin.whatsapp-inbox') }}" class="inline-flex w-auto rounded-lg bg-blue-600 px-4 py-2 mt-4 md:mt-0 text-sm text-white no-underline hover:bg-blue-700 gap-1 ml-14 md:ml-0"><i class="ri-loop-left-ai-line"></i>Refresh</a>
    </div>
    @if($fetchFailed)
        <div class="mb-4 rounded-xl bg-red-50 p-5 text-red-700 shadow-sm">
            Unable to fetch messages right now. Please try again later.
        </div>
    @endif

    <div class="w-full overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
    @if(empty($messages))
        <div class="p-8 text-center text-sm text-gray-500">No messages found.</div>
    @else
        <!-- Search Input Bar -->
        <div class="border-b border-gray-100 p-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 flex items-center pl-3 text-gray-400">
                   <i class="ri-search-line text-lg"></i>
                </div>
                <input 
                    type="text" 
                    id="messageSearch" 
                    placeholder="Search messages, name, or number..." 
                    onkeyup="filterMessages()" 
                    class="w-full rounded-lg border border-gray-200 bg-gray-50/50 pl-9 pr-3 py-2 text-xs text-gray-700 placeholder-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none transition-colors"
                >
            </div>
        </div>

        <!-- Table Container (Responsive without layout breakage) -->
        <div class="w-full overflow-x-auto sm:overflow-x-visible">
            <table id="messagesTable" class="w-full table-fixed border-collapse text-left text-xs text-gray-600">
                <thead class="bg-gray-50/50 uppercase tracking-wider text-gray-400">
                    <tr class="border-b border-gray-100 font-semibold">
                        <th class="w-[28%] p-3 pl-4">Customer</th>
                        <th class="w-[14%] p-3">Type</th>
                        <th class="w-[26%] p-3">Message</th>
                        <th class="w-[16%] p-3">Chat</th>
                        <th class="w-[12%] p-3">Received</th>
                        <th class="w-[4%] p-3 pr-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($messages as $item)
                        @php
                            $name = $item['name'] ?? 'Unknown';
                            $initial = strtoupper(substr($name, 0, 2));
                            
                            $avatarColors = ['bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700', 'bg-amber-100 text-amber-700', 'bg-rose-100 text-rose-700', 'bg-blue-100 text-blue-700'];
                            $colorIndex = ord(substr($initial, 0, 1)) % count($avatarColors);
                            $avatarClass = $avatarColors[$colorIndex];

                            $type = strtolower($item['type'] ?? 'text');
                            $typeStyles = [
                                'text' => 'bg-emerald-50 text-emerald-600 icon:ri-chat-1-line',
                                'image' => 'bg-blue-50 text-blue-600 icon:ri-image-line',
                                'video' => 'bg-purple-50 text-purple-600 icon:ri-video-line',
                                'document' => 'bg-amber-50 text-amber-600 icon:ri-file-text-line',
                                'reaction' => 'bg-amber-50 text-amber-600 icon:ri-emotion-line',
                            ];
                            $typeStyle = $typeStyles[$type] ?? 'bg-gray-100 text-gray-600 icon:ri-message-2-line';
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            
                            <!-- Customer Info -->
                            <td class="p-3 pl-4">
                                <div class="flex items-center gap-2.5 overflow-hidden">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full font-semibold text-xs {{ $avatarClass }}">
                                        {{ $initial }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate font-semibold text-gray-900" title="{{ $name }}">{{ $name }}</div>
                                        <div class="truncate text-gray-400 text-[11px]">{{ $item['from'] ?? '' }}</div>
                                        <div class="flex items-center gap-1 text-[10px] text-emerald-600 font-medium">
                                            <i class="ri-checkbox-circle-fill"></i> Verified
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Message Type -->
                            <td class="p-3">
                                <span class="inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[11px] font-medium capitalize {{ explode(' icon:', $typeStyle)[0] }}">
                                    <i class="{{ explode(' icon:', $typeStyle)[1] ?? 'ri-message-2-line' }}"></i>
                                    {{ $type }}
                                </span>
                            </td>

                            <!-- Message Body (Truncated to fit single row) -->
                            <td class="p-3">
                                <p class="truncate text-gray-700" title="{{ $item['message'] ?? '' }}">
                                    {{ $item['message'] ?? '' }}
                                </p>
                            </td>

                            <!-- Chat Type -->
                            <td class="p-3">
                                @if(!empty($item['isGroup']))
                                    <span class="inline-flex max-w-full items-center gap-1 truncate font-medium text-blue-600 hover:underline cursor-pointer" title="{{ $item['groupName'] ?? 'Group' }}">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-600"></span>
                                        <span class="truncate">{{ $item['groupName'] ?? 'Group' }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 font-medium text-gray-500">
                                         <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Direct</span>
                                @endif
                            </td>

                            <!-- Received At -->
                            <td class="p-3">
                                @if(!empty($item['timestamp']))
                                    @php $date = \Illuminate\Support\Carbon::parse($item['timestamp']); @endphp
                                    <div class="font-medium text-gray-700">
                                        {{ $date->isToday() ? 'Today' : ($date->isYesterday() ? 'Yesterday' : $date->format('M d, Y')) }}
                                    </div>
                                    <div class="text-[11px] text-gray-400">{{ $date->format('h:i A') }}</div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="p-3 pr-6 text-right">
                                <button type="button" title="More options" class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                    <i class="ri-more-2-fill text-base"></i>
                                </button>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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