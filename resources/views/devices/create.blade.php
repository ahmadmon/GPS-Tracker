@extends('01-layouts.master')

@section('title', 'ایجاد دستگاه جدید')

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>ایجاد دستگاه</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <svg class="stroke-icon">
                                    <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                                </svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">دستگاه ها</li>
                        <li class="breadcrumb-item active dana">ایجاد دستگاه</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @csrf

    <div class="card">
        <div class="card-body">
            <div class="card-wrapper border rounded-3">
                <form action="{{ route('device.store') }}" method="POST" class="row">
                    @csrf
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="name">نام
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="name" name="name" value="{{ old('name') }}" type="text"
                               placeholder="نام دستگاه را وارد کنید">
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="model">مدل
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="model" name="model" type="text" value="{{ old('model') }}"
                               placeholder="مدل دستگاه را وارد کنید">
                        <x-input-error :messages="$errors->get('model')" class="mt-2"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="serial">شماره سریال (IMEI)
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="serial" name="serial" type="number" value="{{ old('serial') }}"
                               placeholder="شماره سریال دستگاه را وارد کنید">
                        <x-input-error :messages="$errors->get('serial')" class="mt-2"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="phone_number">شماره سیم‌کارت
                        </label>
                        <input class="form-control" dir="ltr" id="phone_number" name="phone_number" type="number"
                               value="{{ old('phone_number') }}"
                               placeholder="09123456789">
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2"/>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_id">خریدار
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="user_id" id="user_id">
                            <option value="0" selected>انتخاب کنید</option>
                            @foreach($users as $user)
                                <option
                                    value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user?->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2"/>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="status">وضعیت
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="status" id="status">
                            <option value="0" @selected(old('status') == 0)>غیر فعال</option>
                            <option value="1" selected @selected(old('status') == 0)>فعال</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary" type="submit">ایــــجاد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
