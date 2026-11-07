<?php

namespace App\Http\Controllers\Panel;

use App\Enums\ApplicationStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Member;
use App\Models\Product;
use App\Services\ApplicationFlow;
use App\Support\Rupiah;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PengajuanController extends Controller
{
    public function __construct(private ApplicationFlow $alur) {}

    public function index(Request $r)
    {
        Gate::authorize('pengajuan.lihat');

        $q = Application::with(['member', 'product'])->latest('id');
        if (($s = ApplicationStatus::tryFrom((string) $r->query('status')))) {
            $q->where('status', $s->value);
        }
        if (($k = trim((string) $r->query('cari'))) !== '') {
            $q->where('kode', 'like', '%'.addcslashes($k, '%_\\').'%');
        }

        return view('panel.pengajuan.index', [
            'daftar' => $q->paginate(20)->withQueryString(),
            'semuaStatus' => ApplicationStatus::cases(),
            'antrean' => $this->antrean($r),
        ]);
    }

    /** Antrean kerja sesuai peran. */
    private function antrean(Request $r): ?array
    {
        $status = match ($r->user()->role) {
            Role::Teller => ApplicationStatus::MenungguBarang,
            Role::Analis => ApplicationStatus::BarangDiterima,
            Role::Manager, Role::Owner => ApplicationStatus::MenungguManager,
            default => null,
        };

        return $status ? ['status' => $status, 'jumlah' => Application::where('status', $status->value)->count()] : null;
    }

    public function create(Request $r)
    {
        Gate::authorize('pengajuan.buat');
        $user = $r->user();

        return view('panel.pengajuan.create', [
            'produk' => Product::where('is_active', true)->whereNull('branch_id')->orderBy('urutan')->get(),
            'anggota' => $user->role === Role::Teller ? Member::where('status', 'terverifikasi')->orderBy('nama_lengkap')->get() : null,
            'member' => $user->role === Role::Member ? $user->member : null,
            'pilihan' => $r->query('produk'),
        ]);
    }

    public function store(Request $r): RedirectResponse
    {
        Gate::authorize('pengajuan.buat');
        $user = $r->user();
        $r->merge(['pinjaman_diminta' => Rupiah::digit($r->input('pinjaman_diminta'))]);

        $aturan = [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'pinjaman_diminta' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'catatan_member' => ['nullable', 'string', 'max:500'],
        ];
        if ($user->role === Role::Teller) {
            $aturan['member_id'] = ['required', 'integer'];
        }
        $d = $r->validate($aturan, [
            'product_id.required' => 'Pilih jenis barang.',
            'pinjaman_diminta.required' => 'Isi jumlah pinjaman yang diinginkan.',
            'pinjaman_diminta.integer' => 'Jumlah pinjaman harus berupa angka.',
            'member_id.required' => 'Pilih anggota.',
        ]);

        if ($user->role === Role::Member && $user->email_verified_at === null) {
            return back()->withInput()->withErrors(['umum' => 'Verifikasi email Anda dulu (periksa kotak masuk, atau kirim ulang dari halaman ringkasan), lalu ajukan kembali.']);
        }

        // Teller hanya dapat memilih anggota cabangnya (global scope); anggota hanya dirinya sendiri.
        $member = $user->role === Role::Member ? $user->member : Member::findOrFail($d['member_id']);
        abort_unless($member, 403);

        try {
            $a = $this->alur->ajukan($member, Product::findOrFail($d['product_id']), (int) $d['pinjaman_diminta'], $d['catatan_member'] ?? null, $user);
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['umum' => $e->getMessage()]);
        }

        return redirect()->route('panel.pengajuan.show', $a)->with('status', 'Pengajuan dibuat. Bawa barang dan KTP asli ke cabang sebelum '.$a->batas_bawa_barang->translatedFormat('j F Y').'.');
    }

    public function show(Application $pengajuan)
    {
        Gate::authorize('pengajuan.lihat');
        $pengajuan->load(['member', 'product', 'pembuat', 'collaterals.photos', 'approvals.pengambilKeputusan']);

        return view('panel.pengajuan.show', [
            'a' => $pengajuan,
            'taksiran' => $pengajuan->taksiranSaatIni(),
            'periksa' => ApplicationFlow::DAFTAR_PERIKSA,
        ]);
    }

    public function terima(Request $r, Application $pengajuan): RedirectResponse
    {
        Gate::authorize('jaminan.terima');
        $d = $r->validate([
            'identitas_dicocokkan' => ['accepted'],
            'merek' => ['required', 'string', 'max:60'],
            'model' => ['required', 'string', 'max:100'],
            'imei_serial' => ['nullable', 'string', 'max:60'],
            'kelengkapan' => ['nullable', 'string', 'max:255'],
            'kondisi' => ['nullable', 'string', 'max:1000'],
            'no_segel' => ['required', 'string', 'max:40'],
            'lokasi_simpan' => ['nullable', 'string', 'max:60'],
            'foto' => ['nullable', 'array'],
            'foto.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'identitas_dicocokkan.accepted' => 'Centang bahwa identitas penggadai sudah dicocokkan dengan KTP asli.',
            'merek.required' => 'Merek wajib diisi.', 'model.required' => 'Model wajib diisi.', 'no_segel.required' => 'Nomor segel wajib diisi.',
            'foto.*.image' => 'Foto harus berupa gambar JPG, PNG, atau WebP.', 'foto.*.max' => 'Ukuran foto maksimal 4 MB.',
        ]);

        $foto = array_intersect_key($r->file('foto', []), array_flip(['depan', 'belakang', 'serial', 'segel']));

        return $this->jalankan(fn () => $this->alur->terimaBarang($pengajuan, $r->user(), ['identitas_dicocokkan' => true] + $d, $foto), 'Barang diterima dan disegel. Menunggu taksiran analis.', $pengajuan);
    }

    public function taksir(Request $r, Application $pengajuan): RedirectResponse
    {
        Gate::authorize('taksiran.buat');
        $r->merge(['nilai_pasar' => Rupiah::digit($r->input('nilai_pasar')), 'nilai_taksiran' => Rupiah::digit($r->input('nilai_taksiran'))]);
        $d = $r->validate([
            'nilai_pasar' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'nilai_taksiran' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'periksa' => ['nullable', 'array'],
            'periksa.*' => ['in:1'],
            'rekomendasi' => ['required', 'in:setuju,tolak'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ], [
            'nilai_pasar.required' => 'Isi nilai pasar barang.', 'nilai_taksiran.required' => 'Isi nilai taksiran.',
            'rekomendasi.required' => 'Pilih rekomendasi.', 'rekomendasi.in' => 'Rekomendasi tidak valid.',
        ]);
        $d['nilai_pasar'] = (int) $d['nilai_pasar'];
        $d['nilai_taksiran'] = (int) $d['nilai_taksiran'];
        $d['periksa'] = array_intersect_key($d['periksa'] ?? [], ApplicationFlow::DAFTAR_PERIKSA);

        return $this->jalankan(fn () => $this->alur->taksir($pengajuan, $r->user(), $d), 'Taksiran dan rekomendasi tersimpan.', $pengajuan);
    }

    public function putuskan(Request $r, Application $pengajuan): RedirectResponse
    {
        Gate::authorize('persetujuan.putuskan');
        $d = $r->validate(['keputusan' => ['required', 'in:setuju,tolak'], 'catatan' => ['nullable', 'string', 'max:1000']]);

        return $this->jalankan(fn () => $this->alur->putuskan($pengajuan, $r->user(), $d['keputusan'], $d['catatan'] ?? null), 'Keputusan tersimpan.', $pengajuan);
    }

    public function batal(Request $r, Application $pengajuan): RedirectResponse
    {
        Gate::authorize('pengajuan.buat');

        return $this->jalankan(fn () => $this->alur->batalkan($pengajuan, $r->user()), 'Pengajuan dibatalkan.', $pengajuan);
    }

    public function kembalikan(Request $r, Application $pengajuan): RedirectResponse
    {
        Gate::authorize('jaminan.keluarkan');

        return $this->jalankan(fn () => $this->alur->kembalikanBarang($pengajuan, $r->user()), 'Barang dicatat sudah dikembalikan ke penggadai.', $pengajuan);
    }

    private function jalankan(callable $aksi, string $pesan, Application $a): RedirectResponse
    {
        try {
            $aksi();
        } catch (DomainException $e) {
            return back()->withInput()->withErrors(['umum' => $e->getMessage()]);
        }

        return redirect()->route('panel.pengajuan.show', $a)->with('status', $pesan);
    }
}
