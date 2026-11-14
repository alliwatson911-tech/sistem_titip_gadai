<?php

namespace App\Services;

use App\Enums\LoanStatus;
use App\Models\Application;
use App\Models\Auction;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonInterface;

/**
 * Laporan keuangan dan portofolio. Dasar KAS (cash basis): pembayaran dihitung pada tanggal dicatat sah.
 * Model ber-scope (BelongsToBranch) otomatis membatasi Manager ke cabangnya; Owner melihat semua.
 * Portofolio (saldo, jatuh tempo) adalah keadaan SAAT INI, bukan historis pada akhir periode.
 */
class LaporanService
{
    private const TIPE = ['perpanjangan', 'cicil', 'tebus'];

    /** @return array<string,mixed> */
    public function ringkas(CarbonInterface $dari, CarbonInterface $sampai, ?int $cabangId = null): array
    {
        $awal = $dari->copy()->startOfDay();
        $akhir = $sampai->copy()->endOfDay();
        $cab = fn ($q) => $cabangId ? $q->where('branch_id', $cabangId) : $q;

        $perTipe = array_fill_keys(self::TIPE, ['n' => 0, 'total' => 0, 'pokok' => 0, 'sewa' => 0, 'admin' => 0, 'denda' => 0]);
        $sah = $cab(Payment::query())->where('status', 'sah')->whereBetween('dibayar_at', [$awal, $akhir]);
        foreach ((clone $sah)->selectRaw('tipe, COUNT(*) n, SUM(total) total, SUM(pokok_dibayar) pokok, SUM(sewa_modal) sewa, SUM(administrasi) admin, SUM(denda) denda')->groupBy('tipe')->get() as $r) {
            $perTipe[$r->tipe] = ['n' => (int) $r->n, 'total' => (int) $r->total, 'pokok' => (int) $r->pokok, 'sewa' => (int) $r->sewa, 'admin' => (int) $r->admin, 'denda' => (int) $r->denda];
        }
        $metode = (clone $sah)->selectRaw('metode, SUM(total) total')->groupBy('metode')->pluck('total', 'metode');

        $cair = $cab(Disbursement::query())->whereBetween('dicairkan_at', [$awal, $akhir]);
        $nCair = (clone $cair)->count();
        $totalCair = (int) (clone $cair)->sum('jumlah');
        $cairTunai = (int) (clone $cair)->where('metode', 'tunai')->sum('jumlah');

        $dapatDibayar = array_map(fn ($s) => $s->value, LoanStatus::dapatDibayar());
        $portofolio = $cab(Loan::query())->whereIn('status', $dapatDibayar);
        $perStatus = $cab(Loan::query())->selectRaw('status, COUNT(*) n')->groupBy('status')->pluck('n', 'status');

        $lelang = Auction::query()->where('status', 'laku')->whereBetween('updated_at', [$awal, $akhir])
            ->whereHas('loan', fn ($q) => $cabangId ? $q->where('branch_id', $cabangId) : $q);
        $lelangRingkas = (clone $lelang)->selectRaw('COUNT(*) n, COALESCE(SUM(harga_laku),0) laku, COALESCE(SUM(biaya_lelang),0) biaya, COALESCE(SUM(CASE WHEN kelebihan > 0 THEN kelebihan ELSE 0 END),0) lebih, COALESCE(SUM(CASE WHEN kelebihan < 0 THEN -kelebihan ELSE 0 END),0) kurang')->first();

        $pengajuan = $cab(Application::query())->whereBetween('created_at', [$awal, $akhir])->selectRaw('status, COUNT(*) n')->groupBy('status')->pluck('n', 'status');

        $sewa = array_sum(array_column($perTipe, 'sewa'));
        $admin = array_sum(array_column($perTipe, 'admin'));
        $denda = array_sum(array_column($perTipe, 'denda'));

        return [
            'periode' => [$awal->toDateString(), $akhir->toDateString()],
            'pencairan' => ['n' => $nCair, 'total' => $totalCair, 'tunai' => $cairTunai],
            'pembayaran' => $perTipe,
            'kas_masuk' => array_sum(array_column($perTipe, 'total')),
            'kas_masuk_tunai' => (int) ($metode['tunai'] ?? 0),
            'kas_masuk_transfer' => (int) ($metode['transfer'] ?? 0),
            'pokok_kembali' => array_sum(array_column($perTipe, 'pokok')),
            'pendapatan' => ['sewa_modal' => $sewa, 'administrasi' => $admin, 'denda' => $denda, 'total' => $sewa + $admin + $denda],
            'portofolio' => [
                'n' => (clone $portofolio)->count(),
                'saldo_pokok' => (int) (clone $portofolio)->sum('pokok_saldo'),
                'segera' => (clone $portofolio)->whereDate('jatuh_tempo', '>=', today())->whereDate('jatuh_tempo', '<=', today()->addDays(7))->count(),
                'terlambat' => (clone $portofolio)->whereDate('jatuh_tempo', '<', today())->count(),
            ],
            'status_gadai' => collect(LoanStatus::cases())->mapWithKeys(fn ($s) => [$s->value => (int) ($perStatus[$s->value] ?? 0)])->all(),
            'lelang' => ['n' => (int) $lelangRingkas->n, 'harga_laku' => (int) $lelangRingkas->laku, 'biaya' => (int) $lelangRingkas->biaya, 'kelebihan' => (int) $lelangRingkas->lebih, 'kekurangan' => (int) $lelangRingkas->kurang],
            'pengajuan' => $pengajuan->map(fn ($n) => (int) $n)->all(),
        ];
    }

    /** Daftar datar [label, nilai, jenis] untuk tampilan dan CSV. jenis: rp | n */
    public function baris(array $r): array
    {
        $b = [];
        $tambah = function (string $label, int $nilai, string $jenis = 'rp') use (&$b) {
            $b[] = [$label, $nilai, $jenis];
        };

        $tambah('Pencairan — jumlah akad', $r['pencairan']['n'], 'n');
        $tambah('Pencairan — total dana', $r['pencairan']['total']);
        foreach (['perpanjangan' => 'Perpanjangan', 'cicil' => 'Cicil', 'tebus' => 'Tebus'] as $k => $l) {
            $tambah("$l — jumlah transaksi", $r['pembayaran'][$k]['n'], 'n');
            $tambah("$l — total diterima", $r['pembayaran'][$k]['total']);
        }
        $tambah('Kas masuk — total', $r['kas_masuk']);
        $tambah('Kas masuk — tunai', $r['kas_masuk_tunai']);
        $tambah('Kas masuk — transfer', $r['kas_masuk_transfer']);
        $tambah('Pokok kembali', $r['pokok_kembali']);
        $tambah('Pendapatan — sewa modal', $r['pendapatan']['sewa_modal']);
        $tambah('Pendapatan — administrasi', $r['pendapatan']['administrasi']);
        $tambah('Pendapatan — denda', $r['pendapatan']['denda']);
        $tambah('Pendapatan — total', $r['pendapatan']['total']);
        $tambah('Portofolio (kini) — gadai berjalan', $r['portofolio']['n'], 'n');
        $tambah('Portofolio (kini) — saldo pokok', $r['portofolio']['saldo_pokok']);
        $tambah('Portofolio (kini) — jatuh tempo ≤ 7 hari', $r['portofolio']['segera'], 'n');
        $tambah('Portofolio (kini) — terlambat', $r['portofolio']['terlambat'], 'n');
        $tambah('Lelang laku — jumlah', $r['lelang']['n'], 'n');
        $tambah('Lelang laku — harga laku', $r['lelang']['harga_laku']);
        $tambah('Lelang — kelebihan untuk anggota', $r['lelang']['kelebihan']);
        $tambah('Lelang — kekurangan (tidak ditagihkan)', $r['lelang']['kekurangan']);

        return $b;
    }

    /** Laporan harian satu teller: kas tunai bersih dan daftar transaksi. */
    public function harian(User $teller, CarbonInterface $tgl): array
    {
        $bayar = Payment::query()->where('teller_id', $teller->id)->whereDate('dibayar_at', $tgl)->with('loan')->orderBy('id')->get();
        $cair = Disbursement::query()->where('teller_id', $teller->id)->whereDate('dicairkan_at', $tgl)->orderBy('id')->get();

        $masukTunai = (int) $bayar->where('status', 'sah')->where('metode', 'tunai')->sum('total');
        $keluarTunai = (int) $cair->where('metode', 'tunai')->sum('jumlah');

        return [
            'tanggal' => $tgl->toDateString(),
            'pembayaran' => $bayar,
            'pencairan' => $cair,
            'masuk_tunai' => $masukTunai,
            'keluar_tunai' => $keluarTunai,
            'kas_tunai_bersih' => $masukTunai - $keluarTunai,
            'transfer_sah' => (int) $bayar->where('status', 'sah')->where('metode', 'transfer')->sum('total'),
            'transfer_menunggu' => (int) $bayar->where('status', 'menunggu_verifikasi')->sum('total'),
        ];
    }
}
