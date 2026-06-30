<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaksi Wakaf Disetujui</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            color: #374151;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #143E2C;
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #b1cf49;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-text {
            font-size: 16px;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .transaction-card {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
            border-bottom: 1px dashed #f3f4f6;
            padding-bottom: 8px;
        }
        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        .label {
            color: #6b7280;
            font-weight: 600;
        }
        .value {
            color: #111827;
            font-weight: 700;
            text-align: right;
        }
        .nominal-highlight {
            color: #143E2C;
            font-size: 18px;
            font-weight: 800;
        }
        .success-box {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        .success-box h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #065f46;
            font-weight: 700;
        }
        .success-box p {
            margin: 0;
            font-size: 13px;
            color: #065f46;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }
        .footer a {
            color: #143E2C;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Amanah Baiturrahman</h1>
            <p>Transaksi Wakaf Berhasil Diverifikasi</p>
        </div>
        <div class="content">
            <p class="welcome-text">Assalamualaikum Warahmatullahi Wabarakatuh,</p>
            <p>Alhamdulillah, tim Nazhir kami telah melakukan verifikasi dan menyetujui transaksi wakaf Anda. Dana wakaf Anda telah resmi disalurkan ke program yang Anda pilih.</p>
            
            <div class="success-box">
                <h3>Verifikasi Berhasil (Lunas)</h3>
                <p>Status pembayaran Anda saat ini telah ditandai sebagai <strong>LUNAS / BERHASIL</strong>. Kuitansi resmi pembayaran wakaf Anda telah dicetak secara elektronik dan kami lampirkan dalam format PDF pada email ini.</p>
            </div>

            <div class="transaction-card">
                <div class="info-row" style="display: block; text-align: left;">
                    <div class="label" style="float: left;">Kode Referensi:</div>
                    <div class="value" style="float: right; font-family: monospace;">{{ $transaksi->kode_referensi ?? ('WKF-' . str_pad($transaksi->id_transaksi, 6, '0', STR_PAD_LEFT)) }}</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="info-row" style="display: block; text-align: left;">
                    <div class="label" style="float: left;">Program Wakaf:</div>
                    <div class="value" style="float: right;">{{ $transaksi->t03_program_wakaf->nama_program ?? 'Program Wakaf' }}</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="info-row" style="display: block; text-align: left;">
                    <div class="label" style="float: left;">Nama Donatur:</div>
                    <div class="value" style="float: right;">{{ $transaksi->nama ?? 'Hamba Allah' }}</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="info-row" style="display: block; text-align: left; border-bottom: none; margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb;">
                    <div class="label" style="float: left; font-size: 16px; color: #111827;">Nominal Wakaf:</div>
                    <div class="value nominal-highlight" style="float: right;">Rp{{ number_format($transaksi->nominal, 0, ',', '.') }}</div>
                    <div style="clear: both;"></div>
                </div>
            </div>

            <p style="font-size: 13px; color: #6b7280; font-style: italic; text-align: center; margin-top: 40px;">
                Semoga Allah subhanahu wa ta'ala membalas segala kebaikan Anda dengan pahala yang terus mengalir tiada putus, serta melimpahkan rahmat dan keberkahan bagi Anda sekeluarga. Aamiin Yaa Rabbal 'Aalamiin.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Amanah Baiturrahman. All rights reserved.</p>
            <p>Hubungi kami melalui email: <a href="mailto:ratu.syahirah@upi.edu">ratu.syahirah@upi.edu</a></p>
        </div>
    </div>
</body>
</html>
