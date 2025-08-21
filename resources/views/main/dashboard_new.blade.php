@extends('main.master_new')

<!-- ========================
   Start Page Content
  ========================= -->

  @section('content')

<div class="page-wrapper">

    <!-- Start Content -->
    <div class="content">

        <!-- Start Breadcrumb -->
        <div class="d-flex d-block align-items-center justify-content-between flex-wrap gap-3 mb-3">
            <div>
                <h6>Dashboard</h6>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap gap-2">
                {{-- <div style="" id="reportrange" class="reportrange-picker d-flex align-items-center">
                    <i class="isax isax-calendar text-gray-5 fs-14 me-1"></i><span class="reportrange-picker-field">16 Apr
                        25 - 16 Apr 25</span>
                </div> --}}
                <div class="dropdown">
                    <a class="btn btn-primary d-flex align-items-center justify-content-center dropdown-toggle"
                        data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
                        Create New
                    </a>
                    <ul class="dropdown-menu dropdown-menu-start">
                        <li>
                            <a href="{{ url('journal_add') }}" class="dropdown-item d-flex align-items-center">
                                <i class="isax isax-document-text-1 me-2"></i>Buat Jurnal
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('pos/index') }}" class="dropdown-item d-flex align-items-center">
                                <i class="isax isax-money-send me-2"></i>Transaksi POS
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('storefront/setting') }}" class="dropdown-item d-flex align-items-center">
                                <i class="isax isax-money-add me-2"></i>Buat Toko Online
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('invoice/invoice/create') }}" class="dropdown-item d-flex align-items-center">
                                <i class="isax isax-money-recive me-2"></i>Buat Invoice
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('product/create') }}" class="dropdown-item d-flex align-items-center">
                                <i class="isax isax-document me-2"></i>Tambah Produk
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('adjustment/create') }}" class="dropdown-item d-flex align-items-center">
                                <i class="isax isax-document-download me-2"></i>Tambah Penyesuaian
                            </a>
                        </li>
                        
                    </ul>
                </div>

            </div>
        </div>
        <!-- End Breadcrumb -->

        <div style="background:#006ecc" class="rounded welcome-wrap position-relative mb-3">

            <!-- start row -->
            <div class="row">
                <div class="col-lg-8 col-md-9 col-sm-7">
                    <div>
                        <h5 class="text-white mb-1">Selamat Datang, {{ session('name') }}</h5>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <p class="d-flex align-items-center fs-13 text-white mb-0"><i
                                    class="isax isax-calendar5 me-1"></i>{{ date('l, d F Y') }}</p>
                            <p class="d-flex align-items-center fs-13 text-white mb-0"><i
                                    class="isax isax-clock5 me-1"></i>{{ date("H:i:s") }}</p>
                        </div>
                    </div>
                </div><!-- end col -->
            </div>
            <!-- end row -->

            <div class="position-absolute end-0 top-50 translate-middle-y p-2 d-none d-sm-block">
                <img src="{{ asset('reskin') }}/assets/img/icons/dashboard.svg" alt="img">
            </div>
        </div>

        <!-- start row -->
        <div class="row">
            <div class="col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="d-flex align-items-center mb-1"><i
                                    class="isax isax-category5 text-default me-2"></i>Overview</h6>
                        </div>
                        <div class="row g-4">
                            <div class="col-xl-6">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="avatar avatar-44 avatar-rounded bg-primary-subtle text-primary flex-shrink-0 me-2">
                                        <i class="isax isax-document-text-1 fs-20"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1 text-truncate">Invoices</p>
                                        <h6 class="fs-16 fw-semibold mb-0 text-truncate">{{ number_format($invoices) }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex align-items-center me-2">
                                    <span
                                        class="avatar avatar-44 avatar-rounded bg-success-subtle text-success-emphasis flex-shrink-0 me-2">
                                        <i class="isax isax-profile-2user fs-20"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1 text-truncate">Clients</p>
                                        <h6 class="fs-16 fw-semibold mb-0 text-truncate">{{ number_format($customers) }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="avatar avatar-44 avatar-rounded bg-warning-subtle text-warning-emphasis flex-shrink-0 me-2">
                                        <i class="isax isax-dcube fs-20"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1 text-truncate">Amount Due</p>
                                        <h6 class="fs-16 fw-semibold mb-0 text-truncate">{{ number_format($overdue_total) }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex align-items-center me-2">
                                    <span
                                        class="avatar avatar-44 avatar-rounded bg-info-subtle text-info-emphasis flex-shrink-0 me-2">
                                        <i class="isax isax-document-text fs-20"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1 text-truncate">Quotations</p>
                                        <h6 class="fs-16 fw-semibold mb-0 text-truncate">{{ number_format($quotations) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
            <div class="col-md-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="d-flex align-items-center mb-1"><i
                                    class="isax isax-chart-215 text-default me-2"></i>Sales Analytics</h6>
                        </div>
                        <div class="row g-4">
                            <div class="col-xl-6">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="avatar avatar-44 avatar-rounded bg-primary-subtle text-primary flex-shrink-0 me-2">
                                        <i class="isax isax-document-forward fs-20"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1 text-truncate">Total Sales</p>
                                        <h6 class="fs-16 fw-semibold mb-0">{{ number_format($penjualan_all) }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex align-items-center me-2">
                                    <span
                                        class="avatar avatar-44 avatar-rounded bg-success-subtle text-success-emphasis flex-shrink-0 me-2">
                                        <i class="isax isax-programming-arrow fs-20"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1 text-truncate">Purchase</p>
                                        <h6 class="fs-16 fw-semibold mb-0 text-truncate">{{ number_format($total_purchases) }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="avatar avatar-44 avatar-rounded bg-warning-subtle text-warning-emphasis flex-shrink-0 me-2">
                                        <i class="isax isax-dollar-circle fs-20"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1 mb-0">Expenses</p>
                                        <h6 class="fs-16 fw-semibold text-truncate">{{ number_format($expenses) }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex align-items-center me-2">
                                    <span
                                        class="avatar avatar-44 avatar-rounded bg-info-subtle text-info-emphasis flex-shrink-0 me-2">
                                        <i class="isax isax-flag fs-20"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1 text-truncate">Credits</p>
                                        <h6 class="fs-16 fw-semibold mb-0 text-truncate">{{ number_format($penjualan_not_paid) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
           
        </div>
        <!-- end row -->

        <!-- start row -->
        <div class="row">
            <div class="col-md-4 d-flex flex-column">
                <div class="card overflow-hidden z-1 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                            <div>
                                <p class="mb-1">Total Products</p>
                                <div class="d-flex align-items-center">
                                    <h6 class="fs-16 fw-semibold me-2">{{ number_format($products) }}</h6>
                                    <span class="badge badge-sm badge-soft-success"><i
                                            class="isax isax-arrow-up-15 ms-1"></i></span>
                                </div>
                            </div>
                            <span class="avatar avatar-lg bg-light text-dark avatar-rounded">
                                <i class="isax isax-document-text fs-16"></i>
                            </span>
                        </div>
                        <a href="{{ url('product') }}" class="fw-medium text-decoration-underline">View
                            All Product</a>
                    </div> <!-- end card body -->
                    <div class="position-absolute end-0 bottom-0 z-n1">
                        <img src="{{ asset('reskin') }}/assets/img/bg/card-bg-01.svg" alt="img">
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col -->
            <div class="col-md-4 d-flex flex-column">
                <div class="card overflow-hidden z-1 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                            <div>
                                <p class="mb-1">Total Product 1/2 Jadi</p>
                                <div class="d-flex align-items-center">
                                    <h6 class="fs-16 fw-semibold me-2">{{ $inters }}</h6>
                                    <span class="badge badge-sm badge-soft-success"><i
                                            class="isax isax-arrow-up-15 ms-1"></i></span>
                                </div>
                            </div>
                            <span class="avatar avatar-lg bg-light text-dark avatar-rounded">
                                <i class="isax isax-document-text fs-16"></i>
                            </span>
                        </div>
                        <a href="{{ url('inter_product') }}" class="fw-medium text-decoration-underline">View
                            All Product 1/2 Jadi</a>
                    </div> <!-- end card body -->
                    <div class="position-absolute end-0 bottom-0 z-n1">
                        <img src="{{ asset('reskin') }}/assets/img/bg/card-bg-02.svg" alt="img">
                    </div>
                </div> <!-- end card -->
            </div> <!-- end col -->
            <div class="col-md-4 d-flex flex-column">
                <div class="card overflow-hidden z-1 flex-fill">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between border-bottom mb-2 pb-2">
                            <div>
                                <p class="mb-1">Total Material</p>
                                <div class="d-flex align-items-center">
                                    <h6 class="fs-16 fw-semibold me-2">{{ $materials }}</h6>
                                    <span class="badge badge-sm badge-soft-success"><i
                                            class="isax isax-arrow-up-15 ms-1"></i></span>
                                </div>
                            </div>
                            <span class="avatar avatar-lg bg-light text-dark avatar-rounded">
                                <i class="isax isax-document-text fs-16"></i>
                            </span>
                        </div>
                        <a href="{{ url('main_material') }}" class="fw-medium text-decoration-underline">View All Material</a>
                    </div> <!-- end card body -->
                    <div class="position-absolute end-0 bottom-0 z-n1">
                        <img src="{{ asset('reskin') }}/assets/img/bg/card-bg-03.svg" alt="img">
                    </div>
                </div> <!-- end card -->
            </div>
        </div>
        <!-- end row -->

        <!-- start row -->
        <div class="row">
            <div class="col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body pb-0">
                        <div class="mb-3">
                            <h6 class="mb-1">Revenue</h6>
                        </div>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <p class="mb-1">Total Sales</p>
                                <div class="d-flex align-items-center">
                                    <h6 class="fs-16 fw-semibold me-2">{{ number_format($penjualan_all) }}</h6>
                                    <span class="badge badge-sm badge-soft-success"><i
                                            class="isax isax-arrow-up-15 ms-1"></i></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <p class="fs-13 text-dark d-flex align-items-center mb-0"><i
                                        class="fa-solid fa-circle text-primary-transparent fs-12 me-1"></i>Received
                                </p>
                                <p class="fs-13 text-dark d-flex align-items-center mb-0"><i
                                        class="fa-solid fa-circle text-primary fs-12 me-1"></i>Outstanding</p>
                            </div>
                        </div>
                        <div id="revenue_charts"></div>
                       
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
            <div class="col-xl-6 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="mb-1">Clients</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-nowrap table-borderless custom-table">
                                <tbody>
                                    @foreach($list_customers as $cl)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:void(0);"
                                                    class="avatar avatar-lg rounded-circle me-2 flex-shrink-0">
                                                    <img src="{{ asset('reskin') }}/assets/img/users/user-06.jpg"
                                                        class="rounded-circle" alt="img">
                                                </a>
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1"><a
                                                            href="javascript:void(0);">{{ $cl->name }}</a></h6>
                                                    
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="mb-1">{{ $cl->email }} </p>
                                            
                                        </td>
                                        <td>
                                           <p class="fs-13">{{ $cl->phone }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ url('invoice/client') }}" class="btn btn-light btn-lg w-100 text-decoration-underline mt-3">All
                            Clients</a>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div>
        <!-- end row -->

        <!-- start row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                            <h6 class="mb-1">Sales</h6>
                            <a href="{{ url('manajemen-pesanan') }}" class="btn btn-primary mb-1">View all Sales</a>
                        </div>
                        <div class="table-responsive no-filter no-pagination">
                            <table class="table table-nowrap border mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Created On</th>
                                        <th>Amount</th>
                                        <th>Paid</th>
                                        <th>Payment Method</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach($penjualan_8 as $p8)
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0);" class="link-default">{{ $p8->reference }}</a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-0">
                                                        <a href="javascript:void(0);">{{ $p8->customer_id == null ? $p8->cust_name : $p8->customer->name }}</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ date('d M Y', strtotime($p8->created_at)) }}</td>
                                        <td class="text-dark">Rp. {{ number_format($p8->order_total) }}</td>
                                        <td>Rp. {{ number_format($p8->paid) }}</td>
                                        <td class="text-dark">{{ $p8->payment_method === 'randu-wallet' ? 'wallet' : $p8->payment_method }}</td>
                                        <td><?= $p8->detail ;?></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div>
        <!-- end row -->

        <!-- start row -->
        <div class="row">
            <div class="col-lg-12 col-xl-4 d-flex">
                <div class="card flex-fill">
                    <div class="card-body pb-1">
                        <div class="mb-3">
                            <h6 class="mb-1">Recent Transactions</h6>
                        </div>
                        <h6 class="fs-14 fw-semibold mb-3">Today</h6>

                        @foreach ($recent['today'] ?? [] as $p)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
                                    <img src="{{ asset('reskin') }}/assets/img/icons/transaction-01.svg"
                                        class="rounded-circle" alt="img">
                                </a>
                                <div>
                                    <h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">{{ $p->customer_id == null ? $p->cust_name : $p->customer->name }}</a></h6>
                                    <p class="fs-13"><a href="javascript:void(0);"
                                            class="link-default">#{{ $p->reference }}</a></p>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-lg badge-soft-success">{{ number_format($p->paid) }}</span>
                            </div>
                        </div>
                        @endforeach


                        <hr>
                        <h6 class="fs-14 fw-semibold mb-3">Yesterday</h6>

                        @foreach ($recent['yesterday'] ?? [] as $p)

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar avatar-md flex-shrink-0 me-2">
                                    <img src="{{ asset('reskin') }}/assets/img/icons/transaction-02.svg"
                                        class="rounded-circle" alt="img">
                                </a>
                                <div>
                                    <h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">{{ $p->customer_id == null ? $p->cust_name : $p->customer->name }}</a></h6>
                                    <p class="fs-13"><a href="javascript:void(0);"
                                            class="link-default">#{{ $p->reference }}</a></p>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-lg badge-soft-success">{{ number_format($p->paid) }}</span>
                            </div>
                        </div>

                        @endforeach
                       
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->

            <div class="col-md-6 col-xl-4 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="mb-1">Quotations</h6>
                        </div>

                        @foreach($q_list as $qlist)
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center">
                                <a href="javascript:void(0);" class="avatar avatar-lg flex-shrink-0 me-2">
                                    <img src="{{ asset('reskin') }}/assets/img/users/user-02.jpg"
                                        class="rounded-circle" alt="img">
                                </a>
                                <div>
                                    <h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">{{ $qlist->name }}</a></h6>
                                    <p class="fs-13">{{ $qlist->invoice_number }}</p>
                                </div>
                            </div>
                            <div class="text-end">
                                @if($qlist->status === 1)
                                <span
                                    class="badge badge-sm badge-soft-success d-inline-flex align-items-center mb-1">Paid<i
                                        class="isax isax-tick-circle ms-1"></i></span>

                                @else
                                <span
                                    class="badge badge-sm badge-soft-danger d-inline-flex align-items-center mb-1">Not Paid<i
                                        class="isax isax-delete-circle ms-1"></i></span>
                                @endif
                                <p class="fs-13">{{ date('d M Y', strtotime($qlist->due_date)) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
            <div class="col-md-6 col-xl-4 d-flex flex-column">
                
                <div class="card d-flex">
                    <div class="card-body flex-fill">
                        <h6 class="mb-3">Top Sales Statistics</h6>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-3">
                            <p class="d-flex align-items-center fs-13 text-dark mb-0"><i
                                    class="fa-solid fa-circle fs-8 me-1 text-pink"></i>Dell XPS 13</p>
                            <p class="d-flex align-items-center fs-13 text-dark mb-0"><i
                                    class="fa-solid fa-circle fs-8 me-1 text-secondary"></i>Nike T-shirt</p>
                            <p class="d-flex align-items-center fs-13 text-dark mb-0"><i
                                    class="fa-solid fa-circle fs-8 me-1 text-success"></i>Apple iPhone 15</p>
                        </div>
                        <div id="chart_sales"></div>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div>
        <!-- end row -->

        @include('component_new.footer')

    </div>
    <!-- End Content -->
</div>


@endsection