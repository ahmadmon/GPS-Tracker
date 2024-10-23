@extends('01-layouts.master')

@section('title', 'ویرایش کاربر')

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
                            <a href="{{ route('user.index') }}">
                                لیست کاربران
                            </a>
                            </li>
                        <li class="breadcrumb-item dana">ویرایش کاربر</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-wrapper border rounded-3">
                <form action="{{ route('user.update', $user->id) }}" method="POST" class="row" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="name">نام و نام‌خانوادگی
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}"
                               type="text">
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>


                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="phone">شماره تماس
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="phone" dir="ltr" name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               type="text">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
                    </div>



                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="user_type">نوع کاربر
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="user_type" id="user_type">
                            <option value="0" @selected(old('user_type', $user->user_type) == 0)>کاربر</option>
                            <option value="1" @selected(old('user_type', $user->user_type) == 1)>ادمین</option>
                        </select>
                        <x-input-error :messages="$errors->get('user_type')" class="mt-2"/>
                    </div>


                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="status">وضعیت
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="status" id="status">
                            <option value="0" @selected(old('status', $user->status) == 0)>غیر فعال</option>
                            <option value="1" @selected(old('status', $user->status) == 1)>فعال</option>
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
