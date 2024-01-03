use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public function toMail($notifiable)
    {
        $token = $this->token;

        return (new MailMessage)
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', url('password/reset', $token))
            ->line('If you did not request a password reset, no further action is required.');
    }
}