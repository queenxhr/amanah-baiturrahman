<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pendaftaran Akun Nazhir Disetujui</title>
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
        .details-card {
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
            border-bottom: 1px dashed #e5e7eb;
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
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn-login {
            background-color: #143E2C;
            color: #ffffff !important;
            padding: 12px 30px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            border-radius: 9999px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(20, 62, 44, 0.15);
            transition: background-color 0.2s;
        }
        .btn-login:hover {
            background-color: #0f2e21;
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
            <p>Pendaftaran Akun Nazhir Disetujui</p>
        </div>
        <div class="content">
            <p class="welcome-text">Assalamualaikum Warahmatullahi Wabarakatuh,</p>
            <p>Selamat! Pendaftaran akun Nazhir Anda pada platform **Amanah Baiturrahman** telah ditinjau dan disetujui oleh Superadmin. Sekarang Anda dapat masuk dan mulai mengelola program serta laporan penyaluran dana wakaf.</p>
            
            <div class="success-box">
                <h3>Akun Aktif</h3>
                <p>Status pendaftaran Anda saat ini telah ditandai sebagai <strong>AKTIF</strong>. Anda sudah dapat mengakses panel dashboard Nazhir menggunakan email dan password yang Anda daftarkan sebelumnya.</p>
            </div>

            <div class="details-card">
                <div class="info-row" style="display: block; text-align: left;">
                    <div class="label" style="float: left;">Nama Nazhir:</div>
                    <div class="value" style="float: right;">{{ $user->nama }}</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="info-row" style="display: block; text-align: left;">
                    <div class="label" style="float: left;">Email Terdaftar:</div>
                    <div class="value" style="float: right;">{{ $user->email }}</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="info-row" style="display: block; text-align: left; border-bottom: none;">
                    <div class="label" style="float: left;">Nomor Handphone:</div>
                    <div class="value" style="float: right;">{{ $user->no_hp }}</div>
                    <div style="clear: both;"></div>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ url('/nazhir/login') }}" class="btn-login">Masuk ke Panel Nazhir</a>
            </div>

            <p style="font-size: 13px; color: #6b7280; font-style: italic; text-align: center; margin-top: 30px;">
                Jika Anda mengalami kendala saat masuk atau memiliki pertanyaan lebih lanjut, silakan hubungi tim kami.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Amanah Baiturrahman. All rights reserved.</p>
            <p>Hubungi kami melalui email: <a href="mailto:ratu.syahirah@upi.edu">ratu.syahirah@upi.edu</a></p>
        </div>
    </div>
</body>
</html>
