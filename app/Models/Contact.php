<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
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

    public function sentMessageLogs(): HasMany
    {
        return $this->messageLogs()->where('direction', 'sent');
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
            'direction' => 'sent',
            'type' => 'text',
            'message' => $message ?? '',
            'sent_at' => now(),
        ]);

        return true;
    }

    public function sendWhatsappMedia(UploadedFile $file, ?string $caption, ?int $messageId): bool
    {
        $mime = $file->getMimeType() ?? $file->getClientMimeType();
        $type = match (true) {
            str_starts_with($mime, 'image/') => 'image',
            str_starts_with($mime, 'video/') => 'video',
            str_starts_with($mime, 'audio/') => 'audio',
            default => 'document',
        };

        $endpoint = match ($type) {
            'image' => 'send-image',
            'video' => 'send-video',
            'audio' => 'send-audio',
            default => 'send-document',
        };

        $fields = ['number' => $this->phone_number];

        if ($type === 'image' || $type === 'video') {
            $fields['caption'] = $caption ?? '';
        } elseif ($type === 'document') {
            $fields['filename'] = $file->getClientOriginalName();
            $fields['mimetype'] = $mime;
        }

        $response = Http::withoutVerifying()
            ->timeout(30)
            ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
            ->post('https://webwhatsappjs.codewiresolutions.com/'.$endpoint, $fields);

        if (! $response->successful()) {
            return false;
        }

        $storedPath = $file->store('whatsapp', 'public');
        $storedFilename = basename($storedPath);

        $this->update(['message_sent_at' => now()]);

        MessageLog::create([
            'contact_id' => $this->id,
            'whatsapp_message_id' => $messageId,
            'direction' => 'sent',
            'type' => $type,
            'message' => $caption ?? '',
            'media_url' => route('admin.customers.media', ['filename' => $storedFilename]),
            'media_filename' => $file->getClientOriginalName(),
            'media_mimetype' => $mime,
            'sent_at' => now(),
        ]);

        return true;
    }
}
