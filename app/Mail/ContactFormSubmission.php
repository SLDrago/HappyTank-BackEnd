<?php

namespace App\Mail;

use App\Models\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmission extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public function __construct(ContactUs $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        return $this->view('emails.contact-form-submission')
            ->subject('New Contact Inquiry: ' . $this->contact->type)
            ->replyTo($this->contact->email, $this->contact->first_name . ' ' . $this->contact->last_name);
    }
}
