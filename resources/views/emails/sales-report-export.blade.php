<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
        .highlight {
            background-color: #e7f3ff;
            padding: 10px;
            border-left: 4px solid #2196F3;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan Advance</h1>
    </div>
    
    <div class="content">
        <p>Halo,</p>
        
        <p>Laporan penjualan advance yang Anda minta telah berhasil dibuat dan siap untuk diunduh.</p>
        
        <div class="highlight">
            <strong>Detail Laporan:</strong><br>
            • Periode: {{ $dateName }}<br>
            • Format: {{ strtoupper($exportType) }}<br>
            • Waktu pembuatan: {{ date('d/m/Y H:i:s') }}
        </div>
        
        <p>File laporan telah dilampirkan dalam email ini. Silakan unduh dan simpan file tersebut untuk keperluan Anda.</p>
        
        <p>Jika Anda memiliki pertanyaan atau memerlukan bantuan lebih lanjut, jangan ragu untuk menghubungi tim dukungan kami.</p>
        
        <p>Terima kasih telah menggunakan layanan kami.</p>
        
        <p>Salam,<br>
        <strong>Tim Randu</strong></p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.</p>
    </div>
</body>
</html>
