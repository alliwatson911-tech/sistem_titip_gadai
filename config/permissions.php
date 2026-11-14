<?php

/**
 * Matriks hak akses: izin => peran yang diizinkan.
 * Sumber tunggal; dipetakan ke Gate di AppServiceProvider (mis. @can('anggota.lihat')).
 * Cakupan cabang/kepemilikan ditegakkan terpisah oleh global scope BelongsToBranch dan Policy.
 * Lihat docs/00-rancangan-tahap0.md §4.
 */

use App\Enums\Role;

$semuaStaf = [Role::Owner, Role::Manager, Role::Admin, Role::Teller, Role::Analis];

return [
    'pengguna.kelola'            => [Role::Owner],
    'cabang.lihat'               => [Role::Owner, Role::Manager],
    'cabang.ubah'                => [Role::Owner],

    'kebijakan.lihat'            => $semuaStaf,
    'kebijakan.ubah'             => [Role::Owner],
    'produk.ubah_konten'         => [Role::Owner, Role::Admin],

    'anggota.lihat'              => $semuaStaf,
    'anggota.kelola'             => [Role::Owner, Role::Admin],
    'anggota.tambah'             => [Role::Owner, Role::Manager, Role::Admin, Role::Teller],

    'konten.lihat'               => [Role::Owner, Role::Manager, Role::Admin],
    'konten.kelola'              => [Role::Owner, Role::Admin],   // pengumuman dan FAQ (Admin: cabangnya)
    'cabang.kontak'              => [Role::Owner, Role::Admin],   // kontak/lokasi cabang (Admin: cabangnya saja)
    'konten.situs'               => [Role::Owner],                // tagline, sambutan, izin usaha, sosmed (global)
    'profil.ubah'                => [Role::Member],

    'pengajuan.buat'             => [Role::Member, Role::Teller],
    'pengajuan.lihat'            => [...$semuaStaf, Role::Member],

    'jaminan.terima'             => [Role::Teller],
    'jaminan.keluarkan'          => [Role::Teller],

    'taksiran.buat'              => [Role::Analis],
    'taksiran.lihat'             => [Role::Owner, Role::Manager, Role::Teller, Role::Analis, Role::Member],

    'persetujuan.putuskan'       => [Role::Owner, Role::Manager],

    'pencairan.catat'            => [Role::Teller],
    'pembayaran.catat'           => [Role::Teller],
    'pembayaran.verifikasi'      => [Role::Manager, Role::Teller],
    'pembayaran.batalkan'        => [Role::Manager],

    'lelang.kelola'              => [Role::Owner, Role::Manager],
    'lelang.catat'               => [Role::Manager, Role::Teller],

    'gadai.lihat'                => [...$semuaStaf, Role::Member],

    'laporan.cabang'             => [Role::Owner, Role::Manager],
    'laporan.harian'             => [Role::Teller],
    'laporan.konsolidasi'        => [Role::Owner],

    'audit.lihat'                => [Role::Owner, Role::Manager],
];
