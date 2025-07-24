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
                    <li class="breadcrumb-item"><a href="{{ route('notification.index') }}">Notifikasi</a></li>
                    <li class="breadcrumb-item">Berita Dan Pengumuman</li>
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
                            <h5 class="card-title">Notifikasi</h5>
                        </div>


                        <!-- Notification List View -->
                        <div class="list-group">
                            @foreach ($data as $item)
                            <div class="row border-bottom p-3">
                                <div class="col-md-1 mb-1">
                                    <img src="{{ asset('storage/'.$item->image) }}" alt="Notification Image" width="100px">
                                </div>
                                <div class="col-md-11">
                                    <a href="{{ route('notification.show', $item->id) }}">
                                        <h5 class="mb-1">{{ $item->title }}</h5>
                                        <div class="mb-1">
                                            {!! Str::limit($item->description, 250) !!}
                                        </div>
                                        <small class="text-muted">{{ $item->created_at->diffForHumans() }}</small>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                <!-- [Recent Orders] end -->
                <!-- [Table] start -->
                <!-- [Table] end -->
            </div>

        </div>
        <!-- [ Main Content ] end -->

    </div>
</main>
@endsection
@section('js')

@endsection