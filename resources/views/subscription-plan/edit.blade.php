@php
    use App\Enums\Subscription\Plan\PlanType;
@endphp

@extends('01-layouts.master')

@section('title', "ویرایش ({$plan->name})")

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
                            <a href="{{ route('subscription-plan.index') }}">
                                طرح اشتراک
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">ویرایش طرح</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('subscription-plan.update', $plan->slug) }}" method="POST" class="row">
        @csrf
        @method('PUT')

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="m-b-0">اطلاعات اصلی</h5>
                </div>
                <div class="card-body">
                    <div class="card-wrapper border rounded-3 row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name">نام طرح
                                <sup class="text-danger">*</sup>
                            </label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $plan->name) }}" type="text"
                                   placeholder="مثلا طرح پایه...">
                            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="slug">نامک (اسلاگ)
                                <sup class="text-danger">*</sup>
                            </label>
                            <input class="form-control" dir="ltr" id="slug" name="slug"
                                   value="{{ old('slug', $plan->slug) }}" type="text"
                                   placeholder="basic-monthly">
                            <x-input-error :messages="$errors->get('slug')" class="mt-2"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">توضیحات</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-control"
                                      placeholder="مثلا دسترسی به تمامی امکانات اصلی برای یک ماه...">{{ old('description', $plan->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="m-b-0">اطلاعات تکمیلی</h5>
                </div>
                <div class="card-body">
                    <div class="card-wrapper border rounded-3 row">
                        <div class="mb-3" x-data="{
                                                init(){
                                                    new Cleave($refs.price,{
                                                        numeral: true
                                                    })
                                                }
                                              }">
                            <label class="form-label" for="price">قیمت (تومان)
                                <sup class="text-danger">*</sup>
                            </label>
                            <input type="text" class="form-control" value="{{ old('price', $plan->price) }}" x-ref="price" name="price" id="price" placeholder="مبلغ را وارد کنید...">
                            <x-input-error :messages="$errors->get('price')" class="mt-2"/>
                        </div>
                        <div x-data="{
                            isLifetime: @js(old('is_lifetime', (bool)$plan->is_lifetime)) ?? false
                        }">
                            <div class="media">
                                <label class="col-form-label m-r-10">مادام‌العمر</label>
                                <div class="media-body text-end icon-state">
                                    <label class="switch">
                                        <input type="checkbox" checked x-model="isLifetime" name="is_lifetime"><span
                                            class="switch-state"></span>
                                    </label>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('is_lifetime')" class="mt-2"/>
                            <div class="mb-3">
                                <small class="text-muted">فعال کردن این گزینه به این معناست که طرح اشتراک هیچ
                                    محدودیت زمانی ندارد و برای همیشه معتبر خواهد بود.</small>
                            </div>
                            <template x-if="!isLifetime">
                                <div class="mb-3">
                                    <label class="form-label" for="duration">مدت اعتبار (به روز)
                                        <sup class="text-danger">*</sup>
                                    </label>
                                    <input class="form-control" id="duration" name="duration"
                                           value="{{ old('duration', $plan->duration) }}" type="number"
                                           placeholder="30">
                                    <x-input-error :messages="$errors->get('duration')" class="mt-2"/>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="m-b-0">وضعیت و نوع</h5>
                </div>
                <div class="card-body">
                    <div class="card-wrapper border rounded-3 row">
                        <div class="mb-3">
                            <label class="form-label" for="type">نوع
                                <sup class="text-danger">*</sup>
                            </label>
                            <select class="form-select" name="type" id="type">
                                @foreach(PlanType::toSelectOptions() as $type)
                                    <option value="{{ $type['value'] }}" @selected(old('type', $plan->type->value) == $type['value'])>{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="status">وضعیت
                                <sup class="text-danger">*</sup>
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="0" @selected(old('status', $plan->status) == 0)>غیر فعال</option>
                                <option value="1" @selected(old('status', $plan->status) == 1)>فعال</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="col-12 mt-2">
            <button class="btn btn-primary" type="submit">ثبت</button>
        </div>
    </form>

    @push('scripts')
        <!-- Page js-->
        <script src="{{ asset('assets/js/cleave/cleave.min.js') }}"></script>
    @endpush

@endsection
