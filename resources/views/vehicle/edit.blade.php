@extends('01-layouts.master')

@section('title', 'ویرایش وسیله ‌نقلیه')

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6">
                    <h3>ویرایش وسیله‌ نقلیه</h3>
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
                        <li class="breadcrumb-item dana">وسایل نقلیه</li>
                        <li class="breadcrumb-item active dana">ویرایش وسیله‌ نقلیه</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @csrf
    <a href="{{ route('vehicle.index') }}" class="btn btn-primary mb-4">
        <i class="fa fa-arrow-right me-1"></i>
        <span>بازگشت</span>
    </a>
    <div class="card">
        <div class="card-body">
            <div class="card-wrapper border rounded-3">
                <form action="{{ route('vehicle.update', $vehicle->id) }}" method="POST" class="row">
                    @csrf
                    @method('PUT')
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="name">نام وسیله نقلیه
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="name" name="name" value="{{ old('name', $vehicle->name) }}"
                               type="text"
                               placeholder="پژو 206 سفید">
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>


                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="license_plate">پلاک
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="license_plate" name="license_plate"
                               value="{{ old('license_plate', $vehicle->license_plate) }}" type="text"
                               placeholder="پلاک وسیله نقلیه را وارد کنید">
                        <x-input-error :messages="$errors->get('license_plate')" class="mt-2"/>
                    </div>


                    {{--                    <div class="col-md-6 mb-3" x-data="{type: 0}">--}}
                    {{--                        <label class="form-label" for="type">نوع وسیله نقلیه</label>--}}
                    {{--                        <select class="form-select" id="type" x-model="type"--}}
                    {{--                                @change="$dispatch('type-change', { type: type })">--}}
                    {{--                            <option value="0">خودرو</option>--}}
                    {{--                            <option value="1">موتورسیکلت</option>--}}
                    {{--                        </select>--}}
                    {{--                    </div>--}}

                    {{--                    <div--}}
                    {{--                        x-data="{ show: false }"--}}
                    {{--                        @type-change.window="show = $event.target.detail !== 0"></div>--}}
                    {{--                    <div class="col-md-6 mb-3">--}}
                    {{--                        <label class="form-label" for="license_plate">پلاک--}}
                    {{--                        </label>--}}
                    {{--                        <div class="input-group">--}}
                    {{--                            <input class="form-control text-center" type="number" name="license_plate[]"--}}
                    {{--                                   aria-label="license_plate" placeholder="10" value="{{ old('license_plate[0]') }}">--}}
                    {{--                            <input class="form-control text-center" type="number" name="license_plate[]"--}}
                    {{--                                   aria-label="license_plate" placeholder="000" value="{{ old('license_plate[1]') }}">--}}
                    {{--                            <input class="form-control text-center" type="text" name="license_plate[]"--}}
                    {{--                                   aria-label="license_plate" placeholder="الف" value="{{ old('license_plate[2]') }}">--}}
                    {{--                            <input class="form-control text-center" type="number" name="license_plate[]"--}}
                    {{--                                   aria-label="license_plate" placeholder="00" value="{{ old('license_plate[3]') }}"/>--}}
                    {{--                        </div>--}}
                    {{--                        <x-input-error :messages="$errors->get('license_plate')" class="mt-2"/>--}}
                    {{--                    </div>--}}

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_id">راننده
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="user_id" id="user_id">
                            <option value="" selected>انتخاب کنید</option>
                            @foreach($users as $user)
                                <option
                                    value="{{ $user->id }}" @selected(old('user_id', $vehicle->user_id) == $user->id)>{{ $user?->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2"/>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="status">وضعیت
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="status" id="status">
                            <option value="0" @selected(old('status', $vehicle->status) == 0)>غیر فعال</option>
                            <option value="1" selected @selected(old('status', $vehicle->status) == 0)>فعال</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary" type="submit">ویرایش</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
