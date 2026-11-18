# Diagram Alur & Proses Pemungutan Suara (Voting Flow)

Dokumen ini menjelaskan alur operasional pemungutan suara digital dari sudut pandang pemilih (*voter experience*), arsitektur komunikasi sistem, dan diagram urutan teknis (*sequence diagram*).

---

## 1. Diagram Urutan Pemungutan Suara (Sequence Diagram)

```mermaid
sequenceDiagram
    autonumber
    actor Voter as Pemilih (Warga)
    participant UI as Antarmuka Web (Blade/Alpine.js)
    participant Ctrl as VotingProcessController
    participant Svc as VotingService
    participant DB as Basis Data (MySQL 8.4)
    participant Audit as AuditLogService

    Voter->>UI: 1. Login menggunakan NIK / Username & Password
    UI->>Ctrl: Autentikasi Pengguna
    Ctrl->>DB: Validasi Kredensial & Role Voter
    DB-->>Ctrl: Pengguna Valid & Aktif
    Ctrl-->>UI: Redirect ke Dashboard Pemilih

    Voter->>UI: 2. Buka Menu Pemilihan Berlangsung
    UI->>Ctrl: GET /voter/elections/{id}/candidates
    Ctrl->>Svc: Periksa Kelayakan Pemilih & Status Hak Suara
    Svc->>DB: SELECT has_voted FROM election_eligible_voters
    DB-->>Svc: has_voted = false
    Ctrl-->>UI: Tampilkan Daftar Kartu Kandidat (Foto, Visi, Misi)

    Voter->>UI: 3. Klik Tombol "Pilih Calon Ini"
    UI->>Ctrl: GET /voter/elections/{id}/confirm/{candidate_id}
    Ctrl->>Svc: Buat Single-Use Cryptographic Nonce Token
    Svc->>DB: INSERT INTO voting_tokens (hash, expires_at: 15m)
    DB-->>Svc: Token Terbentuk
    Ctrl-->>UI: Tampilkan Layar Konfirmasi Besar & Jelas (Modal / Layar Penuh)

    Voter->>UI: 4. Klik Tombol "Ya, Saya Yakin & Kirim Suara"
    UI->>Ctrl: POST /voter/elections/{id}/cast (candidate_id, nonce_token, CSRF)
    
    rect rgb(240, 248, 255)
        Note over Ctrl,DB: Transaksi Basis Data Atomik (ACID)
        Ctrl->>Svc: castVote(election, voter, candidate_id, nonce_token)
        Svc->>DB: START TRANSACTION
        Svc->>DB: SELECT * FROM election_eligible_voters FOR UPDATE
        Svc->>DB: Validasi Nonce Token (is_used = false & belum expired)
        Svc->>DB: UPDATE election_eligible_voters SET has_voted = true, voted_at = NOW(), receipt_token = 'RCP-...'
        Svc->>DB: UPDATE voting_tokens SET is_used = true
        Svc->>DB: INSERT INTO ballots (id, election_id, candidate_id, cast_date, ballot_hash) [ANONIM]
        Svc->>Audit: Catat Log Transaksi (Tanpa Pilihan Kandidat)
        Audit->>DB: INSERT INTO audit_logs (hash_chained)
        Svc->>DB: COMMIT TRANSACTION
    end

    Svc-->>Ctrl: Berhasil (Kode Tanda Terima: RCP-XXXX-...)
    Ctrl-->>UI: Redirect ke Halaman Tanda Terima Anonim
    UI-->>Voter: 5. Tampilkan Kode Bukti Pemilihan & Status "Hak Suara Telah Digunakan"
```

---

## 2. Rincian 3 Langkah Sederhana untuk Warga (Senior-Friendly UX)

Sistem dirancang dengan filosofi kemudahan akses untuk semua kalangan, khususnya warga lanjut usia yang belum terbiasa dengan aplikasi digital rumit:

### Langkah 1: Membaca & Memilih Calon
- Setiap calon ketua RT/RW disajikan dalam bentuk **Kartu Besar** yang memuat:
  - Nomor urut berukuran ekstra besar di sudut atas.
  - Pas foto calon dengan resolusi jelas.
  - Nama lengkap dan gelar.
  - Ringkasan visi dan misi dalam teks terbaca jelas.
- Tombol aksi berwarna kontras tinggi: **"PILIH KANDIDAT INI"**.

### Langkah 2: Konfirmasi Pilihan Bertingkat
- Setelah tombol pilihan diklik, sistem tidak langsung mengirim suara, melainkan memunculkan layar konfirmasi dengan latar belakang redup (*focus mode*).
- Menampilkan foto dan nama calon yang dipilih dengan pertanyaan:  
  *"Apakah Anda yakin ingin memberikan suara Anda kepada [Nama Calon]?"*
- Terdapat dua tombol besar:
  - Hijau: **"YA, SAYA YAKIN DAN KIRIM SUARA"**
  - Abu-abu netral: **"KEMBALI DAN GANTI PILIHAN"**
- Hal ini mencegah kesalahan klik yang tidak disengaja.

### Langkah 3: Bukti Tanda Terima Anonim (Receipt Token)
- Setelah suara berhasil disimpan secara permanen di database, sistem menampilkan layar sukses:
  - Ikon centang hijau besar: **"Suara Anda Berhasil Dicatat!"**
  - Kode tanda terima pemilihan, contoh: `RCP-LEVCM6-30b7bad52988a7db680cb518cd...`
  - Tombol **"Cetak / Simpan Bukti Suara"** atau tombol **"Selesai & Keluar"**.
- Warga mendapatkan kepastian batin bahwa hak suaranya telah masuk, tanpa sedikit pun membocorkan calon yang dipilihnya ke publik atau panitia.

---

## 3. Penanganan Kondisi Pengecualian & Skenario Gagal

| Skenario | Tindakan Sistem | Pesan ke Pengguna |
|---|---|---|
| **Pemilih mencoba memilih kembali setelah selesai** | Sistem memeriksa `has_voted == true` pada layer Controller dan Service | Dialihkan ke dashboard dengan notifikasi: *"Anda sudah menggunakan hak suara Anda pada pemilihan ini."* |
| **Token kedaluwarsa (> 15 menit)** | Validasi token mendeteksi `expires_at < Carbon::now()` | *"Sesi pemilihan telah kedaluwarsa demi keamanan. Silakan pilih kembali kandidat Anda."* |
| **Pemilihan belum dibuka / telah ditutup** | Middleware dan Service memeriksa status `election.status != 'open'` atau waktu di luar jadwal | *"Pemungutan suara untuk pemilihan ini belum dibuka atau telah berakhir."* |
| **Dua perangkat mencoba vote bersamaan (Race Condition)** | Transaksi kedua terantre di `FOR UPDATE`, lalu memeriksa bahwa `has_voted` sudah berubah menjadi `true` | Request kedua langsung di-*abort* dan di-*rollback* dengan pesan: *"Hak suara Anda telah digunakan sebelumnya."* |
| **Koneksi jaringan terputus saat transaksi berjalan** | Transaksi InnoDB otomatis di-*rollback* penuh | Tidak ada suara hantu yang masuk; pemilih dapat mencoba ulang saat jaringan pulih. |
