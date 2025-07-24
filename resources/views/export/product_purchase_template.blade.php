<!DOCTYPE html>
<html>

<head>
    <title>Export Product List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        
        <table class="table table-striped">
            <tr>

                <th style="border-bottom: 2px solid black;">product_id</th>
                <th style="border-bottom: 2px solid black;">product_name</th>
                <th style="border-bottom: 2px solid black;">selling_price</th>
                <th style="border-bottom: 2px solid black;">hpp</th>
                <th style="border-bottom: 2px solid black;">stock</th>
                <th style="border-bottom: 2px solid black;">unit</th>
                <th style="border-bottom: 2px solid black;">tanggal_transaksi</th>
                <th style="border-bottom: 2px solid black;">jumlah_beli</th>
                <th style="border-bottom: 2px solid black;">buying_price</th>
              

            </tr>

          

            @foreach ($data as $d)
                
                <tr>
                    <td style="border-top:2px solid black;">{{ $d->id }}</td>
                    <td style="border-top:2px solid black;">{{ $d->name }}</td>
                    <td style="border-top:2px solid black;">{{ $d->price }}</td>
                    <td style="border-top:2px solid black;">{{ $d->cost }}</td>
                    <td style="border-top:2px solid black;">{{ $d->quantity }}</td>
                    <td style="border-top:2px solid black;">{{ $d->unit }}</td>
                    <td style="border:2px solid green;background-color:yellow;"></td>
                    <td style="border:2px solid green;background-color:yellow;"></td>
                    <td style="border:2px solid green;background-color:yellow;"></td>
                   
                </tr>
            @endforeach
            
        </table>


    </div>

</body>

</html>
