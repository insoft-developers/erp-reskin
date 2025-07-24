<!DOCTYPE html>
<html>

<head>
    <title>Export Stock Opname</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        
        <table class="table table-striped">
            <tr>

                <th style="border-bottom: 2px solid black;">id</th>
                <th style="border-bottom: 2px solid black;">opname_id</th>
                <th style="border-bottom: 2px solid black;">product_id</th>
                <th style="border-bottom: 2px solid black;">nama_produk</th>
                <th style="border-bottom: 2px solid black;">Jenis</th>
                <th style="border-bottom: 2px solid black;">unit</th>
                <th style="border-bottom: 2px solid black;">hpp</th>
                <th style="border-bottom: 2px solid black;">stok</th>
                <th style="border-bottom: 2px solid black;">nilai_stok</th>
                <th style="border-bottom: 2px solid black;">stok_fisik</th>
                

            </tr>

          

            @foreach ($data as $d)
                <tr>
                    <td style="border-top:2px solid black;">{{ $d->id }}</td>
                    <td style="border-top:2px solid black;">{{ $d->opname_id }}</td>
                    <td style="border-top:2px solid black;">{{ $d->product_id }}</td>
                    @if($d->product_type == 1 || $d->product_type == 2)
                    <td style="border-top:2px solid black;">{{ $d->product->name }}</td>
                    @elseif($d->product_type == 3)
                    <td style="border-top:2px solid black;">{{ $d->material->material_name }}</td>
                    @elseif($d->product_type == 4)
                    <td style="border-top:2px solid black;">{{ $d->inter->product_name }}</td>        
                    @endif
                    
                    @if($d->product_type == 1)
                    <td style="border-top:2px solid black;">produk-jadi</td>
                    @elseif($d->product_type == 2)
                    <td style="border-top:2px solid black;">produk-manufacture</td>
                    @elseif($d->product_type == 3)
                    <td style="border-top:2px solid black;">produk-bahan-baku</td>
                    @elseif($d->product_type == 4)
                    <td style="border-top:2px solid black;">produk-setengah-jadi</td>
                    @endif


                    
                    @if($d->product_type == 1 || $d->product_type == 2)
                    <td style="border-top:2px solid black;">{{ $d->product->unit }}</td>
                    @elseif($d->product_type == 3)
                    <td style="border-top:2px solid black;">{{ $d->material->unit }}</td>
                    @elseif($d->product_type == 4)
                    <td style="border-top:2px solid black;">{{ $d->inter->unit }}</td>        
                    @endif


                    <td style="border-top:2px solid black;">{{ $d->cost }}</td>
                    <td style="border-top:2px solid black;">{{ $d->quantity }}</td>
                    <td style="border-top:2px solid black;">{{ $d->total_value }}</td>
                    <td style="border:2px solid green;background-color:yellow;"></td>
                </tr>
            @endforeach
            
        </table>


    </div>

</body>

</html>
