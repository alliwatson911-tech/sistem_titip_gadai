<?php

namespace App\Services;

use App\Enums\LoanStatus as L;
use App\Models\Loan;
use App\Models\LoanNotice;
use App\Notifications\PengingatGadai;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Pemberitahuan gadai ke anggota lewat email (idempoten: satu pemberitahuan per gadai, jenis, dan tanggal dasar).
 * Jenis: h7 (2-7 hari sebelum jatuh tempo), h1 (0-1 hari), tenggang, siap_lelang, jadwal_lelang.
 * Gagal kirim dicoba lagi pada jalannya perintah berikutnya, maksimal 3 percobaan.
 */
class PengingatService
{
    public const MAKS_PERCOBAAN = 3;

    /** @return array{terkirim:int, gagal:int, dilewati:int} */
    public function jalankan(?CarbonInterface $hari = null): array
    {
        $hari = ($hari ?? today())->copy()->startOfDay();
        $hasil = ['terkirim' => 0, 'gagal' => 0, 'dilewati' => 0];

        $gadai = Loan::withoutGlobalScopes()->with(['member.user', 'product', 'auction', 'branch'])
            ->whereIn('status', array_map(fn ($s) => $s->value, L::dapatDibayar()))->get();

        foreach ($gadai as $l) {
            foreach ($this->jenisUntuk($l, $hari) as [$jenis, $kunci]) {
                $this->kirim($l, $jenis, $kunci, $hari, $hasil);
            }
        }

        return $hasil;
    }

    /** @return array<int, array{0:string,1:string}> pasangan [jenis, kunci] yang berlaku hari ini */
    private function jenisUntuk(Loan $l, CarbonInterface $hari): array
    {
        $sisa = (int) $hari->diffInDays($l->jatuh_tempo->copy()->startOfDay(), false);
        $jt = $l->jatuh_tempo->toDateString();
        $daftar = [];

        if ($l->status === L::Aktif) {
            if ($sisa >= 2 && $sisa <= 7) {
                $daftar[] = ['h7', $jt];
            } elseif ($sisa >= 0 && $sisa <= 1) {
                $daftar[] = ['h1', $jt];
            }
        } elseif ($l->status === L::Tenggang) {
            $daftar[] = ['tenggang', $jt];
        } elseif ($l->status === L::SiapLelang && $l->auction) {
            if ($l->auction->diberitahukan_at) {
                $daftar[] = ['siap_lelang', $l->auction->diberitahukan_at->toDateString()];
            }
            if ($l->auction->tgl_lelang) {
                $daftar[] = ['jadwal_lelang', $l->auction->tgl_lelang->toDateString()];
            }
        }

        return $daftar;
    }

    private function kirim(Loan $l, string $jenis, string $kunci, CarbonInterface $hari, array &$hasil): void
    {
        $n = LoanNotice::firstOrNew(['loan_id' => $l->id, 'jenis' => $jenis, 'kunci' => $kunci, 'channel' => 'email']);
        if ($n->exists && ($n->status !== 'gagal' || $n->percobaan >= self::MAKS_PERCOBAAN)) {
            return; // sudah terkirim/dilewati, atau percobaan habis
        }

        $user = $l->member?->user;
        if (! $user || ! $user->isActive() || blank($user->email)) {
            $n->fill(['status' => 'dilewati', 'percobaan' => $n->percobaan + 1, 'catatan' => 'Akun anggota tidak aktif atau tanpa email'])->save();
            $hasil['dilewati']++;

            return;
        }

        try {
            $user->notify(new PengingatGadai($jenis, $this->data($l, $hari)));
            $n->fill(['status' => 'terkirim', 'percobaan' => $n->percobaan + 1, 'catatan' => null, 'sent_at' => now()])->save();
            $hasil['terkirim']++;
        } catch (\Throwable $e) {
            $n->fill(['status' => 'gagal', 'percobaan' => $n->percobaan + 1, 'catatan' => mb_substr($e->getMessage(), 0, 250)])->save();
            $hasil['gagal']++;
        }
    }

    private function data(Loan $l, CarbonInterface $hari): array
    {
        $t = app(LoanService::class)->tagihan($l, $hari);
        $fmt = fn ($d) => $d ? Carbon::parse($d)->translatedFormat('j F Y') : null;
        $au = $l->auction;
        $tunggu = $au?->diberitahukan_at?->copy()->startOfDay()->addDays((int) ($l->snapshot_syarat['hari_pemberitahuan_lelang'] ?? 7));

        return [
            'no_sbg' => $l->no_sbg,
            'produk' => $l->product->nama,
            'cabang' => $l->branch?->nama ?? 'cabang kami',
            'saldo' => (int) $l->pokok_saldo,
            'jatuh_tempo' => $fmt($l->jatuh_tempo),
            'tenggang_sampai' => $fmt($l->tenggang_sampai),
            'sisa' => (int) $hari->diffInDays($l->jatuh_tempo->copy()->startOfDay(), false),
            'perpanjang' => $t['perpanjang']['boleh'] ? $t['perpanjang']['total'] : null,
            'tebus' => $t['tebus']['total'],
            'tgl_lelang' => $fmt($au?->tgl_lelang),
            'paling_cepat' => $fmt($tunggu),
            'url' => route('panel.gadai.show', $l->id),
        ];
    }
}
