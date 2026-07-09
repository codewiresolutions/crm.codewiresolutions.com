@extends('layouts.admin')

@section('title', 'Customer Management')

@section('content')
    @php $showModal = isset($contact) || $errors->any(); @endphp

    <div class="mb-4 flex items-center justify-between rounded-xl bg-white p-5 shadow-sm">
        <div>
            <h2 class="m-0 text-xl font-semibold">Customers</h2>
            <p class="text-sm text-gray-500">Create, update, and delete customer records.</p>
        </div>
        <button type="button" class="w-auto rounded-lg bg-blue-600 px-4.5 py-2.5 text-white hover:bg-blue-700" onclick="openCustomerModal()">+ Add Customer</button>
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

                <select name="user_type" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5">
                    <option value="" disabled {{ old('user_type', $contact->user_type ?? '') === '' ? 'selected' : '' }}>Select User Type</option>
                    <option value="individual" {{ old('user_type', $contact->user_type ?? '') === 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="dealer" {{ old('user_type', $contact->user_type ?? '') === 'dealer' ? 'selected' : '' }}>Dealer</option>
                </select>
                <textarea name="description" rows="4" placeholder="Description" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5">{{ old('description', $contact->description ?? '') }}</textarea>
                <button type="submit" class="mt-2 w-full rounded-lg bg-blue-600 px-3 py-2.5 text-white hover:bg-blue-700">{{ isset($contact) ? 'Update Customer' : 'Create Customer' }}</button>
            </form>
        </div>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <div class="mb-4 flex gap-2">
            <button type="button" class="tab-btn w-auto rounded-md border border-blue-600 bg-blue-600 px-4 py-2 text-white" data-type="all" onclick="setCustomerTab('all', this)">All</button>
            <button type="button" class="tab-btn w-auto rounded-md border border-gray-200 bg-gray-100 px-4 py-2 text-gray-700" data-type="individual" onclick="setCustomerTab('individual', this)">Individual</button>
            <button type="button" class="tab-btn w-auto rounded-md border border-gray-200 bg-gray-100 px-4 py-2 text-gray-700" data-type="dealer" onclick="setCustomerTab('dealer', this)">Dealer</button>
        </div>
        <input type="text" id="customerSearch" placeholder="Search customers..." onkeyup="filterCustomers()" class="mb-4 w-full rounded-lg border border-gray-300 px-3 py-2.5">
        <table id="customersTable" class="w-full border-collapse">
            <thead>
                <tr>
                    <th class="border-b border-gray-200 p-2.5 text-left">Name</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">User Type</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Email</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Phone</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Description</th>

                    <th class="border-b border-gray-200 p-2.5 text-left">Message At</th>
                    <th class="border-b border-gray-200 p-2.5 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contactItem)
                    <tr data-user-type="{{ $contactItem->user_type }}">
                        <td class="border-b border-gray-200 p-2.5">{{ $contactItem->name }}</td>
                        <td class="border-b border-gray-200 p-2.5">{{ ucfirst($contactItem->user_type) }}</td>
                        <td class="border-b border-gray-200 p-2.5">{{ $contactItem->email }}</td>
                        <td class="border-b border-gray-200 p-2.5">{{ $contactItem->phone_number }}</td>
                        <td class="border-b border-gray-200 p-2.5">{{ $contactItem->description }}</td>

                        <td class="border-b border-gray-200 p-2.5">{{ $contactItem->message_sent_at ? $contactItem->message_sent_at : 'Not sent yet' }}</td>
                        <td class="flex items-center gap-0.5 whitespace-nowrap border-b border-gray-200 p-2.5">
                            <a href="{{ route('admin.customers.edit', $contactItem) }}" title="Edit" class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-lg text-blue-600 no-underline hover:bg-blue-50">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.customers.send-whatsapp') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="number" value="{{ $contactItem->phone_number }}">
                                <input type="hidden" name="message" value="{{ $latestMessage->message ?? '' }}">
                                <button type="submit" title="Send WhatsApp" class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-lg border-none bg-transparent p-0 text-green-600 hover:bg-emerald-50">
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
    </div>

    <script>
        function openCustomerModal() {
            document.getElementById('customerModal').classList.remove('hidden');
            document.getElementById('customerModal').classList.add('flex');
        }
        function closeCustomerModal() {
            document.getElementById('customerModal').classList.remove('flex');
            document.getElementById('customerModal').classList.add('hidden');
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeCustomerModal();
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
            filterCustomers();
        }

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
@endsection
