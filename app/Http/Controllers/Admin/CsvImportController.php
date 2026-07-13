<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CsvImport;
use App\Models\UserType;
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
        $storedPath = $file->store('csv-imports', 'public');

        $rows = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_map(fn ($column) => preg_replace('/[^a-z0-9]/', '', strtolower(trim($column))), array_shift($rows) ?? []);

        $nameIndex = array_search('name', $header, true);
        $numberIndex = array_search('contactnumber', $header, true);

        $originalName = strtolower($file->getClientOriginalName());
        $userTypeId = null;

        if (str_contains($originalName, 'dealer')) {
            $userTypeId = UserType::where('name', 'Dealer')->value('id');
        } elseif (str_contains($originalName, 'individual')) {
            $userTypeId = UserType::where('name', 'Individual')->value('id');
        }

        $created = 0;
        $updated = 0;

        if ($nameIndex !== false && $numberIndex !== false) {
            foreach ($rows as $row) {
                $name = preg_replace('/\s+/', ' ', trim($row[$nameIndex] ?? ''));
                $number = preg_replace('/\s+/', '', $row[$numberIndex] ?? '');

                if ($name === '' || $number === '') {
                    continue;
                }

                $values = ['name' => $name];

                if ($userTypeId !== null) {
                    $values['user_type_id'] = $userTypeId;
                }

                $contact = Contact::updateOrCreate(
                    ['phone_number' => $number],
                    $values
                );

                $contact->wasRecentlyCreated ? $created++ : $updated++;
            }
        }

        $imported = $created + $updated;

        CsvImport::create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $storedPath,
            'rows_imported' => $imported,
            'imported_by' => $request->user()->id,
        ]);

        if ($nameIndex === false || $numberIndex === false) {
            return back()->with('error', 'CSV must contain "Name" and "Contact Number" columns. File saved but no contacts were imported.');
        }

        return back()->with('success', "Added {$created} new and updated {$updated} existing contact(s) from {$file->getClientOriginalName()}.");
    }

    public function download(CsvImport $csvImport)
    {
        return Storage::disk('public')->download($csvImport->path, $csvImport->original_name);
    }
}