<!DOCTYPE html>
<html>

<head>
    <title>Permohonan Hapus Akun</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        body {
            margin-left: 20px;
            margin-right: 20px;

            margin-top: 80px;
        }
    </style>
</head>

<body>
    <form method="POST" action="{{ url('mobile_account_post') }}">
        @csrf
        <div class="container">
            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="form-group" style="margin-top:30px;">
                <label>Email</label>
                <input required id="email" name="email" type="email" class="form-control"
                    placeholder="masukkan email anda">
            </div>
            <div style="margin-top:30px;"></div>
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
    </form>
</body>

</html>
