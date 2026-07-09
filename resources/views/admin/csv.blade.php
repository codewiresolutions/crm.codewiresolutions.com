@extends('layouts.admin')

@section('title', 'CSV Import')

@section('content')
    <div class="mb-4 rounded-xl bg-white p-5 shadow-sm">
        <h2 class="m-0 text-xl font-semibold">Import customers from CSV</h2>
        <p class="mt-1 text-sm text-gray-500">Add customers in bulk. Your file needs a <strong class="text-gray-700">Name</strong> and a <strong class="text-gray-700">Contact Number</strong> column.</p>

        <form action="{{ route('admin.csv.store') }}" method="POST" enctype="multipart/form-data" id="csvUploadForm" class="mt-4">
            @csrf
            <input type="file" name="csv_file" id="csvFileInput" accept=".csv,text/csv" required class="hidden">
            <label for="csvFileInput" id="csvDropzone" class="flex cursor-pointer flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed border-gray-300 px-6 py-10 text-center transition-colors hover:border-blue-400 hover:bg-blue-50/40">
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                        <path d="M12 3v12"></path>
                        <polyline points="7 8 12 3 17 8"></polyline>
                        <path d="M5 21h14a2 2 0 0 0 2-2v-4M3 15v4a2 2 0 0 0 2 2"></path>
                    </svg>
                </span>
                <span class="text-sm text-gray-600" id="csvDropzoneLabel">Drag &amp; drop your CSV here, or <span class="font-medium text-blue-600 hover:underline">browse</span></span>
                <span class="text-xs text-gray-400">.csv only &middot; up to 5 MB</span>
            </label>
        </form>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <h3 class="m-0 mb-3 text-lg font-semibold">Uploaded files</h3>
        @if($imports->isEmpty())
            <p class="text-sm text-gray-500">No CSV files uploaded yet.</p>
        @else
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border-b border-gray-200 p-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">File</th>
                        <th class="border-b border-gray-200 p-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Rows</th>
                        <th class="border-b border-gray-200 p-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Uploaded By</th>
                        <th class="border-b border-gray-200 p-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Uploaded At</th>
                        <th class="border-b border-gray-200 p-2.5 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($imports as $import)
                        <tr>
                            <td class="p-2.5">
                                <span class="flex items-center gap-2 font-medium text-gray-800">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5 shrink-0 text-gray-400">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    {{ $import->original_name }}
                                </span>
                            </td>
                            <td class="p-2.5 text-gray-600">{{ $import->rows_imported }}</td>
                            <td class="p-2.5 text-gray-600">{{ $import->importedBy->name ?? '—' }}</td>
                            <td class="p-2.5 text-gray-600">{{ $import->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-2.5 text-right">
                                <a href="{{ route('admin.csv.download', $import) }}" class="inline-flex items-center gap-1.5 font-medium text-blue-600 no-underline hover:underline">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                    Download
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        var dropzone = document.getElementById('csvDropzone');
        var fileInput = document.getElementById('csvFileInput');
        var label = document.getElementById('csvDropzoneLabel');
        var form = document.getElementById('csvUploadForm');

        fileInput.addEventListener('change', function () {
            if (fileInput.files.length) {
                label.textContent = fileInput.files[0].name;
                form.submit();
            }
        });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                dropzone.classList.add('border-blue-400', 'bg-blue-50/40');
            });
        });

        ['dragleave', 'drop'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (e) {
                e.preventDefault();
                dropzone.classList.remove('border-blue-400', 'bg-blue-50/40');
            });
        });

        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            var files = e.dataTransfer.files;
            if (files.length) {
                fileInput.files = files;
                label.textContent = files[0].name;
                form.submit();
            }
        });
    </script>
@endsection