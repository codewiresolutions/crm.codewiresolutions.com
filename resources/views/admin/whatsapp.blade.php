@extends('layouts.admin')

@section('title', 'WhatsApp Admin')

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
@endsection

@section('content')
    @php $showModal = isset($message) || $errors->any(); @endphp

    <div class="card">
        <p><a href="https://web-whatsappjs.infinicodesystem.site/qr" target="_blank" style="display:inline-block; margin-top:8px; color:#2563eb;">Open QR Code</a></p>
        <h2>WhatsApp</h2>
        <p class="muted">Scan Qr code connect with whatsapp</p>
    </div>

    <div class="card" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h3 style="margin:0;">Saved Messages</h3>
            <p class="muted">Create, update, and delete WhatsApp message templates.</p>
        </div>
        <button type="button" style="width:auto; padding:10px 18px;" onclick="openMessageModal()">+ Add Message</button>
    </div>

    <div id="messageModal" class="modal-overlay" style="{{ $showModal ? 'display:flex;' : 'display:none;' }}" onclick="if(event.target === this) closeMessageModal()">
        <div class="modal-box">
            <div class="modal-header">
                <h3>{{ isset($message) ? 'Update Message' : 'Add Message' }}</h3>
                <button type="button" class="modal-close" onclick="closeMessageModal()">&times;</button>
            </div>
            <form action="{{ isset($message) ? route('admin.whatsapp.update', $message) : route('admin.whatsapp.store') }}" method="POST">
                @csrf
                @if(isset($message))
                    @method('PUT')
                @endif
                <label>Title</label>
                <input type="text" name="title" placeholder="Title" value="{{ old('title', $message->title ?? '') }}" required>
                <label>Message</label>
                <textarea name="message" rows="4" placeholder="Type your message" required>{{ old('message', $message->message ?? '') }}</textarea>

                <button type="submit">{{ isset($message) ? 'Update Message' : 'Save Message' }}</button>
            </form>
        </div>
    </div>

    <div class="card">
        @if($messages->isEmpty())
            <p class="muted">No messages saved yet.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Saved At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td>{{ $item->message }}</td>
                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.whatsapp.edit', $item) }}" class="icon-btn edit" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.whatsapp.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirmDeleteMessage(event);">
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
        @endif
    </div>

    <script>
        function openMessageModal() {
            document.getElementById('messageModal').style.display = 'flex';
        }
        function closeMessageModal() {
            document.getElementById('messageModal').style.display = 'none';
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMessageModal();
        });

        function confirmDeleteMessage(event) {
            event.preventDefault();
            var form = event.target;
            Swal.fire({
                title: 'Delete this message?',
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
    </script>
@endsection
