<!DOCTYPE html>
<html lang="fa_IR" dir="rtl">
<head>
    @include('layouts.auth.head-tag')
    @stack('styles')
</head>
<body dir="rtl">
<!-- Auth page start-->

<div class="container-fluid p-0" >

    @yield('content')

</div>

@include('layouts.auth.scripts')
@stack('scripts')
</body>
</html>
