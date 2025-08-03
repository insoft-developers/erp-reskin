@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
        
            <div style="background: whitesmoke;" class="content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Pengaturan Kode Rekening</h5>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    <table class="table table-hover">
                                        @foreach ($data['group'] as $index => $item)
                                            <tr onclick="on_account_item_click({{ $index }})">
                                                <td class="menu-report-row"><strong><span
                                                            class="report-menu-title">{{ $item }}</span></strong><br><span
                                                        class="report-menu-subtitle">Pengaturan {{ $item }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>

                                </div>
                            </div>

                        </div>
                    </div>


                    <!-- [Recent Orders] end -->
                    <!-- [] start -->
                </div>

            </div>
           

        
    </div>
@endsection
