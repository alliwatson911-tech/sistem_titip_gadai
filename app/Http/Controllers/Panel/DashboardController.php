<?php

namespace App\Http\Controllers\Panel;

use App\Enums\ApplicationStatus;
use App\Enums\LoanStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Payment;
use App\Services\RingkasanPanel;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user()->load('branch');

        return view('panel.beranda', [
            'user' => $user,
            'tugas' => $this->tugas($user),
            'ringkasan' => app(RingkasanPanel::class)->untuk($user),
        ]);
    }

    /** Pekerjaan yang menunggu peran ini: [label, jumlah, tautan]. Jumlah sudah dibatasi cabang/kepemilikan oleh global scope. */
    private function tugas($user): array
    {
        $p = fn (ApplicationStatus $s) => Application::where('status', $s->value)->count();
        $url = fn (ApplicationStatus $s) => route('panel.pengajuan.index', ['status' => $s->value]);
        $bayar = array_map(fn ($s) => $s->value, LoanStatus::dapatDibayar());
        $segera = fn () => Loan::whereIn('status', $bayar)->whereDate('jatuh_tempo', '<=', today()->addDays(7))->count();
        $segeraUrl = route('panel.gadai.index', ['segera' => 1]);
        $siapLelang = fn () => Loan::where('status', LoanStatus::SiapLelang->value)->count();
        $menunggu = fn () => Payment::where('status', 'menunggu_verifikasi')->count();

        return match ($user->role) {
            Role::Teller => [
                ['Menunggu barang dibawa', $p(ApplicationStatus::MenungguBarang), $url(ApplicationStatus::MenungguBarang)],
                ['Disetujui, siap cair', $p(ApplicationStatus::Disetujui), $url(ApplicationStatus::Disetujui)],
                ['Gadai jatuh tempo ≤ 7 hari', $segera(), $segeraUrl],
                ['Transfer menunggu verifikasi', $menunggu(), route('panel.gadai.index')],
            ],
            Role::Analis => [['Menunggu taksiran', $p(ApplicationStatus::BarangDiterima), $url(ApplicationStatus::BarangDiterima)]],
            Role::Manager, Role::Owner => [
                ['Menunggu persetujuan', $p(ApplicationStatus::MenungguManager), $url(ApplicationStatus::MenungguManager)],
                ['Siap lelang', $siapLelang(), route('panel.lelang.index')],
                ['Gadai jatuh tempo ≤ 7 hari', $segera(), $segeraUrl],
                ['Pengajuan berjalan', Application::whereIn('status', array_map(fn ($s) => $s->value, ApplicationStatus::terbuka()))->count(), route('panel.pengajuan.index')],
            ],
            Role::Admin => [['Anggota menunggu verifikasi', Member::where('status', 'calon')->count(), route('panel.anggota', ['status' => 'calon'])]],
            Role::Member => [
                ['Pengajuan berjalan', Application::whereIn('status', array_map(fn ($s) => $s->value, ApplicationStatus::terbuka()))->count(), route('panel.pengajuan.index')],
                ['Gadai berjalan', Loan::whereIn('status', $bayar)->count(), route('panel.gadai.index')],
                ['Jatuh tempo ≤ 7 hari', $segera(), $segeraUrl],
            ],
        };
    }
}
