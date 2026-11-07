<footer class="kaki">
    <div class="wrap">
        <div>
            <a class="merek" href="#beranda">
                <svg viewBox="0 0 32 32" aria-hidden="true"><circle cx="16" cy="16" r="14" fill="#B8862B" stroke="#F4EFE6" stroke-width="2"/><path d="M11.5 8v13h9.5" fill="none" stroke="#1B2A2F" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                linygadai
            </a>
            <p style="margin:.9rem 0 0">{{ $konten['tagline'] ?? '' }}</p>
        </div>
        <ul aria-label="Pintasan">
            <li><a href="#produk">Produk</a></li>
            <li><a href="#panduan">Panduan</a></li>
            <li><a href="#simulasi">Simulasi</a></li>
            <li><a href="#tentang">Tentang Kami</a></li>
            <li><a href="#akun">Masuk Akun</a></li>
        </ul>
        <div>
            <small>Simulasi bersifat perkiraan; angka pasti ditetapkan setelah barang diperiksa langsung di cabang.</small>
            <small style="margin-top:.6rem">Data identitas Anda disimpan terenkripsi dan hanya dapat diakses petugas yang berwenang.</small>
            <small style="margin-top:.6rem">&copy; {{ date('Y') }} linygadai</small>
        </div>
    </div>
</footer>
