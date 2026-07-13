<?php

namespace App\Http\Controllers;

use App\Jobs\SendScheduledGroupResend;
use App\Models\ContactGroup;
use App\Models\GroupScheduledResend;
use App\Models\ResendInterval;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;

class ContactGroupController extends Controller
{
    public function index()
    {
        $groups = ContactGroup::with(['contacts', 'pendingResend'])->latest()->get();
        $messages = WhatsappMessage::latest()->get();
        $resendIntervals = ResendInterval::orderBy('minutes')->get();

        return view('admin.groups', compact('groups', 'messages', 'resendIntervals'));
    }

    public function updateSelectedMessage(Request $request, ContactGroup $group)
    {
        $data = $request->validate([
            'message_id' => ['required', 'exists:whatsapp_messages,id'],
        ]);

        $group->update(['selectedmessage' => $data['message_id']]);

        return response()->json(['success' => true]);
    }

    public function sendWhatsapp(Request $request, ContactGroup $group)
    {
        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
            'message_id' => ['nullable', 'exists:whatsapp_messages,id'],
        ]);

        $sent = 0;

        foreach ($group->contacts as $contact) {
            if ($contact->sendWhatsappMessage($data['message'] ?? '', $data['message_id'] ?? null)) {
                $sent++;
            }
        }

        $status = $sent > 0 ? 'success' : 'error';

        return back()->with($status, $sent.' message(s) sent to group "'.$group->name.'".');
    }

    public function scheduleResend(Request $request, ContactGroup $group)
    {
        $data = $request->validate([
            'resend_interval_id' => ['required', 'exists:resend_intervals,id'],
        ]);

        if (! $group->selectedmessage) {
            return back()->with('error', 'Pick a message for this group before scheduling a resend.');
        }

        $interval = ResendInterval::findOrFail($data['resend_interval_id']);
        $runAt = now()->addMinutes($interval->minutes);

        $resend = GroupScheduledResend::create([
            'contact_group_id' => $group->id,
            'whatsapp_message_id' => $group->selectedmessage,
            'message' => $group->selectedMessage->message ?? '',
            'run_at' => $runAt,
            'status' => 'pending',
        ]);

        SendScheduledGroupResend::dispatch($resend)->delay($runAt);

        return back()->with('success', 'Resend for group "'.$group->name.'" scheduled for '.$runAt->format('M j, g:i A').'.');
    }

    public function cancelScheduledResend(GroupScheduledResend $resend)
    {
        $resend->update(['status' => 'cancelled']);

        return back()->with('success', 'Scheduled resend cancelled.');
    }
}
