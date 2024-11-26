@php use App\Enums\DeviceBrand; @endphp
@extends('01-layouts.master')

@section('title', 'تنظیمات دستگاه')

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
                        <li class="breadcrumb-item dana">
                            <a href="{{ route('device.index') }}">
                                دستگاه ها
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">تنظیمات دستگاه</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

        <!-- DEVICE INFO -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>اطلاعات دستگاه {{ $device->name }}</h5>
                @if(can('edit-device'))
                <div>
                    <a href="{{ route('device.edit', $device->id) }}" class="btn btn-sm btn-primary">ویرایش</a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="card-wrapper border row rounded-3">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="name">نام

                        </label>
                        <input class="form-control" id="name" value="{{ $device->name }}" type="text" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="model">مدل
                        </label>
                        <input class="form-control" id="model" value="{{ $device->model }}" type="text" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="serial">شماره سریال (IMEI)
                        </label>
                        <input class="form-control" id="serial" value="{{ $device->serial }}" disabled type="number">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="phone_number">شماره سیم‌کارت
                        </label>
                        <input class="form-control" dir="ltr" id="phone_number" value="{{ $device->phone_number }}" disabled type="number">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_id">خریدار
                        </label>
                        <input class="form-control" id="user_id" value="{{ $device->user->name }}" disabled type="text">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="password">رمز عبور</label>
                        <input class="form-control disabled" disabled id="password"
                               dir="{{ $device->password ? 'ltr' : 'rtl' }}"
                               value="{{  $device->password ?? 'رمز عبور پیشفرض' }}" type="text">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="brand">برند
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control disabled" disabled id="brand" value="{{  $device->brand }}"
                               type="text">
                    </div>
                </div>
            </div>
        </div>

    <!-- DEVICE CONNECTION -->
    <x-partials.alert.success-alert />
    <x-partials.alert.error-alert />

    <div class="card">
        <div class="card-header">
            <h5>دستورات دستگاه</h5>
        </div>
        <div class="card-body">
            <div class="card-wrapper border rounded-3">
                <form action="{{ route('device.store-sms', $device->id) }}" method="POST" id="form">
                    @csrf

                    @includeWhen($device->brand === DeviceBrand::SINOTRACK,'devices.partials.sinotrack-settings')
                    @includeWhen($device->brand === DeviceBrand::CONCOX,'devices.partials.concox-settings')
                    @includeWhen($device->brand === DeviceBrand::WANWAY,'devices.partials.wanway-settings')


                    <div class="col-12 mt-2 text-end">
                        <button class="btn btn-primary" type="submit">ثــبــت</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
