<!DOCTYPE html>
<html lang="fa" dir="rtl">
@include('01-layouts.head')
@stack('styles')

<body dir="rtl">
    <!-- loader starts-->
    <div class="loader-wrapper">
        <div class="loader-index"> <span></span></div>
        <svg>
            <defs></defs>
            <filter id="goo">
                <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
                <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo">
                </fecolormatrix>
            </filter>
        </svg>
    </div>
    <!-- loader ends-->

    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">

        <!-- Page Header Start-->
        @include('01-layouts.header')
        <!-- Page Header Ends -->


        <!-- Page Body Start-->
        <div class="page-body-wrapper horizontal-menu">

            <!-- Page Sidebar Start-->
            @include('01-layouts.sidebar')
            <!-- Page Sidebar Ends-->


            <div class="page-body">
                <div class="container-fluid">
                   @yield('content')
                </div>
            </div>

            <!-- footer start-->
            @include('01-layouts.footer')
            <!-- footer end-->

        </div>
    </div>



    @include('01-layouts.scripts')
    @stack('scripts')
</body>

</html>
