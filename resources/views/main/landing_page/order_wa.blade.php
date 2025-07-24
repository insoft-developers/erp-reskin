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
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
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
                            Terimakasih atas permintaan anda, mohon tunggu kami akan menggiring Anda langsung ke
                            customer service kami, terimakasih. <br /><br />
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/template/main/vendors/js/vendors.min.js"></script>
    <script>
        $(document).ready(function() {
            setTimeout(() => {
                window.location.href =
                    'https://api.whatsapp.com/send?phone={{ $phoneNumber }}&text={{ $message }}'
            }, 3000)
        })
    </script>
</body>

</html>
