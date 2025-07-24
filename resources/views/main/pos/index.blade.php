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
                        <h5 class="m-b-10">POS</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">POS</a></li>
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
                                
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    <h2>Halaman POS</h2>
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