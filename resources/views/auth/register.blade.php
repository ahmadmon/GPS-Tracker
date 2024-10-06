@extends('layouts.auth.auth')
@section('title', 'ثبت نام')


@section('content')
    <div class="row m-0">
        <div class="col-xl-7 p-0">
            <img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/1.jpg') }}" alt="صفحه ورود"></div>
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
                        <form action="{{ route('register') }}" class="theme-form" method="POST">
                            @csrf
                            <h4>حساب خود را ایجاد کنید</h4>
                            <p>اطلاعات شخصی خود را برای ایجاد حساب وارد کنید</p>
                            <div class="form-group">
                                <label for="name" class="col-form-label pt-0">نام شما</label>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <input class="form-control" id="name" name="name" type="text" value="{{ old('name') }}"
                                               placeholder="نام و نام خانوادگی">
                                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-form-label">پست الکترونیک</label>
                                <input class="form-control" id="email" dir="ltr" type="email" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="example@gmail.com">
                                <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-form-label">گذرواژه</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" dir="ltr" id="password" name="password"
                                           autocomplete="new-password"
                                           placeholder="************">
                                    <div class="show-hide"><span class="show"></span></div>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation" class="col-form-label">تکرار گذرواژه</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" dir="ltr" id="password_confirmation"
                                           name="password_confirmation" autocomplete="new-password"
                                           placeholder="************">
                                    <div class="show-hide"><span class="show"></span></div>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
                            </div>
                            <div class="form-group mb-0">
                                <div class="checkbox p-0">
                                    <input id="checkbox1" type="checkbox" name="rule_confirmation" @checked(old('rule_confirmation'))>
                                    <label class="text-muted" for="checkbox1">موافق با<a class="ms-2" href="#">حریم
                                            خصوصی
                                            خط مشی</a></label>
                                    <x-input-error :messages="$errors->get('rule_confirmation')" class="mt-2"/>
                                </div>
                                <button class="btn btn-primary btn-block dana w-100" type="submit">ایجاد حساب</button>
                            </div>
                            <h6 class="text-muted mt-4 or fw-bolder">یا ثبت‌نام کنید با</h6>
                            <div class="social mt-4">
                                <div class="btn-showcase">
                                    <a class="btn btn-light w-100 dana" href="{{ route('social-login', 'google') }}">
                                        <i class="txt-google-plus" data-feather="at-sign"></i> حساب گوگل
                                    </a>
                                </div>
                            </div>
                            <p class="mt-4 mb-0 text-center">از قبل یک حساب دارید؟<a class="ms-2"
                                                                                     href="{{ route('login') }}">وارد
                                    شوید</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
