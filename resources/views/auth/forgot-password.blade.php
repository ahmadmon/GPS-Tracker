@extends('layouts.auth.auth')
@section('title', 'فراموشی گذرواژه')


@section('content')
    <div class="row m-0">
        <div class="col-xl-7 p-0">
            <img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/2.jpg') }}" alt="صفحه ورود"></div>
        <div class="col-xl-5 p-0">
            <div class="login-card login-dark">
                <div>
                    <div>
                        <a class="logo text-start" href="#">
                            <img class="img-fluid for-light" width="65" src="{{ asset('assets/images/logo/aron.webp') }}"
                                 alt="صفحه ورود">
                            <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                                 alt="صفحه ورود"></a>
                    </div>
                    <div class="login-main">
                        <form action="{{ route('password.email') }}" class="theme-form" method="POST">
                            @csrf
                            <h4>گذرواژه خود را بازنشانی کنید</h4>
                            <p>برای تغییر گذرواژه, شماره موبایل خود را وارد کنید.</p>

                            <x-partials.alert.error-alert />

                            <div class="form-group">
                                <label for="phone" class="col-form-label">شماره موبایل</label>
                                <input class="form-control txt-dark fw-bold" id="phone" dir="ltr" type="text" name="phone"
                                       value="{{ old('phone') }}" autofocus autocomplete="phone"
                                       placeholder="09123456789">
                                <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
                            </div>

                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block dana w-100" type="submit">ادامه
                                </button>
                            </div>

                            <p class="mt-4 mb-0 text-center">
                                <a class="ms-2" href="{{ route('login') }}">بازگشت به صفحه ورود</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
