<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class PasswordResetNotification extends Notification 
{
    use Queueable;

    protected $user;
    protected $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Password Reset Notification')
           // ->greeting('Hello ' - $this->user)
            ->line('You are receiving this email because we received a password reset request for your Kinder account.')
            ->line('Your OTP for password reset is: ' . $this->token)
            //->salutation(new HtmlString('<img src="' . asset('app\Notifications\Tesla.jpg') . '" alt="app\Notifications\Tesla.jpg">')) // Add your logo
            ->line('If you did not request a password reset, no further action is required.');
            
    }

    public function toArray($notifiable)
    {
        return [
            // Additional data if needed
        ];
    }

    public function via($notifiable)
    {
        return['mail'];

    }
}