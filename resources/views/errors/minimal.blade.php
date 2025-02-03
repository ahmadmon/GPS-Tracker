<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/logo/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/logo/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logo/favicon/favicon-16x16.png') }}">
    <link rel="icon" href="{{ asset('assets/images/logo/favicon/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon/favicon.ico') }}" type="image/x-icon">
    <title>@yield('title')</title>
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">
    <!-- Custom css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-style.css')}}">
</head>
<body>
<!-- page-wrapper Start-->
<div class="page-wrapper compact-wrapper" id="pageWrapper">
    <!-- error-layout start-->
    <div class="error-wrapper">
        <div class="container"><img class="img-100" src="{{ asset('assets/images/other-images/sad.png') }}" alt="">
            <div class="error-heading">
                <h2 class="headline font-primary">@yield('code')</h2>
            </div>
            <div class="col-md-8 offset-md-2">
                <p class="sub-content">@yield('message')</p>
            </div>
            <div><a class="btn btn-primary-gradien btn-lg" href="{{ route('home') }}">بازگشت به داشبورد</a></div>
        </div>
    </div>
    <!-- error-layout end-->
</div>
</body>
</html>
