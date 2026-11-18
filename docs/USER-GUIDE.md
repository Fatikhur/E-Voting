# Buku Panduan Pengguna (User Guide) E-Voting RT/RW

Buku panduan ini disusun sebagai acuan operasional bagi seluruh pengguna aplikasi **E-Voting RT/RW**, mencakup peran **Super Admin**, **Admin Wilayah**, **Pengawas Independen**, serta **Warga / Pemilih** (termasuk panduan khusus lansia).

---

## 1. Panduan Masuk ke Sistem (Login)

1. Buka peramban (*web browser*) seperti Google Chrome, Mozilla Firefox, atau Microsoft Edge.
2. Masukkan alamat aplikasi: `http://localhost:8000` (atau domain resmi yang ditentukan).
3. Anda akan diarahkan ke halaman login yang bersih dan mudah digunakan.
4. Masukkan **Username / NIK** dan **Kata Sandi**.
5. Klik tombol biru besar: **"Masuk ke Akun"**.
6. Sistem akan mendeteksi peran akun Anda secara otomatis dan mengarahkan ke dashboard yang sesuai.

---

## 2. Panduan Super Admin

Super Admin memegang tanggung jawab tertinggi atas stabilitas, konfigurasi, dan keamanan infrastruktur sistem.

### 2.1. Dashboard Super Admin
- Menampilkan metrik kesehatan sistem: Total Pengguna, Jumlah Wilayah, Status Integritas Rantai Hash Audit Trail, dan Rekapitulasi Pemilihan.
- Tombol aksi cepat: **"Verifikasi Integritas Audit Trail"**.

### 2.2. Manajemen Akun Pengguna
- **Melihat Daftar Pengguna**: Menu *Manajemen Pengguna* menampilkan seluruh akun yang terdaftar beserta status keaktifan dan perannya.
- **Menambah Pengguna Baru**:
  1. Klik tombol **"Tambah Pengguna"**.
  2. Isi Nama Lengkap, Username, Email, Kata Sandi, dan pilih Peran (*Super Admin*, *Admin Wilayah*, *Pengawas*, atau *Pemilih*).
  3. Klik Simpan.
- **Mengaktifkan / Menonaktifkan Pengguna**: Gunakan tombol *Toggle Status* untuk menonaktifkan akun yang dicurigai tanpa menghapus histori auditnya.

### 2.3. Manajemen Wilayah Administrasi
- Mengelola data hirarkis: **Kelurahan / Desa $\to$ Rukun Warga (RW) $\to$ Rukun Tetangga (RT)**.
- Setiap wilayah memiliki kode unik dan daftar penomoran resmi yang menjadi dasar penetapan cakupan DPT pemilihan.

### 2.4. Audit Trail Kriptografis & Verifikasi Integritas
- Buka menu **"Audit Trail"**.
- Anda dapat menelusuri seluruh riwayat aktivitas: waktu kejadian, pelaku, tipe event, deskripsi, alamat IP, dan hash SHA-256.
- Klik tombol **"Verifikasi Integritas Rantai Hash"**:
  - Jika seluruh data asli: Muncul indikator hijau **"Integritas Rantai Hash Sempurna (100% Valid)"**.
  - Jika ada data database yang diubah langsung lewat SQL/konsol luar: Muncul peringatan merah yang mengidentifikasi baris ID rekaman yang mengalami perubahan tidak sah.
- Anda dapat menekan tombol **"Buat Checkpoint Integritas"** untuk mengunci kondisi audit pada titik waktu tertentu.

---

## 3. Panduan Admin Wilayah (Panitia Pemilihan)

Admin Wilayah bertindak sebagai panitia pelaksana pemilihan tingkat RT atau RW.

### 3.1. Pengelolaan Data Warga & DPT
- **Menambah Data Warga Secara Manual**:
  - Masukkan NIK (16 digit), Nama Lengkap, Alamat, Jenis Kelamin, Tanggal Lahir, RT, RW, dan Status Perkawinan.
  - Sistem secara otomatis menghitung kelayakan pemilih ($\ge 17$ tahun atau sudah menikah).
- **Import Data Warga Massal (CSV/Excel)**:
  - Unduh template CSV yang disediakan.
  - Isi data warga sesuai format kolom: `nik,name,rt_number,rw_number,gender,birth_date,marital_status,address`.
  - Unggah berkas CSV dan klik **"Proses Import"**.
  - Sistem akan menyaring duplikasi NIK dan mengidentifikasi baris data yang belum lengkap.

### 3.2. Membuat & Menjadwalkan Pemilihan
1. Buka menu **"Kelola Pemilihan"** dan klik **"Buat Pemilihan Baru"**.
2. Pilih Tingkat Pemilihan: **Pemilihan Ketua RW** atau **Pemilihan Ketua RT**.
3. Pilih Wilayah Cakupan (misal: RW 01 atau RT 01).
4. Tentukan Masa Jabatan (contoh: 2026 - 2031).
5. Tentukan Jadwal Pemungutan Suara: **Tanggal & Jam Mulai** serta **Tanggal & Jam Berakhir**.
6. Masukkan Deskripsi Pemilihan dan Aturan Tambahan.
7. Simpan sebagai status **Draft** atau langsung jadwalkan sebagai **Scheduled**.

### 3.3. Mengelola Kandidat Calon Ketua
1. Masuk ke halaman detail pemilihan yang berstatus *Draft* atau *Scheduled*.
2. Klik tombol **"Tambah Kandidat"**.
3. Masukkan Nomor Urut Calon (1, 2, 3, dst.).
4. Masukkan Nama Lengkap, Gelar, Riwayat Singkat.
5. Tuliskan **Visi** dan **Misi** secara jelas.
6. Unggah pas foto resmi kandidat (format PNG/JPG, rasio portrait rapi).
7. Simpan kandidat.

> [!WARNING]
> Setelah waktu pemilihan dimulai dan status berubah menjadi **Open**, data profil kandidat dan nomor urut akan **terkunci permanen** dan tidak dapat diubah lagi untuk menjamin keadilan pemilu.

### 3.4. Pengesahan Hasil (Two-Person Approval oleh Admin)
1. Setelah pemilihan berakhir (status *Closed*), buka halaman rekapitulasi pemilihan.
2. Periksa kesesuaian antara total DPT yang hadir dengan total surat suara di kotak suara.
3. Berikan catatan pelaksanaan pada kolom formulir persetujuan.
4. Klik tombol **"Setujui & Tanda Tangan Hasil Pemilihan (Admin)"**.

---

## 4. Panduan Pengawas Independen

Pengawas bertugas memantau netralitas proses, mencatat anomali, dan mengesahkan hasil pemungutan suara bersama Admin.

### 4.1. Dashboard Pengawasan Real-Time
- Menampilkan grafik persentase partisipasi pemilih (*turnout rate*) yang bergerak secara dinamis.
- Memantau jumlah pemilih terdaftar, jumlah yang sudah memilih, dan sisa waktu pemungutan suara.

### 4.2. Pengawasan Log Aktivitas
- Memeriksa setiap tindakan administratif yang dilakukan oleh Admin (pembuatan pemilihan, perubahan jadwal, dll.).
- Memastikan tidak ada upaya manipulasi data atau akses tidak wajar.

### 4.3. Mengirimkan Laporan Pelanggaran / Catatan Pengawasan
1. Buka menu **"Laporan Pengawasan"**.
2. Klik **"Buat Laporan Baru"**.
3. Pilih pemilihan terkait, pilih tingkat keparahan (*Rendah*, *Sedang*, *Tinggi / Kritis*).
4. Tuliskan deskripsi temuan, bukti lapangan, atau rekomendasi tindakan.
5. Simpan laporan. Laporan ini akan tercatat permanen di audit trail dan dapat diakses oleh Super Admin.

### 4.4. Pengesahan Hasil (Two-Person Approval oleh Pengawas)
1. Setelah pemilihan ditutup (*Closed*), Pengawas memeriksa data rekapitulasi.
2. Pastikan tidak ada selisih suara tidak wajar (*vote tampering*).
3. Masukkan catatan pengawasan pada panel persetujuan.
4. Klik tombol **"Setujui & Tanda Tangan Hasil Pemilihan (Pengawas)"**.
5. Setelah kedua pihak (Admin & Pengawas) menyetujui, tombol **"Publikasikan Hasil Resmi ke Warga"** akan aktif.

---

## 5. Panduan Pemilih (Warga) - Termasuk Warga Lanjut Usia (Lansia)

Aplikasi dirancang agar dapat digunakan dengan sangat mudah di smartphone maupun komputer oleh warga segala usia.

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│    LANGKAH 1    │       │    LANGKAH 2    │       │    LANGKAH 3    │
│  PILIH KANDIDAT │ ────▶ │ KONFIRMASI LAGI │ ────▶ │ SIMPAN BUKTI    │
│  (Klik Tombol)  │       │ (Periksa Foto)  │       │ (Tanda Terima)  │
└─────────────────┘       └─────────────────┘       └─────────────────┘
```

### Langkah demi Langkah:
1. **Masuk ke Aplikasi**:  
   Buka link pemilihan. Masukkan NIK dan kata sandi yang telah dibagikan oleh panitia RT/RW.
2. **Buka Menu Pemilihan**:  
   Di layar utama, akan terlihat kotak besar berwarna hijau bertuliskan **"Pemilihan Sedang Berlangsung"**. Tekan tombol **"Gunakan Hak Suara Sekarang"**.
3. **Melihat Foto & Profil Calon**:  
   Layar akan menampilkan kotak calon-calon ketua:
   - Ada Nomor Urut besar di atas foto.
   - Ada Foto Wajah Calon berukuran besar.
   - Ada Nama Calon dan bacaan Visi-Misi singkat.
4. **Memilih Calon**:  
   Tekan tombol biru besar bertuliskan **"PILIH CALON NO. [X]"** di bawah foto calon yang Anda inginkan.
5. **Konfirmasi Pilihan (Layar Kepastian)**:  
   Layar akan menampilkan foto calon yang Anda pilih dan menanyakan:  
   *"Apakah Anda yakin memilih Calon ini?"*
   - Jika **YAKIN**, tekan tombol hijau bertuliskan **"YA, SAYA YAKIN DAN KIRIM SUARA"**.
   - Jika **SALAH KLIK**, tekan tombol abu-abu bertuliskan **"KEMBALI KE DAFTAR CALON"**.
6. **Menerima Tanda Terima**:  
   Muncul tanda centang hijau besar bertuliskan **"Suara Anda Berhasil Disimpan"**.
   Terdapat nomor bukti tanda terima. Anda dapat menyimpan atau memotret layar tersebut sebagai bukti bahwa Anda telah berpartisipasi.
7. **Selesai**:  
   Tekan tombol **"Keluar"**. Hak suara Anda telah tersimpan dengan aman dan rahasia!
