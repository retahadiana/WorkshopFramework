<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Undangan</title>
    <style>
        @page { margin: 34px 42px; }

        body {
            font-family: "Times New Roman", serif;
            color: #111;
            font-size: 12pt;
            line-height: 1.55;
            margin: 0;
        }

        .letterhead {
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
            margin-bottom: 18px;
            text-align: center;
        }

        .org-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .org-unit {
            font-size: 12.5pt;
            font-weight: bold;
            margin-top: 2px;
        }

        .org-address {
            font-size: 10.5pt;
            margin-top: 2px;
        }

        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 12pt;
        }

        .meta td {
            vertical-align: top;
            padding: 1px 0;
        }

        .meta .label {
            width: 88px;
        }

        .recipient {
            margin: 8px 0 16px;
        }

        .subject {
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0 10px;
        }

        .body-text {
            text-align: justify;
            margin-bottom: 10px;
        }

        .agenda {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 12px;
        }

        .agenda td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 12pt;
        }

        .agenda .label {
            width: 150px;
        }

        .signature {
            margin-top: 24px;
            width: 290px;
            margin-left: auto;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin: 58px 0 4px;
        }

        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="letterhead">
        <div class="org-name">{{ $organization }}</div>
        <div class="org-unit">Himpunan Mahasiswa D4 Teknik Informatika</div>
        <div class="org-address">Fakultas Vokasi Universitas Airlangga, Kampus B Surabaya</div>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Nomor</td>
            <td>: {{ $number }}</td>
            <td style="text-align: right;">Surabaya, {{ $date }}</td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td>: -</td>
            <td></td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td>: {{ $subject }}</td>
            <td></td>
        </tr>
    </table>

    <div class="recipient">
        Kepada Yth.<br>
        <span class="bold">Saudari {{ $recipient ?? 'Reta Hadiana Unggula' }}</span><br>
        Anggota HIMA D4 Teknik Informatika<br>
        Di Tempat
    </div>

    <div class="subject">{{ strtoupper($subject) }}</div>

    <div class="body-text">
        Dengan hormat,
        <br><br>
        Sehubungan dengan pelaksanaan program kerja Himpunan Mahasiswa D4 Teknik Informatika,
        kami mengundang Saudari untuk hadir dalam kegiatan rapat koordinasi pengurus yang akan dilaksanakan pada:
    </div>

    <table class="agenda">
        <tr>
            <td class="label">Hari/Tanggal</td>
            <td>: {{ $date }}</td>
        </tr>
        <tr>
            <td class="label">Waktu</td>
            <td>: {{ $time }}</td>
        </tr>
        <tr>
            <td class="label">Tempat</td>
            <td>: {{ $place }}</td>
        </tr>
        <tr>
            <td class="label">Agenda</td>
            <td>: {{ $event }}</td>
        </tr>
    </table>

    <div class="body-text">
        Mengingat pentingnya agenda tersebut, kami mengharapkan kehadiran Saudari tepat waktu.
        Demikian surat undangan ini kami sampaikan. Atas perhatian dan kehadiran Saudari,
        kami ucapkan terima kasih.
    </div>

    <div class="signature">
        Hormat kami,
        <br>
        <span class="bold">Ketua HIMA D4 Teknik Informatika</span>
        <div class="signature-line"></div>
        <div class="bold">Reta Hadiana Unggula</div>
    </div>
</body>
</html>
