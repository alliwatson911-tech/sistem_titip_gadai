<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Member;
use App\Models\PenitipNotification;
use App\Models\User;
use App\Services\Audit;
use App\Services\PenitipNotificationService;
use App\Support\Pii;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    /** Daftar penitip. Cakupan cabang ditegakkan global scope BelongsToBranch. */
    public function index(Request $r)
    {
        $q = Member::with('branch')->latest('id');
        if (in_array($r->query('status'), ['calon', 'terverifikasi', 'ditolak', 'blokir'], true)) {
            $q->where('status', $r->query('status'));
        }
        if (($k = trim((string) $r->query('cari'))) !== '') {
            $q->where('nama_lengkap', 'like', '%'.addcslashes($k, '%_\\').'%');
        }

        return view('panel.anggota.index', [
            'anggota' => $q->paginate(20)->withQueryString(),
            'notifAntreanCount' => PenitipNotification::where('status', 'antrean')->count(),
        ]);
    }

    /** Form input data penitip baru oleh staf di loket cabang */
    public function create(Request $r)
    {
        Gate::authorize('anggota.tambah');

        $user = $r->user();
        $cabang = Branch::where('is_active', true)->where('kode', '!=', 'PUSAT')->orderBy('nama')->get();

        return view('panel.anggota.create', [
            'cabang' => $cabang,
            'defaultBranchId' => $user->branch_id,
        ]);
    }

    /** Simpan pendaftaran penitip baru oleh staf loket */
    public function store(Request $r): RedirectResponse
    {
        Gate::authorize('anggota.tambah');

        $data = $r->validate([
            'nama_lengkap' => ['required', 'string', 'max:120'],
            'nik' => [
                'required',
                'digits:16',
                function ($atribut, $nilai, $gagal) {
                    if (Member::withoutGlobalScopes()->where('nik_hash', Pii::hash($nilai))->exists()) {
                        $gagal('NIK 16 digit ini sudah terdaftar di sistem. Gunakan pencarian untuk melihat data penitip.');
                    }
                },
            ],
            'telepon' => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'branch_id' => ['required', Rule::exists('branches', 'id')->where('is_active', 1)],
            'alamat' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap penitip wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus berupa 16 digit angka.',
            'telepon.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'telepon.regex' => 'Format nomor telepon/WA tidak valid. Contoh: 08123456789.',
            'email.required' => 'Alamat email penitip wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar sebagai akun penitip.',
            'branch_id.required' => 'Pilih cabang loket pendaftaran.',
        ]);

        // Buat password sementara acak tapi mudah dibaca (cth: Persiden482910)
        $passwordSementara = 'Persiden'.rand(100000, 999999);

        [$user, $member] = DB::transaction(function () use ($data, $passwordSementara, $r) {
            $user = new User([
                'name' => $data['nama_lengkap'],
                'email' => $data['email'],
                'password' => $passwordSementara,
            ]);
            $user->forceFill([
                'role' => Role::Member,
                'status' => 'aktif',
                'branch_id' => $data['branch_id'],
                'wajib_ganti_password' => true,
            ])->save();

            $member = new Member([
                'user_id' => $user->id,
                'branch_id' => $data['branch_id'],
                'nama_lengkap' => $data['nama_lengkap'],
                'nik_enc' => $data['nik'],
                'nik_hash' => Pii::hash($data['nik']),
                'telepon' => $data['telepon'],
                'alamat' => $data['alamat'] ?? null,
            ]);
            $member->forceFill([
                'status' => 'terverifikasi', // langsung terverifikasi karena diinput staf loket
                'verified_by' => $r->user()->id,
                'verified_at' => now(),
            ])->save();

            return [$user, $member];
        });

        // Antrekan notifikasi email dan WhatsApp
        PenitipNotificationService::antrekanAkunBaru($user, $member, $passwordSementara);

        Audit::log('anggota.daftar_loket', $member, null, ['petugas_id' => $r->user()->id]);

        $waNotif = PenitipNotification::where('member_id', $member->id)->where('channel', 'whatsapp')->latest('id')->first();
        $waUrl = $waNotif ? PenitipNotificationService::tautanWhatsApp($waNotif) : null;

        return redirect()->route('panel.anggota')->with([
            'status' => "Penitip {$member->nama_lengkap} berhasil didaftarkan dan akun telah aktif.",
            'akun_baru' => [
                'nama' => $member->nama_lengkap,
                'email' => $user->email,
                'password' => $passwordSementara,
                'telepon' => $member->telepon,
                'wa_url' => $waUrl,
            ],
        ]);
    }

    /** Antrean notifikasi penitip (outbox & manual send) */
    public function notifikasi(Request $r)
    {
        Gate::authorize('anggota.tambah');

        $notifikasi = PenitipNotification::with(['user', 'member.branch'])
            ->latest('id')
            ->paginate(25);

        return view('panel.anggota.notifikasi', ['notifikasi' => $notifikasi]);
    }

    public function show(Member $anggota)
    {
        return view('panel.anggota.show', ['m' => $anggota->load('branch'), 'nikPenuh' => null]);
    }

    /** Verifikasi / tolak / blokir. Verifikasi mensyaratkan KTP sudah diunggah. */
    public function tindakan(Request $r, Member $anggota): RedirectResponse
    {
        Gate::authorize('anggota.kelola');
        $d = $r->validate(['aksi' => ['required', 'in:verifikasi,tolak,blokir']]);

        if ($d['aksi'] === 'verifikasi' && ! $anggota->ktp_path) {
            return back()->withErrors(['umum' => 'Penitip belum mengunggah foto KTP.']);
        }

        $status = ['verifikasi' => 'terverifikasi', 'tolak' => 'ditolak', 'blokir' => 'blokir'][$d['aksi']];
        $anggota->forceFill([
            'status' => $status,
            'verified_by' => $status === 'terverifikasi' ? $r->user()->id : null,
            'verified_at' => $status === 'terverifikasi' ? now() : null,
        ])->save();

        return back()->with('status', 'Status penitip diubah menjadi '.$status.'.');
    }

    /** Menampilkan NIK lengkap hanya atas permintaan eksplisit, dan dicatat di audit. */
    public function lihatNik(Member $anggota)
    {
        Gate::authorize('anggota.kelola');
        Audit::log('anggota.lihat_nik', $anggota);

        return view('panel.anggota.show', ['m' => $anggota->load('branch'), 'nikPenuh' => $anggota->nik_enc]);
    }
}
