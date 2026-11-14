<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

/** Email verifikasi alamat email pendaftar. Tautan bertanda tangan, berlaku 24 jam. */
class VerifikasiEmail extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public static function url(object $user): string
    {
        return URL::temporarySignedRoute('email.verifikasi', now()->addHours(24), ['id' => $user->getKey(), 'hash' => sha1($user->email)]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi email akun PERSIDEN HP Titip Gadai')
            ->greeting('Halo, '.$notifiable->name)
            ->line('Terima kasih. Mohon pastikan alamat email ini milik Anda dengan menekan tombol di bawah.')
            ->action('Verifikasi email saya', self::url($notifiable))
            ->line('Tautan berlaku 24 jam.')
            ->line('Jika Anda tidak merasa didaftarkan, silakan abaikan email ini.')
            ->salutation('Salam, PERSIDEN HP Titip Gadai');
    }
}
