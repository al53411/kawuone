<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Absensi - {{ $mapel->nama_mapel }} - {{ $kelas->nama_kelas }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h2,
        .header h3 {
            margin: 2px 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 13px;
        }

        .info-table td {
            padding: 3px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th,
        table.data td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: center;
        }

        table.data th {
            background-color: #f2f2f2;
        }

        table.data td.left {
            text-align: left;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
            display: table;
        }

        .footer-sign {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()"
            style="padding: 8px 16px; background: #000; color: #fff; border: none; cursor: pointer; border-radius: 4px;">Cetak
            / Simpan PDF</button>
    </div>

    <div class="header">
        <h3>REKAPITULASI KEHADIRAN SISWA</h3>
        <h2>{{ strtoupper($profilSekolah->nama_sekolah ?? 'SDN KAWU 1') }}</h2>
        <p>{{ $profilSekolah->alamat ?? '' }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Mata Pelajaran</strong></td>
            <td width="35%">: {{ $mapel->nama_mapel }}</td>
            <td width="15%"><strong>Bulan</strong></td>
            <td width="35%">: {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: {{ $kelas->nama_kelas }}</td>
            <td><strong>Guru Pengampu</strong></td>
            <td>: {{ $guru->nama_lengkap ?? $guru->nama ?? Auth::user()->name }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%" class="left">Nama Siswa</th>
                <th width="10%">Hadir</th>
                <th width="10%">Izin</th>
                <th width="10%">Sakit</th>
                <th width="10%">Alpa</th>
                <th width="10%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswaList as $index => $siswa)
                @php
                    $rekap = $rekapData[$siswa->id] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'total' => 0];
                    $namaSiswa = $siswa->nama_lengkap ?? $siswa->nama ?? $siswa->nama_siswa ?? '-';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="left">{{ $namaSiswa }}</td>
                    <td>{{ $rekap['hadir'] }}</td>
                    <td>{{ $rekap['izin'] }}</td>
                    <td>{{ $rekap['sakit'] }}</td>
                    <td>{{ $rekap['alpa'] }}</td>
                    <td><strong>{{ $rekap['total'] }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-sign"></div>
        <div class="footer-sign">
            <p>Ngawi, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Guru Mata Pelajaran,</p>
            <br><br><br>
            <p><strong>{{ $guru->nama_lengkap ?? $guru->nama ?? Auth::user()->name }}</strong></p>
            <p>NIP. {{ $guru->nip ?? '-' }}</p>
        </div>
    </div>

</body>

</html>