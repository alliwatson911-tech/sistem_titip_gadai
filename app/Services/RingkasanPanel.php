<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\LoanStatus;
use App\Enums\Role;
use App\Models\Application;
use App\Models\Branch;
use App\Models\Disbursement;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Payment;
use App\Models\User;
use App\Support\Rupiah;
use Illuminate\Support\Carbon;

/**
 * Data tampilan dashboard Owner, Manager, dan Admin (kartu KPI, tren 6 bulan, komposisi, daftar).
 * Hanya MEMBACA. Cakupan cabang ditegakkan global scope BelongsToBranch (Manager/Admin = cabangnya, Owner = semua).
 * Angka uang hanya dihitung untuk yang berhak laporan cabang (Owner, Manager); Admin hanya mendapat angka keanggotaan.
 */
class RingkasanPanel
{
    private function bayar(): array
    {
        return array_map(fn ($s) => $s->value, LoanStatus::dapatDibayar());
    }

    /** @return array{0:string,1:string} [angka, satuan] mis. 8350000 -> ['8,4','jt'] */
    public static function pecah(int $n): array
    {
        $fmt = fn (float $v) => rtrim(rtrim(number_format($v, 1, ',', ''), '0'), ',');
        if ($n >= 1_000_000_000) {
            return [$fmt($n / 1_000_000_000), 'M'];
        }
        if ($n >= 1_000_000) {
            return [$fmt($n / 1_000_000), 'jt'];
        }
        if ($n >= 1_000) {
            return [$fmt($n / 1_000), 'rb'];
        }

        return [(string) $n, ''];
    }

    public function untuk(User $u): ?array
    {
        return match ($u->role) {
            Role::Owner, Role::Manager => $this->eksekutif($u),
            Role::Admin => $this->admin($u),
            default => null,
        };
    }

    /** 6 bulan terakhir (termasuk bulan ini): [['ym'=>'2026-11','label'=>'Nov'], ...]. */
    private function bulan(): array
    {
        $nama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $out = [];
        for ($i = 5; $i >= 0; $i--) {
            $b = now()->startOfMonth()->subMonths($i);
            $out[] = ['ym' => $b->format('Y-m'), 'label' => $nama[(int) $b->format('n')]];
        }

        return $out;
    }

    /** @return array<string,int> ym => jumlah/nilai */
    private function perBulan($query, string $kolomTanggal, string $agregat, Carbon $mulai): array
    {
        return $query->where($kolomTanggal, '>=', $mulai)
            ->selectRaw("DATE_FORMAT($kolomTanggal, '%Y-%m') ym, $agregat v")->groupBy('ym')->pluck('v', 'ym')
            ->map(fn ($v) => (int) $v)->all();
    }

    private function deret(array $bulan, array $data): array
    {
        return array_map(fn ($b) => (int) ($data[$b['ym']] ?? 0), $bulan);
    }

    private function persen(int $bagian, int $total): int
    {
        return $total > 0 ? (int) round($bagian * 100 / $total) : 0;
    }

    private function eksekutif(User $u): array
    {
        $bulan = $this->bulan();
        $mulai = now()->startOfMonth()->subMonths(5);
        $bln = now()->startOfMonth();
        $lalu = $bln->copy()->subMonth();
        $bayar = $this->bayar();

        $portofolio = Loan::whereIn('status', $bayar);
        $pokokJalan = (int) (clone $portofolio)->sum('pokok_saldo');
        $nJalan = (clone $portofolio)->count();

        $cairBulan = Disbursement::where('dicairkan_at', '>=', $bln);
        $cairIni = (int) (clone $cairBulan)->sum('jumlah');
        $nCairIni = (clone $cairBulan)->count();
        $cairLalu = (int) Disbursement::whereBetween('dicairkan_at', [$lalu, $bln->copy()->subSecond()])->sum('jumlah');

        $sah = Payment::where('status', 'sah');
        $pendapatanIni = (int) (clone $sah)->where('dibayar_at', '>=', $bln)->selectRaw('COALESCE(SUM(sewa_modal + administrasi + denda),0) v')->value('v');
        $pendapatanLalu = (int) (clone $sah)->whereBetween('dibayar_at', [$lalu, $bln->copy()->subSecond()])->selectRaw('COALESCE(SUM(sewa_modal + administrasi + denda),0) v')->value('v');
        $kasIni = (int) (clone $sah)->where('dibayar_at', '>=', $bln)->sum('total');

        $risiko = Loan::whereIn('status', [LoanStatus::Tenggang->value, LoanStatus::SiapLelang->value]);
        $nRisiko = (clone $risiko)->count();
        $pokokRisiko = (int) (clone $risiko)->sum('pokok_saldo');

        $arah = fn (int $ini, int $lalu) => $lalu > 0 ? (int) round(($ini - $lalu) * 100 / $lalu) : null;
        $lencanaArah = function (?int $p): array {
            if ($p === null) {
                return ['baru', 'netral'];
            }

            return [($p >= 0 ? '↑ +'.$p : '↓ '.abs($p)).'%', $p >= 0 ? 'sukses' : 'proses'];
        };
        $arahCair = $lencanaArah($arah($cairIni, $cairLalu));
        $arahPendapatan = $lencanaArah($arah($pendapatanIni, $pendapatanLalu));

        [$a1, $s1] = self::pecah($pokokJalan);
        [$a2, $s2] = self::pecah($cairIni);
        [$a3, $s3] = self::pecah($pendapatanIni);
        [$a4, $s4] = self::pecah($pokokRisiko);

        $kpi = [
            ['ikon' => 'uang', 'badge' => $nJalan.' gadai', 'nada' => 'netral', 'angka' => $a1, 'satuan' => $s1, 'label' => 'Pokok pinjaman berjalan',
                'chip' => Rupiah::format($pokokJalan), 'catatan' => 'Saldo pokok gadai aktif, tenggang, dan siap lelang', 'kiri' => 'Portofolio gadai', 'tautan' => 'Lihat', 'url' => route('panel.gadai.index')],
            ['ikon' => 'cair', 'badge' => $arahCair[0], 'nada' => $arahCair[1], 'angka' => $a2, 'satuan' => $s2, 'label' => 'Pencairan bulan ini',
                'chip' => $nCairIni.' pencairan', 'catatan' => 'Bulan lalu: '.Rupiah::ringkas($cairLalu), 'kiri' => 'Arus dana keluar', 'tautan' => 'Laporan', 'url' => route('panel.laporan.cabang')],
            ['ikon' => 'koin', 'badge' => $arahPendapatan[0], 'nada' => $arahPendapatan[1], 'angka' => $a3, 'satuan' => $s3, 'label' => 'Pendapatan bulan ini',
                'chip' => 'Kas masuk '.Rupiah::ringkas($kasIni), 'catatan' => 'Sewa modal, administrasi, dan denda', 'kiri' => 'Laporan pendapatan', 'tautan' => 'Rincian', 'url' => route('panel.laporan.cabang')],
            ['ikon' => 'waspada', 'badge' => $this->persen($nRisiko, $nJalan).'% dari berjalan', 'nada' => $nRisiko > 0 ? 'gagal' : 'sukses', 'angka' => $a4, 'satuan' => $s4, 'label' => 'Pokok gadai berisiko',
                'chip' => $nRisiko.' gadai', 'catatan' => 'Masa tenggang dan siap lelang', 'kiri' => 'Pengawasan lelang', 'tautan' => 'Lelang', 'url' => route('panel.lelang.index')],
        ];

        $bulanCair = $this->deret($bulan, $this->perBulan(Disbursement::query(), 'dicairkan_at', 'SUM(jumlah)', $mulai));
        $bulanMasuk = $this->deret($bulan, $this->perBulan(Payment::where('status', 'sah'), 'dibayar_at', 'SUM(total)', $mulai));

        $status = Loan::selectRaw('status, COUNT(*) n')->groupBy('status')->pluck('n', 'status');
        $aktif = (int) ($status[LoanStatus::Aktif->value] ?? 0);
        $sehat = $this->persen($aktif, $nJalan);
        $komposisi = [
            ['label' => 'Aktif', 'nilai' => $aktif, 'warna' => 'lumut'],
            ['label' => 'Masa tenggang', 'nilai' => (int) ($status[LoanStatus::Tenggang->value] ?? 0), 'warna' => 'kuningan'],
            ['label' => 'Siap lelang', 'nilai' => (int) ($status[LoanStatus::SiapLelang->value] ?? 0), 'warna' => 'terakota'],
            ['label' => 'Ditebus', 'nilai' => (int) ($status[LoanStatus::Ditebus->value] ?? 0), 'warna' => 'tinta'],
            ['label' => 'Selesai lelang', 'nilai' => (int) ($status[LoanStatus::SelesaiLelang->value] ?? 0) + (int) ($status[LoanStatus::Dilelang->value] ?? 0), 'warna' => 'abu'],
        ];

        $cabang = null;
        if ($u->role === Role::Owner) {
            $cabang = Branch::where('kode', '!=', 'PUSAT')->orderBy('kode')->get()->map(function ($b) use ($bayar, $bln) {
                $l = Loan::withoutGlobalScopes()->where('branch_id', $b->id);

                return [
                    'nama' => $b->nama,
                    'berjalan' => (clone $l)->whereIn('status', $bayar)->count(),
                    'pokok' => (int) (clone $l)->whereIn('status', $bayar)->sum('pokok_saldo'),
                    'segera' => (clone $l)->whereIn('status', $bayar)->whereDate('jatuh_tempo', '<=', today()->addDays(7))->count(),
                    'risiko' => (clone $l)->whereIn('status', [LoanStatus::Tenggang->value, LoanStatus::SiapLelang->value])->count(),
                    'cair' => (int) Disbursement::withoutGlobalScopes()->where('branch_id', $b->id)->where('dicairkan_at', '>=', $bln)->sum('jumlah'),
                ];
            })->all();
        }

        return [
            'mode' => 'eksekutif',
            'kpi' => $kpi,
            'tren' => [
                'judul' => 'Tren arus dana bulanan', 'sub' => 'Pencairan pinjaman dibanding kas masuk dari pembayaran sah, 6 bulan terakhir.',
                'bulan' => array_column($bulan, 'label'), 'uang' => true,
                'seri' => [['nama' => 'Kas masuk (pembayaran)', 'warna' => 'lumut', 'nilai' => $bulanMasuk], ['nama' => 'Pencairan', 'warna' => 'kuningan', 'nilai' => $bulanCair]],
            ],
            'donat' => ['judul' => 'Kesehatan portofolio', 'sub' => 'Komposisi status seluruh gadai.', 'pusat' => $sehat, 'pusatLabel' => 'GADAI SEHAT',
                'status' => $sehat >= 80 ? ['Sangat sehat', 'sukses'] : ($sehat >= 50 ? ['Perlu dipantau', 'proses'] : ['Waspada', 'gagal']),
                'irisan' => $komposisi],
            'segera' => $this->segera(true),
            'terbaru' => $this->pembayaranTerbaru(),
            'antrean' => $this->antreanManager(),
            'antreanJudul' => 'Menunggu persetujuan Manager',
            'cabang' => $cabang,
        ];
    }

    private function admin(User $u): array
    {
        $bulan = $this->bulan();
        $mulai = now()->startOfMonth()->subMonths(5);
        $bln = now()->startOfMonth();

        $status = Member::selectRaw('status, COUNT(*) n')->groupBy('status')->pluck('n', 'status');
        $ok = (int) ($status['terverifikasi'] ?? 0);
        $calon = (int) ($status['calon'] ?? 0);
        $total = (int) $status->sum();
        $pengajuanIni = Application::where('created_at', '>=', $bln)->count();
        $anggotaBaru = Member::where('created_at', '>=', $bln)->count();
        $jalan = Loan::whereIn('status', $this->bayar())->count();
        $segera = Loan::whereIn('status', $this->bayar())->whereDate('jatuh_tempo', '<=', today()->addDays(7))->count();

        $kpi = [
            ['ikon' => 'anggota', 'badge' => '+'.$anggotaBaru.' bulan ini', 'nada' => $anggotaBaru > 0 ? 'sukses' : 'netral', 'angka' => (string) $ok, 'satuan' => 'anggota', 'label' => 'Anggota terverifikasi',
                'chip' => $total.' total terdaftar', 'catatan' => 'Siap mengajukan titip gadai', 'kiri' => 'Buku anggota', 'tautan' => 'Kelola', 'url' => route('panel.anggota')],
            ['ikon' => 'verifikasi', 'badge' => $calon > 0 ? 'perlu diperiksa' : 'bersih', 'nada' => $calon > 0 ? 'proses' : 'sukses', 'angka' => (string) $calon, 'satuan' => 'calon', 'label' => 'Menunggu verifikasi',
                'chip' => 'Cocokkan KTP asli', 'catatan' => 'Calon anggota yang sudah mendaftar', 'kiri' => 'Antrean verifikasi', 'tautan' => 'Verifikasi', 'url' => route('panel.anggota', ['status' => 'calon'])],
            ['ikon' => 'berkas', 'badge' => 'bulan ini', 'nada' => 'netral', 'angka' => (string) $pengajuanIni, 'satuan' => 'pengajuan', 'label' => 'Pengajuan masuk',
                'chip' => Application::whereIn('status', array_map(fn ($s) => $s->value, ApplicationStatus::terbuka()))->count().' masih berjalan', 'catatan' => 'Dari situs dan loket', 'kiri' => 'Daftar pengajuan', 'tautan' => 'Lihat', 'url' => route('panel.pengajuan.index')],
            ['ikon' => 'jam', 'badge' => $segera.' segera', 'nada' => $segera > 0 ? 'proses' : 'sukses', 'angka' => (string) $jalan, 'satuan' => 'gadai', 'label' => 'Gadai berjalan',
                'chip' => 'Jatuh tempo ≤ 7 hari: '.$segera, 'catatan' => 'Bantu anggota mengingat jatuh tempo', 'kiri' => 'Gadai anggota', 'tautan' => 'Lihat', 'url' => route('panel.gadai.index')],
        ];

        $anggotaBln = $this->deret($bulan, $this->perBulan(Member::query(), 'created_at', 'COUNT(*)', $mulai));
        $pengajuanBln = $this->deret($bulan, $this->perBulan(Application::query(), 'created_at', 'COUNT(*)', $mulai));

        return [
            'mode' => 'admin',
            'kpi' => $kpi,
            'tren' => [
                'judul' => 'Pertumbuhan anggota dan pengajuan', 'sub' => 'Pendaftar baru dibanding pengajuan titip gadai per bulan, 6 bulan terakhir.',
                'bulan' => array_column($bulan, 'label'), 'uang' => false,
                'seri' => [['nama' => 'Anggota baru', 'warna' => 'lumut', 'nilai' => $anggotaBln], ['nama' => 'Pengajuan', 'warna' => 'kuningan', 'nilai' => $pengajuanBln]],
            ],
            'donat' => ['judul' => 'Status keanggotaan', 'sub' => 'Komposisi anggota cabang Anda.', 'pusat' => $this->persen($ok, $total), 'pusatLabel' => 'TERVERIFIKASI',
                'status' => $calon === 0 ? ['Antrean bersih', 'sukses'] : [$calon.' menunggu', 'proses'],
                'irisan' => [
                    ['label' => 'Terverifikasi', 'nilai' => $ok, 'warna' => 'lumut'],
                    ['label' => 'Calon', 'nilai' => $calon, 'warna' => 'kuningan'],
                    ['label' => 'Ditolak', 'nilai' => (int) ($status['ditolak'] ?? 0), 'warna' => 'terakota'],
                    ['label' => 'Diblokir', 'nilai' => (int) ($status['blokir'] ?? 0), 'warna' => 'tinta'],
                ]],
            'segera' => $this->segera(false),
            'terbaru' => null,
            'antrean' => $this->calonAnggota(),
            'antreanJudul' => 'Calon anggota menunggu verifikasi',
            'cabang' => null,
        ];
    }

    /** Gadai dengan jatuh tempo terdekat (belum lunas). */
    private function segera(bool $denganUang): array
    {
        return Loan::with('member')->whereIn('status', $this->bayar())->orderBy('jatuh_tempo')->limit(6)->get()->map(function (Loan $l) use ($denganUang) {
            $hari = (int) today()->diffInDays($l->jatuh_tempo, false);

            return [
                'url' => route('panel.gadai.show', $l), 'sbg' => $l->no_sbg, 'nama' => $l->member?->nama_lengkap ?? '—',
                'hari' => $hari, 'tanggal' => $l->jatuh_tempo->translatedFormat('j M Y'), 'pokok' => $denganUang ? Rupiah::ringkas((int) $l->pokok_saldo) : null,
                'status' => $l->status instanceof LoanStatus ? $l->status : LoanStatus::from($l->status),
            ];
        })->all();
    }

    private function pembayaranTerbaru(): array
    {
        return Payment::with('loan.member')->where('status', 'sah')->latest('dibayar_at')->limit(6)->get()->map(fn (Payment $p) => [
            'kwitansi' => $p->no_kwitansi, 'nama' => $p->loan?->member?->nama_lengkap ?? '—', 'tipe' => ucfirst($p->tipe),
            'total' => Rupiah::format((int) $p->total), 'waktu' => $p->dibayar_at?->diffForHumans() ?? '',
            'url' => $p->loan ? route('panel.gadai.show', $p->loan) : null,
        ])->all();
    }

    private function antreanManager(): array
    {
        return Application::with(['member', 'product'])->where('status', ApplicationStatus::MenungguManager->value)->oldest('diajukan_at')->limit(5)->get()->map(fn (Application $a) => [
            'url' => route('panel.pengajuan.show', $a), 'judul' => $a->member?->nama_lengkap ?? '—',
            'sub' => ($a->product?->nama ?? 'Gadai').' · '.Rupiah::format((int) ($a->pinjaman_disetujui ?? $a->pinjaman_diminta)),
        ])->all();
    }

    private function calonAnggota(): array
    {
        return Member::where('status', 'calon')->oldest()->limit(5)->get()->map(fn (Member $m) => [
            'url' => route('panel.anggota.show', $m), 'judul' => $m->nama_lengkap,
            'sub' => 'Mendaftar '.$m->created_at->diffForHumans().($m->ktp_path ? ' · KTP terunggah' : ' · KTP belum diunggah'),
        ])->all();
    }
}
