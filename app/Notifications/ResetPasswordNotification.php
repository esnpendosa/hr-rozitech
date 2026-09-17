<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $token,
        public readonly string $locale = 'id',
    ) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email));
        $expiry = config('auth.passwords.users.expire', 60);

        if ($this->locale === 'en') {
            return (new MailMessage)
                ->subject('Reset Password - RMIH Platform')
                ->greeting('Hello!')
                ->line('You received this email because we received a password reset request for your account.')
                ->action('Reset Password', $url)
                ->line("This link expires in {$expiry} minutes.")
                ->line('If you did not request a password reset, no further action is required.')
                ->salutation('RMIH Platform Team');
        }

        return (new MailMessage)
            ->subject('Reset Kata Sandi - RMIH Platform')
            ->greeting('Halo!')
            ->line('Anda menerima email ini karena ada permintaan reset kata sandi untuk akun Anda.')
            ->action('Reset Kata Sandi', $url)
            ->line("Tautan ini akan kadaluarsa dalam {$expiry} menit.")
            ->line('Jika Anda tidak meminta reset kata sandi, abaikan email ini.')
            ->salutation('Tim RMIH Platform');
    }
}
