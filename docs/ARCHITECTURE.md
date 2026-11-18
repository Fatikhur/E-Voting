# Arsitektur Sistem E-Voting RT/RW

Dokumen ini menjelaskan rancangan arsitektur tingkat tinggi (*high-level architecture*), pola desain, pemisahan lapisan tanggung jawab (*layered architecture*), siklus hidup pemilihan, dan prinsip desain keamanan dari sistem **E-Voting RT/RW**.

---

## 1. Ikhtisar Arsitektur

Aplikasi E-Voting RT/RW mengadopsi pola arsitektur **Clean MVC + Service Layer Pattern** di atas framework Laravel 13 dan PHP 8.4 LTS.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                              │
│  Laravel Blade Views + Tailwind CSS 4 + Alpine.js 3 + Chart.js         │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTP Requests / Form Submissions
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                         ROUTING & MIDDLEWARE                           │
│  - Web Routes (web.php)                                                │
│  - SecurityHeadersMiddleware (HSTS, CSP, X-Frame-Options)             │
│  - ActiveUserMiddleware (Status user verification)                     │
│  - RoleMiddleware (RBAC Access Authorization)                          │
│  - Rate Limiting (Login: 5/min, Voting: 10/min)                        │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Authorized Requests
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                         CONTROLLER LAYER                               │
│  SuperAdmin / Admin / Pengawas / Voter / Report Controllers            │
│  - Validasi Input Form (Form Requests)                                 │
│  - Delegasi Logika Bisnis ke Service Layer                             │
│  - Transformasi Data & Response Rendering                              │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Business Operations
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                          SERVICE LAYER                                 │
│  - VotingService: Transaksi voting atomik & kerahasiaan suara          │
│  - VoteCountingService: Perhitungan & deteksi tie vote                 │
│  - AuditLogService: Hash-chaining SHA-256 tamper-evident trail         │
│  - ElectionService: Mesin status & penguncian jadwal                   │
│  - ElectionApprovalService: Two-person rule (Admin + Pengawas)         │
│  - VoterEligibilityService: Sinkronisasi DPT & evaluasi syarat warga   │
│  - ResidentImportExportService: Normalisasi NIK, HMAC, AES encryption  │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Eloquent ORM & Query Builder
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                        DATA PERSISTENCE LAYER                          │
│  MySQL 8.4 LTS Database Engine (InnoDB Engine with ACID Transactions)  │
│  - Strict Secret Ballot Table (ballots tanpa FK ke pemilih)            │
│  - Hash Chained Audit Log Table (audit_logs)                           │
│  - Pessimistic Row Locking (SELECT ... FOR UPDATE)                     │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Prinsip Pemisahan Tanggung Jawab (Separation of Concerns)

1. **Controllers Tipis (Slim Controllers)**:
   Controller hanya bertanggung jawab menerima HTTP request, memicu validasi, memanggil metode pada *Service Layer*, dan mengembalikan view atau redirect response. Tidak ada logika matematika pemilihan atau transaksi database langsung di Controller.

2. **Service Layer Terpusat**:
   Seluruh aturan bisnis penting (seperti pengecekan apakah pemilih sudah memilih, penghitungan surat suara, penguncian jadwal pemilihan, penandatanganan Berita Acara, dan pencatatan audit log) dikapsulasi dalam kelas Service independen di namespace `App\Services`.

3. **Domain Models (Eloquent)**:
   Model bertanggung jawab terhadap relasi antar entitas, relasi scope (misal: scope aktif, scope open), casting atribut, serta mutator enkripsi data sensitif warga.

4. **Kemandirian Kotak Suara (Decoupled Secret Ballot)**:
   Entitas `Ballot` secara desain arsitektur dipisahkan secara fisik dari relasi pengguna. Tidak ada metode `belongsTo(User::class)` atau `belongsTo(Resident::class)` pada model `Ballot`.

---

## 3. Komponen Layanan Utama (Core Services)

### 3.1. `VotingService`
- Menangani proses pemungutan suara secara atomik di dalam `DB::transaction`.
- Menerapkan penguncian baris pesimis (`lockForUpdate()`) pada baris DPT di tabel `election_eligible_voters`.
- Memvalidasi token nonce sekali pakai kriptografis (`voting_tokens`).
- Mengubah status pemilih menjadi `has_voted = true`.
- Menyimpan suara ke tabel `ballots` secara terisolasi tanpa jejak pengenal.
- Menghasilkan tanda terima anonim (`receipt_token`) untuk pemilih.
- Mencatat Audit Log transaksi pemungutan suara (tanpa kandidat pilihan).

### 3.2. `VoteCountingService`
- Menghitung perolehan suara setiap kandidat pada pemilihan yang telah ditutup (`closed`) atau dipublikasikan (`published`).
- Membandingkan jumlah total suara pada tabel `ballots` dengan jumlah pemilih yang telah menggunakan hak suara di `election_eligible_voters` (*Consistency Check*).
- Mendeteksi anomali inkonsistensi data suara.
- Mendeteksi kondisi hasil imbang (*tie vote*) dan menandai status `is_tie_vote = true` dengan daftar kandidat berimbang.

### 3.3. `AuditLogService`
- Menjaga rantai hash kriptografis yang tidak dapat dimanipulasi (*tamper-evident SHA-256 hash chain*).
- Setiap rekaman log baru mengambil hash rekaman sebelumnya (`previous_hash`) untuk dikomputasikan ke hash saat ini (`current_hash`).
- Menyediakan metode `verifyChainIntegrity()` untuk menguji keaslian seluruh riwayat sistem dan mendeteksi titik manipulasi jika terjadi perubahan data sepihak di database.
- Menyediakan metode `createCheckpoint()` untuk menandatangani kondisi log pada interval waktu tertentu.

### 3.4. `ElectionApprovalService`
- Menerapkan **Two-Person Rule** (Aturan Dua Pihak).
- Mencegah publikasi hasil suara sebelum diverifikasi dan disetujui secara digital oleh Admin Wilayah dan Pengawas Independen.
- Menyimpan catatan persetujuan, catatan pengawasan, dan stempel waktu digital.

### 3.5. `VoterEligibilityService`
- Menentukan kelayakan warga sebagai pemilih berdasarkan:
  - Usia minimal 17 tahun pada tanggal pemilihan, atau sudah menikah.
  - Status kependudukan aktif (`status = 'active'`).
  - Wilayah domisili yang cocok dengan cakupan pemilihan (RT atau RW).
- Menjalankan sinkronisasi otomatis Daftar Pemilih Tetap (DPT) ke tabel `election_eligible_voters`.

### 3.6. `ResidentImportExportService`
- Memproses berkas CSV data kependudukan.
- Normalisasi NIK 16 digit.
- Pembuatan `nik_hash` menggunakan HMAC-SHA256 untuk indeks pencarian unik tanpa menyimpan NIK polos.
- Enkripsi NIK dan Nomor KK menggunakan AES-256-CBC.
- Pembuatan representasi sensor NIK (`nik_masked`) untuk kebutuhan tampilan visual.

---

## 4. Mesin Status Pemilihan (Election State Machine)

Siklus hidup setiap pemilihan dikendalikan oleh status yang ketat:

| Status | Deskripsi | Aksi yang Diizinkan |
|---|---|---|
| `draft` | Pemilihan baru dibuat, konfigurasi masih dapat diubah | Edit jadwal, tambah/kurangi kandidat, tentukan wilayah |
| `scheduled` | Jadwal telah ditetapkan, DPT telah disinkronkan | Kandidat terkunci, persiapan logistik pemilihan |
| `open` | Waktu mulai tiba, pemungutan suara aktif | Pemilih memberikan suara, monitoring partisipasi real-time |
| `closed` | Waktu berakhir, pemungutan suara dihentikan | Perhitungan suara, verifikasi audit, penandatanganan dua pihak |
| `published` | Hasil resmi telah disahkan oleh Admin & Pengawas | Publikasi grafik hasil, unduh Berita Acara PDF |
| `cancelled` | Pemilihan dibatalkan karena keadaan kahar / pelanggaran | Tercatat dalam audit trail dengan alasan pembatalan |

---

## 5. Model Integritas Data & Transaksi

Sistem mengandalkan sifat **ACID (Atomicity, Consistency, Isolation, Durability)** dari mesin penyimpanan MySQL 8.4 InnoDB:

1. **Atomicity**: Jika kegagalan jaringan terjadi tepat setelah pencatatan DPT tetapi sebelum penyimpanan surat suara, seluruh transaksi di-`rollback` secara otomatis tanpa menyisakan data parsial.
2. **Isolation**: Isolasi tingkat tinggi melalui `lockForUpdate` memastikan bahwa dua proses HTTP paralel yang mencoba menggunakan hak suara satu akun yang sama akan dieksekusi secara berurutan (*serialized*), di mana proses kedua akan langsung ditolak karena status pemilih telah berubah menjadi `has_voted = true`.
3. **Consistency**: Foreign key constraints dan unique constraints mencegah duplikasi data DPT dan integritas referensial wilayah.
4. **Durability**: Seluruh transaksi yang telah di-commit tersimpan permanen pada storage MySQL.
