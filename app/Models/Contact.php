<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function selectedMessage(): BelongsTo
    {
        return $this->belongsTo(WhatsappMessage::class, 'selectedmessage');
    }
}
