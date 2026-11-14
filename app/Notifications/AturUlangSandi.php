<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email tautan atur ulang kata sandi. Tautan berlaku sesuai config auth.passwords.users.expire (menit). */
class AturUlangSandi extends Notification
{
    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('sandi.atur-ulang', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]);
        $menit = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Atur ulang kata sandi PERSIDEN HP Titip Gadai')
            ->greeting('Halo, '.$notifiable->name)
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.')
            ->action('Atur ulang kata sandi', $url)
            ->line("Tautan ini berlaku {$menit} menit dan hanya dapat dipakai satu kali.")
            ->line('Jika Anda tidak meminta ini, abaikan email ini; kata sandi Anda tidak berubah. Jangan bagikan tautan ini kepada siapa pun, termasuk petugas kami.')
            ->salutation('Salam, PERSIDEN HP Titip Gadai');
    }
}
