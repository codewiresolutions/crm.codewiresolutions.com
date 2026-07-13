<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'description',
        'user_type_id',
        'message_sent_at',
        'selectedmessage',
        'is_interested',
    ];

    protected $casts = [
        'message_sent_at' => 'datetime',
        'is_interested' => 'boolean',
    ];

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function selectedMessage(): BelongsTo
    {
        return $this->belongsTo(WhatsappMessage::class, 'selectedmessage');
    }

    public function messageLogs(): HasMany
    {
        return $this->hasMany(MessageLog::class);
    }

    public function sendWhatsappMessage(?string $message, ?int $messageId): bool
    {
        $response = Http::withoutVerifying()
            ->timeout(20)
            ->post('https://webwhatsappjs.codewiresolutions.com/send-message', [
                'number' => $this->phone_number,
                'message' => $message ?? '',
            ]);

        if (! $response->successful()) {
            return false;
        }

        $this->update(['message_sent_at' => now()]);

        MessageLog::create([
            'contact_id' => $this->id,
            'whatsapp_message_id' => $messageId,
            'message' => $message ?? '',
            'sent_at' => now(),
        ]);

        return true;
    }
}
