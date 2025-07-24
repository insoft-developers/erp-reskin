<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$data->invoice_number ?? $data->name ?? 'invoice'}}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap"> --}}
    
    <style>
        body{
            font-size: 12px !important;
        }
        .color-randu{
            color: #385a9c;
        }

        .bg-randu{
            background-color: #385a9c;
            color: white;
        }

        .bg-termin{
            background-color: #e67f24;
            color: white;
        }

        * {
            font-family: 'Poppins', sans-serif !important;
        }
    </style>
</head>

<body>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-12" style="border-bottom: 1px solid #a6a6a6; padding-bottom: 10px">
            <table style="width: 100%; margin-bottom: 15px">
                <tr>
                    <td>
                        <img src="{{ asset('storage/'.$data->logo) }}" width="100px" alt="">
                    </td>

                    <td class="text-end">
                        <div style="font-size: 16px; margin-bottom: 8px;"><strong>{{ $data->from_name }}</strong></div>
                        <div style="font-size: 14px;">{{ $data->from_address }}</div>
                        <div style="font-size: 14px;">{{ $data->from_email.', '.$data->from_phone }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row invoice-header" style="margin-bottom: 20px;">
        <div class="col-md-12">
            <div class="color-randu" style="font-weight: 800; font-size: 30px;"><b>{{ $data->is_quotation == 1 ? 'QUOTATION' : 'INVOICE' }}</b></div>
        </div>
    </div>

    <div class="row invoice-body" style="margin-bottom: 35px;">
        <table style="width: 100%; line-height: 10px;">
            <tr>
                <td style="font-size: 12px;">{{ $data->is_quotation == 1 ? 'Quotation' : 'Invoice' }} to:</td>
                <td class="text-end">
                    <strong>{{ $data->invoice_number }}</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="font-size: 14px; font-weight: 900; margin: 15px 0px 10px;"><b>{{ $data->client->name ?? null }}</b></div>
                </td>
                <td class="text-end">
                    <div>{{ \Carbon\Carbon::parse($data->created)->format('d M Y') }}</div>
                </td>
            </tr>
            <tr>
                <td style="font-size: 12px; line-height: 13px; width: 500px;">
                    {{ $data->client->address ?? null }}
                </td>
            </tr>
            <tr>
                <td style="font-size: 12px; padding-top: 5px;">
                    {{ $data->client->email ?? null }}, {{ $data->client->phone ?? null }}
                </td>
            </tr>
        </table>
    </div>

    <div class="row" style="padding-bottom: 25px; border-bottom: 1px dotted #a6a6a6;">
        <div class="col-md-12">
            <table class="table table-bordered table-striped table-invoice">
                <thead class="bg-randu">
                    <tr>
                        <th style="padding: 10px;">NO</th>
                        <th style="padding: 10px;">DESCRIPTION</th>
                        <th style="padding: 10px;">QTY</th>
                        <th style="padding: 10px;" class="text-end">PRICE</th>
                        <th style="padding: 10px;" class="text-end">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->invoiceDetail as $item)
                        <tr>
                            <td style="padding: 7px;" class="text-center">{{ $loop->iteration }}</td>
                            <td style="padding: 7px;">{{ $item->short_description }} <br> <small><i>{!! $item->description !!}</i></small></td>
                            <td style="padding: 7px;" class="text-center">{{ $item->qty }}</td>
                            <td style="padding: 7px;" class="text-end">{{ ($data->currency->symbol ?? null) .' '. number_format($item->price, 0, ',', '.') }}</td>
                            <td style="padding: 7px;" class="text-end">{{ ($data->currency->symbol ?? null) .' '. number_format($item->sub_total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row invoice-footer" style="margin-bottom: 60px; margin-top: 25px">
        <div class="col-md-12">
            <table width="100%">
                <tr>
                    <td colspan="2"><strong>PAYMENT METHOD</strong></td>
                    <td></td>

                    <td class="text-end">Sub Total:</td>
                    <td class="text-end">{{ ($data->currency->symbol ?? null) .' '. number_format($data->sub_total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    @if ($data->payment_method == 'bank-bca' ||
                        $data->payment_method == 'bank-bni' ||
                        $data->payment_method == 'bank-mandiri' ||
                        $data->payment_method == 'bank-bri' ||
                        $data->payment_method == 'bank-lain')
                        
                        <td colspan="2" style="font-size: 12px;">Bank Name: {{ $data->bank_name }} <br> Account Number: {{ $data->bank_number }} <br> Account Name: {{ $data->bank_owner }}</td>
                        <td></td>
                    @else
                        <td colspan="2" style="font-size: 12px;">
                            {{ paymentMethodCast($data->payment_method) }} 
                            @if ($data->payment_method == 'randu-wallet')
                                <a href="{{ $data->payment_url }}" _target="blank" style="color: #fff; background-color: #8dbb31; padding: 3px 10px; border-radius: 3px; text-decoration: none;">Pay Here</a></td>
                            @endif
                        <td></td>
                    @endif

                    <td class="text-end">Tax {{ $data->tax }}%:</td>
                    <td class="text-end">{{ ($data->currency->symbol ?? null) .' '. number_format($data->tax_amount, 0, ',', '.') }}</td>
                </tr>
                @if ($data->payment_method == 'bank-bca' ||
                    $data->payment_method == 'bank-bni' ||
                    $data->payment_method == 'bank-mandiri' ||
                    $data->payment_method == 'bank-bri' ||
                    $data->payment_method == 'bank-lain')
                    <tr>

                        <td colspan="3" style="font-size: 12px;"></td>
                        <td class="text-end">Discount</td>
                        <td class="text-end">{{ ($data->currency->symbol ?? null) .' '. number_format($data->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($data->payment_method == 'bank-bca' ||
                    $data->payment_method == 'bank-bni' ||
                    $data->payment_method == 'bank-mandiri' ||
                    $data->payment_method == 'bank-bri' ||
                    $data->payment_method == 'bank-lain')
                    {{-- <tr>
                        <td colspan="2" style="font-size: 12px;">Account Name: {{ $data->bank_owner }}</td>
                    </tr> --}}
                @endif
                
                <tr>
                    @if ($data->payment_method == 'bank-bca' ||
                        $data->payment_method == 'bank-bni' ||
                        $data->payment_method == 'bank-mandiri' ||
                        $data->payment_method == 'bank-bri' ||
                        $data->payment_method == 'bank-lain')
                        
                        <td style="padding-bottom: 30px; font-size: 12px;">{!! nl2br(e($data->additional_intruction)) !!}</td>
                    @else
                        <td style="padding-bottom: 30px; font-size: 12px;">{!! nl2br(e($data->additional_intruction)) !!}</td>
                        <td style="padding-bottom: 30px;" colspan="3" class="text-end">Discount {{ $data->discount_type == 'percent' ? "$data->discount_value%" : '' }}:</td>
                        <td style="padding-bottom: 30px;" class="text-end">{{ ($data->currency->symbol ?? null) .' '. number_format($data->discount_amount, 0, ',', '.') }}</td>
                    @endif
                </tr>
                <tr>
                    <td colspan="3" style="padding-bottom: 30px;"><b></b></td>
                    <td colspan="2" class="text-end bg-randu" style="padding: 10px;" width="300px;">TOTAL: {{ ($data->currency->symbol ?? null) .' '. number_format($data->grand_total, 0, ',', '.') }}</td>
                </tr>
                @if ($termin != [])
                    <tr>
                        <td colspan="3" style="padding-bottom: 10px;"><b></b></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding-bottom: 30px;"><b></b></td>
                        <td colspan="2" class="text-end bg-termin" style="padding: 10px;" width="600px;">Down Payment / Termin #{{ $termin->number }}: {{ ($data->currency->symbol ?? null) .' '. number_format($termin->nominal, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table width="100%">
                <tr>
                    <td width="500px"><strong>Notes:</strong></td>
                    <td class="text-end" rowspan="2" style="position: relative; padding: 0; text-align: right; vertical-align: bottom; padding-right: 5px; padding-bottom: 5px;"><strong>{{ $data->signature_name }} <br> {{ $data->signature_position }}</strong></td>
                </tr>
                <tr>
                    <td style="padding-bottom: 15px;">{!! $data->notes !!}</td>
                </tr>
                <tr style="border-top: 1px solid black;">
                    <td style="padding-bottom: 15px; padding-top: 15px;">
                        @if ($user->is_upgraded != 1)
                            Invoice Powered By: <a href="https://www.randu.co.id" target="_blank">www.randu.co.id</a>
                        @endif
                    </td>
                    <td class="text-end" style="padding-bottom: 15px; padding-top: 15px">Due Date: {{ \Carbon\Carbon::parse($data->due_date)->format('d M Y') }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
