<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupScheduledResend extends Model
{
    protected $fillable = [
        'contact_group_id',
        'whatsapp_message_id',
        'message',
        'run_at',
        'status',
    ];

    protected $casts = [
        'run_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ContactGroup::class, 'contact_group_id');
    }

    public function whatsappMessage(): BelongsTo
    {
        return $this->belongsTo(WhatsappMessage::class);
    }
}
