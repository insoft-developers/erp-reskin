<!DOCTYPE html>
<html>
<head>
    <title>randu.co.id</title>
</head>
<body>
    {{-- <h1>{{ $details['title'] }}</h1>
    <p>{{ $details['body'] }}</p>
   --}}
   <p>Yth. Bapak/Ibu {{ $details['nama'] }}</p> 
   <p>Untuk mengatur kata sandi baru akun anda silahkan klik link dibawah ini (atau copy paste ke browser anda)</p>
   <a href="{{ url('forgot_password/reset_password') }}?id={{ $details['id'] }}&code={{ $details['link'] }}">KLIK UNTUK ATUR KATA SANDI BARU</a>
   <p>setelah itu gunakan email dan password anda untuk <a href="{{ url('/frontend_login') }}">masuk :</a>    
    <p>Salam, <br>Admin Randu.co.id</p>
</body>
</html>