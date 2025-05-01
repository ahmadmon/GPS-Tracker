@php use App\Enums\DeviceBrand; @endphp
@extends('01-layouts.master')

@section('title', 'ویرایش دستگاه')

@section('content')
{{--@dd($errors->all())--}}
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
                            <a href="{{ route('device.index') }}">دستگاه ها</a>
                        </li>
                        <li class="breadcrumb-item dana txt-dark">ویرایش دستگاه</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @csrf

    <div class="card">
        <div class="card-body">
            <div class="card-wrapper border rounded-3">
                <form action="{{ route('device.update', $device->id) }}" method="POST" class="row">
                    @csrf
                    @method('PUT')
                    <div class="col-12 mb-3">
                        <label class="form-label" for="name">نام
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="name" name="name" value="{{ old('name',$device->name) }}" type="text"
                               placeholder="نام دستگاه را وارد کنید">
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="model">مدل
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="model" name="model" type="text" value="{{ old('model',$device->model) }}"
                               placeholder="مدل دستگاه را وارد کنید">
                        <x-input-error :messages="$errors->get('model')" class="mt-2"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="serial">شماره سریال (IMEI)
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="serial" name="serial" type="number" value="{{ old('serial',$device->serial) }}"
                               placeholder="شماره سریال دستگاه را وارد کنید">
                        <x-input-error :messages="$errors->get('serial')" class="mt-2"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="phone_number">شماره سیم کارت
                        </label>
                        <input class="form-control" dir="ltr" id="phone_number" name="phone_number" type="number"
                               value="{{ old('phone_number',$device->phone_number) }}"
                               placeholder="09123456789">
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2"/>
                    </div>

                    @notRole(['user'])
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_id">کاربر
                            <sup class="text-danger">*</sup>
                        </label>
                        <x-partials.alpine.input.select-option :options="$users->pluck('name', 'id')->toArray()"
                                                               name="user_id" :value="$device->user_id"/>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2"/>
                    </div>
                    @endnotRole

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_id">مختص به وسیله نقلیه
                            <sup class="text-danger">*</sup>
                        </label>
                        @php
                            $options = $vehicles->mapWithKeys(fn($item) => [$item->id => implode(' - ', [$item->name , $item->license_plate])])->toArray();
                        @endphp
                        <x-partials.alpine.input.select-option :$options
                                                               name="vehicle_id" :value="$device->vehicle_id"/>
                        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2"/>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="brand">برند
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="brand" id="brand">
                            <option value="">برند را انتخاب کنید...</option>
                            <option
                                value="{{ DeviceBrand::SINOTRACK->value }}" @selected(old('brand', $device->brand->value) == DeviceBrand::SINOTRACK->value)>
                                سینوترک (sinotrack)
                            </option>
                            <option
                                value="{{ DeviceBrand::WANWAY->value }}" @selected(old('brand', $device->brand->value) == DeviceBrand::WANWAY->value)>
                                ون وی (wan way)
                            </option>
                            <option
                                value="{{ DeviceBrand::CONCOX->value }}" @selected(old('brand', $device->brand->value) == DeviceBrand::CONCOX->value)>
                                کانکاکس (concox)
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('brand')" class="mt-2"/>
                    </div>


                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="status">وضعیت
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="status" id="status">
                            <option value="0" @selected(old('status',$device->status) == 0)>غیر فعال</option>
                            <option value="1" selected @selected(old('status',$device->status) == 0)>فعال</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary" type="submit">ویـــرایش</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
