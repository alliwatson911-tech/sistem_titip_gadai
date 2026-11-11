/* linygadai — halaman publik. Tanpa pustaka. Rumus biaya TIDAK dihitung di sini: diambil dari server (GET /simulasi). */
(function () {
  'use strict';
  var d = document, root = d.documentElement;
  root.classList.add('js');
  var gerakKurang = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var rupiah = new Intl.NumberFormat('id-ID');
  var fmt = function (n) { return 'Rp ' + rupiah.format(n); };

  /* ---- menu mobile ---- */
  var tmb = d.getElementById('tombol-menu'), menu = d.getElementById('menu');
  if (tmb && menu) {
    tmb.addEventListener('click', function () {
      var buka = menu.classList.toggle('buka');
      tmb.setAttribute('aria-expanded', buka ? 'true' : 'false');
    });
    menu.addEventListener('click', function (e) {
      if (e.target.closest('a')) { menu.classList.remove('buka'); tmb.setAttribute('aria-expanded', 'false'); }
    });
    d.addEventListener('keydown', function (e) { if (e.key === 'Escape') { menu.classList.remove('buka'); tmb.setAttribute('aria-expanded', 'false'); } });
  }

  /* ---- efek header saat digulir ---- */
  var kepala = d.querySelector('.kepala');
  if (kepala) {
    var aturKepala = function () {
      if (window.scrollY > 8) {
        kepala.classList.add('tergulir');
      } else {
        kepala.classList.remove('tergulir');
      }
    };
    window.addEventListener('scroll', aturKepala, { passive: true });
    aturKepala();
  }

  /* ---- penanda bagian aktif di menu ---- */
  var tautan = {};
  d.querySelectorAll('#menu a[href^="#"]').forEach(function (a) { tautan[a.getAttribute('href').slice(1)] = a; });
  if ('IntersectionObserver' in window) {
    var pengamat = new IntersectionObserver(function (items) {
      items.forEach(function (it) {
        var kunci = it.target.dataset.spy;
        if (it.isIntersecting && tautan[kunci]) {
          Object.keys(tautan).forEach(function (k) { tautan[k].removeAttribute('aria-current'); });
          tautan[kunci].setAttribute('aria-current', 'true');
        }
      });
    }, { rootMargin: '-45% 0px -50% 0px' });
    d.querySelectorAll('[data-spy]').forEach(function (el) { pengamat.observe(el); });

    var muncul = new IntersectionObserver(function (items) {
      items.forEach(function (it) { if (it.isIntersecting) { it.target.classList.add('tampil'); muncul.unobserve(it.target); } });
    }, { rootMargin: '0px 0px -8% 0px' });
    d.querySelectorAll('[data-muncul]').forEach(function (el) { muncul.observe(el); });
  } else {
    d.querySelectorAll('[data-muncul]').forEach(function (el) { el.classList.add('tampil'); });
  }

  /* ---- billboard ---- */
  var papan = d.getElementById('billboard');
  if (papan) {
    d.querySelectorAll('[data-geser]').forEach(function (b) {
      b.addEventListener('click', function () {
        var langkah = papan.firstElementChild ? papan.firstElementChild.getBoundingClientRect().width + 20 : 400;
        papan.scrollBy({ left: Number(b.dataset.geser) * langkah, behavior: gerakKurang ? 'auto' : 'smooth' });
      });
    });
  }

  /* ---- bila ada galat form, lompat ke bagian akun ---- */
  if (d.body.dataset.ke) {
    var tujuan = d.getElementById(d.body.dataset.ke);
    if (tujuan) tujuan.scrollIntoView();
  }

  /* ---- filter kategori produk ---- */
  var tabs = d.querySelectorAll('.tab-pill');
  var kartuList = d.querySelectorAll('.kartu-modern');
  if (tabs.length && kartuList.length) {
    var terapkanFilter = function (kat, gantiUrl) {
      tabs.forEach(function (t) {
        var cocok = t.dataset.filter === kat;
        t.classList.toggle('aktif', cocok);
        t.setAttribute('aria-selected', cocok ? 'true' : 'false');
      });
      kartuList.forEach(function (k) {
        if (kat === 'semua' || k.dataset.kategori === kat) {
          k.style.display = '';
        } else {
          k.style.display = 'none';
        }
      });
      if (gantiUrl && window.history && window.history.replaceState) {
        var url = new URL(window.location.href);
        if (kat === 'semua') {
          url.searchParams.delete('kategori');
        } else {
          url.searchParams.set('kategori', kat);
        }
        window.history.replaceState(null, '', url.toString());
      }
    };

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        terapkanFilter(tab.dataset.filter, true);
      });
    });

    // Cek jika ada URL param ?kategori=... saat halaman dimuat
    try {
      var paramKat = new URLSearchParams(window.location.search).get('kategori');
      if (paramKat && d.querySelector('.tab-pill[data-filter="' + paramKat + '"]')) {
        terapkanFilter(paramKat, false);
      }
    } catch (e) {}
  }

  /* ---- tombol kembali ke atas (back-to-top) ---- */
  var btnTop = d.getElementById('back-to-top');
  if (btnTop) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 300) {
        btnTop.classList.add('aktif');
      } else {
        btnTop.classList.remove('aktif');
      }
    }, { passive: true });

    btnTop.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: gerakKurang ? 'auto' : 'smooth' });
    });
  }

  /* ---- simulasi ---- */
  var dataEl = d.getElementById('data-produk');
  var pilih = d.getElementById('sim-produk');
  if (!dataEl || !pilih) return;

  var formSim = d.getElementById('sim-form');
  if (formSim) formSim.addEventListener('submit', function (e) { e.preventDefault(); }); // tanpa penangan inline (CSP)
  var produk = JSON.parse(dataEl.textContent);
  var taksir = d.getElementById('sim-taksiran'), ltv = d.getElementById('sim-ltv'), ltvNilai = d.getElementById('sim-ltv-nilai');
  var nota = d.getElementById('nota'), pesan = d.getElementById('n-pesan');
  var el = function (id) { return d.getElementById(id); };
  var tunda = null, pembatal = null;

  function produkAktif() { return produk.filter(function (p) { return String(p.id) === pilih.value; })[0] || produk[0]; }
  function angkaTaksiran() { return parseInt((taksir.value || '').replace(/\D/g, ''), 10) || 0; }
  var satuanAktif = 'bulanan';
  var tabsSatuan = d.querySelectorAll('.tab-satuan');
  var boxHarian = d.getElementById('durasi-harian');
  var boxMingguan = d.getElementById('durasi-mingguan');
  var boxBulanan = d.getElementById('durasi-bulanan');
  var sliderHari = d.getElementById('sim-hari-custom');
  var sliderHariNilai = d.getElementById('sim-hari-custom-nilai');

  function periode() { var r = d.querySelector('input[name="periode"]:checked'); return r ? parseInt(r.value, 10) : 1; }
  function hariHarian() { return sliderHari ? parseInt(sliderHari.value, 10) : 7; }
  function hariMingguan() { var r = d.querySelector('input[name="durasi_minggu"]:checked'); return r ? parseInt(r.value, 10) : 7; }

  function aturRentang() {
    var p = produkAktif(), maks = Math.floor(p.ltvMaxBp / 500) * 500;
    ltv.min = 1000; ltv.max = maks; ltv.step = 500;
    if (parseInt(ltv.value, 10) > maks || !ltv.dataset.disentuh) ltv.value = maks;
    ltvNilai.textContent = (ltv.value / 100).toLocaleString('id-ID') + '%';
    el('sim-batas').textContent = 'Pinjaman ' + fmt(p.min) + ' sampai ' + fmt(p.max) + ' · maksimal ' + (p.ltvMaxBp / 100).toLocaleString('id-ID') + '% dari taksiran';
  }

  function tulis(id, nilai) { var n = el(id); if (n) n.textContent = nilai; }

  function tampilkan(h) {
    nota.classList.remove('memuat'); pesan.textContent = '';
    tulis('n-produk', h.produk); tulis('n-taksiran', fmt(h.taksiran));
    tulis('n-ltv', (h.ltv_bp / 100).toLocaleString('id-ID') + '%');
    tulis('n-pokok', fmt(h.pokok));
    
    var ketHari = h.hari_total + ' hari';
    if (satuanAktif === 'mingguan') {
      ketHari = h.hari_total + ' hari (' + Math.round(h.hari_total / 7) + ' minggu)';
    } else if (satuanAktif === 'harian' && h.hari_total === 7) {
      ketHari = '7 hari (1 pekan)';
    }
    tulis('n-hari', ketHari);
    tulis('n-sewa', fmt(h.sewa_modal_total)); tulis('n-admin', fmt(h.administrasi_total));
    tulis('n-total', fmt(h.total_tebus));
    var tgl = new Date(h.jatuh_tempo + 'T00:00:00');
    tulis('n-jatuh', tgl.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));
    var tb = el('n-rincian'); tb.textContent = '';
    if (h.rincian.length > 1) {
      h.rincian.forEach(function (r) {
        var tr = d.createElement('tr');
        [r.keterangan, fmt(r.sewa_modal), fmt(r.administrasi)].forEach(function (t) { var td = d.createElement('td'); td.textContent = t; tr.appendChild(td); });
        tb.appendChild(tr);
      });
    }
    el('n-rincian-bungkus').hidden = h.rincian.length < 2;
  }

  function hitung() {
    if (pembatal) pembatal.abort();
    var p = produkAktif(), tak = angkaTaksiran();
    if (!tak) { pesan.textContent = 'Isi perkiraan taksiran barang Anda.'; return; }
    pembatal = 'AbortController' in window ? new AbortController() : null;
    nota.classList.add('memuat');
    var q = new URLSearchParams({ produk: p.id, taksiran: tak, ltv_bp: ltv.value });
    if (satuanAktif === 'harian') {
      q.set('hari', hariHarian());
    } else if (satuanAktif === 'mingguan') {
      q.set('hari', hariMingguan());
    } else {
      q.set('periode', periode());
    }
    fetch(el('sim-form').dataset.url + '?' + q.toString(), { headers: { Accept: 'application/json' }, signal: pembatal ? pembatal.signal : undefined })
      .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
      .then(function (x) {
        if (x.ok) { tampilkan(x.j); return; }
        nota.classList.remove('memuat');
        var m = x.j.pesan || (x.j.errors ? Object.values(x.j.errors)[0][0] : 'Masukan tidak valid.');
        pesan.textContent = m;
      })
      .catch(function (e) { if (e.name !== 'AbortError') { nota.classList.remove('memuat'); pesan.textContent = 'Simulasi belum dapat dimuat. Coba lagi sebentar.'; } });
  }
  function jadwalkan() { clearTimeout(tunda); tunda = setTimeout(hitung, 220); }

  /* Tab pergantian satuan: harian / mingguan / bulanan */
  if (tabsSatuan.length) {
    tabsSatuan.forEach(function (btn) {
      btn.addEventListener('click', function () {
        tabsSatuan.forEach(function (b) { b.classList.remove('aktif'); b.setAttribute('aria-selected', 'false'); });
        btn.classList.add('aktif');
        btn.setAttribute('aria-selected', 'true');
        satuanAktif = btn.dataset.satuan;

        if (boxHarian) boxHarian.hidden = satuanAktif !== 'harian';
        if (boxMingguan) boxMingguan.hidden = satuanAktif !== 'mingguan';
        if (boxBulanan) boxBulanan.hidden = satuanAktif !== 'bulanan';

        jadwalkan();
      });
    });
  }

  /* Slider & radio durasi harian */
  if (sliderHari) {
    sliderHari.addEventListener('input', function () {
      var val = sliderHari.value;
      if (sliderHariNilai) sliderHariNilai.textContent = val + ' hari';
      var rad = d.querySelector('input[name="durasi_hari"][value="' + val + '"]');
      if (rad) {
        rad.checked = true;
      } else {
        d.querySelectorAll('input[name="durasi_hari"]').forEach(function (r) { r.checked = false; });
      }
      jadwalkan();
    });
  }
  d.querySelectorAll('input[name="durasi_hari"]').forEach(function (r) {
    r.addEventListener('change', function () {
      if (sliderHari) {
        sliderHari.value = r.value;
        if (sliderHariNilai) sliderHariNilai.textContent = r.value + ' hari';
      }
      jadwalkan();
    });
  });

  /* Radio durasi mingguan & bulanan */
  d.querySelectorAll('input[name="durasi_minggu"]').forEach(function (r) { r.addEventListener('change', jadwalkan); });
  d.querySelectorAll('input[name="periode"]').forEach(function (r) { r.addEventListener('change', jadwalkan); });

  pilih.addEventListener('change', function () {
    var p = produkAktif(), tak = angkaTaksiran();
    var minTak = Math.round(p.min / (p.ltvMaxBp / 10000));
    if (tak < minTak) {
      taksir.value = rupiah.format(Math.round(minTak * 1.2));
    }
    aturRentang();
    jadwalkan();
  });
  taksir.addEventListener('input', function () {
    var angka = taksir.value.replace(/\D/g, '').slice(0, 13);
    taksir.value = angka ? rupiah.format(parseInt(angka, 10)) : '';
    jadwalkan();
  });
  ltv.addEventListener('input', function () { ltv.dataset.disentuh = '1'; ltvNilai.textContent = (ltv.value / 100).toLocaleString('id-ID') + '%'; jadwalkan(); });
  d.querySelectorAll('input[name="periode"]').forEach(function (r) { r.addEventListener('change', jadwalkan); });

  /* tombol "Hitung simulasi" pada kartu produk memilih jenis barang yang sama */
  d.querySelectorAll('[data-pilih]').forEach(function (a) {
    a.addEventListener('click', function () {
      var p = produk.filter(function (x) { return x.slug === a.dataset.pilih; })[0];
      if (p) {
        pilih.value = String(p.id);
        var tak = angkaTaksiran();
        var minTak = Math.round(p.min / (p.ltvMaxBp / 10000));
        if (tak < minTak) {
          taksir.value = rupiah.format(Math.round(minTak * 1.2));
        }
        aturRentang();
        jadwalkan();
      }
    });
  });

  /* dukung parameter URL misal ?produk=gadai-laptop atau ?produk=2 */
  var urlParams = new URLSearchParams(window.location.search);
  var paramProduk = urlParams.get('produk');
  if (paramProduk) {
    var cocok = produk.filter(function (x) { return x.slug === paramProduk || String(x.id) === paramProduk; })[0];
    if (cocok) {
      pilih.value = String(cocok.id);
      var minTak = Math.round(cocok.min / (cocok.ltvMaxBp / 10000));
      taksir.value = rupiah.format(Math.max(minTak, 3000000));
    }
  }

  aturRentang();
  taksir.value = rupiah.format(3000000);
  hitung();
})();
