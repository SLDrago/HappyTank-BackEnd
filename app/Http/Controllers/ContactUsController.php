<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use App\Mail\ContactFormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'type' => 'required|in:General inquiry,Technical Support',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $contact = ContactUs::create($validatedData);

        // Send email
        Mail::to('your-email@example.com')->send(new ContactFormSubmission($contact));

        return response()->json(['message' => 'Contact form submitted successfully!'], 201);
    }
}
