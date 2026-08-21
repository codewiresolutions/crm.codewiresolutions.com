@extends('layouts.admin')

@section('title', 'WhatsApp Admin')

@section('content')
    @php $showModal = isset($message) || $errors->any(); @endphp
    <div class="flex items-center bg-white p-5 shadow-sm mb-4 rounded-xl">
        <span class="w-16 h-16 flex items-center justify-center bg-green-100 rounded-xl shrink-0 text-4xl"><i class="ri-whatsapp-line text-green-700"></i></span>
        <div class="pl-5">
        <h2 class="mt-2 text-xl font-semibold">Connect With WhatsApp</h2>
        <p class="text-sm text-gray-500">Scan Qr code connect with whatsapp</p>
        <p><a href="https://webwhatsappjs.codewiresolutions.com/qr" target="_blank" class="inline-flex mt-3 text-white  bg-blue-600 gap-1 px-4 py-1.5 rounded-lg text-sm"><i class="ri-qr-code-line"></i>Open QR Code</a></p>
    </div>
    </div> 


    
     <div class="rounded-xl bg-white p-5 shadow-sm mb-4">
    <div class="flex items-center justify-between">
    <div class="flex items-center gap-4"> 
        <span class="w-13 h-13 flex items-center justify-center bg-gray-100 rounded-full shrink-0 text-2xl"><i class="ri-message-2-line text-blue-600"></i>
        </span>
        <div>
            <h3 class="m-0 text-lg font-semibold">Messages</h3>
            <p class="text-sm text-gray-500">Create, update, and delete WhatsApp message templates.</p>
        </div>
    </div>
    <button type="button" class="w-auto rounded-lg bg-blue-600 px-3 py-2 text-white hover:bg-blue-700" onclick="openMessageModal()">
        + Add Message
    </button>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 my-6">
    <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-4 shadow-sm">
    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-purple-100 text-purple-600 text-xl shrink-0">
      <i class="ri-send-plane-fill"></i>
    </div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Total Templates</p>
      <h4 class="text-xl font-bold text-gray-800 m-0">0</h4>
    </div>
  </div>
  <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-4 shadow-sm">
    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-green-50 text-green-600 text-xl shrink-0">
     <i class="ri-send-plane-fill"></i>
    </div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Active Templates</p>
      <h4 class="text-xl font-bold text-gray-800 m-0">0</h4>
    </div>
  </div>
  <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-4 shadow-sm">
    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-amber-100 text-amber-600 text-xl shrink-0">
      <i class="ri-pause-line"></i>
    </div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Inactive Templates</p>
      <h4 class="text-xl font-bold text-gray-800 m-0">0</h4>
    </div>
  </div>
  <div class="flex items-center gap-3 rounded-xl bg-gray-50 p-4 shadow-sm">
    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-rose-50 text-rose-600 text-xl shrink-0">
      <i class="ri-delete-bin-line"></i>
    </div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Deleted Templates</p>
      <h4 class="text-xl font-bold text-gray-800 m-0">0</h4>
    </div>
  </div>

</div>
</div>
    <div id="messageModal" class="fixed inset-0 z-50 items-center justify-center bg-gray-900/50 {{ $showModal ? 'flex' : 'hidden' }}" onclick="if(event.target === this) closeMessageModal()">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="m-0 text-lg font-semibold">{{ isset($message) ? 'Update Message' : 'Add Message' }}</h3>
                <button type="button" class="w-auto border-none bg-transparent p-0 px-2 text-2xl leading-none text-gray-500 hover:text-gray-900" onclick="closeMessageModal()">&times;</button>
            </div>
            <form action="{{ isset($message) ? route('admin.whatsapp.update', $message) : route('admin.whatsapp.store') }}" method="POST">
                @csrf
                @if(isset($message))
                    @method('PUT')
                @endif
                <label class="text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" placeholder="Title" value="{{ old('title', $message->title ?? '') }}" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5">
                <label class="mt-2 block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" rows="4" placeholder="Type your message" required class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2.5">{{ old('message', $message->message ?? '') }}</textarea>

                <button type="submit" class="mt-2 w-full rounded-lg bg-blue-600 px-3 py-2.5 text-white hover:bg-blue-700">{{ isset($message) ? 'Update Message' : 'Save Message' }}</button>
            </form>
        </div>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        @if($messages->isEmpty())
             <div class="flex min-h-[250px] w-full flex-col items-center justify-center rounded-2xl border-2 border-dashed border-purple-200 bg-white p-8">
            <div class="relative mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-purple-50">
                <div class="relative flex h-14 w-14 items-center justify-center rounded-xl bg-purple-100 text-purple-600">
                    <i class="ri-message-3-line text-3xl"></i>
                    <span class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-purple-200 text-purple-700">
                        <i class="ri-emotion-unhappy-line text-xs"></i>
                    </span>
                </div>
            </div>
            <h3 class="font-medium text-gray-700">No messages saved yet</h3>
            <p class="mt-1 text-sm text-gray-400">You haven't created any WhatsApp message template.</p>
            <p class="text-sm mt-1 text-gray-400">Click the <span class="text-blue-600 font-medium">"Add Message"</span> button to get started</p>
        </div>
        @else
            <input type="text" id="messageSearch" placeholder="Search messages..." onkeyup="filterMessages()" class="mb-4 w-full rounded-lg border border-gray-300 px-3 py-2.5">
            <table id="messagesTable" class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border-b border-gray-200 p-2.5 text-left">Title</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Message</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Saved At</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $item)
                        <tr>
                            <td class="border-b border-gray-200 p-2.5">{{ $item->title }}</td>
                            <td class="border-b border-gray-200 p-2.5">{{ $item->message }}</td>
                            <td class="border-b border-gray-200 p-2.5">{{ $item->created_at->format('Y-m-d H:i') }}</td>
                            <td class="flex items-center gap-0.5 whitespace-nowrap border-b border-gray-200 p-2.5">
                                <a href="{{ route('admin.whatsapp.edit', $item) }}" title="Edit" class="inline-flex h-8.5 w-8.5 items-center justify-center rounded-lg text-blue-600 no-underline hover:bg-blue-50">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.whatsapp.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirmDeleteMessage(event);">
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
        @endif
    </div>

    <script>
        function openMessageModal() {
            document.getElementById('messageModal').classList.remove('hidden');
            document.getElementById('messageModal').classList.add('flex');
        }
        function closeMessageModal() {
            document.getElementById('messageModal').classList.remove('flex');
            document.getElementById('messageModal').classList.add('hidden');
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMessageModal();
        });

        function filterMessages() {
            var query = document.getElementById('messageSearch').value.trim().toLowerCase();
            var rows = document.querySelectorAll('#messagesTable tbody tr');
            rows.forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        }

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
