<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\MessageLog;
use App\Models\ResendInterval;
use App\Models\User;
use App\Models\UserType;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function dashboard()
    {
        $totalContacts = Contact::count();
        $sentMessagesCount = Contact::whereNotNull('message_sent_at')->count();
        $totalUsers = User::count();
        $totalMessages = WhatsappMessage::count();

        return view('admin.dashboard', compact('totalContacts', 'sentMessagesCount', 'totalUsers', 'totalMessages'));
    }

    public function whatsapp()
    {
        $contacts = Contact::latest()->get();
        $messages = WhatsappMessage::latest()->get();

        return view('admin.whatsapp', compact('contacts', 'messages'));
    }

    public function storeMessage(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        WhatsappMessage::create($data);

        return redirect()->route('admin.whatsapp')->with('success', 'Message saved successfully.');
    }

    public function editMessage(WhatsappMessage $message)
    {
        $contacts = Contact::latest()->get();
        $messages = WhatsappMessage::latest()->get();

        return view('admin.whatsapp', compact('contacts', 'messages', 'message'));
    }

    public function updateMessage(Request $request, WhatsappMessage $message)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message->update($data);

        return redirect()->route('admin.whatsapp')->with('success', 'Message updated successfully.');
    }

    public function destroyMessage(WhatsappMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.whatsapp')->with('success', 'Message deleted successfully.');
    }

    public function sendWhatsapp(Request $request)
    {
        $data = $request->validate([
            'number' => ['required', 'string'],
            'message' => ['nullable', 'string', 'max:1000'],
            'message_id' => ['nullable', 'exists:whatsapp_messages,id'],
        ]);

        $contact = Contact::where('phone_number', $data['number'])->first();

        if ($contact && $contact->sendWhatsappMessage($data['message'] ?? '', $data['message_id'] ?? null)) {
            return back()->with('success', 'WhatsApp message sent successfully.');
        }

        return back()->with('error', 'Unable to send WhatsApp message.');
    }

    public function bulkSendWhatsapp(Request $request)
    {
        $data = $request->validate([
            'contact_ids' => ['required', 'array', 'min:2'],
            'contact_ids.*' => ['exists:contacts,id'],
            'message' => ['nullable', 'string', 'max:1000'],
            'message_id' => ['nullable', 'exists:whatsapp_messages,id'],
        ]);

        $contacts = Contact::whereIn('id', $data['contact_ids'])->get();

        foreach ($contacts as $contact) {
            $contact->sendWhatsappMessage($data['message'] ?? '', $data['message_id'] ?? null);
        }

        $group = ContactGroup::create([
            'name' => ContactGroup::generateRandomName(),
            'selectedmessage' => $data['message_id'] ?? null,
        ]);
        $group->contacts()->attach($contacts->pluck('id'));

        return redirect()->route('admin.customers.index')
            ->with('success', 'Message sent to '.$contacts->count().' customers and group "'.$group->name.'" created.');
    }

    public function index()
    {
        $contacts = Contact::with('userType')->latest()->paginate(15);
        $latestMessage = WhatsappMessage::latest()->first();
        $userTypes = UserType::all();
        $messages = WhatsappMessage::latest()->get();
        $groups = ContactGroup::with(['contacts', 'pendingResend'])->latest()->get();
        $resendIntervals = ResendInterval::orderBy('minutes')->get();

        return view('admin.customers', compact('contacts', 'latestMessage', 'userTypes', 'messages', 'groups', 'resendIntervals'));
    }

    public function export()
    {
        $contacts = Contact::with('userType')->latest()->get();

        $filename = 'customers-'.now()->format('Y-m-d-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $columns = ['Name', 'Type', 'Email', 'Phone', 'Description', 'Sent At'];

        $callback = function () use ($contacts, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($contacts as $contact) {
                fputcsv($handle, [
                    $contact->name,
                    $contact->userType->name ?? '',
                    $contact->email,
                    $contact->phone_number,
                    strip_tags($contact->description ?? ''),
                    $contact->message_sent_at?->format('Y-m-d H:i') ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportMessages()
    {
        $logs = MessageLog::with('contact')->latest('sent_at')->get();

        $filename = 'messages-'.now()->format('Y-m-d-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $columns = ['Customer', 'Phone', 'Email', 'Message', 'Sent At'];

        $callback = function () use ($logs, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->contact->name ?? '',
                    $log->contact->phone_number ?? '',
                    $log->contact->email ?? '',
                    $log->message,
                    $log->sent_at?->format('Y-m-d H:i') ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'string'],
            'description' => ['required', 'string'],
            'user_type_id' => ['required', 'exists:user_types,id'],
        ]);

        $contact = Contact::create($validated);

        if ($request->expectsJson()) {
            return response()->json($contact, 201);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Contact $contact)
    {
        $contacts = Contact::with('userType')->latest()->paginate(15);
        $userTypes = UserType::all();
        $latestMessage = WhatsappMessage::latest()->first();
        $messages = WhatsappMessage::latest()->get();
        $groups = ContactGroup::with(['contacts', 'pendingResend'])->latest()->get();
        $resendIntervals = ResendInterval::orderBy('minutes')->get();

        return view('admin.customers', compact('contacts', 'contact', 'userTypes', 'latestMessage', 'messages', 'groups', 'resendIntervals'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'string'],
            'description' => ['required', 'string'],
            'user_type_id' => ['required', 'exists:user_types,id'],
        ]);

        $contact->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function updateSelectedMessage(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'message_id' => ['required', 'exists:whatsapp_messages,id'],
        ]);

        $contact->update(['selectedmessage' => $data['message_id']]);

        return response()->json(['success' => true]);
    }

    public function toggleInterested(Contact $contact)
    {
        $contact->update(['is_interested' => ! $contact->is_interested]);

        return response()->json(['is_interested' => $contact->is_interested]);
    }

    public function messages(Contact $contact)
    {
        $logs = $contact->messageLogs()->latest('sent_at')->get();

        return response()->json([
            'name' => $contact->name,
            'type' => $contact->userType->name ?? '',
            'email' => $contact->email,
            'phone_number' => $contact->phone_number,
            'message_sent_at' => $contact->message_sent_at?->format('Y-m-d H:i'),
            'description' => $contact->description,
            'created_at' => $contact->created_at?->format('Y-m-d H:i'),
            'selectedmessage' => $contact->selectedmessage,
            'messages' => $logs->map(fn (MessageLog $log) => [
                'message' => $log->message,
                'sent_at' => $log->sent_at->format('Y-m-d H:i'),
            ]),
        ]);
    }
}
