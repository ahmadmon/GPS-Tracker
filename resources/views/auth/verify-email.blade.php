@extends('layouts.auth.auth')
@section('title', 'احراز هویت')


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
                        <form action="{{ route('verification.send') }}" class="theme-form" method="POST">
                            @csrf
                            <h4>تایید پست الکترونیک</h4>
                            <small class="d-block mb-4">
                                ممنون از ثبت نام شما! قبل از شروع، آیا می‌توانید آدرس ایمیل خود را با کلیک روی لینک
                                ارسالی از طرف ما تأیید کنید؟ اگر ایمیل را دریافت نکردید، با کمال میل ایمیل دیگری برای
                                شما ارسال خواهیم کرد.
                            </small>

                            @if (session('status') == 'verification-link-sent')
                                <div class="mb-4 fw-bold text-success">
                                    لینک تایید جدیدی به آدرس ایمیلی که هنگام ثبت‌نام وارد کردید، ارسال شده است.
                                </div>
                            @endif


                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block samim w-100" type="submit">ارسال مجدد لینک
                                    تایید
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <p class="mt-4 mb-0 text-center">
                                <a type="button" onclick="$(this).parent().parent().submit()">خروج از حساب
                                </a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



{{--<x-guest-layout>--}}
{{--    <div class="mb-4 text-sm text-gray-600">--}}
{{--        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}--}}
{{--    </div>--}}

{{--    @if (session('status') == 'verification-link-sent')--}}
{{--        <div class="mb-4 font-medium text-sm text-green-600">--}}
{{--            {{ __('A new verification link has been sent to the email address you provided during registration.') }}--}}
{{--        </div>--}}
{{--    @endif--}}

{{--    <div class="mt-4 flex items-center justify-between">--}}
{{--        <form method="POST" action="{{ route('verification.send') }}">--}}
{{--            @csrf--}}

{{--            <div>--}}
{{--                <x-primary-button>--}}
{{--                    {{ __('Resend Verification Email') }}--}}
{{--                </x-primary-button>--}}
{{--            </div>--}}
{{--        </form>--}}

{{--        <form method="POST" action="{{ route('logout') }}">--}}
{{--            @csrf--}}

{{--            <button type="submit"--}}
{{--                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">--}}
{{--                {{ __('Log Out') }}--}}
{{--            </button>--}}
{{--        </form>--}}
{{--    </div>--}}
{{--</x-guest-layout>--}}
