<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class WhatsappInboxController extends Controller
{
    public function index()
    {
        $response = Http::withoutVerifying()
            ->timeout(20)
            ->get('https://webwhatsappjs.codewiresolutions.com/messages');

        $messages = $response->successful() ? ($response->json('messages') ?? []) : [];

        return view('admin.whatsapp-inbox', [
            'messages' => $messages,
            'fetchFailed' => ! $response->successful(),
        ]);
    }
}