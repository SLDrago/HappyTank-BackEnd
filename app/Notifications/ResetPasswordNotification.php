<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;

class ResetPasswordNotification extends Notification
{

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = env('FRONTEND_URL') ?: 'http://localhost:4173';
        $actionUrl = $frontendUrl . '/reset-password?token=' . urlencode($this->token) . '&email=' . urlencode($this->email);

        // Use custom Blade template for email
        return (new MailMessage)
            ->view('emails.reset_password', ['actionUrl' => $actionUrl])
            ->subject(__('Reset Password Notification'));
    }
}
