@extends('master')
@section('style')
<style>
@media (min-width: 375px) {
            .modal-dialog {
                max-width: 360px;
            }
        }
@media (min-width: 390px) {
    .modal-dialog {
        max-width: 370px;
    }
}

@media (min-width: 414px) {
    .modal-dialog {
        max-width: 390px;
    }
}
@media (min-width: 576px) {
            .modal-dialog {
                max-width: 500px;
            }
        }
        @media (min-width: 768px) {
            .modal-dialog {
                max-width: 700px;
            }
        }
        @media (min-width: 992px) {
            .modal-dialog {
                max-width: 900px;
            }
        }
        @media (min-width: 1200px) {
            .modal-dialog {
                max-width: 1100px;
            }
        }


</style>
@endsection
@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Staff</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Lihat Staff</a></li>
                        {{-- <li class="breadcrumb-item">Tables</li> --}}
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
                                <h5 class="card-title">Kelola Staff</h5>
                                <button onclick="create_staff()"
                                    class="avatar-text avatar-md bg-white pull-right;">
                                    <i class="feather-plus"></i>
                                </button>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <input type="hidden" name="user_id" id="userId" value="{{ session('id') }}">
                                            <select class="form-control cust-control" id="branchId">
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-control cust-control" id="position">
                                                <option value="">Pilih Posisi Staff </option>
                                            </select>
                                        </div>
                                        <div class="mtop20"></div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="text" id="cari" placeholder="Cari disini.."
                                                    class="form-control cust-control">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="mtop30"></div>
                                    <div class="table-responsive">
                                        <table id="table-staff" class="table table-striped mb-0">
                                            <thead>
                                                <tr class="border-b">
                                                    <th width="0%">ID</th>
                                                    <th width="3%">Nama Staff</th>
                                                    <th width="*">Email</th>
                                                    <th width="*">Posisi</th>
                                                    <th>Cabang</th>
                                                    <th width="*">Phone</th>
                                                    <th>Pin</th>
                                                    <th>Username</th>
                                                    <th width="15%">Tanggal Mulai Bekerja</th>
                                                    <th width="15%">Status</th>
                                                    <th width="15%" class="text-end">Opsi</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="mtop30"></div>
                                </div>
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