@extends('layouts.auth.auth')
@section('title', 'سمفا - ورود')

@push('styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush


@section('content')
    <div class="row m-0">
        <div class="col-xl-7 p-0">
            <img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/police-amiat-eghtesadi.webp') }}" alt="صفحه ورود"></div>
        <div class="col-xl-5 p-0">
            <div class="login-card login-dark">
                <div>
                    <div class="d-flex justify-content-start align-items-center">
                        <a class="logo text-start me-2" href="javascript:void(0)">
                            <img class="img-fluid for-light" width="50"
                                 src="{{ asset('assets/images/logo/Economic_Security_Police_of_Iran-logo.png') }}"
                                 alt="صفحه ورود">
                            <img class="img-fluid for-dark" width="50" src="{{ asset('assets/images/logo/Economic_Security_Police_of_Iran-logo.png') }}"
                                 alt="صفحه ورود">
                        </a>
                        <h3 class="fw-bold pb-4">سَمـفـا - پلیس امنیت اقتصادی</h3>
                    </div>
                    <div class="login-main">
                        <form action="{{ route('login') }}" class="theme-form" method="POST" autocomplete="off"
                              autofocus
                              x-data="{ phone: @js(old('phone', request()?->phone)) || '', error: false }">
                            @csrf
                            <h4>وارد حساب کاربری شوید</h4>
                            <p>شماره موبایل و گذرواژه خود را برای ورود وارد کنید</p>

                            <x-partials.alert.error-alert/>

                            <div class="form-group">
                                <label for="phone" class="col-form-label">شماره موبایل</label>
                                <input class="form-control txt-dark fw-bold" id="phone" dir="ltr" type="text"
                                       name="phone"
                                       value="{{ old('phone', request()?->phone) }}" autofocus autocomplete="phone"
                                       x-model="phone"
                                       placeholder="09123456789">
                                <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
                                <div x-show="error" x-cloak>
                                    <span class="text-danger mt-1 fw-bold"
                                          x-text="'لطفا شماره موبایل, وارد کنید.'"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-form-label">گذرواژه</label>
                                <div class="form-input position-relative">
                                    <input class="form-control txt-dark fw-bold" type="password" dir="ltr" id="password"
                                           name="password"
                                           autocomplete="new-password"
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

{{--                                <a--}}

{{--                                    :href="phone.length === 11 ? `/otp/verification/${phone}` : 'javascript:void(0)'"--}}
{{--                                    @click="if(phone.length !== 11) error = true"--}}
{{--                                    class="btn btn-outline-primary btn-block dana w-100 mt-3">--}}
{{--                                    ورود با رمز یک‌بار مصرف--}}
{{--                                    <i class="fa fa-regular fa-chevron-left ms-1" style="font-size: 12px"></i>--}}
{{--                                </a>--}}
                            </div>

{{--                            <p class="mt-4 mb-0 text-center">حساب کاربری ندارید؟--}}
{{--                                <a class="ms-2" href="{{ route('register') }}">ثبت نام کنید</a>--}}
{{--                            </p>--}}
{{--                            <p class="mt-3 mb-0 text-center">گذرواژه را فراموش کرده اید؟--}}
{{--                                <a class="ms-2" href="{{ route('password.request') }}">بازیابی گذرواژه</a>--}}
{{--                            </p>--}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
