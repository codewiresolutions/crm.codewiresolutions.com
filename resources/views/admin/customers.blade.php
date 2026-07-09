@extends('layouts.admin')

@section('title', 'Customer Management')

@section('styles')
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.5);
        align-items: center;
        justify-content: center;
        z-index: 50;
    }
    .modal-box {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .modal-header h3 { margin: 0; }
    .modal-close {
        width: auto;
        padding: 0 8px;
        background: transparent;
        color: #6b7280;
        font-size: 22px;
        line-height: 1;
        margin: 0;
        border: none;
    }
    .modal-close:hover { color: #111827; }
    .tab-btn {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
        padding: 8px 16px;
        border-radius: 6px;
    }
    .tab-btn.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    #customersTable .actions {
        gap: 2px;
    }
@endsection

@section('content')
    @php $showModal = isset($contact) || $errors->any(); @endphp

    <div class="card" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h2>Customers</h2>
            <p class="muted">Create, update, and delete customer records.</p>
        </div>
        <button type="button" style="width:auto; padding:10px 18px;" onclick="openCustomerModal()">+ Add Customer</button>
    </div>

    <div id="customerModal" class="modal-overlay" style="{{ $showModal ? 'display:flex;' : 'display:none;' }}" onclick="if(event.target === this) closeCustomerModal()">
        <div class="modal-box">
            <div class="modal-header">
                <h3>{{ isset($contact) ? 'Update Customer' : 'Add Customer' }}</h3>
                <button type="button" class="modal-close" onclick="closeCustomerModal()">&times;</button>
            </div>
            <form action="{{ isset($contact) ? route('admin.customers.update', $contact) : route('admin.customers.store') }}" method="POST">
                @csrf
                @if(isset($contact))
                    @method('PUT')
                @endif
                <input type="text" name="name" placeholder="Name" value="{{ old('name', $contact->name ?? '') }}" required>
                <input type="email" name="email" placeholder="Email" value="{{ old('email', $contact->email ?? '') }}" required>
                <input type="text" name="phone_number" placeholder="Phone Number" value="{{ old('phone_number', $contact->phone_number ?? '') }}" required>

                <select name="user_type" required>
                    <option value="" disabled {{ old('user_type', $contact->user_type ?? '') === '' ? 'selected' : '' }}>Select User Type</option>
                    <option value="individual" {{ old('user_type', $contact->user_type ?? '') === 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="dealer" {{ old('user_type', $contact->user_type ?? '') === 'dealer' ? 'selected' : '' }}>Dealer</option>
                </select>
                <textarea name="description" rows="4" placeholder="Description" required>{{ old('description', $contact->description ?? '') }}</textarea>
                <button type="submit">{{ isset($contact) ? 'Update Customer' : 'Create Customer' }}</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="tabs" style="display:flex; gap:8px; margin-bottom:16px;">
            <button type="button" class="tab-btn active" data-type="all" onclick="setCustomerTab('all', this)" style="width:auto;">All</button>
            <button type="button" class="tab-btn" data-type="individual" onclick="setCustomerTab('individual', this)" style="width:auto;">Individual</button>
            <button type="button" class="tab-btn" data-type="dealer" onclick="setCustomerTab('dealer', this)" style="width:auto;">Dealer</button>
        </div>
        <input type="text" id="customerSearch" placeholder="Search customers..." onkeyup="filterCustomers()" style="margin-bottom:16px;">
        <table id="customersTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Description</th>
                    <th>User Type</th>
                    <th>Message Sent At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contactItem)
                    <tr data-user-type="{{ $contactItem->user_type }}">
                        <td>{{ $contactItem->name }}</td>
                        <td>{{ $contactItem->email }}</td>
                        <td>{{ $contactItem->phone_number }}</td>
                        <td>{{ $contactItem->description }}</td>
                        <td>{{ ucfirst($contactItem->user_type) }}</td>
                        <td>{{ $contactItem->message_sent_at ? $contactItem->message_sent_at : 'Not sent yet' }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.customers.edit', $contactItem) }}" class="icon-btn edit" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.customers.send-whatsapp') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="number" value="{{ $contactItem->phone_number }}">
                                <input type="hidden" name="message" value="{{ $latestMessage->message ?? '' }}">
                                <button type="submit" class="icon-btn whatsapp" title="Send WhatsApp">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.472-.148-.67.15-.198.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"></path>
                                        <path d="M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.334.098 11.892c0 2.096.549 4.14 1.588 5.945L0 24l6.335-1.652a11.95 11.95 0 0 0 5.702 1.447h.005c6.585 0 11.943-5.335 11.95-11.893a11.82 11.82 0 0 0-3.472-8.453zM12.042 21.751h-.004a9.933 9.933 0 0 1-5.068-1.387l-.363-.215-3.759.982 1.003-3.649-.237-.375a9.9 9.9 0 0 1-1.527-5.276c.006-5.473 4.474-9.93 9.96-9.93a9.9 9.9 0 0 1 7.036 2.923 9.86 9.86 0 0 1 2.917 7.021c-.006 5.473-4.474 9.906-9.958 9.906z"></path>
                                    </svg>
                                </button>
                            </form>
                            <form action="{{ route('admin.customers.destroy', $contactItem) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete(event);">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="icon-btn delete" title="Delete">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            document.getElementById('customerModal').style.display = 'flex';
        }
        function closeCustomerModal() {
            document.getElementById('customerModal').style.display = 'none';
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeCustomerModal();
        });

        var activeCustomerTab = 'all';

        function setCustomerTab(type, btn) {
            activeCustomerTab = type;
            document.querySelectorAll('.tab-btn').forEach(function (b) {
                b.classList.remove('active');
            });
            btn.classList.add('active');
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
