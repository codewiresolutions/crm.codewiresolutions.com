<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageLog extends Model
{
    protected $fillable = [
        'contact_id',
        'whatsapp_message_id',
        'direction',
        'type',
        'message',
        'media_url',
        'media_filename',
        'media_mimetype',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function whatsappMessage(): BelongsTo
    {
        return $this->belongsTo(WhatsappMessage::class);
    }
}