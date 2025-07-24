<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Qrcode View</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap');
        html {
            font-family: "Poppins", sans-serif;
        }
        .row {
            width: 100%;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .kolom {
            display: flex;
            align-items: center;
            flex-direction: column;
            border: 1px solid black;
            padding: 1em;
        }

        img {
            width: 200px;
        }
        .qr {
            background-color: rgba(228, 152, 54, 0.836);
            border-radius: 10px;
            padding: .8em;
        }
        
        .text-menu {
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 1.5em;
        }

        .meja {
            font-size: 2em;
            font-weight: bold;
        }

        .menu {
            font-size: .7em;
            font-weight: 100;
        }

        .scan {
            display: block;
            text-align: center;
            color: white;
            font-size: 1.2em;
            text-transform: uppercase;
        }

        .text-scan {
            margin-top: 1em;
            font-size: 2em;
            text-transform: uppercase;
            font-weight: 500;
            text-align: center;
            line-height: 35px;
        }
    </style>
</head>
<body>
    


    <div class="row">
        @if ($qrcode != null)
            @foreach ($qrcode as $key => $item)
                <div class="kolom">
                    {{-- <div class="text">
                    </div> --}}
                    <div class="text-menu">
                        <div>Menu</div>
                        <div class="meja"> Meja {{ $key }}</div>
                    </div>
                    <div class="qr">
                        <img src="{{ asset('front_store/qrcode.png') }}" alt="qrcode">
                        <span class="scan">scan me</span>
                    </div>

                    <div class="text-scan">scan qr code <br> to order</div>
                </div>
            @endforeach
        @endif
    </div>

</body>
</html>