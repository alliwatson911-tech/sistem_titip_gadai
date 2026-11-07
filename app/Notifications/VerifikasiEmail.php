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
            ->subject('Verifikasi email akun linygadai')
            ->greeting('Halo, '.$notifiable->name)
            ->line('Terima kasih telah mendaftar. Mohon pastikan alamat email ini milik Anda dengan menekan tombol di bawah.')
            ->action('Verifikasi email saya', self::url($notifiable))
            ->line('Tautan berlaku 24 jam. Pengajuan titip gadai memerlukan email yang sudah terverifikasi.')
            ->line('Jika Anda tidak merasa mendaftar, abaikan email ini.')
            ->salutation('Salam, linygadai');
    }
}
