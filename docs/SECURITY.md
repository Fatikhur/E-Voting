# Arsitektur Keamanan & Kriptografi E-Voting RT/RW

Dokumen ini menjelaskan secara komprehensif sistem pertahanan keamanan, prinsip kriptografi, dan mitigasi risiko yang diimplementasikan pada aplikasi **E-Voting RT/RW**.

---

## 1. Prinsip Keamanan Inti

Sistem dirancang berdasarkan 5 pilar keamanan pemilu modern:

1. **Integritas (Integrity)**: Suara yang masuk tidak dapat diubah, ditambah, atau dikurangi.
2. **Kerahasiaan (Secrecy)**: Pilihan setiap pemilih bersifat anonim dan tidak dapat dilacak oleh siapapun, termasuk administrator dan pengawas.
3. **Keadilan (One Person, One Vote)**: Setiap pemilih yang berhak hanya dapat menggunakan hak suaranya tepat satu kali.
4. **Dapat Diaudit (Auditability)**: Setiap aksi administratif dan pemungutan suara meninggalkan jejak audit kriptografis yang *tamper-evident*.
5. **Ketersediaan & Ketahanan (Resilience)**: Tahan terhadap serangan konkurensi (race condition), serangan ulap (replay attack), dan brute-force.

---

## 2. Jaminan Satu Pemilih Satu Suara (One Person, One Vote)

Untuk mencegah pemilih memberikan suara lebih dari satu kali melalui teknik seperti:
- Pengiriman request HTTP bersamaan dari banyak tab/perangkat (*concurrent requests*).
- Klik ganda pada tombol konfirmasi (*double click*).
- *Replay attack* token yang sudah pernah terkirim.

Sistem menerapkan **Pertahanan 4 Lapis di Tingkat Backend**:

```
[ Request Masuk ]
       │
       ▼
┌────────────────────────────────────────────────────────┐
│ LAPIS 1: RATE LIMITING DI TINGKAT ROUTE               │
│ - Maksimal 10 request voting per menit per sesi        │
└──────────────────────────┬─────────────────────────────┘
                           ▼
┌────────────────────────────────────────────────────────┐
│ LAPIS 2: VALIDASI SINGLE-USE CRYPTOGRAPHIC NONCE      │
│ - Token nonce acak dengan masa berlaku 15 menit        │
│ - Status token diverifikasi belum pernah digunakan     │
└──────────────────────────┬─────────────────────────────┘
                           ▼
┌────────────────────────────────────────────────────────┐
│ LAPIS 3: PESSIMISTIC LOCKING PADA LEVEL DATABASE       │
│ - Membuka DB::transaction                              │
│ - SELECT * FROM election_eligible_voters FOR UPDATE    │
│ - Mengunci baris data DPT pemilih secara eksklusif     │
└──────────────────────────┬─────────────────────────────┘
                           ▼
┌────────────────────────────────────────────────────────┐
│ LAPIS 4: ATOMIC COMMIT & TANDA TERIMA                  │
│ - Verifikasi has_voted == false                        │
│ - Update has_voted = true & hanguskan token nonce      │
│ - Insert surat suara ke tabel ballots                  │
│ - Commit Transaksi                                     │
└────────────────────────────────────────────────────────┘
```

### Implementasi Pessimistic Lock pada `VotingService.php`
```php
DB::transaction(function () use ($election, $user, $candidateId, $token) {
    // Kunci baris DPT pemilih untuk mencegah race condition
    $voterRecord = ElectionEligibleVoter::where('election_id', $election->id)
        ->where('user_id', $user->id)
        ->lockForUpdate()
        ->first();

    if (!$voterRecord || $voterRecord->has_voted) {
        throw new \RuntimeException('Anda sudah menggunakan hak suara Anda.');
    }

    // Hanguskan token nonce
    $votingToken->update(['is_used' => true, 'used_at' => Carbon::now()]);

    // Tandai DPT
    $voterRecord->update([
        'has_voted' => true,
        'voted_at' => Carbon::now(),
        'receipt_token' => $receiptToken,
    ]);

    // Simpan surat suara secara anonim
    Ballot::create([
        'id' => (string) Str::uuid(),
        'election_id' => $election->id,
        'candidate_id' => $candidateId,
        'cast_date' => Carbon::now()->toDateString(),
        'ballot_hash' => Ballot::generateHash($election->id, $candidateId, Str::random(32)),
        'is_valid' => true,
    ]);
});
```

---

## 3. Kerahasiaan Suara Mutlak (Secret Ballot Architecture)

### Masalah pada Sistem Konvensional
Banyak sistem e-voting pemula menyimpan kolom `user_id` atau `resident_id` di dalam tabel `votes`/`ballots`. Hal ini merupakan celah privasi fatal yang memungkinkan siapa saja yang memiliki akses database mengetahui pilihan politik setiap warga.

### Solusi Arsitektural E-Voting RT/RW
1. **Pemisahan Fisik Skema**:
   - Tabel `election_eligible_voters` mencatat **SIAPA** yang telah memilih (`has_voted = true`, `voted_at`), tetapi **TIDAK MENCATAT KANDIDAT YANG DIPILIH**.
   - Tabel `ballots` mencatat **KANDIDAT** yang dipilih, tetapi **TIDAK MEMILIKI IDENTITAS PEMILIH** (`no user_id`, `no resident_id`, `no ip_address`, `no receipt_token`).
2. **Mitigasi Serangan Korelasi Waktu (Timing Attack Prevention)**:
   - Jika tabel `ballots` mencatat stempel waktu presisi tinggi (misal `14:22:05.123`), penyerang dapat mencocokkan waktu tersebut dengan `voted_at` pada tabel pemilih.
   - **Solusi**: Tabel `ballots` hanya mencatat `cast_date` (tanggal saja, tanpa jam/menit/detik), sedangkan kunci primernya berupa UUID acak v4 yang tidak berurutan (*unordered random UUID*).
3. **Audit Log Aman**:
   - Log pemungutan suara hanya mencatat aksi:  
     *"Pemilih Ahmad (User ID #4) berhasil menggunakan hak suara pada Pemilihan Ketua RW 01"*.
   - Parameter kandidat yang dipilih **secara ketat tidak dimasukkan** ke dalam payload metadata audit log.

---

## 4. Perlindungan Data Sensitif Warga (NIK & No KK)

Data kependudukan warga dilindungi dengan prinsip *Privacy by Design*:

### 4.1. HMAC-SHA256 Deterministic Hashing (`nik_hash`)
Untuk melakukan pengecekan apakah suatu NIK sudah terdaftar atau belum tanpa menyimpan NIK dalam teks biasa, sistem menghasilkan HMAC berbasis secret key aplikasi:
```php
$nikHash = hash_hmac('sha256', trim($nik), config('app.key'));
```
Indeks unik dipasang pada kolom `nik_hash`. Nilai ini searah (*one-way*), sehingga penyerang yang mencuri database tidak dapat mendekripsi hash menjadi NIK asli tanpa *brute force dictionary*.

### 4.2. Enkripsi Dua Arah AES-256-CBC (`nik_encrypted`, `no_kk_encrypted`)
Informasi NIK dan Nomor Kartu Keluarga disimpan terenkripsi menggunakan cipher AES-256-CBC melalui modul `Illuminate\Support\Facades\Crypt`. Hanya aplikasi dengan `APP_KEY` valid yang dapat mendekripsi data tersebut.

### 4.3. Penyamaran Tampilan Layar (`nik_masked`)
Ketika petugas membuka antarmuka data warga, sistem hanya menampilkan NIK tersensor:
Contoh: `3201234567890001` disensor menjadi `3201**********01`.

---

## 5. Audit Trail Kriptografis (Tamper-Evident Hash Chain)

Sistem mengadopsi mekanisme pencatatan berantai seperti *blockchain* ringan pada tabel `audit_logs`:

```
┌─────────────────────┐       ┌─────────────────────┐       ┌─────────────────────┐
│    Audit Log #1     │       │    Audit Log #2     │       │    Audit Log #3     │
├─────────────────────┤       ├─────────────────────┤       ├─────────────────────┤
│ id: 1               │       │ id: 2               │       │ id: 3               │
│ prev_hash: 'GENESIS'│ ┌────▶│ prev_hash: Hash #1  │ ┌────▶│ prev_hash: Hash #2  │
│ payload: Data A     │ │     │ payload: Data B     │ │     │ payload: Data C     │
│ current_hash: 7a8f..│─┘     │ current_hash: b3c1..│─┘     │ current_hash: e4d2..│
└─────────────────────┘       └─────────────────────┘       └─────────────────────┘
```

### Formula Perhitungan Hash:
$$\text{current\_hash} = \text{SHA256}(\text{previous\_hash} \parallel \text{created\_at} \parallel \text{user\_id} \parallel \text{action} \parallel \text{event\_type} \parallel \text{description} \parallel \text{metadata})$$

### Pendeteksian Kerusakan Data (`verifyChainIntegrity`)
Setiap kali dijalankan, fungsi verifikasi memeriksa:
1. Apakah `previous_hash` baris saat ini persis sama dengan `current_hash` baris sebelumnya.
2. Apakah komputasi ulang hash dari payload baris saat ini menghasilkan nilai yang identik dengan `current_hash` yang tersimpan.

Jika ada pihak tidak bertanggung jawab yang mengubah isi database (misal menghapus log atau mengubah stempel waktu), verifikasi akan segera gagal dan mengidentifikasi nomor ID baris yang rusak (`TAMPERED`).

---

## 6. Aturan Persetujuan Dua Pihak (Two-Person Rule)

Untuk mencegah publikasi hasil suara sepihak oleh Administrator, sistem mewajibkan pengesahan digital ganda:

1. **Admin Wilayah**: Memeriksa hasil rekapitulasi, memastikan seluruh warga yang hadir telah tercatat, dan menandatangani persetujuan pertama.
2. **Pengawas Independen**: Melakukan audit independen terhadap jumlah surat suara fisik vs digital, memeriksa log anomali, dan menandatangani persetujuan kedua.
3. Status pemilihan hanya dapat dialihkan dari `closed` menjadi `published` jika kolom `is_fully_approved = true` pada tabel `election_approvals`.
4. Jika terjadi **Hasil Seri (Tie Vote)**, tombol publikasi diblokir sampai Berita Acara Penyelesaian Musyawarah diunggah ke dalam catatan persetujuan.

---

## 7. Pertahanan OWASP Top 10

| Risiko Keamanan | Mekanisme Pertahanan di Aplikasi |
|---|---|
| **SQL Injection** | Menggunakan PDO Parameter Binding dan Laravel Eloquent ORM secara konsisten. Tidak ada raw query concatenations. |
| **Cross-Site Scripting (XSS)** | Auto-escaping pada Blade templating (`{{ $var }}`) dan pembersihan data input HTML. |
| **Cross-Site Request Forgery (CSRF)** | Token CSRF wajib pada seluruh form POST/PUT/DELETE. Dilindungi oleh middleware `VerifyCsrfToken`. |
| **Broken Access Control (IDOR)** | Diperiksa melalui Laravel Gate & Policy (`ElectionPolicy`, `ResidentPolicy`, `CandidatePolicy`) dan middleware role RBAC. |
| **Brute Force Login** | Rate limiter `RateLimiter::for('login')` membatasi percobaan maksimal 5 kali per menit per IP/username. |
| **Clickjacking & MIME-Sniffing** | `SecurityHeadersMiddleware` menambahkan header `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, dan `X-XSS-Protection`. |
| **Session Fixation** | Regenerasi ID sesi (`$request->session()->regenerate()`) dilakukan seketika setelah pengguna berhasil login. |
