<?php

namespace App\Services;

use App\Models\Member;
use App\Models\PenitipNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class PenitipNotificationService
{
    /** Buat antrean notifikasi email dan WhatsApp untuk penitip baru yang didaftarkan di loket. */
    public static function antrekanAkunBaru(User $user, Member $member, string $passwordSementara): void
    {
        $cabangNama = $member->branch?->nama_publik ?? $member->branch?->nama ?? 'PERSIDEN HP';
        $loginUrl = url('/masuk');

        $pesanWa = "Halo {$member->nama_lengkap},\n\n"
            . "Akun Anda di PERSIDEN HP Titip Gadai telah berhasil didaftarkan oleh staf loket cabang {$cabangNama}.\n\n"
            . "Berikut data akses portal digital penitip Anda:\n"
            . "• Link Masuk: {$loginUrl}\n"
            . "• Email Akun: {$user->email}\n"
            . "• Kata Sandi Sementara: {$passwordSementara}\n\n"
            . "Demi keamanan, Anda wajib mengganti kata sandi setelah berhasil masuk pertama kali.\n\n"
            . "Terima kasih,\nPERSIDEN HP Titip Gadai\nAman · Cepat · Terpercaya";

        $pesanEmail = "Selamat datang di PERSIDEN HP Titip Gadai.\n\n"
            . "Staf cabang {$cabangNama} telah membuatkan akun digital untuk memantau transaksi titipan barang Anda.\n\n"
            . "Akses Portal:\n"
            . "Link: {$loginUrl}\n"
            . "Email: {$user->email}\n"
            . "Kata Sandi Sementara: {$passwordSementara}\n\n"
            . "Silakan ganti kata sandi saat pertama kali masuk.";

        // 1. Antrekan Notifikasi WhatsApp
        $teleponClean = preg_replace('/\D/', '', (string) $member->telepon);
        if ($teleponClean !== '' && str_starts_with($teleponClean, '0')) {
            $teleponClean = '62' . substr($teleponClean, 1);
        }

        PenitipNotification::create([
            'user_id' => $user->id,
            'member_id' => $member->id,
            'channel' => 'whatsapp',
            'tujuan' => $teleponClean ?: (string) $member->telepon,
            'subjek' => 'Akses Akun PERSIDEN HP Titip Gadai',
            'pesan' => $pesanWa,
            'status' => 'antrean',
        ]);

        // 2. Antrekan / Kirim Notifikasi Email
        PenitipNotification::create([
            'user_id' => $user->id,
            'member_id' => $member->id,
            'channel' => 'email',
            'tujuan' => $user->email,
            'subjek' => 'Akses Akun PERSIDEN HP Titip Gadai',
            'pesan' => $pesanEmail,
            'status' => 'antrean',
        ]);
    }

    /** URL kirim langsung via WhatsApp Web / App oleh petugas cabang */
    public static function tautanWhatsApp(PenitipNotification $notif): string
    {
        $hp = preg_replace('/\D/', '', $notif->tujuan);
        if (str_starts_with($hp, '0')) {
            $hp = '62' . substr($hp, 1);
        }
        return 'https://wa.me/' . $hp . '?text=' . rawurlencode($notif->pesan);
    }
}

