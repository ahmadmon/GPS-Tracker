@extends('layouts.auth.auth')
@section('title', 'بازنشانی گذرواژه')

@section('content')
    <div class="row m-0">
        <div class="col-xl-7 p-0">
            <img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/2.jpg') }}" alt="صفحه ورود"></div>
        <div class="col-xl-5 p-0">
            <div class="login-card login-dark">
                <div>
                    <div class="d-flex justify-content-start align-items-center">
                        <a class="logo text-start me-2" href="javascript:void(0)">
                            <img class="img-fluid for-light" width="50"
                                 src="{{ asset('assets/images/logo/samfa-logo.png') }}"
                                 alt="صفحه ورود">
                            <img class="img-fluid for-dark" width="50" src="{{ asset('assets/images/logo/samfa-logo.png') }}"
                                 alt="صفحه ورود">
                        </a>
                        <h3 class="fw-bold pb-4">سَمـفـا</h3>
                    </div>
                    <div class="login-main">
                        <form action="{{ route('password.store') }}" class="theme-form" method="POST">
                            @csrf
                            <h4>گذرواژه جدید خود را وارد کنید.</h4>

                            <div class="form-group">
                                <label for="phone" class="col-form-label">شماره موبایل</label>
                                <input class="form-control fw-bold disabled" id="phone" dir="ltr" type="text"
                                       name="phone"
                                       value="{{ $phone ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-form-label">گذرواژه جدید
                                <sup class="text-danger fw-bold">*</sup>
                                </label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" dir="ltr" id="password" name="password"
                                           autocomplete="new-password"
                                           placeholder="************">
                                    <div class="show-hide"><span class="show"></span></div>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation" class="col-form-label">تکرار گذرواژه جدید
                                    <sup class="text-danger fw-bold">*</sup>
                                </label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" dir="ltr" id="password_confirmation"
                                           name="password_confirmation" autocomplete="new-password"
                                           placeholder="************">
                                    <div class="show-hide"><span class="show"></span></div>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
                            </div>

                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block dana w-100" type="submit">بازنشانی و ورود
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
