@extends('layouts.admin')

@section('title', 'CSV Import')

@section('content')
    <div class="mb-4 rounded-xl bg-white p-5 shadow-sm">
        <h2 class="m-0 text-xl font-semibold">CSV Import</h2>
        <p class="text-sm text-gray-500">Upload a CSV with <strong>Name</strong> and <strong>Contact Number</strong> columns to add customers in bulk.</p>

        <form action="{{ route('admin.csv.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-center gap-3">
            @csrf
            <input type="file" name="csv_file" accept=".csv,text/csv" required class="w-auto rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <button type="submit" class="w-auto rounded-lg bg-blue-600 px-4.5 py-2.5 text-white hover:bg-blue-700">Upload &amp; Import</button>
        </form>
    </div>

    <div class="rounded-xl bg-white p-5 shadow-sm">
        <h3 class="m-0 mb-3 text-lg font-semibold">Uploaded Files</h3>
        @if($imports->isEmpty())
            <p class="text-sm text-gray-500">No CSV files uploaded yet.</p>
        @else
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border-b border-gray-200 p-2.5 text-left">File</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Rows Imported</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Uploaded By</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Uploaded At</th>
                        <th class="border-b border-gray-200 p-2.5 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($imports as $import)
                        <tr>
                            <td class="border-b border-gray-200 p-2.5">{{ $import->original_name }}</td>
                            <td class="border-b border-gray-200 p-2.5">{{ $import->rows_imported }}</td>
                            <td class="border-b border-gray-200 p-2.5">{{ $import->importedBy->name ?? '—' }}</td>
                            <td class="border-b border-gray-200 p-2.5">{{ $import->created_at->format('Y-m-d H:i') }}</td>
                            <td class="border-b border-gray-200 p-2.5">
                                <a href="{{ route('admin.csv.download', $import) }}" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-blue-600 no-underline hover:bg-blue-50">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5">
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
@endsection