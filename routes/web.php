<?php

use App\Http\Controllers\Auth\AturUlangSandiController;
use App\Http\Controllers\Auth\DuaFaktorTantanganController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LupaSandiController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifikasiEmailController;
use App\Http\Controllers\Panel\AkunController;
use App\Http\Controllers\Panel\AuditController;
use App\Http\Controllers\Panel\BerkasController;
use App\Http\Controllers\Panel\DashboardController;
use App\Http\Controllers\Panel\DuaFaktorController;
use App\Http\Controllers\Panel\FaqController;
use App\Http\Controllers\Panel\GadaiController;
use App\Http\Controllers\Panel\KontakCabangController;
use App\Http\Controllers\Panel\KontenSitusController;
use App\Http\Controllers\Panel\LaporanController;
use App\Http\Controllers\Panel\LelangController;
use App\Http\Controllers\Panel\MemberController;
use App\Http\Controllers\Panel\PembayaranController;
use App\Http\Controllers\Panel\PenebusanController;
use App\Http\Controllers\Panel\PengajuanController;
use App\Http\Controllers\Panel\PengumumanController;
use App\Http\Controllers\Panel\ProfilController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Controllers\Publik\BerandaController;
use App\Http\Controllers\Publik\HalamanPublikController;
use App\Http\Controllers\Publik\SimulasiController;
use App\Http\Controllers\Publik\SitemapController;
use Illuminate\Support\Facades\Route;

// Halaman publik utama & endpoint AJAX
Route::get('/', BerandaController::class)->name('beranda');
Route::get('/simulasi', SimulasiController::class)->middleware('throttle:60,1')->name('simulasi');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Halaman publik khusus tiap panel navigasi
Route::get('/layanan', [HalamanPublikController::class, 'layanan'])->name('publik.layanan');
Route::get('/standar', [HalamanPublikController::class, 'standar'])->name('publik.standar');
Route::get('/panduan', [HalamanPublikController::class, 'panduan'])->name('publik.panduan');
Route::get('/syarat', [HalamanPublikController::class, 'syarat'])->name('publik.syarat');
Route::get('/simulasi-gadai', [HalamanPublikController::class, 'simulasi'])->name('publik.simulasi');
Route::get('/faq', [HalamanPublikController::class, 'faq'])->name('publik.faq');
Route::get('/tentang', [HalamanPublikController::class, 'tentang'])->name('publik.tentang');

// Halaman masuk dan daftar terpisah dan tersendiri
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'create'])->name('masuk');
    Route::get('/daftar', [RegisterController::class, 'create'])->name('daftar');
    Route::post('/masuk', [LoginController::class, 'store'])->middleware('throttle:20,1');
    Route::post('/daftar', [RegisterController::class, 'store'])->middleware('throttle:6,1');

    // Tahap kedua masuk (2FA) untuk staf.
    Route::get('/verifikasi-2fa', [DuaFaktorTantanganController::class, 'create'])->name('dua-faktor.tantangan');
    Route::post('/verifikasi-2fa', [DuaFaktorTantanganController::class, 'store'])->middleware('throttle:15,1')->name('dua-faktor.periksa');

    // Lupa kata sandi: tautan lewat email, sekali pakai, berlaku 60 menit.
    Route::get('/lupa-sandi', [LupaSandiController::class, 'create'])->name('sandi.lupa');
    Route::post('/lupa-sandi', [LupaSandiController::class, 'store'])->middleware('throttle:10,1')->name('sandi.kirim');
    Route::get('/atur-ulang-sandi/{token}', [AturUlangSandiController::class, 'create'])->name('sandi.atur-ulang');
    Route::post('/atur-ulang-sandi', [AturUlangSandiController::class, 'store'])->middleware('throttle:10,1')->name('sandi.simpan');
});

Route::get('/email/verifikasi/{id}/{hash}', [VerifikasiEmailController::class, 'verifikasi'])->middleware(['signed', 'throttle:20,1'])->name('email.verifikasi');

Route::post('/keluar', [LoginController::class, 'destroy'])->middleware('auth')->name('keluar');

Route::middleware(['auth', 'wajib.2fa'])->prefix('panel')->group(function () {
    Route::get('/', DashboardController::class)->name('panel');

    // Pengajuan titip gadai (semua peran; tindakan dibatasi Gate di controller dan status oleh ApplicationFlow).
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('panel.pengajuan.index');
    Route::get('/pengajuan/baru', [PengajuanController::class, 'create'])->name('panel.pengajuan.create');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->middleware('throttle:20,1')->name('panel.pengajuan.store');
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('panel.pengajuan.show');
    Route::post('/pengajuan/{pengajuan}/terima', [PengajuanController::class, 'terima'])->name('panel.pengajuan.terima');
    Route::post('/pengajuan/{pengajuan}/taksir', [PengajuanController::class, 'taksir'])->name('panel.pengajuan.taksir');
    Route::post('/pengajuan/{pengajuan}/putuskan', [PengajuanController::class, 'putuskan'])->name('panel.pengajuan.putuskan');
    Route::post('/pengajuan/{pengajuan}/batal', [PengajuanController::class, 'batal'])->name('panel.pengajuan.batal');
    Route::post('/pengajuan/{pengajuan}/kembalikan', [PengajuanController::class, 'kembalikan'])->name('panel.pengajuan.kembalikan');

    Route::post('/pengajuan/{pengajuan}/cairkan', [GadaiController::class, 'cairkan'])->name('panel.gadai.cairkan');

    // Gadai berjalan, pembayaran, penebusan, lelang
    Route::get('/gadai', [GadaiController::class, 'index'])->name('panel.gadai.index');
    Route::get('/gadai/{gadai}', [GadaiController::class, 'show'])->name('panel.gadai.show');
    Route::get('/gadai/{gadai}/nota', [GadaiController::class, 'nota'])->name('panel.gadai.nota');
    Route::post('/gadai/{gadai}/taksir-ulang', [GadaiController::class, 'taksirUlang'])->name('panel.gadai.taksir-ulang');
    Route::post('/gadai/{gadai}/bayar', [PembayaranController::class, 'store'])->middleware('throttle:30,1')->name('panel.pembayaran.store');
    Route::post('/gadai/{gadai}/keluarkan', [PenebusanController::class, 'store'])->name('panel.penebusan.store');
    Route::post('/pembayaran/{pembayaran}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('panel.pembayaran.verifikasi');
    Route::post('/pembayaran/{pembayaran}/batalkan', [PembayaranController::class, 'batalkan'])->name('panel.pembayaran.batalkan');
    Route::get('/pembayaran/{pembayaran}/kwitansi', [PembayaranController::class, 'kwitansi'])->name('panel.pembayaran.kwitansi');
    Route::get('/pembayaran/{pembayaran}/bukti', [PembayaranController::class, 'bukti'])->name('panel.pembayaran.bukti');
    Route::get('/lelang', [LelangController::class, 'index'])->name('panel.lelang.index');
    Route::post('/lelang/{gadai}/jadwal', [LelangController::class, 'jadwal'])->name('panel.lelang.jadwal');
    Route::post('/lelang/{gadai}/hasil', [LelangController::class, 'hasil'])->name('panel.lelang.hasil');
    Route::post('/lelang/{gadai}/kelebihan', [LelangController::class, 'kelebihan'])->name('panel.lelang.kelebihan');

    // Anggota
    Route::middleware('can:anggota.lihat')->group(function () {
        Route::get('/anggota', [MemberController::class, 'index'])->name('panel.anggota');
        Route::get('/anggota/{anggota}', [MemberController::class, 'show'])->name('panel.anggota.show');
        Route::post('/anggota/{anggota}/tindakan', [MemberController::class, 'tindakan'])->name('panel.anggota.tindakan');
        Route::post('/anggota/{anggota}/nik', [MemberController::class, 'lihatNik'])->name('panel.anggota.nik');
    });

    // Berkas pribadi (disk privat, otorisasi per permintaan)
    Route::get('/berkas/ktp/{anggota}', [BerkasController::class, 'ktp'])->name('panel.berkas.ktp');
    Route::get('/berkas/foto/{foto}', [BerkasController::class, 'foto'])->name('panel.berkas.foto');
    Route::get('/berkas/kuasa/{gadai}', [BerkasController::class, 'kuasa'])->name('panel.berkas.kuasa');
    Route::post('/akun/kirim-verifikasi', [VerifikasiEmailController::class, 'kirimUlang'])->middleware('throttle:3,10')->name('panel.akun.verifikasi.kirim');

    // Ganti kata sandi sendiri (semua peran)
    Route::get('/akun/sandi', [AkunController::class, 'sandi'])->name('panel.akun.sandi');
    Route::put('/akun/sandi', [AkunController::class, 'ubahSandi'])->middleware('throttle:6,1')->name('panel.akun.sandi.ubah');

    // Autentikasi dua faktor (staf)
    Route::get('/akun/2fa', [DuaFaktorController::class, 'tampil'])->name('panel.akun.2fa');
    Route::post('/akun/2fa', [DuaFaktorController::class, 'mulai'])->name('panel.akun.2fa.mulai');
    Route::post('/akun/2fa/konfirmasi', [DuaFaktorController::class, 'konfirmasi'])->middleware('throttle:10,1')->name('panel.akun.2fa.konfirmasi');
    Route::delete('/akun/2fa', [DuaFaktorController::class, 'nonaktifkan'])->middleware('throttle:6,1')->name('panel.akun.2fa.nonaktif');
    Route::post('/akun/2fa/kode-baru', [DuaFaktorController::class, 'kodeBaru'])->middleware('throttle:6,1')->name('panel.akun.2fa.kode');

    // Profil anggota
    Route::get('/profil', [ProfilController::class, 'edit'])->name('panel.profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('panel.profil.update');
    Route::post('/profil/ktp', [ProfilController::class, 'unggahKtp'])->middleware('throttle:10,1')->name('panel.profil.ktp');

    // Konten situs
    Route::resource('pengumuman', PengumumanController::class)->except('show')->parameters(['pengumuman' => 'pengumuman'])->names('panel.pengumuman');
    Route::resource('faq', FaqController::class)->except('show')->parameters(['faq' => 'faq'])->names('panel.faq');
    Route::get('/kontak', [KontakCabangController::class, 'index'])->name('panel.kontak.index');
    Route::get('/kontak/{cabang}', [KontakCabangController::class, 'edit'])->name('panel.kontak.edit');
    Route::put('/kontak/{cabang}', [KontakCabangController::class, 'update'])->name('panel.kontak.update');
    Route::get('/situs', [KontenSitusController::class, 'edit'])->name('panel.situs');
    Route::put('/situs', [KontenSitusController::class, 'update'])->name('panel.situs.update');

    // Laporan dan audit
    Route::get('/laporan', [LaporanController::class, 'cabang'])->name('panel.laporan.cabang');
    Route::get('/laporan/konsolidasi', [LaporanController::class, 'konsolidasi'])->name('panel.laporan.konsolidasi');
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('panel.laporan.harian');
    Route::get('/audit', [AuditController::class, 'index'])->name('panel.audit.index');
    Route::get('/audit/{audit}', [AuditController::class, 'show'])->name('panel.audit.show');

    // Pengguna (Owner)
    Route::middleware('can:pengguna.kelola')->group(function () {
        Route::resource('pengguna', UserController::class)->except(['show', 'destroy'])->parameters(['pengguna' => 'pengguna'])->names('panel.pengguna');
        Route::post('/pengguna/{pengguna}/reset-sandi', [UserController::class, 'resetSandi'])->name('panel.pengguna.reset');
    });
});
