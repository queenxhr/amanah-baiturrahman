<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kuitansi Resmi Wakaf</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #374151;
            margin: 0;
            padding: 0;
            font-size: 12px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 10px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo-title {
            font-size: 22px;
            font-weight: 900;
            font-style: italic;
            color: #638734;
            line-height: 1.1;
        }
        .logo-subtitle {
            font-size: 10px;
            color: #9ca3af;
            font-weight: 500;
            margin-top: 5px;
        }
        .title-right {
            text-align: right;
        }
        .title-right h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.5px;
        }
        .title-right .ref-code {
            font-family: monospace;
            font-size: 12px;
            font-weight: bold;
            color: #638734;
            margin-top: 5px;
        }
        .meta-table {
            width: 100%;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .meta-col {
            width: 50%;
            vertical-align: top;
        }
        .meta-label {
            font-size: 9px;
            font-weight: bold;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .meta-value-title {
            font-size: 13px;
            font-weight: 800;
            color: #111827;
        }
        .meta-value-text {
            color: #4b5563;
            margin-top: 3px;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .item-table th {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px;
            font-size: 9px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            text-align: left;
        }
        .item-table td {
            border: 1px solid #e5e7eb;
            padding: 12px 10px;
            vertical-align: top;
        }
        .item-title {
            font-weight: bold;
            color: #111827;
        }
        .item-desc {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }
        .total-row td {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .total-label {
            text-transform: uppercase;
            font-size: 10px;
            color: #111827;
            padding: 10px;
        }
        .total-amount {
            color: #638734;
            font-size: 13px;
            font-weight: 800;
            text-align: right;
            padding: 10px;
        }
        .footer-table {
            width: 100%;
            margin-top: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: 9999px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .sign-title {
            font-size: 9px;
            color: #9ca3af;
            font-weight: bold;
            margin-bottom: 50px;
        }
        .sign-name {
            font-weight: bold;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
            display: inline-block;
            width: 150px;
            text-align: center;
        }
        .sign-note {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 3px;
            text-align: center;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="logo-title">Amanah Baiturrahman</div>
                    <div class="logo-subtitle">Jl. Gedong Lima No.30, Padalarang, Bandung Barat</div>
                </td>
                <td class="title-right" style="width: 40%; vertical-align: top;">
                    <h2>KUITANSI WAKAF</h2>
                    <div class="ref-code">{{ $transaction->kode_referensi ?? ('WKF-' . str_pad($transaction->id_transaksi, 6, '0', STR_PAD_LEFT)) }}</div>
                </td>
            </tr>
        </table>

        <!-- Meta Info -->
        <table class="meta-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="meta-col">
                    <div class="meta-label">Wakif / Donatur</div>
                    <div class="meta-value-title">{{ $transaction->nama ?? 'Hamba Allah' }}</div>
                    <div class="meta-value-text">Status Keanggotaan: Wakif Terdaftar</div>
                </td>
                <td class="meta-col" style="text-align: right;">
                    <div class="meta-label">Rincian Pembayaran</div>
                    <div class="meta-value-text">Tanggal: <strong>{{ date('d F Y H:i', strtotime($transaction->created_at ?? now())) }}</strong></div>
                    <div class="meta-value-text">Metode: <strong>QRIS / Transfer Manual</strong></div>
                </td>
            </tr>
        </table>

        <!-- Item Table -->
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Item Penyaluran</th>
                    <th style="width: 30%; text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-title">Wakaf Tunai</div>
                        <div class="item-desc">Disalurkan untuk program: {{ $transaction->t03_program_wakaf->nama_program ?? 'Program Wakaf Baiturrahman' }}</div>
                    </td>
                    <td style="text-align: right; font-weight: bold; font-size: 13px;">
                        Rp{{ number_format($transaction->nominal, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="total-row">
                    <td class="total-label">Total Pembayaran</td>
                    <td class="total-amount">
                        Rp{{ number_format($transaction->nominal, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <table class="footer-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    <div class="meta-label" style="margin-bottom: 5px;">Status Pembayaran</div>
                    <span class="status-badge">Berhasil / Lunas</span>
                </td>
                <td style="width: 40%; vertical-align: top; text-align: right;">
                    <div style="display: inline-block; text-align: left;">
                        <div class="sign-title" style="text-align: center;">Tertanda,</div>
                        <div class="sign-name">Pengelola Baiturrahman</div>
                        <div class="sign-note">Kuitansi sah dicetak secara elektronik</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
