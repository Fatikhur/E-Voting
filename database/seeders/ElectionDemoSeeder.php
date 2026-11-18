<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionApproval;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use App\Models\Village;
use App\Services\VoterEligibilityService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ElectionDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        $supervisor = User::where('username', 'pengawas')->first();
        if (!$admin) {
            return;
        }

        $village = Village::first();
        $rw1 = Rw::where('rw_number', '01')->first();
        $rt1 = Rt::where('rw_id', $rw1->id)->where('rt_number', '01')->first();
        $rt2 = Rt::where('rw_id', $rw1->id)->where('rt_number', '02')->first();

        $voterEligibilityService = app(VoterEligibilityService::class);

        // ==========================================
        // 1. PEMILIHAN UTAMA AKTIF (STATUS: OPEN)
        // Pemilihan Ketua RW 01 Dusun Harmoni Indah
        // ==========================================
        $electionRw = Election::firstOrCreate(
            ['code' => 'PEM-RW01-2026'],
            [
                'title' => 'Pemilihan Ketua RW 01 Dusun Harmoni Indah Periode 2026-2031',
                'type' => 'ketua_rw',
                'village_id' => $village->id,
                'rw_id' => $rw1->id,
                'rt_id' => null,
                'term_period' => '2026 - 2031',
                'start_time' => Carbon::now()->subHours(2),
                'end_time' => Carbon::now()->addHours(10),
                'description' => 'Pemilihan digital Ketua RW 01 untuk menentukan kepemimpinan wilayah Dusun Harmoni Indah 5 tahun ke depan.',
                'status' => 'open',
                'tie_breaker_rule' => 'Musyawarah mufakat panitia pengawas bersama para kandidat dan tokoh masyarakat atau pemungutan suara putaran kedua.',
                'created_by' => $admin->id,
            ]
        );

        // Inisialisasi entri approval
        ElectionApproval::firstOrCreate(['election_id' => $electionRw->id]);

        // Kandidat 1
        $resKandidat1 = Resident::where('name', 'like', '%Ahmad Fauzi%')->first();
        Candidate::firstOrCreate(
            ['election_id' => $electionRw->id, 'candidate_number' => 1],
            [
                'name' => 'H. Ahmad Fauzi, S.E.',
                'resident_id' => $resKandidat1?->id,
                'address' => 'Jl. Harmoni Raya Blok A No. 12',
                'vision' => 'Mewujudkan RW 01 yang Harmonis, Aman, Transparan, dan Berdaya Saing Tinggi Berbasis Gotong Royong Digital.',
                'mission' => "1. Meningkatkan pelayanan administrasi warga secara cepat dan ramah.\n2. Optimalisasi keamanan lingkungan 24 jam dengan sistem CCTV dan pos ronda terintegrasi.\n3. Pemberdayaan UMKM warga melalui bazar bulanan dan pelatihan wirausaha.\n4. Pengelolaan kas RW yang transparan dan dapat diakses warga secara berkala.",
                'bio' => 'Tokoh masyarakat, pengusaha, aktif di kepengurusan DKM dan paguyuban warga selama 10 tahun.',
                'status' => 'active',
            ]
        );

        // Kandidat 2
        $resKandidat2 = Resident::where('name', 'like', '%Budi Santoso%')->first();
        Candidate::firstOrCreate(
            ['election_id' => $electionRw->id, 'candidate_number' => 2],
            [
                'name' => 'Ir. Budi Santoso, M.T.',
                'resident_id' => $resKandidat2?->id,
                'address' => 'Jl. Harmoni Raya Blok B No. 05',
                'vision' => 'Transformasi Lingkungan RW 01 yang Hijau, Nyaman, Modern, dan Ramah Anak Serta Lansia.',
                'mission' => "1. Revitalisasi ruang terbuka hijau (RTH) dan taman bermain anak.\n2. Pengelolaan sampah mandiri dan bank sampah bernilai ekonomis.\n3. Program kesehatan lansia terpadu dan Posyandu aktif mingguan.\n4. Pembangunan drainase modern guna pencegahan genangan air di musim hujan.",
                'bio' => 'Praktisi tata kota dan lingkungan, alumni Teknik Sipil, berpengalaman memimpin proyek fasilitas sosial warga.',
                'status' => 'active',
            ]
        );

        // Kandidat 3
        $resKandidat3 = Resident::where('name', 'like', '%Siti Aminah%')->first();
        Candidate::firstOrCreate(
            ['election_id' => $electionRw->id, 'candidate_number' => 3],
            [
                'name' => 'Siti Aminah, S.Pd.',
                'resident_id' => $resKandidat3?->id,
                'address' => 'Jl. Melati RT 03 Blok C No. 09',
                'vision' => 'RW 01 Maju Bersama: Mengedepankan Pendidikan Warga, Kepedulian Sosial, dan Kerukunan Antar-Generasi.',
                'mission' => "1. Beasiswa pendidikan bagi anak warga kurang mampu dari kas sosial peduli.\n2. Peningkatan kegiatan kepemudaan (Karang Taruna) di bidang olahraga dan seni.\n3. Penguatan peran PKK dalam ketahanan pangan keluarga.\n4. Komunikasi warga dua arah yang terbuka tanpa sekat.",
                'bio' => 'Pendidik, aktivis sosial perempuan dan anak, pengurus aktif PKK RW 01.',
                'status' => 'active',
            ]
        );

        // Sinkronisasi DPT untuk pemilihan aktif ini
        $voterEligibilityService->syncDptForElection($electionRw, $admin);

        // ==========================================
        // 2. PEMILIHAN TERJADWAL (STATUS: SCHEDULED)
        // Pemilihan Ketua RT 01 / RW 01
        // ==========================================
        $electionRt1 = Election::firstOrCreate(
            ['code' => 'PEM-RT01-2026'],
            [
                'title' => 'Pemilihan Ketua RT 01 / RW 01 Periode 2026-2031',
                'type' => 'ketua_rt',
                'village_id' => $village->id,
                'rw_id' => $rw1->id,
                'rt_id' => $rt1->id,
                'term_period' => '2026 - 2031',
                'start_time' => Carbon::now()->addDays(2),
                'end_time' => Carbon::now()->addDays(2)->addHours(8),
                'description' => 'Pemilihan Ketua RT 01 untuk masa bakti 2026-2031.',
                'status' => 'scheduled',
                'tie_breaker_rule' => 'Musyawarah mufakat.',
                'created_by' => $admin->id,
            ]
        );
        ElectionApproval::firstOrCreate(['election_id' => $electionRt1->id]);

        Candidate::firstOrCreate(
            ['election_id' => $electionRt1->id, 'candidate_number' => 1],
            [
                'name' => 'Fatikhur Rizki',
                'address' => 'Jl. Harmoni 1 No. 10',
                'vision' => 'RT 01 Guyub Rukun, Bersih, dan Kompak.',
                'mission' => "1. Kerja bakti rutin sebulan sekali.\n2. Pelayanan surat pengantar RT cepat via chat.",
                'status' => 'active',
            ]
        );

        Candidate::firstOrCreate(
            ['election_id' => $electionRt1->id, 'candidate_number' => 2],
            [
                'name' => 'Dewi Lestari',
                'address' => 'Jl. Harmoni 1 No. 15',
                'vision' => 'RT 01 Aman, Nyaman, dan Ramah Lingkungan.',
                'mission' => "1. Penataan taman lorong RT.\n2. Fasilitasi pengajian dan arisan rukun warga.",
                'status' => 'active',
            ]
        );

        $voterEligibilityService->syncDptForElection($electionRt1, $admin);

        // ==========================================
        // 3. PEMILIHAN SELESAI (STATUS: PUBLISHED)
        // Pemilihan Ketua RT 02 / RW 01 yang telah terbit Berita Acaranya
        // ==========================================
        $electionRt2 = Election::firstOrCreate(
            ['code' => 'PEM-RT02-2026'],
            [
                'title' => 'Pemilihan Ketua RT 02 / RW 01 Periode 2026-2031',
                'type' => 'ketua_rt',
                'village_id' => $village->id,
                'rw_id' => $rw1->id,
                'rt_id' => $rt2->id,
                'term_period' => '2026 - 2031',
                'start_time' => Carbon::now()->subDays(2),
                'end_time' => Carbon::now()->subDays(1),
                'description' => 'Pemilihan Ketua RT 02 yang telah selesai dan hasil resmi telah disahkan.',
                'status' => 'published',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(1),
                'published_by' => $admin->id,
                'tie_breaker_rule' => 'Musyawarah mufakat.',
                'created_by' => $admin->id,
            ]
        );

        $approval = ElectionApproval::firstOrCreate(['election_id' => $electionRt2->id]);
        $approval->update([
            'admin_user_id' => $admin->id,
            'admin_approved' => true,
            'admin_approved_at' => Carbon::now()->subDays(1),
            'admin_notes' => 'Hasil penghitungan suara sah dan telah diverifikasi oleh Panitia Pemilihan.',
            'supervisor_user_id' => $supervisor?->id,
            'supervisor_approved' => true,
            'supervisor_approved_at' => Carbon::now()->subDays(1),
            'supervisor_notes' => 'Proses pemungutan suara berlangsung jujur, adil, tertib, dan tanpa pelanggaran.',
            'status' => 'approved',
        ]);

        Candidate::firstOrCreate(
            ['election_id' => $electionRt2->id, 'candidate_number' => 1],
            [
                'name' => 'Eko Prasetyo',
                'address' => 'Jl. Harmoni 2 No. 08',
                'vision' => 'RT 02 Unggul dan Berkarakter.',
                'mission' => "1. Pelayanan warga 24 jam.\n2. Peningkatan kerukunan tetangga.",
                'status' => 'active',
            ]
        );

        Candidate::firstOrCreate(
            ['election_id' => $electionRt2->id, 'candidate_number' => 2],
            [
                'name' => 'Rina Kartika',
                'address' => 'Jl. Harmoni 2 No. 21',
                'vision' => 'RT 02 Sehat, Bersih, dan Berdaya.',
                'mission' => "1. Program Jumat Bersih.\n2. Peningkatan pos ronda malam.",
                'status' => 'active',
            ]
        );
    }
}
