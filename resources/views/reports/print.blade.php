<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - {{ $election->title }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1a1a1a;
            line-height: 1.5;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .header h1 {
            font-size: 14pt;
            text-transform: uppercase;
            margin: 0;
            font-weight: bold;
        }
        .header h2 {
            font-size: 13pt;
            text-transform: uppercase;
            margin: 4px 0;
        }
        .header p {
            font-size: 10pt;
            color: #444;
            margin: 0;
        }
        .title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th {
            background-color: #f2f2f2;
            padding: 8px;
            font-size: 10pt;
            text-align: center;
        }
        td {
            padding: 8px;
            font-size: 10pt;
        }
        .signatures {
            margin-top: 40px;
            width: 100%;
            border: none;
        }
        .signatures td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .signature-space {
            height: 70px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="background: #2563eb; color: white; padding: 12px; text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: white; color: #2563eb; font-weight: bold; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer;">
            🖨️ Cetak Dokumen Sekarang
        </button>
        <span style="margin-left: 15px; font-size: 10pt;">Gunakan opsi 'Save as PDF' pada dialog print browser jika ingin menyimpan ke PDF.</span>
    </div>

    <div class="header">
        <h1>Rukun Warga 01 Dusun Harmoni Indah</h1>
        <h2>Kelurahan Sukamaju - Kecamatan Cilodong - Kota Depok</h2>
        <p>Sekretariat: Jl. Harmoni Raya No. 01, Sukamaju, Cilodong, Kota Depok, Jawa Barat 16413</p>
    </div>

    <div class="title">
        BERITA ACARA HASIL PENGHITUNGAN SUARA PEMILIHAN DIGITAL<br>
        Nomor: {{ $election->code }}/BA-PL/{{ date('Y') }}
    </div>

    <p>
        Pada hari ini, <strong>{{ Carbon\Carbon::parse($election->end_time)->translatedFormat('l, d F Y') }}</strong>, telah dilaksanakan rekapitulasi akhir hasil pemungutan suara secara digital (*E-Voting*) untuk:
    </p>

    <table style="border: none; margin-bottom: 12px;">
        <tr style="border: none;"><td style="border: none; width: 30%;"><strong>Nama Agenda</strong></td><td style="border: none;">: {{ $election->title }}</td></tr>
        <tr style="border: none;"><td style="border: none;"><strong>Tingkat / Wilayah</strong></td><td style="border: none;">: {{ $election->scope_display }}</td></tr>
        <tr style="border: none;"><td style="border: none;"><strong>Periode Jabatan</strong></td><td style="border: none;">: {{ $election->term_period }}</td></tr>
        <tr style="border: none;"><td style="border: none;"><strong>Waktu Pelaksanaan</strong></td><td style="border: none;">: {{ $election->start_time->format('d/m/Y H:i') }} WIB s/d {{ $election->end_time->format('d/m/Y H:i') }} WIB</td></tr>
    </table>

    <p><strong>I. DATA PEMILIH DAN PENGGUNAAN HAK SUARA</strong></p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Uraian Rekapitulasi</th>
                <th style="width: 25%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Jumlah Pemilih Terdaftar dalam DPT</td>
                <td style="text-align: center;"><strong>{{ number_format($results['statistics']['total_eligible_voters']) }}</strong> Orang</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Jumlah Pemilih yang Menggunakan Hak Suara</td>
                <td style="text-align: center;"><strong>{{ number_format($results['statistics']['total_voted']) }}</strong> Orang</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Jumlah Pemilih yang Tidak Menggunakan Hak Suara</td>
                <td style="text-align: center;"><strong>{{ number_format($results['statistics']['total_unvoted']) }}</strong> Orang</td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>Tingkat Partisipasi Pemilih</td>
                <td style="text-align: center;"><strong>{{ $results['statistics']['turnout_percentage'] }}%</strong></td>
            </tr>
            <tr>
                <td style="text-align: center;">5</td>
                <td>Total Surat Suara Sah Diterima di Kotak Suara Rahasia</td>
                <td style="text-align: center;"><strong>{{ number_format($results['statistics']['total_valid_ballots']) }}</strong> Suara</td>
            </tr>
        </tbody>
    </table>

    <p><strong>II. PEROLEHAN SUARA MASING-MASING KANDIDAT</strong></p>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">No. Urut</th>
                <th>Nama Lengkap Kandidat</th>
                <th style="width: 25%;">Jumlah Perolehan Suara</th>
                <th style="width: 15%;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results['candidates'] as $candidate)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $candidate['candidate_number'] }}</td>
                    <td>{{ $candidate['name'] }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($candidate['votes_count']) }} Suara</td>
                    <td style="text-align: center;">{{ $candidate['percentage'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>
        Berdasarkan rekapitulasi di atas, ditetapkan bahwa 
        @if($results['winner'])
            calon dengan nomor urut <strong>{{ $results['winner']['candidate_number'] }}</strong> atas nama <strong>{{ $results['winner']['name'] }}</strong> dinyatakan sebagai <strong>KANDIDAT TERPILIH</strong>.
        @elseif($results['is_tie'])
            terjadi perolehan suara seri antar-kandidat, dan penyelesaian akan mengacu pada ketetapan aturan tertulis: <em>"{{ $election->tie_breaker_rule }}"</em>.
        @else
            belum ada suara sah yang masuk.
        @endif
    </p>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td>
                Menyetujui dan Mengesahkan,<br>
                <strong>Panitia Pemilihan (Admin)</strong>
                <div class="signature-space"></div>
                <strong>( {{ $election->approval?->admin?->name ?? 'Panitia Pemilihan' }} )</strong><br>
                <span style="font-size: 8pt; color: #555;">Tercatat Digital: {{ $election->approval?->admin_approved_at?->format('d/m/Y H:i') ?? '-' }}</span>
            </td>
            <td>
                Mengetahui dan Mengawasi,<br>
                <strong>Pengawas Independen</strong>
                <div class="signature-space"></div>
                <strong>( {{ $election->approval?->supervisor?->name ?? 'Pengawas Pemilihan' }} )</strong><br>
                <span style="font-size: 8pt; color: #555;">Tercatat Digital: {{ $election->approval?->supervisor_approved_at?->format('d/m/Y H:i') ?? '-' }}</span>
            </td>
        </tr>
    </table>

</body>
</html>
