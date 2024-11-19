@extends('01-layouts.master')

@section('title', 'پروفایل کاربری')

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-12 d-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">پروفایل کاربری</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">تغییر اطلاعات شما</h4>
                </div>
                <div class="card-body row">
                    <livewire:components.change-user-info :$user/>
                </div>
            </div>
        </div>

        <div class="col-12">
            <form action="{{ route('profile.change-password') }}" method="POST" class="row" autocomplete="off">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">تغییر گذرواژه</h4>
                    </div>
                    <div class="card-body">

                        <x-partials.alert.success-alert />

                        <div class="row">
                            <div class="form-group col-md-4 mb-3">
                                <label for="old_password" class="col-form-label">گذرواژه قبلی</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" dir="ltr" id="old_password"
                                           name="old_password"
                                           autocomplete="new-password">
                                </div>
                                <x-input-error :messages="$errors->get('old_password')" class="mt-2"/>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="password" class="col-form-label">گذرواژه جدید</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" dir="ltr" id="password" name="password"
                                           autocomplete="new-password" value="{{ old('password') }}"
                                           placeholder="************">
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="password_confirmation" class="col-form-label">تکرار گذرواژه جدید</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" dir="ltr" id="password_confirmation"
                                           name="password_confirmation" autocomplete="new-password" value="{{ old('password') }}"
                                           placeholder="************">
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2"/>
                            </div>
                            <div class="col-12">
                                <p class="mt-3 mb-0 text-end">گذرواژه قبلی را فراموش کرده اید؟
                                    <a class="ms-2" href="{{ route('profile.forgot-password') }}">بازیابی گذرواژه</a>
                                </p>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <button class="btn btn-primary" type="submit">تغییر</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')

@endpush
