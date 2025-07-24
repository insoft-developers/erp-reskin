@include('front_store.template.head');

<body>
    <div class="page-wraper">

        <!-- Preloader -->
        <div id="preloader">
            <div class="loader">
                <div class="load-circle">
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
        <!-- Preloader end-->

        <!-- Header -->
        @include('front_store.template.header_page');
        <!-- Header -->

        <!-- Page Content -->
        <div class="page-content">
            <div class="container bottom-content">
                @yield('content-page')
            </div>
        </div>
        <!-- Page Content End-->


        <!-- Menubar -->
        @if (!isset($menubar))
            @include('front_store.template.menu_bar')
        @endif
        <!-- Menubar -->

    </div>
    <!--**********************************
    Scripts
***********************************-->
    @include('front_store.template.script')
</body>

</html>
