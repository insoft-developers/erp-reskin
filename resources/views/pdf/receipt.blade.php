<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
    <style>
        /* Reset margin and padding for all elements */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8px;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 45mm;
            /* Ubah ke 45mm */
            padding: 2mm;
        }

        hr.dashed {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .mb-4 {
            margin-bottom: 4mm;
        }

        .mb-2 {
            margin-bottom: 2mm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 1mm 0;
            /* Mengatur padding antar sel */
            vertical-align: top;
            /* Menyelaraskan konten ke atas */
        }

        tr {
            margin-bottom: 2mm;
            /* Mengatur margin antar baris */
        }

        /* Menambahkan titik pada elemen li dengan prioritas tinggi */
        ul.disc {
            list-style-type: disc !important;
            padding-left: 10px !important;
            /* Mengatur jarak indentasi */
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <div class="container" style="max-width: 190px; padding-left: 15px">
        <div class="text-center mb-4">
            <h1 class="text-lg font-bold">{{ $bussines_name }}</h1>
            <p>{{ $bussines_address }}</p>
        </div>
        <hr class="dashed">
        <div class="mb-2">
            <table>
                <tr>
                    <th class="text-left">No Nota:</th>
                    <td>{{ $no_nota }}</td>
                </tr>
                <tr>
                    <th class="text-left">Waktu Pesanan:</th>
                    <td>{{ $waktu_pesanan }}</td>
                </tr>
                @if ($branch)
                    <tr>
                        <th class="text-left">Cabang:</th>
                        <td>{{ $branch }}</td>
                    </tr>
                @endif
                <tr>
                    <th class="text-left">Kasir:</th>
                    <td>{{ $kasir }}</td>
                </tr>
                @if ($meja)
                    <tr>
                        <th class="text-left">Meja:</th>
                        <td>{{ $meja }}</td>
                    </tr>
                @endif
                <tr>
                    <th class="text-left">Nama Konsumen:</th>
                    <td>{{ $nama_konsumen }}</td>
                </tr>
                <tr>
                    <th class="text-left">Pembayaran:</th>
                    <td>{{ $pembayaran }}</td>
                </tr>
                <tr>
                    <th class="text-left">Flag:</th>
                    <td>{{ $flag }}</td>
                </tr>
            </table>
        </div>
        <hr class="dashed">
        <div class="mb-2">
            <table>
                <thead>
                    <tr>
                        @if ($with_check === true)
                            <th style="padding-right: 15px"></th>
                        @endif
                        <th class="text-left">Item</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $item)
                        <tr>
                            @if ($with_check === true)
                                <td>
                                    <div style="width: 12px; height: 12px; border: 1px solid gray"></div>
                                </td>
                            @endif
                            <td>
                                <div>{{ $item->product->name }} ({{ $item->quantity ?? 0 }})</div>
                                @if ($item->note)
                                    <div style="font-size: 7px; color: #777">{{ $item->note }}</div>
                                @endif
                                <ul class="disc">
                                    @foreach ($item->variant as $variant)
                                        @if ($variant->quantity > 0)
                                            <li>
                                                <div>{{ $variant->variant->varian_name }} ({{ $variant->quantity }})
                                                </div>
                                                @if ($variant->note)
                                                    <div style="font-size: 7px; color: #777">{{ $variant->note }}</div>
                                                @endif
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-right">
                                {{ number_format($item->price, 0, ',', '.') }}
                                <ul>
                                    @foreach ($item->variant as $variant)
                                        @if ($variant->quantity > 0)
                                            @if ($variant->note)
                                                <li style="font-size: 7px; opacity: 0">-</li>
                                            @endif
                                            <li style="color: #444">
                                                {{ number_format($variant->price, 0, ',', '.') }}
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-right">
                                <div>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
                                <ul>
                                    @foreach ($item->variant as $variant)
                                        @if ($variant->quantity > 0)
                                            @if ($variant->note)
                                                <li style="font-size: 7px; opacity: 0">-</li>
                                            @endif
                                            <li style="color: #444">
                                                {{ number_format($variant->price * $variant->quantity, 0, ',', '.') }}
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr class="dashed">
        <div class="mb-2">
            <table>
                <tr>
                    <th class="text-left">Subtotal: <span style="font-weight: 400; color: #444">{{ $total_product }}
                            PRODUK</span></th>
                    <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @if ($diskon)
                    <tr>
                        <th class="text-left">Diskon: (-)</th>
                        <td class="text-right">{{ number_format($diskon, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($shipping)
                    <tr>
                        <th class="text-left">Shipping:</th>
                        <td class="text-right">{{ number_format($shipping, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($tax)
                    <tr>
                        <th class="text-left">Pajak:</th>
                        <td class="text-right">{{ number_format($tax, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if (!empty($pembulatan) && $pembulatan != 0)
                    <tr>
                        <th class="text-left">Pembulatan:</th>
                        <td class="text-right">{{ number_format($pembulatan, 0, ',', '.') }}</td>
                    </tr>
                @endif
                
                <tr>
                    <td colspan="2"><hr class="dashed"></td>
                </tr>
                <tr>
                    <th class="text-left">Total:</th>
                    <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
                </tr>
                @if (isset($total_bayar))
                    <tr>
                        <th class="text-left">Total Bayar:</th>
                        <td class="text-right">{{ number_format($total_bayar, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if (isset($kembalian) && $kembalian > 0)
                    <tr>
                        <th class="text-left">Kembalian:</th>
                        <td class="text-right">{{ number_format($kembalian, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        </div>
        <hr class="dashed">
        <div class="text-center">
            {{-- <p>Terima Kasih atas Kunjungan Anda</p>
            <p>Jika Ada Komplain, Silakan Hubungi WA: 081333362679</p>
            <p>Password WiFi: AKU SAYANG KAMU</p> --}}
            {!! $footer !!}
        </div>
    </div>
</body>

</html>
