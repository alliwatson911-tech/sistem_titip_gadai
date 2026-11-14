@extends('layouts.panel')
@section('judul', 'Pendaftaran Penitip Baru')
@section('isi')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1>Pendaftaran Penitip Baru (Loket Cabang)</h1>
            <p class="sub">Petugas menginput identitas fisik penitip berdasarkan KTP asli di loket cabang PERSIDEN HP.</p>
        </div>
        <a href="{{ route('panel.anggota') }}" class="btn btn--kecil">&larr; Kembali ke Daftar Penitip</a>
    </div>

    @if ($errors->any())
        <div class="pesan-galat" style="background: rgba(228,22,27,.1); border-left: 4px solid var(--merah-hp, #E4161B); padding: 1rem; margin-bottom: 1.5rem; border-radius: 4px;">
            <b style="color: var(--merah-hp, #E4161B);">Mohon periksa kesalahan berikut:</b>
            <ul style="margin: .5rem 0 0; padding-left: 1.2rem; color: var(--tinta);">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel-form" style="max-width: 720px; background: #fff; padding: 2rem; border-radius: var(--radius-m); border: 1.5px solid var(--garis);">
        <form method="POST" action="{{ route('panel.anggota.store') }}">
            @csrf

            <div class="bidang">
                <label for="nama_lengkap">Nama Lengkap Sesuai KTP <span style="color: red;">*</span></label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required placeholder="Contoh: Budi Santoso" autocomplete="off">
            </div>

            <div class="bidang">
                <label for="nik">Nomor Induk Kependudukan (NIK 16 Digit) <span style="color: red;">*</span></label>
                <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required maxlength="16" pattern="[0-9]{16}" inputmode="numeric" placeholder="16 digit angka sesuai KTP" autocomplete="off">
                <p class="catatan-kecil" style="margin-top: .35rem; color: var(--muted);">Data NIK disimpan dengan enkripsi aman di basis data.</p>
            </div>

            <div class="dua-kolom" style="gap: 1.25rem;">
                <div class="bidang">
                    <label for="telepon">Nomor WhatsApp / HP Aktif <span style="color: red;">*</span></label>
                    <input type="tel" id="telepon" name="telepon" value="{{ old('telepon') }}" required placeholder="Contoh: 08123456789" autocomplete="off">
                    <p class="catatan-kecil" style="margin-top: .35rem; color: var(--muted);">Notifikasi rincian akun dan jatuh tempo akan dikirimkan ke nomor ini.</p>
                </div>

                <div class="bidang">
                    <label for="email">Alamat Email Penitip <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="Contoh: nama@domain.com" autocomplete="off">
                    <p class="catatan-kecil" style="margin-top: .35rem; color: var(--muted);">Digunakan untuk akses masuk (login) ke portal digital penitip.</p>
                </div>
            </div>

            <div class="bidang">
                <label for="branch_id">Cabang Loket Pendaftaran <span style="color: red;">*</span></label>
                <select id="branch_id" name="branch_id" required>
                    @foreach ($cabang as $c)
                        <option value="{{ $c->id }}" @selected(old('branch_id', $defaultBranchId) == $c->id)>
                            {{ $c->nama_publik }} ({{ $c->kode }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="bidang">
                <label for="alamat">Alamat Domisili Penitip (Opsional)</label>
                <textarea id="alamat" name="alamat" rows="2" placeholder="Alamat jalan, kelurahan, kecamatan">{{ old('alamat') }}</textarea>
            </div>

            <div style="background: var(--kertas-2); border-left: 4px solid var(--kuningan); padding: 1rem 1.25rem; border-radius: 4px; margin: 1.5rem 0; font-size: .9rem; line-height: 1.6; color: var(--tinta-2);">
                <b>Proses Otomatis Sistem:</b>
                <ul style="margin: .35rem 0 0; padding-left: 1.2rem;">
                    <li>Sistem akan membuat akun User dan Member langsung dengan status <strong>terverifikasi</strong>.</li>
                    <li>Sistem menerbitkan <strong>kata sandi sementara</strong> yang wajib diganti penitip saat login pertama.</li>
                    <li>Notifikasi rincian akun otomatis dimasukkan ke antrean kirim WhatsApp &amp; Email.</li>
                </ul>
            </div>

            <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1.5rem;">
                <button type="submit" class="btn btn--utama">Daftarkan Penitip &amp; Buat Akun</button>
                <a href="{{ route('panel.anggota') }}" class="btn">Batal</a>
            </div>
        </form>
    </div>
@endsection

