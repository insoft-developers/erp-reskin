<!DOCTYPE html>
<html>

<head>
    <title>randu.co.id</title>
</head>

<body>
    {{-- <h1>{{ $details['title'] }}</h1>
    <p>{{ $details['body'] }}</p>
   --}}
    <p>Halo kak {{ $details['nama'] }}</p>
    <p>Kenalin saya Istabel dari Randu. silahkan klik link aktivasi di bawah ini</p>
    <a href="https://app.randu.co.id/account_activate?id={{ $details['id'] }}&code={{ $details['link'] }}">
        https://app.randu.co.id/account_activate?id={{ $details['id'] }}&code={{ $details['link'] }}
    </a>
    <p>Jika kak {{ $details['nama'] }} memiliki pertanyaan atau memerlukan bantuan lebih lanjut, jangan ragu untuk
        menghubungi Trainer atau Customer Service di:</p>
    <a href="https://help.randu.co.id">Website Bantuan & Tutorial</a>
    <p>Terima kasih sudah mendaftar di Randu! Jika kak {{ $details['nama'] }} sudah aktivasi silakan login ke: <a
            href="{{ url('/frontend_login') }}">Dashboard Aplikasi</a>
    <p>Sayangku Padamu Selalu,<br>Istabel dari Randu</p>
</body>
