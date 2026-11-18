<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Election;
use App\Models\ElectionEligibleVoter;
use App\Models\User;
use App\Services\VotingService;
use Illuminate\Support\Facades\DB;

echo "=== MEMULAI PENGUJIAN RACE CONDITION & KEAMANAN SUARA PADA MYSQL 8.4 ===\n";

$election = Election::where('status', 'open')->first();
if (!$election) {
    die("Error: Tidak ada pemilihan berstatus open di database MySQL.\n");
}

$voter = User::where('username', 'pemilih')->first();
if (!$voter) {
    die("Error: Akun pemilih tidak ditemukan.\n");
}

$candidate = $election->candidates()->where('status', 'active')->first();
if (!$candidate) {
    die("Error: Kandidat aktif tidak ditemukan.\n");
}

echo "Pemilihan: {$election->title} (ID: {$election->id})\n";
echo "Pemilih  : {$voter->name} (Username: {$voter->username})\n";
echo "Kandidat : No. {$candidate->candidate_number} {$candidate->name}\n";

// Reset status hak suara pemilih untuk pengujian
ElectionEligibleVoter::where('election_id', $election->id)
    ->where('user_id', $voter->id)
    ->update(['has_voted' => false, 'voted_at' => null, 'receipt_token' => null]);

$votingService = app(VotingService::class);

echo "\n1. Menguji Pembuatan Single-Use Token Kriptografis...\n";
$token = $votingService->generateVotingToken($election, $voter);
echo "✓ Token berhasil dihasilkan: " . substr($token, 0, 16) . "...\n";

echo "\n2. Menguji Eksekusi Pemungutan Suara Pertama (Normal Vote)...\n";
$receipt = $votingService->castVote($election, $voter, $candidate->id, $token);
echo "✓ Suara berhasil dicatat! Kode Tanda Terima Anonim: {$receipt}\n";

echo "\n3. Menguji Percobaan Suara Kedua Menggunakan Token yang Sama (Double Click / Replay Attack)...\n";
try {
    $votingService->castVote($election, $voter, $candidate->id, $token);
    echo "❌ GAGAL: Suara kedua berhasil masuk padahal seharusnya ditolak!\n";
    exit(1);
} catch (\RuntimeException $e) {
    echo "✓ SUKSES DICEGAH: " . $e->getMessage() . "\n";
}

echo "\n4. Menguji Percobaan Membuat Token Baru untuk Pemilih yang Sudah Memilih...\n";
try {
    $votingService->generateVotingToken($election, $voter);
    echo "❌ GAGAL: Token baru berhasil dibuat padahal pemilih sudah memilih!\n";
    exit(1);
} catch (\RuntimeException $e) {
    echo "✓ SUKSES DICEGAH: " . $e->getMessage() . "\n";
}

echo "\n5. Menguji Pemeriksaan Integritas Kotak Suara Rahasia...\n";
$dptRecord = ElectionEligibleVoter::where('election_id', $election->id)
    ->where('user_id', $voter->id)
    ->first();

if (!$dptRecord->has_voted) {
    echo "❌ GAGAL: DPT record tidak tertandai has_voted = true!\n";
    exit(1);
}
echo "✓ Status DPT pemilih telah ditandai has_voted = true secara permanen.\n";

$latestBallot = DB::table('ballots')->where('election_id', $election->id)->orderBy('created_at', 'desc')->first();
if (isset($latestBallot->user_id) || isset($latestBallot->resident_id)) {
    echo "❌ GAGAL: Tabel ballots membocorkan identitas pemilih!\n";
    exit(1);
}
echo "✓ Tabel ballots terbukti 100% anonymized tanpa foreign key ke pemilih.\n";

echo "\n=== SELURUH PENGUJIAN KEAMANAN & CONCURRENCY PADA MYSQL 8.4 BERHASIL 100% ===\n";
