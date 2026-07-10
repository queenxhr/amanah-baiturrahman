<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Permohonan Pencairan Dana Wakaf</title>
    <style>
        @page {
            size: A4 portrait;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            line-height: 1.4;
        }

        /* === KOP SURAT === */
        .kop {
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
            text-align: center;
            width: 100%;
        }

        .kop h1 {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #143E2C;
            margin: 0 0 2px 0;
        }

        .kop h2 {
            font-size: 11pt;
            font-weight: normal;
            color: #333;
            margin: 0 0 2px 0;
        }

        .kop p {
            font-size: 8.5pt;
            color: #555;
            margin: 0;
        }

        /* === IDENTITAS SURAT === */
        .surat-header {
            margin-bottom: 15px;
            width: 100%;
        }

        .surat-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .surat-header td {
            padding: 2px 0;
            vertical-align: top;
        }

        .surat-header td.label-col {
            width: 15%;
        }

        .surat-header td.colon-col {
            width: 3%;
            text-align: center;
        }

        .surat-header td.value-col {
            width: 82%;
        }

        /* === PENERIMA === */
        .penerima {
            margin-bottom: 15px;
            line-height: 1.4;
        }

        /* === SALAM === */
        .salam {
            margin-bottom: 10px;
            font-weight: bold;
        }

        /* === BODY === */
        .body-text {
            text-align: justify;
            margin-bottom: 10px;
            text-indent: 1cm;
        }

        .body-text-no-indent {
            text-align: justify;
            margin-bottom: 10px;
        }

        /* === NOMINAL BOX === */
        .nominal-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-left: 4px solid #143E2C;
            padding: 8px 15px;
            margin: 12px 0;
            text-align: center;
        }

        .nominal-amount {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .nominal-terbilang {
            font-size: 9.5pt;
            font-weight: normal;
            font-style: italic;
        }

        /* === REKENING === */
        .rekening-table {
            margin: 10px 0 12px 1cm;
            width: 80%;
            border-collapse: collapse;
        }

        .rekening-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .rekening-table td.label-col {
            width: 30%;
        }

        .rekening-table td.colon-col {
            width: 5%;
            text-align: center;
        }

        .rekening-table td.value-col {
            width: 65%;
            font-weight: bold;
        }

        /* === PENUTUP === */
        .penutup {
            text-align: justify;
            margin-bottom: 10px;
            text-indent: 1cm;
        }

        .salam-penutup {
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* === TANDA TANGAN SECTION === */
        .ttd-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .ttd-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }

        .ttd-block {
            text-align: center;
            width: 100%;
        }

        .ttd-block .kota-tanggal {
            margin-bottom: 4px;
        }

        .ttd-block .ttd-label {
            font-weight: bold;
            margin-bottom: 5px;
            line-height: 1.3;
        }

        .ttd-block .ttd-qr {
            margin: 6px 0;
            text-align: center;
        }

        .ttd-block .ttd-qr img {
            border: 1px solid #eee;
            padding: 2px;
            background: #fff;
        }

        .ttd-block .ttd-name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 3px;
            display: inline-block;
            width: 170px;
        }

        /* === APPROVAL SECTION === */
        .approval-section {
            margin-top: 25px;
            border-top: 1px solid #000;
            padding-top: 10px;
            page-break-inside: avoid;
        }

        .approval-title {
            font-size: 9.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            color: #143E2C;
        }

        .approval-table {
            width: 100%;
            border-collapse: collapse;
        }

        .approval-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }

        .approval-block {
            text-align: center;
            width: 100%;
        }

        .approval-block .approval-role {
            font-weight: bold;
            margin-bottom: 60px;
        }

        .approval-block .approval-name {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 3px;
            display: inline-block;
            width: 170px;
        }
    </style>
</head>

<body>

    <div class="kop">
        <h1>Amanah Baiturrahman</h1>
        <h2>Lembaga Wakaf & Pemberdayaan Umat</h2>
        <p>Jl. Gedong Lima No.30, Kertajaya, Kec. Padalarang, Kabupaten Bandung Barat, Jawa Barat 40553 | Telp: (022)
            1234567 | Email: amanah.baiturrahman@gmail.com</p>
    </div>

    <div class="surat-header">
        <table>
            <tr>
                <td class="label-col">Nomor</td>
                <td class="colon-col">:</td>
                <td class="value-col">
                    {{ $pencairan->nomor_surat ?? ('PNC/' . str_pad($pencairan->id_pencairan, 4, '0', STR_PAD_LEFT) . '/AW/' . \Carbon\Carbon::now()->format('VII/Y')) }}
                </td>
            </tr>
            <tr>
                <td class="label-col">Perihal</td>
                <td class="colon-col">:</td>
                <td class="value-col"><strong>Permohonan Pencairan Dana Wakaf</strong></td>
            </tr>
        </table>
    </div>

    <div class="penerima">
        Kepada Yth.<br>
        <strong>Pengurus Lembaga Pusat / Yayasan Amanah Baiturrahman</strong><br>
        di Tempat
    </div>

    <div class="salam">Assalamu'alaikum Warahmatullahi Wabarakatuh,</div>

    <div class="body-text">
        Segala puji bagi Allah SWT yang telah melimpahkan rahmat dan hidayah-Nya kepada kita semua. Shalawat serta salam
        semoga senantiasa tercurah kepada junjungan kita Nabi Muhammad SAW, keluarga, sahabat, hingga para pengikutnya
        yang setia.
    </div>
    <div class="body-text">
        Sehubungan dengan berjalannya program pembangunan/pengembangan
        <strong>{{ $pencairan->program ? $pencairan->program->nama_program : 'Program Wakaf' }}</strong>, bersama surat
        ini kami selaku Nazhir bermaksud mengajukan permohonan pencairan dana wakaf sebesar:
    </div>

    <div class="nominal-box">
        <div class="nominal-amount">Rp{{ number_format($pencairan->jumlah_dana, 0, ',', '.') }}</div>
        <div class="nominal-terbilang">(Terbilang: {{ $terbilang }} Rupiah)</div>
    </div>

    <div class="body-text">
        Adapun dana tersebut akan digunakan untuk: <strong>{{ $pencairan->keterangan ?? '-' }}</strong>
    </div>

    <div class="body-text-no-indent" style="margin-top: 10px;">
        Pencairan dana mohon dapat ditransfer melalui rekening resmi berikut:
    </div>

    <table class="rekening-table">
        <tr>
            <td class="label-col">Nama Bank</td>
            <td class="colon-col">:</td>
            <td class="value-col">Bank Syariah Indonesia (BSI)</td>
        </tr>
        <tr>
            <td class="label-col">Nomor Rekening</td>
            <td class="colon-col">:</td>
            <td class="value-col">1234567890</td>
        </tr>
        <tr>
            <td class="label-col">Atas Nama</td>
            <td class="colon-col">:</td>
            <td class="value-col">Yayasan Amanah Baiturrahman</td>
        </tr>
    </table>

    <div class="penutup">
        Demikian surat permohonan ini kami sampaikan. Atas perhatian, kerja sama, dan terealisasinya permohonan ini,
        kami ucapkan jazakumullah khairan katsiran.
    </div>

    <div class="salam-penutup">Wassalamu'alaikum Warahmatullahi Wabarakatuh,</div>

    <table class="ttd-table">
        <tr>
            <td></td>
            <td>
                <div class="ttd-block">
                    <div class="kota-tanggal">Bandung,
                        {{ \Carbon\Carbon::parse($pencairan->created_at)->locale('id')->isoFormat('D MMMM Y') }}
                    </div>
                    <div class="ttd-label">Yang Mengajukan,<br>Nazhir</div>
                    <div class="ttd-qr">
                        @if(!empty($qrCode))
                            <img src="{{ $qrCode }}" alt="QR TTD" width="75" height="75">
                        @else
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=75x75&data={{ urlencode($pencairan->user ? $pencairan->user->nama : 'Nazhir') }}"
                                alt="QR TTD" width="75" height="75">
                        @endif
                    </div>
                    <div class="ttd-name">{{ $pencairan->user ? $pencairan->user->nama : 'Nazhir' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="approval-section">
        <div class="approval-title">Disetujui Oleh (Approval)</div>
        <table class="approval-table">
            <tr>
                <td>
                    <div class="approval-block">
                        <div class="approval-role">Ketua Wadiah</div>
                        <div class="approval-name">&nbsp;</div>
                    </div>
                </td>
                <td>
                    <div class="approval-block">
                        <div class="approval-role">Bendahara</div>
                        <div class="approval-name">&nbsp;</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>