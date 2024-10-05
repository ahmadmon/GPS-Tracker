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
                            <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}"
                                 alt="صفحه ورود">
                            <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                                 alt="صفحه ورود"></a>
                    </div>
                    <div class="login-main">
                        <form action="{{ route('password.email') }}" class="theme-form" method="POST">
                            @csrf
                            <h4>گذرواژه خود را بازنشانی کنید</h4>
                            <p>فقط آدرس ایمیل خود را به ما اطلاع دهید تا یک لینک بازنشانی گذرواژه برای شما ارسال کنیم که
                                به شما امکان انتخاب گذرواژه جدید را می‌دهد.</p>

                            <x-auth-session-status class="mb-4" :status="session('status')"/>

                            <div class="form-group">
                                <label for="email" class="col-form-label">پست الکترونیک</label>
                                <input class="form-control" id="email" dir="ltr" type="email" name="email"
                                       value="{{ old('email') }}" autofocus
                                       placeholder="example@gmail.com">
                                <x-input-error :messages="$errors->get('email')" class="mt-2"/>
                            </div>

                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block samim w-100" type="submit">ارسال لینک بازنشانی
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
