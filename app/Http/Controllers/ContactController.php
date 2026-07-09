<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        ]);

        $response = Http::withoutVerifying()
            ->timeout(20)
            ->post('https://webwhatsappjs.codewiresolutions.com/send-message', [
                'number' => $data['number'],
                'message' => $data['message'] ?? '',
            ]);

        if ($response->successful()) {
            $contact = Contact::where('phone_number', $data['number'])->first();

            if ($contact) {
                $contact->update(['message_sent_at' => now()]);
            }

            return back()->with('success', 'WhatsApp message sent successfully.');
        }

        return back()->with('error', 'Unable to send WhatsApp message.');
    }

    public function index()
    {
        $contacts = Contact::latest()->paginate(15);
        $latestMessage = WhatsappMessage::latest()->first();

        return view('admin.customers', compact('contacts', 'latestMessage'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'string'],
            'description' => ['required', 'string'],
            'user_type' => ['required', 'in:individual,dealer'],
        ]);

        $contact = Contact::create($validated);

        if ($request->expectsJson()) {
            return response()->json($contact, 201);
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Contact $contact)
    {
        $contacts = Contact::latest()->paginate(15);

        return view('admin.customers', compact('contacts', 'contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone_number' => ['required', 'string'],
            'description' => ['required', 'string'],
            'user_type' => ['required', 'in:individual,dealer'],
        ]);

        $contact->update($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
