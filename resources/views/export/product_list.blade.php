<!DOCTYPE html>
<html>

<head>
    <title>Export Product List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>

    <div class="container">
        <center>
            <h4>Product List</h4>
            
        </center>


        <table class="table table-striped">
            <tr>

                <th style="border-bottom: 2px solid black;">ID</th>
                <th style="border-bottom: 2px solid black;">Name</th>
                <th style="border-bottom: 2px solid black;">Code</th>
                <th style="border-bottom: 2px solid black;">Category</th>
                <th style="border-bottom: 2px solid black;">Harga Jual</th>
                <th style="border-bottom: 2px solid black;">HPP</th>
                <th style="border-bottom: 2px solid black;">Stock</th>
                <th style="border-bottom: 2px solid black;">Unit</th>
                <th style="border-bottom: 2px solid black;">Nilai Barang</th>

            </tr>

            @php
                $total_nilai_barang = 0;
                $total_stock = 0;

                $total_harga = 0;
                $total_hpp = 0;
            @endphp

            @foreach ($data as $d)
                @php
                    $category = \App\Models\ProductCategory::where('id', $d->category_id);
                    if ($category->count() > 0) {
                        $cat = $category->first();
                        $cat_name = $cat->name;
                    } else {
                        $cat_name = '';
                    }

                    $nilai_barang = $d->cost * $d->quantity;

                    $total_nilai_barang = $total_nilai_barang + $nilai_barang;
                    $total_stock = $total_stock + $d->quantity;
                    $total_harga = $total_harga + $d->price;
                    $total_hpp = $total_hpp + $d->cost;

                @endphp
                <tr>
                    <td style="border-top:2px solid black;">{{ $d->id }}</td>
                    <td style="border-top:2px solid black;">{{ $d->name }}</td>
                    <td style="border-top:2px solid black;">{{ $d->code }}</td>
                    <td style="border-top:2px solid black;">{{ $cat_name }}</td>
                    <td style="border-top:2px solid black;">{{ $d->price }}</td>
                    <td style="border-top:2px solid black;">{{ $d->cost }}</td>
                    <td style="border-top:2px solid black;">{{ $d->quantity }}</td>
                    <td style="border-top:2px solid black;">{{ $d->unit }}</td>
                    <td style="border-top:2px solid black;">{{ $nilai_barang }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="border-top:2px solid black;"><strong></strong></td>
                <td colspan="2" style="border-top:2px solid black;"><strong>TOTAL</strong></td>
                <td style="border-top:2px solid black;"><strong>{{ $total_harga }}</strong></td>
                <td style="border-top:2px solid black;"><strong>{{ $total_hpp }}</strong></td>
                <td style="border-top:2px solid black;"><strong>{{ $total_stock }}</strong></td>
                <td style="border-top:2px solid black;"><strong></strong></td>
                <td style="border-top:2px solid black;"><strong>{{ $total_nilai_barang }}</strong></td>
            </tr>
            <tr>

                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </table>


    </div>

</body>

</html>
