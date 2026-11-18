/* SIAKAD — interaksi kecil di sisi browser (tanpa library). */
(function () {
  'use strict';

  // 1. Navbar: munculkan judul kecil & garis bawah saat halaman di-scroll (pola iOS large title)
  var navbar = document.querySelector('.navbar');
  if (navbar) {
    var onScroll = function () { navbar.classList.toggle('scrolled', window.scrollY > 40); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // 2. Sidebar sebagai drawer di layar kecil
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-open-nav]')) {
      document.body.classList.add('nav-open');
    } else if (document.body.classList.contains('nav-open') && !e.target.closest('.sidebar')) {
      document.body.classList.remove('nav-open');
    }
  });

  // 3. Tutup pesan alert
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.alert .close');
    if (btn) btn.closest('.alert').remove();
  });

  // 4. Konfirmasi ala UIAlertController untuk form berisiko (hapus, reset, dll.)
  //    <form data-confirm="Judul" data-confirm-text="Penjelasan" data-confirm-ok="Hapus">
  var dialog = document.getElementById('confirm-dialog');
  var pendingForm = null;
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.dataset.confirm || form.dataset.confirmed === '1' || !dialog || !dialog.showModal) return;
    e.preventDefault();
    pendingForm = form;
    dialog.querySelector('h3').textContent = form.dataset.confirm;
    dialog.querySelector('p').textContent = form.dataset.confirmText || '';
    dialog.querySelector('[data-ok]').textContent = form.dataset.confirmOk || 'Hapus';
    dialog.showModal();
  });
  if (dialog) {
    dialog.querySelector('[data-cancel]').addEventListener('click', function () { dialog.close(); pendingForm = null; });
    dialog.querySelector('[data-ok]').addEventListener('click', function () {
      dialog.close();
      if (pendingForm) { pendingForm.dataset.confirmed = '1'; pendingForm.requestSubmit ? pendingForm.requestSubmit() : pendingForm.submit(); }
    });
  }

  // 5. Halaman input nilai: hitung nilai akhir & huruf langsung saat dosen mengetik
  var sheet = document.querySelector('[data-grade-sheet]');
  if (sheet) {
    var bobot = JSON.parse(sheet.dataset.bobot);
    var skala = JSON.parse(sheet.dataset.skala);
    var kelasHuruf = function (h) {
      if (h === 'A' || h === 'AB') return 'grade grade-a';
      if (h === 'B' || h === 'BC') return 'grade grade-b';
      if (h === 'C') return 'grade grade-c';
      if (h === 'D' || h === 'E') return 'grade grade-d';
      return 'grade';
    };
    var hitung = function (tr) {
      var vals = {}, lengkap = true, valid = true;
      ['tugas', 'uts', 'uas'].forEach(function (k) {
        var input = tr.querySelector('[data-k="' + k + '"]');
        var raw = input.value.trim().replace(',', '.');
        var ok = raw === '' || (!isNaN(raw) && +raw >= 0 && +raw <= 100);
        input.classList.toggle('invalid', !ok);
        if (!ok) valid = false;
        if (raw === '') lengkap = false;
        vals[k] = parseFloat(raw);
      });
      var akhirEl = tr.querySelector('[data-akhir]');
      var hurufEl = tr.querySelector('[data-huruf]');
      if (!valid || !lengkap) { akhirEl.textContent = '–'; hurufEl.textContent = '–'; hurufEl.className = 'grade'; return; }
      var akhir = (vals.tugas * bobot.tugas + vals.uts * bobot.uts + vals.uas * bobot.uas) / 100;
      var huruf = 'E';
      for (var i = 0; i < skala.length; i++) { if (akhir >= skala[i].min) { huruf = skala[i].huruf; break; } }
      akhirEl.textContent = akhir.toFixed(2).replace('.', ',');
      hurufEl.textContent = huruf;
      hurufEl.className = kelasHuruf(huruf);
    };
    sheet.addEventListener('input', function (e) {
      var tr = e.target.closest('tr[data-row]');
      if (tr) { hitung(tr); sheet.dataset.dirty = '1'; }
    });
    sheet.querySelectorAll('tr[data-row]').forEach(hitung);

    // Peringatan jika meninggalkan halaman sebelum menyimpan
    window.addEventListener('beforeunload', function (e) {
      if (sheet.dataset.dirty === '1') { e.preventDefault(); e.returnValue = ''; }
    });
    sheet.closest('form').addEventListener('submit', function () { sheet.dataset.dirty = '0'; });
  }

  // 6. Filter otomatis: <select data-autosubmit> langsung mengirim form-nya
  document.querySelectorAll('select[data-autosubmit]').forEach(function (s) {
    s.addEventListener('change', function () { s.form.submit(); });
  });
})();
