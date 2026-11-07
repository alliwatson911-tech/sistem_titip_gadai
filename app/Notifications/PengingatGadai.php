<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email pemberitahuan gadai ke anggota. Berisi nomor nota, saldo, dan tagihan; TIDAK memuat NIK atau data pribadi lain.
 * $d disiapkan PengingatService: no_sbg, produk, saldo, jatuh_tempo, tenggang_sampai, sisa, perpanjang, tebus, tgl_lelang, paling_cepat, url, cabang.
 */
class PengingatGadai extends Notification
{
    public function __construct(public string $jenis, public array $d) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $d = $this->d;
        $rp = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.');

        $judul = match ($this->jenis) {
            'h7' => "Gadai {$d['no_sbg']} jatuh tempo {$d['sisa']} hari lagi",
            'h1' => $d['sisa'] <= 0 ? "Gadai {$d['no_sbg']} jatuh tempo hari ini" : "Gadai {$d['no_sbg']} jatuh tempo besok",
            'tenggang' => "Gadai {$d['no_sbg']} melewati jatuh tempo",
            'siap_lelang' => "Pemberitahuan penting: barang gadai {$d['no_sbg']} masuk proses lelang",
            'jadwal_lelang' => "Lelang barang gadai {$d['no_sbg']} dijadwalkan {$d['tgl_lelang']}",
        };

        $m = (new MailMessage)->subject($judul)->greeting('Halo, '.$notifiable->name);

        switch ($this->jenis) {
            case 'h7':
            case 'h1':
                $m->line("Gadai {$d['produk']} Anda (nota {$d['no_sbg']}) jatuh tempo pada {$d['jatuh_tempo']}.")
                    ->line('Sebelum jatuh tempo, Anda dapat memperpanjang, mencicil sebagian pokok, atau menebus barang di '.$d['cabang'].'. Barang yang diperpanjang atau ditebus tepat waktu tidak masuk proses lelang.');
                break;
            case 'tenggang':
                $m->line("Gadai {$d['produk']} Anda (nota {$d['no_sbg']}) telah melewati jatuh tempo ({$d['jatuh_tempo']}).")
                    ->line("Anda berada dalam masa tenggang sampai {$d['tenggang_sampai']}. Sewa modal tetap berjalan selama terlambat. Segera datang ke {$d['cabang']} untuk memperpanjang, mencicil, atau menebus.");
                break;
            case 'siap_lelang':
                $m->line("Masa tenggang gadai {$d['produk']} Anda (nota {$d['no_sbg']}) telah berakhir tanpa pembayaran, sehingga barang jaminan masuk proses lelang.")
                    ->line('Anda MASIH DAPAT menebus, memperpanjang, atau mencicil sebelum lelang dilaksanakan; pembayaran tersebut membatalkan proses lelang.')
                    ->line("Lelang paling cepat dapat dilaksanakan pada {$d['paling_cepat']}. Hasil lelang digunakan untuk melunasi pokok, sewa modal, dan biaya lelang; bila ada kelebihan, kelebihan itu menjadi hak Anda.");
                break;
            case 'jadwal_lelang':
                $m->line("Lelang barang jaminan gadai {$d['produk']} Anda (nota {$d['no_sbg']}) dijadwalkan pada {$d['tgl_lelang']}.")
                    ->line('Anda masih dapat menebus, memperpanjang, atau mencicil sampai lelang dilaksanakan; pembayaran tersebut membatalkan lelang.');
                break;
        }

        $m->line("Saldo pokok: {$rp($d['saldo'])}.");
        if ($d['perpanjang'] !== null) {
            $m->line("Perkiraan biaya hari ini: perpanjang {$rp($d['perpanjang'])}; tebus {$rp($d['tebus'])} (dihitung sistem, angka final tercantum di kwitansi).");
        } else {
            $m->line("Perkiraan biaya tebus hari ini: {$rp($d['tebus'])} (dihitung sistem, angka final tercantum di kwitansi).");
        }

        return $m->action('Lihat gadai saya', $d['url'])
            ->line('Pembayaran dilakukan di cabang melalui teller atau dengan transfer yang dicatat teller. Kami tidak pernah meminta kata sandi atau kode apa pun lewat email, WhatsApp, atau telepon.')
            ->salutation('Salam, linygadai');
    }
}
