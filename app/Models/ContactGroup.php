<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContactGroup extends Model
{
    protected $fillable = [
        'name',
        'selectedmessage',
    ];

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'contact_group_members');
    }

    public function selectedMessage(): BelongsTo
    {
        return $this->belongsTo(WhatsappMessage::class, 'selectedmessage');
    }

    public function scheduledResends(): HasMany
    {
        return $this->hasMany(GroupScheduledResend::class);
    }

    public function pendingResend(): HasOne
    {
        return $this->hasOne(GroupScheduledResend::class)->where('status', 'pending')->latestOfMany();
    }

    public static function generateRandomName(): string
    {
        $adjectives = ['Sunny', 'Swift', 'Bright', 'Golden', 'Silent', 'Bold', 'Happy', 'Lucky', 'Rapid', 'Cosmic'];
        $nouns = ['Falcons', 'Tigers', 'Eagles', 'Wolves', 'Dolphins', 'Panthers', 'Hawks', 'Foxes', 'Lions', 'Otters'];

        return $adjectives[array_rand($adjectives)].' '.$nouns[array_rand($nouns)].' '.random_int(100, 999);
    }
}
