@php
    use Carbon\Carbon;
@endphp
<!-- Topbar Start -->
<div class="header" style="background:#0c25df">
    <div class="main-header">

        <!-- Logo -->
        <div class="header-left">
            <a href="{{ url('/') }}" class="logo">
               <img src="{{ asset('reskin/assets/img/logo.png') }}?v={{ time() }}" alt="Logo">
            </a>
            <a href="{{ url('/') }}" class="dark-logo">
                <img src="{{ asset('reskin/assets/img/logo.png') }}?v={{ time() }}" alt="Logo">
            </a>
        </div>

        <!-- Sidebar Menu Toggle Button -->
        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>

        <div class="header-user" style="color:white;">
            <div class="nav user-menu nav-list">
                <div class="me-auto d-flex align-items-center" id="header-search">



                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-divide mb-0">
                            <li class="breadcrumb-item d-flex align-items-center"><a href="{{ url('/') }}"><i
                                        class="isax isax-home-2 me-1"></i>Dashboard</a></li>
                            
                        </ol>
                    </nav>

                </div>

                <div class="d-flex align-items-center">

                    <!-- Notification -->
                    <div class="notification_item me-2">
                        <a href="#" class="btn btn-menubar position-relative" id="notification_popup"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i style="color: white;" class="isax isax-notification-bing5"></i>
                            <span class="position-absolute badge bg-success border border-white"></span>
                        </a>
                        <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 300px;">

                            <div class="p-2 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold"> Notifications</h6>
                                    </div>
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle drop-arrow-none link-dark"
                                                data-bs-toggle="dropdown" data-bs-offset="0,15" aria-expanded="false">
                                                <i class="isax isax-setting-2 fs-16 text-body align-middle"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <!-- item-->
                                                <a onclick="markAllAsRead()" href="javascript:void(0);"
                                                    class="dropdown-item"><i class="ti ti-bell-check me-1"></i>Mark as
                                                    Read</a>
                                                <!-- item-->

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification Dropdown -->
                            <div class="notification-body position-relative z-2 rounded-0" data-simplebar>
                                @foreach (notifications(3) as $item)
                                    <!-- Item-->
                                    <a href="{{ route('notification.show', $item->id) }}"><div class="dropdown-item notification-item py-2 text-wrap border-bottom"
                                        id="notification-1">
                                        <div class="d-flex">
                                            <div class="me-2 position-relative flex-shrink-0">
                                                
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-0 fw-semibold text-dark">{{ $item->title }}</p>
                                                <p class="mb-1 text-wrap fs-14">
                                                    {!! Str::limit($item->description, 50) !!}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fs-12"><i class="isax isax-clock me-1"></i>{{ $item->created_at->diffForHumans() }}</span>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div></a>
                                @endforeach


                            </div>

                            <!-- View All-->
                            <div class="p-2 rounded-bottom border-top text-center">
                                <a href="{{ route('notification.index') }}" class="text-center fw-medium fs-14 mb-0">
                                    View All
                                </a>
                            </div>

                        </div>
                    </div>

                    <!-- Light/Dark Mode Button -->
                    <div class="me-2 theme-item">
                        <a href="javascript:void(0);" id="dark-mode-toggle" class="theme-toggle btn btn-menubar">
                            <i class="isax isax-moon"></i>
                        </a>
                        <a href="javascript:void(0);" id="light-mode-toggle" class="theme-toggle btn btn-menubar">
                            <i class="isax isax-sun-1"></i>
                        </a>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown profile-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <span class="avatar online">

                                @if (is_null($dataUser->profile_picture))
                                    <img src="{{ asset('template/main') }}/images/avatar/1.png" alt="Img"
                                        class="img-fluid user-avtar">
                                @else
                                    <img src="{{ asset('storage/' . $dataUser->profile_picture) }}" alt="Img"
                                        class="img-fluid user-avtar">
                                @endif
                            </span>
                        </a>
                        <div class="dropdown-menu p-2">
                            <div class="d-flex align-items-center bg-light rounded-1 p-2 mb-2">
                                <span class="avatar avatar-lg me-2">
                                    @if (is_null($dataUser->profile_picture))
                                        <img src="{{ asset('template/main') }}/images/avatar/1.png" alt="img"
                                            class="img-fluid user-avtar">
                                    @else
                                        <img src="{{ asset('storage/' . $dataUser->profile_picture) }}" alt="img"
                                            class="img-fluid user-avtar">
                                    @endif
                                </span>
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">{{ session('name') }}</h6>
                                    <p class="fs-13">{{ session('email') }}</p>
                                </div>
                            </div>

                            <!-- Item-->
                            {{-- <a class="dropdown-item d-flex align-items-center" href="account-settings.html">
                                 <i class="isax isax-setting me-2"></i>Pengaturan Aplikasi
                             </a> --}}

                            <!-- Item-->
                            <a class="dropdown-item d-flex align-items-center"
                                href="{{ route('account.profile.settings') }}">
                                <i class="isax isax-profile-circle me-2"></i>Pengaturan Akun
                            </a>

                            <!-- Item-->
                            {{-- <div
                                 class="form-check form-switch form-check-reverse d-flex align-items-center justify-content-between dropdown-item mb-0">
                                 <label class="form-check-label" for="notify"><i
                                         class="isax isax-notification me-2"></i>Notifications</label>
                                 <input class="form-check-input" type="checkbox" role="switch" id="notify">
                             </div> --}}

                            <hr class="dropdown-divider my-2">

                            <!-- Item-->
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                @if (isset($dataUser) && $dataUser->is_upgraded)
                                    <div style="color: green;"><i class="isax isax-star me-2"></i>Premium (
                                        {{ Carbon::parse($dataUser->upgrade_expiry)->format('d-m-Y') }} )</div>
                                @else
                                    <div style="color: red;"><i class="isax isax-star me-2"></i>Free Account</div>
                                @endif
                            </a>

                            <hr class="dropdown-divider my-2">

                            <!-- Item-->
                            <a class="dropdown-item logout d-flex align-items-center"
                                href="{{ url('frontend_logout') }}">
                                <i class="isax isax-logout me-2"></i>Keluar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="dropdown mobile-user-menu profile-dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown"
                data-bs-auto-close="outside">
                <span class="avatar avatar-md online">
                    <img src="{{ asset('reskin') }}/assets/img/profiles/avatar-01.jpg" alt="Img"
                        class="img-fluid rounded-circle">
                </span>
            </a>
            <div class="dropdown-menu p-2 mt-0">
                <a class="dropdown-item d-flex align-items-center" href="profile.html">
                    <i class="isax isax-profile-circle me-2"></i>Profile Settings
                </a>
                <a class="dropdown-item d-flex align-items-center" href="reports.html">
                    <i class="isax isax-document-text me-2"></i>Reports
                </a>
                <a class="dropdown-item d-flex align-items-center" href="account-settings.html">
                    <i class="isax isax-setting me-2"></i>Settings
                </a>
                <a class="dropdown-item logout d-flex align-items-center" href="login.html">
                    <i class="isax isax-logout me-2"></i>Signout
                </a>
            </div>
        </div>
        <!-- /Mobile Menu -->

    </div>
</div>
<!-- Topbar End -->
