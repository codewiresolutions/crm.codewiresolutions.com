@extends('layouts.admin')

@section('title', 'Customer Management')

@section('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endsection

@section('content')
    @php $showModal = isset($contact) || $errors->any(); @endphp

    <div class="mb-4 flex items-center justify-between rounded-xl bg-white p-5 shadow-sm">
        <div>
            <h2 class="m-0 text-xl font-semibold">Customers</h2>
            <p class="text-sm text-gray-500">Create, update, and delete customer records.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="w-auto rounded-lg bg-blue-600 px-4.5 py-2.5 text-white hover:bg-blue-700" onclick="openCustomerModal()">+ Add Customer</button>
        </div>
    </div>

    <div id="customerModal" class="fixed inset-0 z-50 items-center justify-center bg-gray-900/50 {{ $showModal ? 'flex' : 'hidden' }}" onclick="if(event.target === this) closeCustomerModal()">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="m-0 text-lg font-semibold">{{ isset($contact) ? 'Update Customer' : 'Add Customer' }}</h3>
                <button type="button" class="w-auto border-none bg-transparent p-0 px-2 text-2xl leading-none text-gray-500 hover:text-gray-900" onclick="closeCustomerModal()">&times;</button>
            </div>
            <form action="{{ isset($contact) ? route('admin.customers.update', $contact) : route('admin.customers.store') }}" method="POST">
                @csrf
                @if(isset($contact))
                    @method('PUT')
                @endif
                <input type="text" name="name" placeholder="Name" value="{{ old('name', $contact->name ?? '') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5">
                <input type="email" name="email" placeholder="Email" value="{{ old('email', $contact->email ?? '') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5">
                <input type="text" name="phone_number" placeholder="Phone Number" value="{{ old('phone_number', $contact->phone_number ?? '') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5">

                <select name="user_type_id" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5">
                    <option value="" disabled {{ old('user_type_id', $contact->user_type_id ?? '') === '' ? 'selected' : '' }}>Select User Type</option>
                    @foreach($userTypes as $type)
                        <option value="{{ $type->id }}" {{ (string) old('user_type_id', $contact->user_type_id ?? '') === (string) $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                    @endforeach
                </select>
                <div id="descriptionEditor" class="mt-2 rounded-lg bg-white" style="height:150px;"></div>
                <textarea name="description" id="descriptionInput" class="hidden">{{ old('description', $contact->description ?? '') }}</textarea>
                <button type="submit" class="mt-2 w-full rounded-lg bg-blue-600 px-3 py-2.5 text-white hover:bg-blue-700">{{ isset($contact) ? 'Update Customer' : 'Create Customer' }}</button>
            </form>
        </div>
    </div>

    <div id="messageHistoryModal" class="fixed inset-0 z-50 hidden items-center justify-center mh-backdrop" onclick="if(event.target === this) closeMessageHistoryModal()">
        <div class="mh-card" role="dialog" aria-modal="true" aria-labelledby="messageHistoryName">
            <div class="mh-header">
                <div class="mh-header-main">
                    <div id="messageHistoryAvatar" class="mh-avatar"></div>
                    <div class="mh-header-text">
                        <div class="mh-name-row">
                            <h3 id="messageHistoryName" class="mh-name"></h3>
                            <span id="messageHistoryTypeBadge" class="mh-badge"></span>
                        </div>
                        <p id="messageHistorySubline" class="mh-subline"></p>
                    </div>
                </div>
                <button type="button" class="mh-close-btn" onclick="closeMessageHistoryModal()" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="mh-body">
                <div class="mh-details">
                    <div class="mh-detail-cols">
                        <div class="mh-detail-col">
                            <span class="mh-label">Email</span>
                            <div class="mh-value mh-value-row">
                                <a id="messageHistoryEmail" href="#" class="mh-link"></a>
                                <button type="button" class="mh-copy-btn" data-copy-target="email" onclick="copyMessageHistoryValue(this)">Copy</button>
                            </div>
                        </div>

                        <div class="mh-detail-col">
                            <span class="mh-label">Phone</span>
                            <div class="mh-value mh-value-row">
                                <span id="messageHistoryPhone" class="mh-tabular"></span>
                                <button type="button" class="mh-copy-btn" data-copy-target="phone" onclick="copyMessageHistoryValue(this)">Copy</button>
                            </div>
                        </div>

                        <div class="mh-detail-col">
                            <span class="mh-label">Last sent</span>
                            <div id="messageHistorySentAt" class="mh-value"></div>
                        </div>
                    </div>

                    <div class="mh-detail-row mh-detail-row-top">
                        <span class="mh-label">Description</span>
                        <div id="messageHistoryDescription" class="mh-value"></div>
                    </div>
                </div>

                <div class="mh-messages">
                    <div class="mh-messages-header">
                        <span class="mh-section-label">Messages</span>
                        <span id="messageHistoryCount" class="mh-count-pill"></span>
                    </div>
                    <div class="mh-messages-list-wrap">
                        <ul id="messageHistoryList" class="mh-messages-list"></ul>
                    </div>
                </div>
            </div>

            <div class="mh-footer">
                <select id="messageHistoryMessageSelect" required class="mh-message-select" onchange="updateMessageHistorySelectedMessage(this)">
                    <option value="" disabled selected>Select message</option>
                    @foreach($messages as $msg)
                        <option value="{{ $msg->message }}" data-id="{{ $msg->id }}">{{ $msg->title }}</option>
                    @endforeach
                </select>
                <button type="button" id="messageHistorySendBtn" class="mh-btn mh-btn-primary">Send WhatsApp</button>
            </div>
        </div>
    </div>

    <div id="whatsappChatModal" class="fixed inset-0 z-50 hidden items-center justify-center wc-backdrop" onclick="if(event.target === this) closeWhatsappChatModal()">
        <div class="wc-card" role="dialog" aria-modal="true" aria-labelledby="wcName">
            <div class="wc-header">
                <div class="wc-header-main">
                    <div id="wcAvatar" class="wc-avatar"></div>
                    <div class="min-w-0">
                        <h3 id="wcName" class="wc-name"></h3>
                        <p id="wcPhone" class="wc-phone"></p>
                    </div>
                </div>
                <button type="button" class="wc-close-btn" onclick="closeWhatsappChatModal()" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div id="wcBody" class="wc-body"></div>
            <div class="wc-footer">
                <input type="text" id="wcMessageInput" class="wc-input" placeholder="Type a message" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); sendWhatsappChatMessage(); }">
                <button type="button" id="wcSendBtn" class="wc-send-btn" title="Send" onclick="sendWhatsappChatMessage()">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                        <path d="M2.01 21l20.99-9-20.99-9-.01 7 15 2-15 2z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <div class="mb-4 flex gap-2">
            <button type="button" class="tab-btn w-auto rounded-md border border-blue-600 bg-blue-600 px-4 py-2 text-white" data-type="all" onclick="setCustomerTab('all', this)">All</button>
            <button type="button" class="tab-btn w-auto rounded-md border border-gray-200 bg-gray-100 px-4 py-2 text-gray-700" data-type="individual" onclick="setCustomerTab('individual', this)">Individual</button>
            <button type="button" class="tab-btn w-auto rounded-md border border-gray-200 bg-gray-100 px-4 py-2 text-gray-700" data-type="dealer" onclick="setCustomerTab('dealer', this)">Dealer</button>
            <button type="button" class="tab-btn w-auto rounded-md border border-gray-200 bg-gray-100 px-4 py-2 text-gray-700" data-type="groups" onclick="setCustomerTab('groups', this)">Groups</button>
        </div>

        <div id="customerListSection">
        <div class="mb-4 flex gap-2">
            <input type="text" id="customerSearch" placeholder="Search customers..." onkeyup="filterCustomers()" class="flex-1 rounded-lg border border-gray-300 px-3 py-2.5">
            <a href="{{ route('admin.customers.export') }}" class="w-auto bg-cyan-600 px-4.5 py-2.5 text-white no-underline hover:bg-cyan-700">Export CSV</a>
            <a href="{{ route('admin.customers.export-with-messages') }}" class="w-auto bg-cyan-600 px-4.5 py-2.5 text-white no-underline hover:bg-cyan-700">Export CSV with Msg</a>

        </div>

        <form id="bulkWhatsappForm" action="{{ route('admin.customers.bulk-send-whatsapp') }}" method="POST" class="mb-4 hidden flex-col gap-3 rounded-lg border border-emerald-200 bg-emerald-50 p-3" onsubmit="return prepareBulkWhatsappSubmit(this)">
            @csrf
            <span id="bulkSelectedCount" class="text-sm font-medium text-emerald-800">0 selected</span>
            <div id="bulkSelectedCustomers" class="flex flex-wrap gap-2"></div>
            <div class="flex items-center gap-2">
                <select id="bulkMessageSelect" name="message" required class="flex-1 rounded-lg border border-gray-300 px-2 py-1.5 text-sm">
                    <option value="" disabled selected>Select message</option>
                    @foreach($messages as $msg)
                        <option value="{{ $msg->message }}" data-id="{{ $msg->id }}">{{ $msg->title }}</option>
                    @endforeach
                </select>
                <button type="submit" title="Send WhatsApp to selected" class="inline-flex h-8.5 w-8.5 shrink-0 items-center justify-center rounded-lg border-none bg-transparent p-0 text-green-600 hover:bg-emerald-100">
                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-4.5 w-4.5">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.67.15-.198.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"></path>
                        <path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.334.098 11.892c0 2.096.549 4.14 1.588 5.945L0 24l6.335-1.652a11.95 11.95 0 0 0 5.702 1.447h.005c6.585 0 11.943-5.335 11.95-11.893a11.82 11.82 0 0 0-3.472-8.453zM12.042 21.751h-.004a9.933 9.933 0 0 1-5.068-1.387l-.363-.215-3.759.982 1.003-3.649-.237-.375a9.9 9.9 0 0 1-1.527-5.276c.006-5.473 4.474-9.93 9.96-9.93a9.9 9.9 0 0 1 7.036 2.923 9.86 9.86 0 0 1 2.917 7.021c-.006 5.473-4.474 9.906-9.958 9.906z"></path>
                    </svg>
                </button>
            </div>
            <div id="bulkContactIdsContainer"></div>
            <input type="hidden" name="message_id" id="bulkMessageIdInput">
        </form>

        <table id="customersTable" class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="border-b border-gray-200 p-2.5 text-left"><input type="checkbox" id="selectAllCustomers" onchange="toggleSelectAllCustomers(this)"></th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Name</th>
                    <th class="border-b border-gray-200 p-2.5 text-left ">C-Type</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Email</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Phone</th>
{{--                    <th class="border-b border-gray-200 p-2.5 text-left">Description</th>--}}

                    <th class="border-b border-gray-200 p-2.5 text-left">Sent At</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contactItem)
                    <tr data-user-type="{{ strtolower($contactItem->userType->name ?? '') }}" data-contact-id="{{ $contactItem->id }}" class="{{ $contactItem->is_interested ? 'row-interested' : '' }}">
                        <td class="border-b border-gray-200 p-2.5">
                            <input type="checkbox" class="customer-select-checkbox" value="{{ $contactItem->id }}" onchange="updateBulkWhatsappBar()">
                        </td>
                        <td class="border-b border-gray-200 p-2.5">
                            <button
                                type="button"
                                class="message-history-btn border-none bg-transparent p-0 pb-1"
                                onclick="openMessageHistoryModal({{ $contactItem->id }})"
                            >
                                {{ $contactItem->name }}
                            </button>
                        </td>
                        <td class="border-b border-gray-200 p-2.5 ">{{ $contactItem->userType->name ?? '' }}</td>
                        <td class="border-b border-gray-200 p-2.5">{{ $contactItem->email ?? '------' }}</td>
                        <td class="border-b border-gray-200 p-2.5" title="Double-click to mark as interested" ondblclick="toggleInterested({{ $contactItem->id }}, this)">{{ $contactItem->phone_number }}</td>
{{--                        <td class="border-b border-gray-200 p-2.5">{{ $contactItem->description }}</td>--}}

                        <td class="border-b border-gray-200 p-2.5">{{ $contactItem->message_sent_at ? $contactItem->message_sent_at : 'Not sent yet' }}</td>
                        <td class="flex items-center gap-0.5 whitespace-nowrap border-b border-gray-200 p-2.5">
                            <button
                                type="button"
                                title="WhatsApp Chat"
                                class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-lg border-none bg-transparent p-0 text-emerald-600 hover:bg-emerald-50"
                                onclick="openWhatsappChatModal({{ $contactItem->id }})"
                            >
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
                                    <rect x="7" y="2" width="10" height="20" rx="2" ry="2"></rect>
                                    <line x1="11" y1="18" x2="13" y2="18"></line>
                                </svg>
                            </button>
                            <a href="{{ route('admin.customers.edit', $contactItem) }}" title="Edit" class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-lg text-blue-600 no-underline hover:bg-blue-50">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.customers.send-whatsapp') }}" method="POST" class="inline-flex items-center gap-1">
                                @csrf
                                <input type="hidden" name="number" value="{{ $contactItem->phone_number }}">
                                <input type="hidden" name="message_id" class="js-message-id" value="{{ $contactItem->selectedmessage }}">
                                <select name="message" required class="rounded-lg border border-gray-300 px-1.5 py-1.5 text-xs" onclick="event.stopPropagation()" data-update-url="{{ route('admin.customers.update-selected-message', $contactItem) }}" onchange="updateSelectedMessage(this)">
                                    <option value="" disabled {{ !$contactItem->selectedmessage ? 'selected' : '' }}>Select message</option>
                                    @foreach($messages as $msg)
                                        <option value="{{ $msg->message }}" data-id="{{ $msg->id }}" {{ (int) $contactItem->selectedmessage === $msg->id ? 'selected' : '' }}>{{ $msg->title }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" title="Send WhatsApp" class="inline-flex h-8.5 w-8.5 shrink-0 items-center justify-center rounded-lg border-none bg-transparent p-0 text-green-600 hover:bg-emerald-50">
                                    <svg viewBox="0 0 24 24" fill="currentColor" class="h-4.5 w-4.5">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.67.15-.198.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"></path>
                                        <path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.334.098 11.892c0 2.096.549 4.14 1.588 5.945L0 24l6.335-1.652a11.95 11.95 0 0 0 5.702 1.447h.005c6.585 0 11.943-5.335 11.95-11.893a11.82 11.82 0 0 0-3.472-8.453zM12.042 21.751h-.004a9.933 9.933 0 0 1-5.068-1.387l-.363-.215-3.759.982 1.003-3.649-.237-.375a9.9 9.9 0 0 1-1.527-5.276c.006-5.473 4.474-9.93 9.96-9.93a9.9 9.9 0 0 1 7.036 2.923 9.86 9.86 0 0 1 2.917 7.021c-.006 5.473-4.474 9.906-9.958 9.906z"></path>
                                    </svg>
                                </button>
                            </form>
                            <form action="{{ route('admin.customers.destroy', $contactItem) }}" method="POST" class="inline" onsubmit="return confirmDelete(event);">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-lg border-none bg-transparent p-0 text-red-600 hover:bg-red-50">
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
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $contacts->links() }}
        </div>
        </div>

        <div id="groupsSection" class="hidden">
            @include('admin.partials.groups-table')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        var csrfToken = '{{ csrf_token() }}';

        function updateSelectedMessage(selectEl) {
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

        function toggleInterested(contactId, cellEl) {
            var row = cellEl.closest('tr');
            var wasInterested = row.classList.contains('row-interested');
            row.classList.toggle('row-interested');

            fetch('{{ url('admin/customers') }}/' + contactId + '/toggle-interested', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(function (res) {
                    if (!res.ok) throw new Error('Request failed with status ' + res.status);
                    return res.json();
                })
                .then(function (data) {
                    row.classList.toggle('row-interested', data.is_interested);
                })
                .catch(function (err) {
                    console.error(err);
                    row.classList.toggle('row-interested', wasInterested);
                });
        }

        function updateBulkWhatsappBar() {
            var allCheckboxes = document.querySelectorAll('.customer-select-checkbox');
            var checkboxes = document.querySelectorAll('.customer-select-checkbox:checked');
            var bar = document.getElementById('bulkWhatsappForm');
            document.getElementById('bulkSelectedCount').textContent = checkboxes.length + ' selected';

            var selectAll = document.getElementById('selectAllCustomers');
            if (selectAll) {
                selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
                selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
            }

            var chipContainer = document.getElementById('bulkSelectedCustomers');
            chipContainer.innerHTML = '';
            checkboxes.forEach(function (cb) {
                var row = cb.closest('tr');
                var nameBtn = row ? row.querySelector('.message-history-btn') : null;
                var name = nameBtn ? nameBtn.textContent.trim() : cb.value;
                var contactId = cb.value;

                var chip = document.createElement('span');
                chip.className = 'inline-flex items-center gap-1 rounded-full border border-emerald-300 bg-white px-2 py-1 text-xs text-emerald-800';

                var label = document.createElement('span');
                label.textContent = name;
                chip.appendChild(label);

                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.title = 'Remove ' + name;
                removeBtn.className = 'font-bold leading-none text-emerald-600 hover:text-emerald-900';
                removeBtn.textContent = '×';
                removeBtn.onclick = function () { removeCustomerFromBulk(contactId); };
                chip.appendChild(removeBtn);

                chipContainer.appendChild(chip);
            });

            if (checkboxes.length >= 2) {
                bar.classList.remove('hidden');
                bar.classList.add('flex');
            } else if (checkboxes.length === 0) {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
            }
        }

        function toggleSelectAllCustomers(checkbox) {
            document.querySelectorAll('.customer-select-checkbox').forEach(function (cb) {
                cb.checked = checkbox.checked;
            });
            updateBulkWhatsappBar();
        }

        function removeCustomerFromBulk(contactId) {
            var checkbox = document.querySelector('.customer-select-checkbox[value="' + contactId + '"]');
            if (checkbox) checkbox.checked = false;
            updateBulkWhatsappBar();
        }

        function prepareBulkWhatsappSubmit() {
            var checkboxes = document.querySelectorAll('.customer-select-checkbox:checked');
            if (checkboxes.length < 2) return false;

            var messageSelect = document.getElementById('bulkMessageSelect');
            var option = messageSelect.options[messageSelect.selectedIndex];
            if (!option || !option.dataset.id) return false;

            document.getElementById('bulkMessageIdInput').value = option.dataset.id;

            var container = document.getElementById('bulkContactIdsContainer');
            container.innerHTML = '';
            checkboxes.forEach(function (cb) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'contact_ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });

            return true;
        }

        var messageHistoryMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var messageHistoryCurrentEmail = '';
        var messageHistoryCurrentPhone = '';
        var messageHistoryCurrentContactId = null;

        function updateMessageHistorySelectedMessage(selectEl) {
            var option = selectEl.options[selectEl.selectedIndex];
            var messageId = option ? option.dataset.id : null;
            if (!messageId || !messageHistoryCurrentContactId) return;

            var row = document.querySelector('tr[data-contact-id="' + messageHistoryCurrentContactId + '"]');
            if (row) {
                var rowSelect = row.querySelector('form[action*="send-whatsapp"] select[name="message"]');
                var rowHiddenInput = row.querySelector('.js-message-id');
                if (rowSelect) {
                    var rowOption = rowSelect.querySelector('option[data-id="' + messageId + '"]');
                    if (rowOption) rowSelect.value = rowOption.value;
                }
                if (rowHiddenInput) rowHiddenInput.value = messageId;
            }

            fetch('{{ url('admin/customers') }}/' + messageHistoryCurrentContactId + '/selected-message', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message_id: messageId })
            });
        }

        function parseMessageHistoryDate(value) {
            if (!value) return null;
            var d = new Date(value.replace(' ', 'T'));
            return isNaN(d.getTime()) ? null : d;
        }

        function formatMessageHistoryDateTime(value) {
            var d = parseMessageHistoryDate(value);
            if (!d) return null;
            var hours = d.getHours();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            var hours12 = hours % 12 || 12;
            var minutes = ('0' + d.getMinutes()).slice(-2);
            return messageHistoryMonths[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear() + ' · ' + hours12 + ':' + minutes + ' ' + ampm;
        }

        function formatMessageHistoryDateOnly(value) {
            var d = parseMessageHistoryDate(value);
            if (!d) return null;
            return messageHistoryMonths[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
        }

        function formatMessageHistoryShortDateTime(value) {
            var d = parseMessageHistoryDate(value);
            if (!d) return value;
            var hours = ('0' + d.getHours()).slice(-2);
            var minutes = ('0' + d.getMinutes()).slice(-2);
            return messageHistoryMonths[d.getMonth()] + ' ' + d.getDate() + ' · ' + hours + ':' + minutes;
        }

        function formatMessageHistoryPhone(raw) {
            if (!raw) return '';
            var digits = raw.replace(/\D/g, '');
            if (digits.indexOf('92') === 0) digits = digits.slice(2);
            else if (digits.indexOf('0') === 0) digits = digits.slice(1);
            if (digits.length !== 10) return raw;
            return '+92 ' + digits.slice(0, 3) + ' ' + digits.slice(3);
        }

        function getMessageHistoryInitials(name) {
            if (!name) return '';
            var parts = name.trim().split(/\s+/).slice(0, 2);
            return parts.map(function (p) { return p.charAt(0).toUpperCase(); }).join('');
        }

        function copyMessageHistoryValue(btn) {
            var target = btn.dataset.copyTarget;
            var value = target === 'email' ? messageHistoryCurrentEmail : messageHistoryCurrentPhone;
            if (!value) return;

            navigator.clipboard.writeText(value).then(function () {
                var original = btn.textContent;
                btn.textContent = '✓';
                setTimeout(function () { btn.textContent = original; }, 1500);
            });
        }

        function openMessageHistoryModal(contactId) {
            fetch('{{ url('admin/customers') }}/' + contactId + '/messages', {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (res) {
                    if (!res.ok) throw new Error('Request failed with status ' + res.status);
                    return res.json();
                })
                .then(function (data) {
                    messageHistoryCurrentContactId = contactId;
                    messageHistoryCurrentEmail = data.email || '';
                    messageHistoryCurrentPhone = data.phone_number || '';

                    document.getElementById('messageHistoryAvatar').textContent = getMessageHistoryInitials(data.name);
                    document.getElementById('messageHistoryName').textContent = data.name;
                    document.getElementById('messageHistoryTypeBadge').textContent = data.type || '';
                    document.getElementById('messageHistoryTypeBadge').style.display = data.type ? '' : 'none';

                    var since = formatMessageHistoryDateOnly(data.created_at);
                    var count = data.messages.length;
                    document.getElementById('messageHistorySubline').textContent =
                        (since ? 'Customer since ' + since : 'New customer') + ' · ' + count + (count === 1 ? ' message sent' : ' messages sent');

                    var emailLink = document.getElementById('messageHistoryEmail');
                    emailLink.textContent = data.email || '------';
                    emailLink.href = data.email ? 'mailto:' + data.email : '#';

                    document.getElementById('messageHistoryPhone').textContent = formatMessageHistoryPhone(data.phone_number) || '------';
                    document.getElementById('messageHistorySentAt').textContent = formatMessageHistoryDateTime(data.message_sent_at) || 'Not sent yet';
                    document.getElementById('messageHistoryDescription').innerHTML = data.description || 'No description';
                    document.getElementById('messageHistoryCount').textContent = String(count);

                    var messageSelect = document.getElementById('messageHistoryMessageSelect');
                    var selectedOption = data.selectedmessage
                        ? messageSelect.querySelector('option[data-id="' + data.selectedmessage + '"]')
                        : null;
                    messageSelect.value = selectedOption ? selectedOption.value : '';

                    var list = document.getElementById('messageHistoryList');
                    list.innerHTML = '';

                    if (!data.messages.length) {
                        var empty = document.createElement('li');
                        empty.className = 'mh-messages-empty';
                        empty.textContent = 'No messages sent yet.';
                        list.appendChild(empty);
                    } else {
                        data.messages.forEach(function (log) {
                            var li = document.createElement('li');
                            li.className = 'mh-message-row';

                            var msgSpan = document.createElement('span');
                            msgSpan.className = 'mh-message-text';
                            msgSpan.textContent = log.message;

                            var timeSpan = document.createElement('span');
                            timeSpan.className = 'mh-message-time';
                            timeSpan.textContent = formatMessageHistoryShortDateTime(log.sent_at);

                            li.appendChild(msgSpan);
                            li.appendChild(timeSpan);
                            list.appendChild(li);
                        });
                    }

                    document.getElementById('messageHistoryModal').classList.remove('hidden');
                    document.getElementById('messageHistoryModal').classList.add('flex');
                })
                .catch(function (err) {
                    console.error(err);
                    alert('Unable to load customer details. Please try again.');
                });
        }
        function closeMessageHistoryModal() {
            document.getElementById('messageHistoryModal').classList.remove('flex');
            document.getElementById('messageHistoryModal').classList.add('hidden');
        }

        var wcCurrentContactId = null;

        function appendWhatsappChatBubble(data) {
            var body = document.getElementById('wcBody');
            var empty = body.querySelector('.wc-empty');
            if (empty) empty.remove();

            var d = new Date();
            var dateLabel = formatMessageHistoryDateOnly(d.toISOString());
            var lastSep = body.querySelector('.wc-date-sep:last-of-type span');
            if (!lastSep || lastSep.textContent !== dateLabel) {
                var sep = document.createElement('div');
                sep.className = 'wc-date-sep';
                var sepLabel = document.createElement('span');
                sepLabel.textContent = dateLabel;
                sep.appendChild(sepLabel);
                body.appendChild(sep);
            }

            var row = document.createElement('div');
            row.className = 'wc-row wc-row-sent';

            var bubble = document.createElement('div');
            bubble.className = 'wc-bubble wc-bubble-sent';

            var text = document.createElement('div');
            text.className = 'wc-bubble-text';
            text.textContent = data.message;
            bubble.appendChild(text);

            var time = document.createElement('div');
            time.className = 'wc-bubble-time';
            time.textContent = ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
            bubble.appendChild(time);

            row.appendChild(bubble);
            body.appendChild(row);
            body.scrollTop = body.scrollHeight;
        }

        function sendWhatsappChatMessage() {
            var input = document.getElementById('wcMessageInput');
            var message = input.value.trim();
            if (!message || !wcCurrentContactId) return;

            var row = document.querySelector('tr[data-contact-id="' + wcCurrentContactId + '"]');
            var phoneInput = row ? row.querySelector('input[name="number"]') : null;
            var number = phoneInput ? phoneInput.value : null;
            if (!number) return;

            var sendBtn = document.getElementById('wcSendBtn');
            sendBtn.disabled = true;

            fetch('{{ route('admin.customers.send-whatsapp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ number: number, message: message })
            })
                .then(function (res) {
                    if (!res.ok) throw new Error('Request failed with status ' + res.status);
                    input.value = '';
                    wcFetchSeq++;
                    appendWhatsappChatBubble({ message: message });
                    var sentAtCell = row ? row.children[5] : null;
                    if (sentAtCell) sentAtCell.textContent = 'Just now';
                    if (wcCurrentContactId) fetchWhatsappChat(wcCurrentContactId, true);
                })
                .catch(function (err) {
                    console.error(err);
                    alert('Unable to send WhatsApp message. Please try again.');
                })
                .finally(function () {
                    sendBtn.disabled = false;
                });
        }

        var wcPollTimer = null;
        var wcPollIntervalMs = 5000;
        var wcMessagesSignature = null;
        var wcFetchSeq = 0;

        function renderWhatsappChatMessages(data) {
            document.getElementById('wcAvatar').textContent = getMessageHistoryInitials(data.name);
            document.getElementById('wcName').textContent = data.name;
            document.getElementById('wcPhone').textContent = formatMessageHistoryPhone(data.phone_number);

            var body = document.getElementById('wcBody');
            var wasNearBottom = (body.scrollHeight - body.scrollTop - body.clientHeight) < 60;
            body.innerHTML = '';

            if (!data.messages.length) {
                var empty = document.createElement('div');
                empty.className = 'wc-empty';
                empty.textContent = 'No conversation yet.';
                body.appendChild(empty);
            } else {
                var lastDateLabel = null;

                data.messages.forEach(function (m) {
                    var d = parseMessageHistoryDate(m.timestamp);
                    var dateLabel = d ? formatMessageHistoryDateOnly(m.timestamp) : null;

                    if (dateLabel && dateLabel !== lastDateLabel) {
                        var sep = document.createElement('div');
                        sep.className = 'wc-date-sep';
                        var sepLabel = document.createElement('span');
                        sepLabel.textContent = dateLabel;
                        sep.appendChild(sepLabel);
                        body.appendChild(sep);
                        lastDateLabel = dateLabel;
                    }

                    var row = document.createElement('div');
                    row.className = 'wc-row ' + (m.direction === 'sent' ? 'wc-row-sent' : 'wc-row-received');

                    var bubble = document.createElement('div');
                    bubble.className = 'wc-bubble ' + (m.direction === 'sent' ? 'wc-bubble-sent' : 'wc-bubble-received');

                    var text = document.createElement('div');
                    text.className = 'wc-bubble-text';
                    text.textContent = m.message || (m.type && m.type !== 'text' ? '[' + m.type + ']' : '');
                    bubble.appendChild(text);

                    var time = document.createElement('div');
                    time.className = 'wc-bubble-time';
                    time.textContent = d ? (('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2)) : '';
                    bubble.appendChild(time);

                    row.appendChild(bubble);
                    body.appendChild(row);
                });
            }

            if (wasNearBottom) body.scrollTop = body.scrollHeight;
        }

        function fetchWhatsappChat(contactId, isPoll) {
            var seq = ++wcFetchSeq;

            fetch('{{ url('admin/customers') }}/' + contactId + '/whatsapp-chat', {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (res) {
                    if (!res.ok) throw new Error('Request failed with status ' + res.status);
                    return res.json();
                })
                .then(function (data) {
                    if (seq !== wcFetchSeq || wcCurrentContactId !== contactId) return;

                    var signature = data.messages.length + ':' + (data.messages.length ? data.messages[data.messages.length - 1].timestamp : '');
                    if (isPoll && signature === wcMessagesSignature) return;
                    wcMessagesSignature = signature;

                    renderWhatsappChatMessages(data);
                })
                .catch(function (err) {
                    console.error(err);
                    if (!isPoll) alert('Unable to load WhatsApp chat. Please try again.');
                });
        }

        function openWhatsappChatModal(contactId) {
            wcCurrentContactId = contactId;
            wcMessagesSignature = null;
            document.getElementById('wcMessageInput').value = '';

            document.getElementById('whatsappChatModal').classList.remove('hidden');
            document.getElementById('whatsappChatModal').classList.add('flex');

            fetchWhatsappChat(contactId, false);

            if (wcPollTimer) clearInterval(wcPollTimer);
            wcPollTimer = setInterval(function () {
                if (wcCurrentContactId) fetchWhatsappChat(wcCurrentContactId, true);
            }, wcPollIntervalMs);
        }

        function closeWhatsappChatModal() {
            document.getElementById('whatsappChatModal').classList.remove('flex');
            document.getElementById('whatsappChatModal').classList.add('hidden');
            wcCurrentContactId = null;
            wcMessagesSignature = null;
            if (wcPollTimer) {
                clearInterval(wcPollTimer);
                wcPollTimer = null;
            }
        }

        document.getElementById('messageHistorySendBtn').addEventListener('click', function () {
            if (!messageHistoryCurrentContactId) return;
            var row = document.querySelector('tr[data-contact-id="' + messageHistoryCurrentContactId + '"]');
            var form = row && row.querySelector('form[action*="send-whatsapp"]');
            if (!form) return;
            if (form.requestSubmit) form.requestSubmit(); else form.submit();
        });

        var descriptionQuill = null;
        function initDescriptionEditor() {
            if (descriptionQuill) return;
            descriptionQuill = new Quill('#descriptionEditor', {
                theme: 'snow',
                placeholder: 'Description',
            });
            var hidden = document.getElementById('descriptionInput');
            descriptionQuill.root.innerHTML = hidden.value;
            descriptionQuill.on('text-change', function () {
                hidden.value = descriptionQuill.root.innerHTML;
            });
        }

        function openCustomerModal() {
            document.getElementById('customerModal').classList.remove('hidden');
            document.getElementById('customerModal').classList.add('flex');
            initDescriptionEditor();
        }
        function closeCustomerModal() {
            document.getElementById('customerModal').classList.remove('flex');
            document.getElementById('customerModal').classList.add('hidden');
        }
        @if($showModal)
            initDescriptionEditor();
        @endif
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeCustomerModal();
                closeMessageHistoryModal();
                closeWhatsappChatModal();
            }
        });

        var activeCustomerTab = 'all';
        var tabActiveClasses = ['bg-blue-600', 'text-white', 'border-blue-600'];
        var tabInactiveClasses = ['bg-gray-100', 'text-gray-700', 'border-gray-200'];

        function setCustomerTab(type, btn) {
            activeCustomerTab = type;
            document.querySelectorAll('.tab-btn').forEach(function (b) {
                b.classList.remove.apply(b.classList, tabActiveClasses);
                b.classList.add.apply(b.classList, tabInactiveClasses);
            });
            btn.classList.remove.apply(btn.classList, tabInactiveClasses);
            btn.classList.add.apply(btn.classList, tabActiveClasses);

            var listSection = document.getElementById('customerListSection');
            var groupsSection = document.getElementById('groupsSection');

            if (type === 'groups') {
                listSection.classList.add('hidden');
                groupsSection.classList.remove('hidden');
            } else {
                groupsSection.classList.add('hidden');
                listSection.classList.remove('hidden');
                filterCustomers();
            }

            var url = new URL(window.location.href);
            if (type === 'all') {
                url.searchParams.delete('tab');
            } else {
                url.searchParams.set('tab', type);
            }
            history.replaceState(null, '', url);
        }

        (function () {
            var tab = new URLSearchParams(window.location.search).get('tab');
            if (!['all', 'individual', 'dealer', 'groups'].includes(tab)) return;
            var btn = document.querySelector('.tab-btn[data-type="' + tab + '"]');
            if (btn) setCustomerTab(tab, btn);
        })();

        function confirmDelete(event) {
            event.preventDefault();
            var form = event.target;
            Swal.fire({
                title: 'Delete this customer?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Delete'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        function filterCustomers() {
            var query = document.getElementById('customerSearch').value.trim().toLowerCase();
            var rows = document.querySelectorAll('#customersTable tbody tr');
            rows.forEach(function (row) {
                var matchesTab = activeCustomerTab === 'all' || row.dataset.userType === activeCustomerTab;
                var matchesSearch = row.textContent.toLowerCase().includes(query);
                row.style.display = (matchesTab && matchesSearch) ? '' : 'none';
            });
        }
    </script>
    <style>

        tr.row-interested td {
            background-color: #dcfce7;
        }

        tr.row-interested td:first-child {
            box-shadow: inset 4px 0 0 0 #80af61;
        }

        .message-history-btn {
            color: #6b7484;
            text-decoration-line: underline;
            text-decoration-style: dashed;
            text-decoration-color: #c3c9d4;
            text-underline-offset: 3px;
        }

        /* Message history modal */
        .mh-backdrop {
            background: rgba(15, 23, 42, 0.5);
        }

        .mh-card {
            width: 760px;
            max-width: calc(100vw - 48px);
            max-height: calc(100vh - 64px);
            display: flex;
            flex-direction: column;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 24px 64px rgba(15, 23, 42, 0.35);
            font-family: 'Public Sans', system-ui, -apple-system, sans-serif;
            overflow: hidden;
        }

        .mh-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 28px 32px;
            background: linear-gradient(135deg, #f5f8ff 0%, #ffffff 65%);
            border-bottom: 1px solid #eef1f5;
        }

        .mh-header-main {
            display: flex;
            align-items: center;
            gap: 18px;
            min-width: 0;
        }

        .mh-avatar {
            flex-shrink: 0;
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #e8eefc;
            color: #2557d6;
            font-weight: 700;
            font-size: 19px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.6) inset, 0 6px 14px rgba(37, 87, 214, 0.14);
        }

        .mh-header-text {
            min-width: 0;
        }

        .mh-name-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .mh-name {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #1a2233;
            letter-spacing: -0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mh-badge {
            flex-shrink: 0;
            padding: 4px 10px;
            border-radius: 999px;
            background: #e8eefc;
            color: #2557d6;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .mh-subline {
            margin: 6px 0 0;
            font-size: 13.5px;
            color: #6b7484;
        }

        .mh-close-btn {
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #6b7484;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            transition: background-color 0.15s ease;
        }

        .mh-close-btn:hover {
            background: #f2f4f8;
        }

        .mh-body {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 24px 32px;
            overflow-y: auto;
        }

        .mh-details {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        .mh-detail-cols {
            display: flex;
            gap: 20px;
        }

        .mh-detail-col {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .mh-detail-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mh-detail-row-top {
            align-items: flex-start;
        }

        .mh-label {
            flex: 0 0 110px;
            font-size: 12px;
            font-weight: 600;
            color: #8a92a3;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .mh-detail-col .mh-label {
            flex: 0 0 auto;
        }

        .mh-value {
            flex: 1;
            font-size: 14px;
            color: #1a2233;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mh-detail-row-top .mh-value {
            white-space: normal;
            word-break: break-word;
        }

        .mh-value-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mh-link {
            color: #1a2233;
            text-decoration: none;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mh-link:hover {
            text-decoration: underline;
        }

        .mh-tabular {
            font-variant-numeric: tabular-nums;
        }

        .mh-copy-btn {
            flex-shrink: 0;
            border: 1px solid #d9dee7;
            background: #ffffff;
            color: #4b5565;
            font-size: 11.5px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 6px;
            cursor: pointer;
            white-space: nowrap;
        }

        .mh-copy-btn:hover {
            background: #f2f4f8;
        }

        .mh-messages {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .mh-messages-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .mh-section-label {
            font-size: 12px;
            font-weight: 600;
            color: #8a92a3;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .mh-count-pill {
            padding: 2px 9px;
            border-radius: 999px;
            background: #eef1f5;
            color: #4b5565;
            font-size: 12px;
            font-weight: 600;
        }

        .mh-messages-list-wrap {
            max-height: 220px;
            overflow-y: auto;
            padding: 2px;
        }

        .mh-messages-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .mh-message-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
            padding: 12px;
            border: 1px solid #eef1f5;
            border-radius: 10px;
            background: #fafbfc;
        }

        .mh-message-text {
            font-size: 13.5px;
            color: #1a2233;
            min-width: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .mh-message-time {
            flex-shrink: 0;
            font-size: 12px;
            color: #a5adbd;
        }

        .mh-messages-empty {
            grid-column: 1 / -1;
            padding: 12px;
            font-size: 13px;
            color: #a5adbd;
        }

        .mh-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px 32px;
            background: #fafbfc;
            border-top: 1px solid #eef1f5;
        }

        .mh-message-select {
            flex: 1;
            min-width: 0;
            height: 40px;
            padding: 0 12px;
            border: 1px solid #d9dee7;
            border-radius: 9px;
            background: #ffffff;
            color: #1a2233;
            font-size: 13.5px;
        }

        .mh-btn {
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 18px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            text-decoration: none;
            box-sizing: border-box;
            transition: background-color 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
        }

        .mh-btn-primary {
            border: none;
            background: #1faa53;
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(31, 170, 83, 0.28);
        }

        .mh-btn-primary:hover {
            background: #188a44;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(31, 170, 83, 0.32);
        }

        .mh-btn-primary:active {
            transform: translateY(0);
        }

        /* WhatsApp chat modal */
        .wc-backdrop {
            background: rgba(15, 23, 42, 0.5);
        }

        .wc-card {
            width: 420px;
            max-width: calc(100vw - 48px);
            max-height: calc(100vh - 64px);
            display: flex;
            flex-direction: column;
            background: #efeae2;
            border-radius: 16px;
            box-shadow: 0 24px 64px rgba(15, 23, 42, 0.35);
            font-family: 'Public Sans', system-ui, -apple-system, sans-serif;
            overflow: hidden;
        }

        .wc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            background: #075e54;
            color: #ffffff;
        }

        .wc-header-main {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .wc-avatar {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #128c7e;
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .wc-name {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wc-phone {
            margin: 2px 0 0;
            font-size: 12px;
            color: #d9f2ee;
        }

        .wc-close-btn {
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            transition: background-color 0.15s ease;
        }

        .wc-close-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .wc-footer {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #f0f2f5;
            border-top: 1px solid #e4e6ea;
        }

        .wc-input {
            flex: 1;
            min-width: 0;
            height: 40px;
            padding: 0 14px;
            border: none;
            border-radius: 20px;
            background: #ffffff;
            color: #111b21;
            font-size: 13.5px;
        }

        .wc-input:focus {
            outline: 2px solid #128c7e33;
        }

        .wc-send-btn {
            flex-shrink: 0;
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 50%;
            background: #128c7e;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            transition: background-color 0.15s ease;
        }

        .wc-send-btn:hover {
            background: #075e54;
        }

        .wc-send-btn:disabled {
            opacity: 0.6;
            cursor: default;
        }

        .wc-body {
            flex: 1;
            min-height: 320px;
            overflow-y: auto;
            padding: 16px;
            background-color: #efeae2;
            background-image: radial-gradient(rgba(0, 0, 0, 0.04) 1px, transparent 1px);
            background-size: 14px 14px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .wc-empty {
            margin: auto;
            font-size: 13px;
            color: #8a92a3;
        }

        .wc-date-sep {
            align-self: center;
            margin: 10px 0;
        }

        .wc-date-sep span {
            background: #e1f0fb;
            color: #4b5565;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .wc-row {
            display: flex;
            margin: 2px 0;
        }

        .wc-row-sent {
            justify-content: flex-end;
        }

        .wc-row-received {
            justify-content: flex-start;
        }

        .wc-bubble {
            max-width: 75%;
            padding: 6px 9px 8px;
            border-radius: 8px;
            box-shadow: 0 1px 0.5px rgba(0, 0, 0, 0.13);
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .wc-bubble-sent {
            background: #d9fdd3;
            border-top-right-radius: 0;
        }

        .wc-bubble-received {
            background: #ffffff;
            border-top-left-radius: 0;
        }

        .wc-bubble-text {
            font-size: 13.5px;
            color: #111b21;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .wc-bubble-time {
            align-self: flex-end;
            font-size: 10.5px;
            color: #667781;
        }

    </style>
@endsection
