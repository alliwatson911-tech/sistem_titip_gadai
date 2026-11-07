<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Manajemen staf oleh Owner. Akun anggota dibuat lewat pendaftaran, bukan di sini. */
class UserController extends Controller
{
    private const PERAN_STAF = ['owner', 'manager', 'admin', 'teller', 'analis'];

    public function index()
    {
        Gate::authorize('pengguna.kelola');

        return view('panel.pengguna.index', [
            'pengguna' => User::with('branch')->where('role', '!=', Role::Member->value)->orderBy('role')->orderBy('branch_id')->orderBy('name')->paginate(30),
        ]);
    }

    public function create()
    {
        Gate::authorize('pengguna.kelola');

        return view('panel.pengguna.form', ['u' => new User(['status' => 'aktif']), 'cabang' => $this->cabang()]);
    }

    public function store(Request $r): RedirectResponse
    {
        Gate::authorize('pengguna.kelola');
        $d = $this->validasi($r);
        $sandi = Str::password(14, symbols: false);

        $u = new User(['name' => $d['name'], 'email' => $d['email'], 'password' => $sandi]);
        $u->forceFill(['role' => $d['role'], 'branch_id' => $d['branch_id'], 'status' => 'aktif', 'email_verified_at' => now()])->save();

        return redirect()->route('panel.pengguna.index')
            ->with('status', "Akun {$u->email} dibuat.")
            ->with('sandi_baru', ['email' => $u->email, 'sandi' => $sandi]);
    }

    public function edit(User $pengguna)
    {
        Gate::authorize('pengguna.kelola');
        abort_if($pengguna->role === Role::Member, 404);

        return view('panel.pengguna.form', ['u' => $pengguna, 'cabang' => $this->cabang()]);
    }

    public function update(Request $r, User $pengguna): RedirectResponse
    {
        Gate::authorize('pengguna.kelola');
        abort_if($pengguna->role === Role::Member, 404);

        $d = $this->validasi($r, $pengguna);
        $d['status'] = $r->validate(['status' => ['required', 'in:aktif,nonaktif']])['status'];

        $diri = $pengguna->is($r->user());
        if ($diri && ($d['role'] !== $pengguna->role->value || $d['status'] !== 'aktif')) {
            return back()->withInput()->withErrors(['umum' => 'Anda tidak dapat mengubah peran atau menonaktifkan akun Anda sendiri.']);
        }
        $kehilanganOwner = $pengguna->role === Role::Owner && ($d['role'] !== 'owner' || $d['status'] !== 'aktif');
        if ($kehilanganOwner && User::where('role', Role::Owner->value)->where('status', 'aktif')->count() <= 1) {
            return back()->withInput()->withErrors(['umum' => 'Harus ada minimal satu Owner aktif.']);
        }

        $pengguna->name = $d['name'];
        $pengguna->email = $d['email'];
        $pengguna->forceFill(['role' => $d['role'], 'branch_id' => $d['branch_id'], 'status' => $d['status']])->save();

        if ($d['status'] === 'nonaktif') {
            DB::table('sessions')->where('user_id', $pengguna->id)->delete();
        }

        return redirect()->route('panel.pengguna.index')->with('status', 'Akun diperbarui.');
    }

    public function resetSandi(User $pengguna): RedirectResponse
    {
        Gate::authorize('pengguna.kelola');
        abort_if($pengguna->role === Role::Member, 404);

        $sandi = Str::password(14, symbols: false);
        $pengguna->password = $sandi;
        $pengguna->save();
        DB::table('sessions')->where('user_id', $pengguna->id)->delete();
        Audit::log('user.reset_sandi', $pengguna);

        return redirect()->route('panel.pengguna.index')
            ->with('status', "Kata sandi {$pengguna->email} direset dan semua sesinya diakhiri.")
            ->with('sandi_baru', ['email' => $pengguna->email, 'sandi' => $sandi]);
    }

    private function cabang()
    {
        return Branch::where('is_active', true)->where('kode', '!=', 'PUSAT')->orderBy('kode')->get();
    }

    private function validasi(Request $r, ?User $abaikan = null): array
    {
        $d = $r->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($abaikan?->id)],
            'role' => ['required', Rule::in(self::PERAN_STAF)],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('is_active', 1)->whereNot('kode', 'PUSAT')],
        ], [
            'name.required' => 'Nama wajib diisi.', 'email.required' => 'Email wajib diisi.', 'email.unique' => 'Email sudah dipakai.',
            'role.in' => 'Peran tidak valid.', 'branch_id.exists' => 'Cabang tidak valid.',
        ]);

        $peran = Role::from($d['role']);
        if ($peran->isBranchBound() && empty($d['branch_id'])) {
            abort(back()->withInput()->withErrors(['branch_id' => 'Peran ini wajib terikat pada satu cabang.']));
        }
        $d['branch_id'] = $peran->isBranchBound() ? (int) $d['branch_id'] : null; // Owner tidak terikat cabang

        return $d;
    }
}
