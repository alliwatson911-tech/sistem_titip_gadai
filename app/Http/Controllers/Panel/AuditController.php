<?php

namespace App\Http\Controllers\Panel;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Penampil audit (baca saja). Owner: semua. Manager: hanya catatan cabangnya. */
class AuditController extends Controller
{
    public function index(Request $r)
    {
        Gate::authorize('audit.lihat');
        $d = $r->validate(['aksi' => ['nullable', 'string', 'max:60'], 'cari' => ['nullable', 'string', 'max:120'], 'dari' => ['nullable', 'date'], 'sampai' => ['nullable', 'date']]);

        $q = AuditLog::with('user')->latest('id');
        $this->batasi($q, $r);
        if (! empty($d['aksi'])) {
            $q->where('aksi', 'like', addcslashes($d['aksi'], '%_\\').'%');
        }
        if (! empty($d['cari'])) {
            $q->whereHas('user', fn ($u) => $u->where('email', 'like', '%'.addcslashes($d['cari'], '%_\\').'%')->orWhere('name', 'like', '%'.addcslashes($d['cari'], '%_\\').'%'));
        }
        if (! empty($d['dari'])) {
            $q->where('created_at', '>=', $d['dari'].' 00:00:00');
        }
        if (! empty($d['sampai'])) {
            $q->where('created_at', '<=', $d['sampai'].' 23:59:59');
        }

        return view('panel.audit.index', ['daftar' => $q->paginate(50)->withQueryString()]);
    }

    public function show(Request $r, AuditLog $audit)
    {
        Gate::authorize('audit.lihat');
        $q = AuditLog::whereKey($audit->id);
        $this->batasi($q, $r);
        abort_unless($q->exists(), 404);

        return view('panel.audit.show', ['a' => $audit->load('user')]);
    }

    private function batasi($q, Request $r): void
    {
        if ($r->user()->role !== Role::Owner) {
            $q->where('branch_id', $r->user()->branch_id ?? 0); // catatan tanpa cabang (mis. gagal masuk) hanya untuk Owner
        }
    }
}
