<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Member;
use App\Models\User;
use App\Notifications\VerifikasiEmail;
use App\Services\Situs;
use App\Support\Pii;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Registrasi calon anggota. Peran selalu 'member'; tidak ada input peran dari form. */
class RegisterController extends Controller
{
    public function create()
    {
        $semuaCabang = Branch::where('is_active', true)->orderByRaw("FIELD(kode,'PUSAT','A','B')")->get()->keyBy('kode');

        return view('auth.daftar', [
            'cabang' => $semuaCabang->where('kode', '!=', 'PUSAT')->values(),
            'semuaCabang' => $semuaCabang,
            'pusat' => $semuaCabang->get('PUSAT'),
            'konten' => Situs::konten(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:120'],
            'nik' => ['required', 'digits:16', function ($atribut, $nilai, $gagal) {
                // Pesan umum: tidak membocorkan apakah NIK sudah terdaftar.
                if (Member::withoutGlobalScopes()->where('nik_hash', Pii::hash($nilai))->exists()) {
                    $gagal('Data tidak dapat diproses. Jika Anda merasa sudah mendaftar, hubungi admin cabang.');
                }
            }],
            'telepon' => ['required', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
            'branch_id' => ['required', Rule::exists('branches', 'id')->where('is_active', 1)->whereNot('kode', 'PUSAT')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'setuju' => ['accepted'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit angka.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.regex' => 'Nomor telepon tidak valid. Contoh: 08123456789.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah dipakai.',
            'branch_id.required' => 'Pilih cabang.',
            'branch_id.exists' => 'Cabang tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 10 karakter.',
            'password.letters' => 'Kata sandi harus memuat huruf.',
            'password.numbers' => 'Kata sandi harus memuat angka.',
            'setuju.accepted' => 'Anda harus menyetujui pernyataan data benar.',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = new User(['name' => $data['nama_lengkap'], 'email' => $data['email'], 'password' => $data['password']]);
            $user->forceFill(['role' => Role::Member, 'status' => 'aktif', 'branch_id' => null])->save();

            Member::create([
                'user_id' => $user->id,
                'branch_id' => $data['branch_id'],
                'nama_lengkap' => $data['nama_lengkap'],
                'nik_enc' => $data['nik'],
                'nik_hash' => Pii::hash($data['nik']),
                'telepon' => $data['telepon'],
            ]);

            return $user;
        });

        $user->notify(new VerifikasiEmail);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('panel')->with('status', 'Pendaftaran berhasil. Data Anda akan diverifikasi admin cabang.');
    }
}
