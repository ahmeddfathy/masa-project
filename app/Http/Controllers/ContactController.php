<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'message' => 'required|string',
        ]);

        // Send email
        Mail::to('ahmeddfathy087@gmail.com')->send(new ContactFormMail($validated));

        return redirect()->back()->with('success', 'تم إرسال رسالتك بنجاح!');
    }
}
