<?php

namespace App\Jobs;

use App\Models\GroupScheduledResend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendScheduledGroupResend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public GroupScheduledResend $resend)
    {
    }

    public function handle(): void
    {
        $this->resend->refresh();

        if ($this->resend->status !== 'pending') {
            return;
        }

        foreach ($this->resend->group->contacts as $contact) {
            $contact->sendWhatsappMessage($this->resend->message, $this->resend->whatsapp_message_id);
        }

        $this->resend->update(['status' => 'sent']);
    }
}
