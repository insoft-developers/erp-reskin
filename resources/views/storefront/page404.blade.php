@extends('storefront.template.layouts.notfound')

@section('content')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #063970 0%, #063970 50%, #063970 100%);
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: white;
            overflow: hidden;
            position: relative;
        }

        .stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            background-image: radial-gradient(2px 2px at 20px 30px, #eee, rgba(0,0,0,0)),
                            radial-gradient(2px 2px at 40px 70px, #fff, rgba(0,0,0,0)),
                            radial-gradient(2px 2px at 50px 160px, #ddd, rgba(0,0,0,0)),
                            radial-gradient(2px 2px at 90px 40px, #fff, rgba(0,0,0,0)),
                            radial-gradient(2px 2px at 130px 80px, #fff, rgba(0,0,0,0));
            background-repeat: repeat;
            background-size: 200px 200px;
            animation: twinkle 4s ease-in-out infinite alternate;
        }

        .container {
            max-width: 600px;
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        h1 {
            font-size: 8rem;
            font-weight: bold;
            color: white;
            margin-bottom: 1rem;
            line-height: 1;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #e2e8f0;
        }

        p {
            color: #cbd5e1;
            margin-bottom: 2rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(45deg, #063970, #063970);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -5px rgba(6, 57, 112, 0.5);
        }

        .meteor {
            position: absolute;
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #fff, transparent);
            animation: meteor 2s linear infinite;
            transform: rotate(-45deg);
        }

        .meteor:nth-child(2) {
            top: 30%;
            left: 70%;
            animation-delay: 1.3s;
        }

        .meteor:nth-child(3) {
            top: 40%;
            left: 80%;
            animation-delay: 2.6s;
        }

        @keyframes twinkle {
            0% { opacity: 0.3; }
            100% { opacity: 1; }
        }

        @keyframes meteor {
            0% {
                transform: rotate(-45deg) translateX(0);
                opacity: 1;
            }
            100% {
                transform: rotate(-45deg) translateX(-1000px);
                opacity: 0;
            }
        }

        @media (max-width: 640px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }

            h1 {
                font-size: 6rem;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="stars"></div>
    <div class="meteor"></div>
    <div class="meteor"></div>
    <div class="meteor"></div>
    
    <div class="container">
        <h1>404</h1>
        <h2>Halaman Tidak Ditemukan</h2>
        <p>Maaf, sepertinya halaman atau file yang kamu cari tidak dapat ditemukan atau telah dihapus.</p>
        <a href="https://randu.co.id" class="btn">Kembali ke Dashboard Randu</a>
    </div>
</body>
</html>
@endsection
