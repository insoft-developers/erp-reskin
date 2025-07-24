@php
    use Carbon\Carbon;
@endphp
<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [Start] nxl-head-mobile-toggler !-->
            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <!--! [End] nxl-navigation-toggle !-->

        </div>
        <!--! [End] Header Left !-->
        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">

<div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
    <a href="{{ route('storefront', session('username')) }}" class="text-warning d-flex align-items-center" target="_blank" rel="noopener noreferrer">
        <i class="feather-shopping-bag" style="margin-right: 5px;"></i> Lihat Toko Online
    </a>
    <a href="{{ url('/wallet-logs') }}" class="text-success d-flex align-items-center">
        <i class="feather-credit-card" style="margin-right: 5px;"></i> <span id="balance">Rp 0</span>
    </a>
</div>


                <div class="dropdown nxl-h-item">
                    <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button"
                        data-bs-auto-close="outside">
                        <i class="feather-bell"></i>
                        @if (markAsRead() == false)
                            <span class="badge bg-danger nxl-h-badge">0</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                        <div class="d-flex justify-content-between align-items-center notifications-head">
                            <h6 class="fw-bold text-dark mb-0">Notifications</h6>
                            <a href="javascript:void(0);" onclick="markAllAsRead()"
                                class="fs-11 text-success text-end ms-auto" data-bs-toggle="tooltip"
                                title="Make as Read">
                                <i class="feather-check"></i>
                                <span>Make as Read</span>
                            </a>
                        </div>
                        @foreach (notifications(3) as $item)
                            <a href="{{ route('notification.show', $item->id) }}">
                                <div class="notifications-item">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt=""
                                        class="rounded me-3 border" />
                                    <div class="notifications-desc">
                                        <div class="font-body text-truncate-2-line"> <span class="fw-semibold text-dark"
                                                style="{{ markAsRead() == true ? 'color: grey !important;' : 'color: black !important;' }}">{{ $item->title }}</span>
                                            <div
                                                style="{{ markAsRead() == true ? 'color: grey !important;' : 'color: black !important;' }}">
                                                {!! Str::limit($item->description, 50) !!}
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div
                                                class="notifications-date text-muted border-bottom border-bottom-dashed">
                                                {{ $item->created_at->diffForHumans() }}</div>
                                            <div class="d-flex align-items-center float-end gap-2">
                                                {{-- <a href="javascript:void(0);" class="d-block wd-8 ht-8 rounded-circle bg-gray-300" data-bs-toggle="tooltip" title="Make as Read"></a> --}}
                                                {{-- <a href="javascript:void(0);" class="text-danger" data-bs-toggle="tooltip" title="Remove"> --}}
                                                <i class="feather-x fs-12"></i>
                            </a>
                    </div>
                </div>
            </div>
        </div>
        </a>
        @endforeach
        <div class="text-center notifications-footer">
            <a href="{{ route('notification.index') }}" class="fs-13 fw-semibold text-dark">All Notifications</a>
        </div>
    </div>
    </div>
    <div class="dropdown nxl-h-item">
        <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
            @if (is_null($dataUser->profile_picture))
                <img src="{{ asset('template/main') }}/images/avatar/1.png" alt="user-image"
                    class="img-fluid user-avtar me-0" />
            @else
                <img src="{{ asset('storage/' . $dataUser->profile_picture) }}" alt="user-image"
                    class="img-fluid user-avtar me-0" />
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
            <div class="dropdown-header">
                <div class="d-flex align-items-center">
                    @if (is_null($dataUser->profile_picture))
                        <img src="{{ asset('template/main') }}/images/avatar/1.png" alt="user-image"
                            class="img-fluid user-avtar" />
                    @else
                        <img src="{{ asset('storage/' . $dataUser->profile_picture) }}" alt="user-image"
                            class="img-fluid user-avtar me-0" />
                    @endif
                    <div style="margin-left: 10px;">
                        <h6 class="text-dark mb-0">{{ session('name') }}</h6>
                        <span class="fs-12 fw-medium text-muted">{{ session('email') }}</span>
                        <div class="mt-1">
                            @if (isset($dataUser) && $dataUser->is_upgraded)
                                <span class="badge bg-soft-success text-success">{{ $premiumTitle }}</span> <span
                                    style="font-size: 12px; font-weight: 500; color: orange">{{ Carbon::parse($dataUser->upgrade_expiry)->format('d-m-Y') }}</span>
                            @else
                                <span class="badge bg-soft-danger text-danger">FREE ACCOUNT</span>
                            @endif
                        </div>
                        <div class="mt-2">
                            <b>Key:</b> {{ $dataUser->user_key ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>


            <a href="{{ route('account.profile.settings') }}" class="dropdown-item">
                <i class="feather-user"></i>
                <span>Pengaturan Akun</span>
            </a>
            @if (session('role') != 'staff')
            <div class="dropdown-divider"></div>
            <a href="{{ url('/setting') }}" class="dropdown-item">
                <i class="feather-settings"></i>
                <span>Pengaturan Aplikasi</span>
            </a>
            @endif
            <div class="dropdown-divider"></div>
            <a href="{{ url('frontend_logout') }}" class="dropdown-item">
                <i class="feather-log-out"></i>
                <span>Keluar</span>
            </a>
        </div>
    </div>
    </div>
    </div>
    <!--! [End] Header Right !-->
    </div>
</header>
