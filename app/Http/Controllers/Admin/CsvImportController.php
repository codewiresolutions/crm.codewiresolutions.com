<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CsvImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CsvImportController extends Controller
{
    public function index()
    {
        $imports = CsvImport::with('importedBy')->latest()->get();

        return view('admin.csv', compact('imports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('csv_file');
        $storedPath = $file->store('csv-imports', 'local');

        $rows = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_map(fn ($column) => preg_replace('/[^a-z0-9]/', '', strtolower(trim($column))), array_shift($rows) ?? []);

        $nameIndex = array_search('name', $header, true);
        $numberIndex = array_search('contactnumber', $header, true);

        $imported = 0;

        if ($nameIndex !== false && $numberIndex !== false) {
            foreach ($rows as $row) {
                $name = trim($row[$nameIndex] ?? '');
                $number = trim($row[$numberIndex] ?? '');

                if ($name === '' || $number === '') {
                    continue;
                }

                Contact::create([
                    'name' => $name,
                    'phone_number' => $number,
                ]);

                $imported++;
            }
        }

        CsvImport::create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $storedPath,
            'rows_imported' => $imported,
            'imported_by' => $request->user()->id,
        ]);

        if ($nameIndex === false || $numberIndex === false) {
            return back()->with('error', 'CSV must contain "Name" and "Contact Number" columns. File saved but no contacts were imported.');
        }

        return back()->with('success', "Imported {$imported} contact(s) from {$file->getClientOriginalName()}.");
    }

    public function download(CsvImport $csvImport)
    {
        return Storage::disk('local')->download($csvImport->path, $csvImport->original_name);
    }
}