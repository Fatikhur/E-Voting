# Panduan Pengujian Otomatis (Automated Testing Guide)

Dokumen ini menjelaskan strategi pengujian, skenario cakupan, dan cara menjalankan seluruh rangkaian pengujian otomatis (*automated test suite*) pada sistem **E-Voting RT/RW**.

---

## 1. Ikhtisar Rangkaian Pengujian

Aplikasi E-Voting RT/RW dilengkapi dengan **26 skenario pengujian komprehensif** dengan **73 asersi keamanan** menggunakan framework **PHPUnit 11 / Pest**, serta skrip simulasi serangan konkurensi langsung pada basis data **MySQL 8.4 LTS**.

```
tests/
├── Feature/
│   ├── AuthenticationTest.php       (Otentikasi, rate limit, remember me, logout)
│   ├── AuthorizationTest.php        (RBAC, pembatasan hak akses 4 role, policy)
│   ├── VotingSecurityTest.php       (One Person One Vote, token nonce, secret ballot)
│   ├── AuditTrailIntegrityTest.php  (Rantai hash SHA-256, deteksi manipulasi log)
│   ├── ElectionApprovalTest.php     (Two-Person Rule, pencegahan publikasi sepihak, tie-vote)
│   └── ConcurrencyVotingTest.php    (Simulasi double voting, race condition di DB)
├── Unit/
│   └── ExampleTest.php              (Unit test dasar)
└── mysql_concurrency_test.php       (Pengujian konkurensi langsung di engine MySQL 8.4)
```

---

## 2. Matriks Pengujian & Skenario Spesifik

### 2.1. Pengujian Otentikasi (`AuthenticationTest.php`)
- **Login Sukses**: Memverifikasi bahwa user dengan kredensial valid berhasil masuk dan diarahkan ke dashboard sesuai role masing-masing.
- **Login Gagal**: Memverifikasi bahwa kredensial salah menghasilkan error validasi yang tepat tanpa membocorkan eksistensi akun.
- **Rate Limiting Login**: Memverifikasi bahwa upaya login gagal lebih dari 5 kali berturut-turut akan diblokir selama 60 detik (*Too Many Attempts*).
- **Logout Aman**: Memverifikasi bahwa sesi dihanguskan dan token sesi di-regenerasi setelah pengguna keluar.

### 2.2. Pengujian Otorisasi & RBAC (`AuthorizationTest.php`)
- **Pemisahan Hak Akses Role**:
  - Super Admin dilarang memberikan suara (*voter endpoint blocked*).
  - Admin Wilayah dilarang membuka antarmuka Super Admin atau memberikan suara atas nama warga.
  - Pengawas dilarang memanipulasi data kandidat atau mengubah data kependudukan warga.
  - Pemilih dilarang keras mengakses endpoint manajemen (`/admin/*`, `/superadmin/*`, `/pengawas/*`).
- **Verifikasi Status Pengguna Aktif**: Pengguna dengan status `is_active = false` dicegat oleh `ActiveUserMiddleware` dan dilarang masuk ke sistem.

### 2.3. Pengujian Keamanan Suara (`VotingSecurityTest.php`)
- **One Person One Vote**: Pemilih yang telah berhasil memberikan suara tidak dapat memilih kembali pada pemilihan yang sama.
- **Single-Use Cryptographic Nonce**:
  - Token voting hanya berlaku satu kali.
  - Token yang sudah ditukarkan menjadi suara akan berstatus `is_used = true` dan ditolak jika dikirimkan kembali (*Anti-Replay Attack*).
  - Token yang telah melewati masa kedaluwarsa 15 menit otomatis ditolak.
- **Secret Ballot Decoupling**:
  - Memverifikasi secara asersi skema bahwa baris surat suara yang tersimpan di tabel `ballots` **sama sekali tidak memiliki kolom** `user_id`, `resident_id`, atau `receipt_token`.
- **Tanda Terima Anonim**: Pemilih menerima kode bukti penerimaan (*receipt token*) yang tidak berkorelasi dengan kandidat pilihannya.

### 2.4. Pengujian Integritas Audit Trail (`AuditTrailIntegrityTest.php`)
- **Pembuatan Rantai Hash (Hash Chaining)**: Setiap entri audit baru memiliki `previous_hash` yang merujuk pada `current_hash` entri sebelumnya.
- **Deteksi Manipulasi Data (Tamper-Evidence)**:
  - Melakukan simulasi pengubahan deskripsi audit secara ilegal di database tanpa melalui sistem.
  - Memverifikasi bahwa metode `AuditLogService::verifyChainIntegrity()` mendeteksi anomali dengan status `is_valid = false`, mengembalikan kode `DATA_TAMPERED`, dan menunjukkan dengan tepat ID rekaman yang dimanipulasi.
- **Pembuatan Checkpoint Integritas**: Memverifikasi penyimpanan titik validasi pada tabel `audit_checkpoints`.

### 2.5. Pengujian Persetujuan Dua Pihak (`ElectionApprovalTest.php`)
- **Pencegahan Publikasi Sepihak**: Status pemilihan tidak dapat diubah menjadi `published` jika hanya disetujui oleh salah satu pihak (misal: hanya Admin).
- **Pengesahan Sempurna**: Hasil pemilihan hanya dapat dipublikasikan setelah **KEDUA PIHAK** (Admin dan Pengawas) menandatangani secara digital (`is_fully_approved = true`).
- **Penanganan Hasil Seri (Tie Vote)**:
  - Memverifikasi bahwa jika kandidat memperoleh jumlah suara yang sama besar, sistem mendeteksi `is_tie_vote = true`.
  - Sistem mewajibkan pencatatan Berita Acara Musyawarah (`tie_resolution_notes`) sebelum persetujuan akhir dapat diselesaikan.

### 2.6. Pengujian Konkurensi & Race Condition (`ConcurrencyVotingTest.php` & `mysql_concurrency_test.php`)
- Menguji ketahanan mekanisme `lockForUpdate` pada tabel DPT ketika beberapa thread atau proses mencoba mengirimkan suara untuk akun yang sama.
- Membuktikan bahwa hanya 1 suara yang berhasil dicatat, sementara request lainnya dibatalkan tanpa menghasilkan suara ganda.

---

## 3. Cara Menjalankan Pengujian

### 3.1. Menjalankan Seluruh Test Suite
```bash
php artisan test
```
*Output yang diharapkan:*
```
Tests:    26 passed (73 assertions)
Duration: ~3.0s
```

### 3.2. Menjalankan Test Berdasarkan File Tertentu
```bash
# Menjalankan pengujian keamanan pemungutan suara
php artisan test tests/Feature/VotingSecurityTest.php

# Menjalankan pengujian rantai hash audit log
php artisan test tests/Feature/AuditTrailIntegrityTest.php

# Menjalankan pengujian otorisasi & RBAC
php artisan test tests/Feature/AuthorizationTest.php
```

### 3.3. Menjalankan Pengujian Konkurensi Langsung pada MySQL 8.4 Engine
Skrip khusus yang melakukan simulasi serangan langsung pada koneksi MySQL lokal:
```bash
php tests/mysql_concurrency_test.php
```

*Output yang diharapkan:*
```
=== MEMULAI PENGUJIAN RACE CONDITION & KEAMANAN SUARA PADA MYSQL 8.4 ===
Pemilihan: Pemilihan Ketua RW 01 Dusun Harmoni Indah Periode 2026-2031 (ID: 1)
Pemilih  : Fatikhur Rizki (Pemilih 1) (Username: pemilih)
Kandidat : No. 1 H. Ahmad Fauzi, S.E.

1. Menguji Pembuatan Single-Use Token Kriptografis...
✓ Token berhasil dihasilkan: ...

2. Menguji Eksekusi Pemungutan Suara Pertama (Normal Vote)...
✓ Suara berhasil dicatat! Kode Tanda Terima Anonim: RCP-...

3. Menguji Percobaan Suara Kedua Menggunakan Token yang Sama (Double Click / Replay Attack)...
✓ SUKSES DICEGAH: Token voting tidak valid, sudah digunakan, atau telah kedaluwarsa.

4. Menguji Percobaan Membuat Token Baru untuk Pemilih yang Sudah Memilih...
✓ SUKSES DICEGAH: Anda sudah menggunakan hak suara Anda pada pemilihan ini.

5. Menguji Pemeriksaan Integritas Kotak Suara Rahasia...
✓ Status DPT pemilih telah ditandai has_voted = true secara permanen.
✓ Tabel ballots terbukti 100% anonymized tanpa foreign key ke pemilih.

=== SELURUH PENGUJIAN KEAMANAN & CONCURRENCY PADA MYSQL 8.4 BERHASIL 100% ===
```
