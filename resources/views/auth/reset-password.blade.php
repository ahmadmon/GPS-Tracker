@extends('layouts.auth.auth')
@section('title', 'بازنشانی گذرواژه')


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
                        <form action="{{ route('password.store') }}" class="theme-form" method="POST">
                            @csrf
                            <h4>گذرواژه جدید خود را وارد کنید.</h4>

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="form-group">
                                <label for="email" class="col-form-label">پست الکترونیک</label>
                                <input class="form-control" id="email" dir="ltr" type="email" name="email"
                                       value="{{ old('email', $request->email) }}" autofocus autocomplete="username">
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
                                <button class="btn btn-primary btn-block samim w-100" type="submit">بازنشانی و ورود
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
