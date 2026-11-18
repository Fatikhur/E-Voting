<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Berita Acara - {{ $election->title }}</title>
    <style>
        @page {
            margin: 25mm 20mm 20mm 20mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111;
            line-height: 1.4;
            font-size: 11pt;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 13pt;
            text-transform: uppercase;
            margin: 0;
            font-weight: bold;
        }
        .header h2 {
            font-size: 12pt;
            text-transform: uppercase;
            margin: 3px 0;
        }
        .header p {
            font-size: 9pt;
            color: #333;
            margin: 0;
        }
        .title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }
        table.data, table.data th, table.data td {
            border: 1px solid #444;
        }
        table.data th {
            background-color: #f0f0f0;
            padding: 6px;
            font-size: 9.5pt;
            text-align: center;
        }
        table.data td {
            padding: 6px;
            font-size: 9.5pt;
        }
        .signatures {
            margin-top: 30px;
            width: 100%;
            border: none;
        }
        .signatures td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Rukun Warga 01 Dusun Harmoni Indah</h1>
        <h2>Kelurahan Sukamaju - Kecamatan Cilodong - Kota Depok</h2>
        <p>Sekretariat: Jl. Harmoni Raya No. 01, Sukamaju, Cilodong, Depok, Jawa Barat 16413</p>
    </div>

    <div class="title">
        BERITA ACARA HASIL PENGHITUNGAN SUARA PEMILIHAN DIGITAL<br>
        Nomor: {{ $election->code }}/BA-PL/{{ date('Y') }}
    </div>

    <p style="font-size: 10pt;">
        Pada hari ini, <strong>{{ Carbon\Carbon::parse($election->end_time)->translatedFormat('l, d F Y') }}</strong>, telah dilaksanakan rekapitulasi akhir hasil pemungutan suara digital (E-Voting) untuk:
    </p>

    <table style="border: none; margin-bottom: 8px; font-size: 10pt;">
        <tr><td style="width: 30%; border: none;"><strong>Nama Pemilihan</strong></td><td style="border: none;">: {{ $election->title }}</td></tr>
        <tr><td style="border: none;"><strong>Wilayah</strong></td><td style="border: none;">: {{ $election->scope_display }}</td></tr>
        <tr><td style="border: none;"><strong>Periode Jabatan</strong></td><td style="border: none;">: {{ $election->term_period }}</td></tr>
        <tr><td style="border: none;"><strong>Waktu Selesai</strong></td><td style="border: none;">: {{ $election->end_time->format('d/m/Y H:i') }} WIB</td></tr>
    </table>

    <p style="font-size: 10pt; font-weight: bold; margin-bottom: 4px;">I. REKAPITULASI PENGGUNAAN HAK SUARA</p>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th>Uraian</th>
                <th style="width: 25%;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Daftar Pemilih Tetap (DPT) Terdaftar</td>
                <td style="text-align: center;"><strong>{{ number_format($results['statistics']['total_eligible_voters']) }}</strong> Orang</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Pemilih yang Menggunakan Hak Suara</td>
                <td style="text-align: center;"><strong>{{ number_format($results['statistics']['total_voted']) }}</strong> Orang</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Pemilih yang Tidak Hadir / Memilih</td>
                <td style="text-align: center;"><strong>{{ number_format($results['statistics']['total_unvoted']) }}</strong> Orang</td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>Tingkat Partisipasi Pemilih</td>
                <td style="text-align: center;"><strong>{{ $results['statistics']['turnout_percentage'] }}%</strong></td>
            </tr>
            <tr>
                <td style="text-align: center;">5</td>
                <td>Total Surat Suara Sah di Kotak Suara</td>
                <td style="text-align: center;"><strong>{{ number_format($results['statistics']['total_valid_ballots']) }}</strong> Suara</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 10pt; font-weight: bold; margin-top: 14px; margin-bottom: 4px;">II. PEROLEHAN SUARA KANDIDAT</p>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th>Nama Kandidat</th>
                <th style="width: 25%;">Perolehan Suara</th>
                <th style="width: 15%;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results['candidates'] as $can)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $can['candidate_number'] }}</td>
                    <td>{{ $can['name'] }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ number_format($can['votes_count']) }}</td>
                    <td style="text-align: center;">{{ $can['percentage'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size: 10pt; margin-top: 10px;">
        @if($results['winner'])
            Dengan demikian, calon nomor urut <strong>{{ $results['winner']['candidate_number'] }}</strong> atas nama <strong>{{ $results['winner']['name'] }}</strong> ditetapkan sebagai <strong>KANDIDAT TERPILIH</strong>.
        @elseif($results['is_tie'])
            Hasil pemungutan suara menghasilkan perolehan suara seri (*tie vote*). Penyelesaian dilaksanakan mengacu pada aturan: <em>"{{ $election->tie_breaker_rule }}"</em>.
        @endif
    </p>

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td>
                Mengesahkan,<br>
                <strong>Panitia Pemilihan (Admin)</strong>
                <div class="signature-space"></div>
                <strong>( {{ $election->approval?->admin?->name ?? 'Panitia Pemilihan' }} )</strong><br>
                <span style="font-size: 8pt; color: #555;">Tanda Tangan Digital Terverifikasi</span>
            </td>
            <td>
                Mengetahui & Mengawasi,<br>
                <strong>Pengawas Independen</strong>
                <div class="signature-space"></div>
                <strong>( {{ $election->approval?->supervisor?->name ?? 'Pengawas Pemilihan' }} )</strong><br>
                <span style="font-size: 8pt; color: #555;">Tanda Tangan Digital Terverifikasi</span>
            </td>
        </tr>
    </table>

</body>
</html>
