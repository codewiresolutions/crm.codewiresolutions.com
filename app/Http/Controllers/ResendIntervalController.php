<?php

namespace App\Http\Controllers;

use App\Models\ResendInterval;
use Illuminate\Http\Request;

class ResendIntervalController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'minutes' => ['required', 'integer', 'min:1'],
        ]);

        ResendInterval::create($data);

        return back()->with('success', 'Resend delay option added.');
    }

    public function destroy(ResendInterval $resendInterval)
    {
        $resendInterval->delete();

        return back()->with('success', 'Resend delay option removed.');
    }
}
