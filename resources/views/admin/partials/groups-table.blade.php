<table class="w-full border-collapse">
    <thead>
        <tr>
            <th class="border-b border-gray-200 p-2.5 text-left">Group</th>
            <th class="border-b border-gray-200 p-2.5 text-left">Customers</th>
            <th class="border-b border-gray-200 p-2.5 text-left">Actions</th>
            <th class="border-b border-gray-200 p-2.5 text-left">Resend</th>
        </tr>
    </thead>
    <tbody>
        @forelse($groups as $group)
            <tr>
                <td class="border-b border-gray-200 p-2.5 font-medium">{{ $group->name }}</td>
                <td class="border-b border-gray-200 p-2.5 text-sm text-gray-600">{{ $group->contacts->pluck('name')->join(', ') }}</td>
                <td class="whitespace-nowrap border-b border-gray-200 p-2.5">
                    <form action="{{ route('admin.groups.send-whatsapp', $group) }}" method="POST" class="inline-flex items-center gap-1">
                        @csrf
                        <input type="hidden" name="message_id" class="js-message-id" value="{{ $group->selectedmessage }}">
                        <select name="message" required class="rounded-lg border border-gray-300 px-1.5 py-1.5 text-xs" data-update-url="{{ route('admin.groups.update-selected-message', $group) }}" onchange="updateGroupSelectedMessage(this)">
                            <option value="" disabled {{ !$group->selectedmessage ? 'selected' : '' }}>Select message</option>
                            @foreach($messages as $msg)
                                <option value="{{ $msg->message }}" data-id="{{ $msg->id }}" {{ (int) $group->selectedmessage === $msg->id ? 'selected' : '' }}>{{ $msg->title }}</option>
                            @endforeach
                        </select>
                        <button type="submit" title="Send WhatsApp" class="inline-flex h-8.5 w-8.5 shrink-0 items-center justify-center rounded-lg border-none bg-transparent p-0 text-green-600 hover:bg-emerald-50">
                            <svg viewBox="0 0 24 24" fill="currentColor" class="h-4.5 w-4.5">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.67.15-.198.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"></path>
                                <path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.334.098 11.892c0 2.096.549 4.14 1.588 5.945L0 24l6.335-1.652a11.95 11.95 0 0 0 5.702 1.447h.005c6.585 0 11.943-5.335 11.95-11.893a11.82 11.82 0 0 0-3.472-8.453zM12.042 21.751h-.004a9.933 9.933 0 0 1-5.068-1.387l-.363-.215-3.759.982 1.003-3.649-.237-.375a9.9 9.9 0 0 1-1.527-5.276c.006-5.473 4.474-9.93 9.96-9.93a9.9 9.9 0 0 1 7.036 2.923 9.86 9.86 0 0 1 2.917 7.021c-.006 5.473-4.474 9.906-9.958 9.906z"></path>
                            </svg>
                        </button>
                    </form>
                </td>
                <td class="whitespace-nowrap border-b border-gray-200 p-2.5">
                    @if($group->pendingResend)
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-600">Resends {{ $group->pendingResend->run_at->format('M j, g:i A') }}</span>
                            <form action="{{ route('admin.groups.cancel-scheduled-resend', $group->pendingResend) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Cancel scheduled resend" class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs text-gray-600 hover:bg-gray-50">Cancel</button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('admin.groups.schedule-resend', $group) }}" method="POST" class="inline-flex items-center gap-1">
                            @csrf
                            <select name="resend_interval_id" required class="rounded-lg border border-gray-300 px-1.5 py-1.5 text-xs">
                                <option value="" disabled selected>Resend in...</option>
                                @foreach($resendIntervals as $interval)
                                    <option value="{{ $interval->id }}">{{ $interval->label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" title="Schedule resend" class="inline-flex h-8.5 w-8.5 shrink-0 items-center justify-center rounded-lg border-none bg-transparent p-0 text-blue-600 hover:bg-blue-50">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
                                    <circle cx="12" cy="12" r="9"></circle>
                                    <polyline points="12 7 12 12 15 15"></polyline>
                                </svg>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-4 text-center text-sm text-gray-500">No groups yet. Select 2 or more customers and send a WhatsApp message to create one.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>
    var csrfToken = '{{ csrf_token() }}';

    function updateGroupSelectedMessage(selectEl) {
        var option = selectEl.options[selectEl.selectedIndex];
        var messageId = option ? option.dataset.id : null;
        if (!messageId) return;

        var hiddenInput = selectEl.closest('form').querySelector('.js-message-id');
        if (hiddenInput) hiddenInput.value = messageId;

        fetch(selectEl.dataset.updateUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message_id: messageId })
        });
    }
</script>
