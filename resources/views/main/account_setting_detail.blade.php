@extends('master')

@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10"></h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('setting') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('account_setting') }}">Pengaturan Kode Rekening</a></li>
                        <li class="breadcrumb-item">{{ $title }}</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex d-md-none">
                            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">


                        </div>
                    </div>
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div class="main-content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">{{ $title }}</h5>
                                <a href="javascript:void(0);" onclick="add_item()"
                                    class="avatar-text avatar-md bg-default text-white pull-right;"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="feather-plus bg-dark"></i>
                                </a>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <form id="form-setting-account" method="POST">
                                    @csrf
                                    <input type="hidden" name="account_table" value="{{ $table }}">
                                    <input type="hidden" id="redirect-link" value="{{ url()->current() }}">
                                    <div class="container mtop30 main-box">
                                        <div class="row">
                                            <div class="col-md-12">
                                                @php
                                                    $nomor = $data->count();
                                                    $nomor2 = 12;

                                                @endphp
                                                @foreach ($data as $index => $item)
                                                    <div class="form-group mtop20">
                                                        @if ($item->can_be_deleted == 3)
                                                            <div class="row">

                                                                <div class="col-md-11">
                                                                    <input name="account_item[]"
                                                                        id="account_item_{{ $index }}" type="text"
                                                                        class="form-control cust-control"
                                                                        value="{{ $item->name }}">
                                                                </div>
                                                                <div class="col-md-1"><i
                                                                        onclick="account_delete({{ $item->id }}, {{ $item->account_code_id }})"
                                                                        class="fa fa-remove btn-hapus-akun"></i></div>
                                                            </div>
                                                        @else
                                                            <input readonly name="account_item[]"
                                                                id="account_item_{{ $index }}" type="text"
                                                                class="form-control cust-control"
                                                                value="{{ $item->name }}">
                                                        @endif

                                                        <input type="hidden" name="id[]" value="{{ $item->id }}">
                                                        <input type="hidden" name="account_code_id[]"
                                                            value="{{ $item->account_code_id }}">
                                                    </div>
                                                @endforeach
                                                <div id="setting-input-container"></div>
                                            </div>
                                        </div>

                                        <div class="row mtop20">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary pull-right">Simpan</button>
                                            </div>
                                        </div>

                                        <div class="mtop60"></div>

                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>


                    <!-- [Recent Orders] end -->
                    <!-- [] start -->
                </div>

            </div>
            <!-- [ Main Content ] end -->

        </div>
    </main>
@endsection
