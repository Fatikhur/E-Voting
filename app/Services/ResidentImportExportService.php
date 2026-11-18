<?php

namespace App\Services;

use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ResidentImportExportService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Import data warga dari file CSV.
     * Header CSV yang didukung:
     * NIK, Nama, No_KK, Tempat_Lahir, Tanggal_Lahir (YYYY-MM-DD), Jenis_Kelamin (L/P), Alamat, Kode_Kelurahan, No_RW, No_RT, No_Telp, Status_Warga, Layak_Pilih (1/0)
     */
    public function importCsv(UploadedFile $file, User $actor): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            throw new \RuntimeException('Gagal membuka file CSV yang diunggah.');
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header) {
            fclose($handle);
            throw new \RuntimeException('File CSV kosong atau tidak valid.');
        }

        // Normalisasi nama kolom header
        $normalizedHeaders = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', $h))));
        }, $header);

        $insertedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $rowNumber = 1;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 2000, ',')) !== false) {
                $rowNumber++;
                if (count($row) < 3 || empty(array_filter($row))) {
                    continue; // Skip baris kosong
                }

                $data = array_combine(
                    array_slice($normalizedHeaders, 0, count($row)),
                    array_slice($row, 0, count($normalizedHeaders))
                );

                $rawNik = $data['nik'] ?? '';
                $cleanNik = preg_replace('/\D/', '', trim($rawNik));

                if (strlen($cleanNik) !== 16) {
                    $errors[] = "Baris #{$rowNumber}: NIK '{$rawNik}' tidak valid (harus 16 digit angka).";
                    $skippedCount++;
                    continue;
                }

                $name = trim($data['nama'] ?? $data['name'] ?? '');
                if (empty($name)) {
                    $errors[] = "Baris #{$rowNumber}: Nama warga tidak boleh kosong.";
                    $skippedCount++;
                    continue;
                }

                // Resolusi wilayah
                $villageCode = trim($data['kode_kelurahan'] ?? $data['village_code'] ?? '');
                $village = $villageCode ? Village::where('code', $villageCode)->first() : Village::first();
                if (!$village) {
                    $errors[] = "Baris #{$rowNumber}: Data kelurahan tidak ditemukan.";
                    $skippedCount++;
                    continue;
                }

                $rwNumber = str_pad(preg_replace('/\D/', '', trim($data['no_rw'] ?? $data['rw'] ?? '1')), 2, '0', STR_PAD_LEFT);
                $rw = Rw::firstOrCreate(
                    ['village_id' => $village->id, 'rw_number' => $rwNumber],
                    ['name' => "RW {$rwNumber}"]
                );

                $rtNumber = str_pad(preg_replace('/\D/', '', trim($data['no_rt'] ?? $data['rt'] ?? '1')), 2, '0', STR_PAD_LEFT);
                $rt = Rt::firstOrCreate(
                    ['rw_id' => $rw->id, 'rt_number' => $rtNumber],
                    ['name' => "RT {$rtNumber}"]
                );

                $nikHash = Resident::hashNik($cleanNik);
                $nikEncrypted = Crypt::encryptString($cleanNik);
                $nikMasked = Resident::maskNik($cleanNik);

                $noKk = trim($data['no_kk'] ?? $data['kk'] ?? '');
                $noKkEncrypted = !empty($noKk) ? Crypt::encryptString($noKk) : null;

                $birthDate = null;
                $rawBirthDate = trim($data['tanggal_lahir'] ?? $data['birth_date'] ?? '');
                if (!empty($rawBirthDate)) {
                    try {
                        $birthDate = Carbon::parse($rawBirthDate)->toDateString();
                    } catch (\Exception $e) {
                        $birthDate = null;
                    }
                }

                $gender = strtoupper(trim($data['jenis_kelamin'] ?? $data['gender'] ?? 'L'));
                if (!in_array($gender, ['L', 'P'])) {
                    $gender = 'L';
                }

                $status = strtolower(trim($data['status_warga'] ?? $data['status'] ?? 'active'));
                if (!in_array($status, ['active', 'moved', 'deceased'])) {
                    $status = 'active';
                }

                $isEligible = isset($data['layak_pilih'])
                    ? filter_var($data['layak_pilih'], FILTER_VALIDATE_BOOLEAN)
                    : true;

                $resident = Resident::where('nik_hash', $nikHash)->first();

                if ($resident) {
                    $resident->update([
                        'name' => $name,
                        'no_kk_encrypted' => $noKkEncrypted ?? $resident->no_kk_encrypted,
                        'birth_place' => trim($data['tempat_lahir'] ?? $data['birth_place'] ?? $resident->birth_place),
                        'birth_date' => $birthDate ?? $resident->birth_date,
                        'gender' => $gender,
                        'address' => trim($data['alamat'] ?? $data['address'] ?? $resident->address),
                        'village_id' => $village->id,
                        'rw_id' => $rw->id,
                        'rt_id' => $rt->id,
                        'phone' => trim($data['no_telp'] ?? $data['phone'] ?? $resident->phone),
                        'status' => $status,
                        'is_eligible_voter' => $isEligible,
                    ]);
                    $updatedCount++;
                } else {
                    Resident::create([
                        'nik_hash' => $nikHash,
                        'nik_encrypted' => $nikEncrypted,
                        'nik_masked' => $nikMasked,
                        'name' => $name,
                        'no_kk_encrypted' => $noKkEncrypted,
                        'birth_place' => trim($data['tempat_lahir'] ?? $data['birth_place'] ?? null),
                        'birth_date' => $birthDate,
                        'gender' => $gender,
                        'address' => trim($data['alamat'] ?? $data['address'] ?? "RT {$rtNumber} / RW {$rwNumber}"),
                        'village_id' => $village->id,
                        'rw_id' => $rw->id,
                        'rt_id' => $rt->id,
                        'phone' => trim($data['no_telp'] ?? $data['phone'] ?? null),
                        'status' => $status,
                        'is_eligible_voter' => $isEligible,
                    ]);
                    $insertedCount++;
                }
            }

            fclose($handle);
            DB::commit();

            $this->auditLogService->log(
                action: 'import_residents_csv',
                eventType: 'resident',
                description: "Import data warga via CSV: {$insertedCount} baru, {$updatedCount} diperbarui, {$skippedCount} dilewati.",
                metadata: [
                    'inserted' => $insertedCount,
                    'updated' => $updatedCount,
                    'skipped' => $skippedCount,
                    'errors_count' => count($errors),
                ],
                user: $actor
            );

            return [
                'success' => true,
                'inserted' => $insertedCount,
                'updated' => $updatedCount,
                'skipped' => $skippedCount,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            fclose($handle);
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Ekspor data warga terdaftar ke format CSV.
     */
    public function exportCsv(?int $rwId = null, ?int $rtId = null): string
    {
        $query = Resident::with(['village', 'rw', 'rt'])->orderBy('name', 'asc');

        if ($rwId) {
            $query->where('rw_id', $rwId);
        }
        if ($rtId) {
            $query->where('rt_id', $rtId);
        }

        $residents = $query->get();

        $output = fopen('php://temp', 'r+');

        // Header CSV
        fputcsv($output, [
            'NIK_MASKED',
            'NAMA_LENGKAP',
            'JENIS_KELAMIN',
            'TEMPAT_LAHIR',
            'TANGGAL_LAHIR',
            'USIA',
            'ALAMAT',
            'KELURAHAN',
            'RW',
            'RT',
            'NO_TELP',
            'STATUS_WARGA',
            'STATUS_PEMILIH',
        ]);

        foreach ($residents as $resident) {
            fputcsv($output, [
                $resident->nik_masked,
                $resident->name,
                $resident->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                $resident->birth_place ?? '-',
                $resident->birth_date ? $resident->birth_date->format('Y-m-d') : '-',
                $resident->age ?? '-',
                $resident->address,
                $resident->village ? $resident->village->name : '-',
                $resident->rw ? $resident->rw->rw_number : '-',
                $resident->rt ? $resident->rt->rt_number : '-',
                $resident->phone ?? '-',
                ucfirst($resident->status),
                $resident->is_eligible_voter ? 'Memenuhi Syarat' : 'Tidak Memenuhi Syarat',
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }
}
