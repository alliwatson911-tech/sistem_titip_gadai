/* linygadai — panel. Penangan kecil agar CSP dapat melarang skrip inline. Tanpa pustaka. */
(function () {
  'use strict';

  // Formulir dengan data-konfirmasi="..." meminta persetujuan sebelum dikirim.
  document.addEventListener('submit', function (e) {
    var pesan = e.target && e.target.dataset ? e.target.dataset.konfirmasi : null;
    if (pesan && !window.confirm(pesan)) e.preventDefault();
  });

  // Tombol dengan data-konfirmasi="..." (di dalam formulir) meminta persetujuan saat diklik.
  document.addEventListener('click', function (e) {
    var tombol = e.target.closest ? e.target.closest('button[data-konfirmasi]') : null;
    if (tombol && !window.confirm(tombol.dataset.konfirmasi)) e.preventDefault();

    // Tombol cetak pada nota dan kwitansi.
    var cetak = e.target.closest ? e.target.closest('[data-cetak]') : null;
    if (cetak) window.print();
  });
})();
