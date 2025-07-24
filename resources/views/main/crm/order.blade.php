<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="shortcut icon" type="image/x-icon" href="/template/main/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/template/main/css/bootstrap.min.css" />

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/vendors.min.css" />
    <!--! END: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="/template/main/css/theme.min.css" />
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/template/main/vendors/css/select2-theme.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @if ($scriptheader)
        {!! $scriptheader !!}
    @endif
</head>

<body class="bg-white">
    <div class="h-screen w-full flex items-center justify-center">
        <div class="container px-4 mx-auto">
            <div class="grid grid-cols-12">
                <div class="col-span-12 flex items-center justify-center">
                    <div class="w-[500px]">
                        <div class="alert alert-success text-center">
                            Terimakasih orderan anda sedang di proses, mohon tunggu 1-24 Jam, kami akan segera
                            menghubungi anda melalui whatsapp
                        </div>
                        <div class="text-center">Nomor Order Anda: {{ $reference_id }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
