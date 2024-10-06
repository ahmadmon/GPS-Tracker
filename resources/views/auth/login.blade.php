@extends('layouts.auth.auth')
@section('title', 'ورود')


@section('content')
    <div class="row m-0">
        <div class="col-xl-7 p-0">
            <img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/2.jpg') }}" alt="صفحه ورود"></div>
        <div class="col-xl-5 p-0">
            <div class="login-card login-dark">
                <div>
                    <div>
                        <a class="logo text-start" href="#">
                            <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}"
                                 alt="صفحه ورود">
                            <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                                 alt="صفحه ورود"></a>
                    </div>
                    <div class="login-main">
                        <form action="{{ route('login') }}" class="theme-form" method="POST">
                            @csrf
                            <h4>وارد حساب کاربری شوید</h4>
                            <p>پست‌الکترونیک و گذرواژه خود را برای ورود وارد کنید</p>
                            <div class="form-group">
                                <label for="email" class="col-form-label">پست الکترونیک</label>
                                <input class="form-control" id="email" dir="ltr" type="email" name="email"
                                       value="{{ old('email') }}" autofocus autocomplete="username"
                                       placeholder="example@gmail.com">
                                <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-form-label">گذرواژه</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" dir="ltr" id="password" name="password"
                                           autocomplete="current-password"
                                           placeholder="************">
                                    <div class="show-hide"><span class="show"></span></div>
                                </div>
                                <x-input-error :messages="$errors->get('password')"/>
                            </div>
                            <div class="form-group mb-0">
                                <div class="checkbox p-0">
                                    <input id="checkbox1" type="checkbox" name="remember" @checked(old('remember'))>
                                    <label class="text-muted" for="checkbox1">من را به خاطر بسپار</label>
                                    <x-input-error :messages="$errors->get('remember')"/>
                                </div>
                                <button class="btn btn-primary btn-block dana w-100" type="submit">ورود به حساب
                                </button>
                            </div>
                            <h6 class="text-muted mt-4 or fw-bolder">یا ورود کنید با</h6>
                            <div class="social mt-4">
                                <div class="btn-showcase">
                                    <a class="btn btn-light w-100 dana" href="{{ route('social-login', 'google') }}">
                                        <i class="txt-google-plus" data-feather="at-sign"></i> حساب گوگل
                                    </a>
                                </div>
                            </div>
                            <p class="mt-4 mb-0 text-center">حساب کاربری ندارید؟
                                <a class="ms-2" href="{{ route('register') }}">ثبت نام کنید</a>
                            </p>
                            <p class="mt-3 mb-0 text-center">گذرواژه را فراموش کرده اید؟
                                <a class="ms-2" href="{{ route('password.request') }}">بازیابی گذرواژه</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
