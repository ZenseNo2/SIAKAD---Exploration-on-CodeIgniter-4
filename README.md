# SIAKAD — Sistem Informasi Akademik (CodeIgniter 4)

Aplikasi akademik dengan tiga peran:

| Peran | Fitur |
|---|---|
| **Admin** | Data mahasiswa, dosen, program studi, mata kuliah, tahun akademik (aktif & periode KRS), jadwal kelas (cek bentrok dosen/ruang) |
| **Dosen** | Kelas yang diampu, input nilai Tugas/UTS/UAS (nilai akhir & huruf otomatis), pantau mahasiswa perwalian |
| **Mahasiswa** | Isi KRS (cek jadwal bentrok, kuota, batas SKS dari IPS), jadwal mingguan, KHS per semester, transkrip & IPK |

Dibangun dengan CodeIgniter **4.7.6** (folder `system/` sudah disertakan, jadi tidak wajib Composer).

## Kebutuhan
- PHP **8.2+** dengan ekstensi `intl`, `mbstring`, `mysqli` (atau `sqlite3`)
- MySQL / MariaDB (XAMPP 8.2+, Laragon, dll.)

> XAMPP: buka `php.ini`, hapus titik koma pada `;extension=intl`, lalu restart Apache.

## Menjalankan
```bash
# 1. Buat database kosong bernama "siakad" (phpMyAdmin atau: CREATE DATABASE siakad;)
# 2. Sesuaikan koneksi di file .env (hostname, username, password)
# 3. Buat tabel dan data contoh
php spark migrate
php spark db:seed DatabaseSeeder
# 4. Jalankan
php spark serve
```
Buka http://localhost:8080

| Peran | Username | Password |
|---|---|---|
| Admin | `admin` | `admin123` |
| Dosen | `0712058801` | `0712058801` |
| Mahasiswa | `25110001` | `25110001` |

Lewat XAMPP (tanpa `spark serve`): taruh folder di `htdocs/siakad`, ubah `app.baseURL` di `.env` menjadi
`http://localhost/siakad/public/`, lalu buka alamat tersebut.

Ingin mengulang data dari awal: `php spark migrate:refresh` lalu `php spark db:seed DatabaseSeeder`.

Penjelasan lengkap (langkah penerapan, alur kode, aturan KRS & IPK, desain UI): **`Penjelasan_SIAKAD.html`**.
