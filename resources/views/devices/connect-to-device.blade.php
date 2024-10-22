@extends('01-layouts.master')

@section('title', 'فعال سازی دستگاه')

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-12 d-flex">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <svg class="stroke-icon">
                                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">دستگاه ها</li>
                        <li class="breadcrumb-item active dana txt-dark">فعال سازی دستگاه</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @csrf

        <!-- DEVICE INFO -->
        <div class="card">
            <div class="card-header">
                <h5>اطلاعات دستگاه {{ $device->name }}</h5>
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
                </div>
            </div>
        </div>

    <!-- DEVICE CONNECTION -->
    <x-partials.alert.success-alert />
    <x-partials.alert.error-alert />

    <div class="card">
        <div class="card-header">
            <h5>فعالسازی دستگاه</h5>
        </div>
        <div class="card-body">
            <div class="card-wrapper border rounded-3">
                <form action="{{ route('device.connect-to-device', $device->id) }}" method="POST" class="row">
                    @csrf
                    <div class="col-12 mb-3">
                        <label class="form-label" for="command">دستور مربوط به دستگاه را وارد کنید.
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="command" name="command" dir="ltr" value="{{ old('command') }}" type="text"
                               placeholder="">
                        <x-input-error :messages="$errors->get('command')" class="mt-2"/>
                    </div>


                    <div class="col-12 mt-2 text-end">
                        <button class="btn btn-primary" type="submit">ثــبــت</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
