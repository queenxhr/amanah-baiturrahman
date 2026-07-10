<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifikasi Email Akun Wakif</title>
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
        .info-box {
            background-color: #f0fdf4;
            border-left: 4px solid #143E2C;
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        .info-box h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #143E2C;
            font-weight: 700;
        }
        .info-box p {
            margin: 0;
            font-size: 13px;
            color: #374151;
        }
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn-verify {
            background-color: #143E2C;
            color: #ffffff !important;
            padding: 14px 36px;
            text-decoration: none;
            font-weight: bold;
            font-size: 15px;
            border-radius: 9999px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(20, 62, 44, 0.15);
        }
        .details-card {
            background-color: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .info-row {
            display: block;
            text-align: left;
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
            float: left;
        }
        .value {
            color: #111827;
            font-weight: 700;
            float: right;
        }
        .clearfix { clear: both; }
        .expire-note {
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
            margin-top: 5px;
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
            <p>Verifikasi Email Akun Wakif</p>
        </div>
        <div class="content">
            <p class="welcome-text">Assalamualaikum Warahmatullahi Wabarakatuh,</p>
            <p>Jazakallahu Khairan, <strong>{{ $user->nama }}</strong>! Terima kasih telah mendaftarkan diri sebagai Wakif di <strong>Amanah Baiturrahman</strong>.</p>

            <div class="info-box">
                <h3>Satu Langkah Lagi!</h3>
                <p>Untuk mengaktifkan akun Anda dan mulai berwakaf, silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda. Link ini berlaku selama <strong>24 jam</strong>.</p>
            </div>

            <div class="details-card">
                <div class="info-row">
                    <div class="label">Nama:</div>
                    <div class="value">{{ $user->nama }}</div>
                    <div class="clearfix"></div>
                </div>
                <div class="info-row" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
                    <div class="label">Email:</div>
                    <div class="value">{{ $user->email }}</div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="btn-container">
                <a href="{{ $verificationUrl }}" class="btn-verify">
                    ✓ Verifikasi Email Saya
                </a>
                <p class="expire-note">Link verifikasi berlaku 24 jam sejak email ini diterima.</p>
            </div>

            <p style="font-size: 12px; color: #6b7280; margin-top: 20px;">
                Jika Anda tidak dapat mengklik tombol di atas, salin dan tempelkan link berikut ke browser Anda:<br>
                <a href="{{ $verificationUrl }}" style="color: #143E2C; word-break: break-all; font-size: 11px;">{{ $verificationUrl }}</a>
            </p>

            <p style="font-size: 13px; color: #6b7280; font-style: italic; text-align: center; margin-top: 30px;">
                Jika Anda tidak mendaftar di Amanah Baiturrahman, abaikan email ini.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Amanah Baiturrahman. All rights reserved.</p>
            <p>Hubungi kami melalui email: <a href="mailto:ratu.syahirah@upi.edu">ratu.syahirah@upi.edu</a></p>
        </div>
    </div>
</body>
</html>
