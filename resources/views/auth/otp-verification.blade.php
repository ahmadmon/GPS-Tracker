@extends('layouts.auth.auth')
@section('title', 'تایید شماره موبایل')

@push('styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@section('content')
    <div class="row m-0">
        <div class="col-xl-7 p-0">
            <img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/2.jpg') }}" alt="صفحه ورود"></div>
        <div class="col-xl-5 p-0">
            <div class="login-card login-dark">
                <div>
                    <div>
                        <a class="logo text-start" href="#">
                            <img class="img-fluid for-light" width="65"
                                 src="{{ asset('assets/images/logo/aron.webp') }}"
                                 alt="صفحه ورود">
                            <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo_dark.png') }}"
                                 alt="صفحه ورود"></a>
                    </div>
                    <div class="login-main">
                        <h4>تایید شماره موبایل</h4>
                        <small class="d-block mb-4">
                            لطفا شماره موبایل خود را با وارد کردن کد ۴ رقمی که به شماره شما ارسال کردیم تایید کنید.
                        </small>

                        <x-partials.alert.success-alert/>

                        <div class="form-group mb-0">
                            <form method="POST" action="{{ route('verify-otp', $user) }}">
                                @csrf
                                <input type="hidden" name="recovery" value="{{ request()?->recovery }}">
                                <div class="form-group">
                                    <label class="col-form-label" for="otp_code">کد ارسال شده به شماره <span
                                            class="text-muted">{{ $user->phone }}</span> را وارد کنید.
                                    </label>
                                    <div class="row">
                                        <div class="col-12" x-data="{ otp: {{ old('otp_code') }} || '' }">
                                            <input class="form-control text-center mb-1"
                                                   autofocus autocomplete="otp_code"
                                                   x-model="otp" @keyup="if(otp.length === 4) $('form').submit()"
                                                   id="otp_code" name="otp_code"
                                                   value="{{ old('otp_code') }}"
                                                   type="number" placeholder="00 00">
                                            <x-input-error :messages="$errors->get('otp_code')" class="mt-2"/>
                                        </div>
                                        <div class="text-end mt-2" x-data="timer({{ $duration }})">
                                            <span class="fw-bold" x-show="seconds > 0"
                                                  x-text="minutes + ':' + seconds "></span>
                                            <a href="{{ route('verification-otp', $user->phone) }}"
                                               class="fw-bold text-info" x-show="seconds <= 0" x-cloak>
                                                <i class="fa fa-solid fa-rotate-left" style="font-size: 12px"></i>
                                                <small>ارسال مجدد کد تایید</small>
                                            </a>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-start">
                                                <button class="btn btn-primary w-100 btn-block m-t-10"
                                                        type="submit">تایید
                                                </button>
                                            </div>
                                            <a href="{{ route('login', ['phone' => $user->phone]) }}"
                                               class="btn btn-outline-primary btn-block dana w-100 mt-3">
                                                ورود با گذرواژه
                                                <i class="fa fa-regular fa-chevron-left" style="font-size: 12px"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>

                        <p class="mt-4 mb-0 text-center">
                            <a href="{{ route('login', ['phone' => $user->phone]) }}"
                               class="text-primary d-flex align-items-center justify-content-center">
                                ویرایش شماره
                                <i class="fa fa-solid fa-caret-left ms-1"></i>
                            </a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('timer', (duration) => ({
                time: duration,
                minutes: Math.floor(this.time / 60),
                seconds: this.time / 60,

                init() {
                    this.startTimer();
                },

                startTimer() {
                    const interval = setInterval(() => {
                        if (this.time > 0) {
                            this.time--;
                            this.minutes = Math.floor(this.time / 60);
                            this.seconds = this.time % 60;

                            this.seconds = this.seconds < 10 ? '0' + this.seconds : this.seconds;
                        } else {
                            clearInterval(interval);
                        }
                    }, 1000);
                }


            }))
        })
    </script>
@endpush
