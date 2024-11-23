@extends('01-layouts.master')

@section('title', 'ویرایش سازمان')

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
                            <a href="{{ route('company.index') }}">
                                لیست سازمان ها
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">ویرایش سازمان</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('company.update', $company->id) }}" method="POST" class="row" autocomplete="off" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">اطلاعات کلی</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="profile-title">
                                <div x-data="previewImage($refs.input)">
                                    <div class="d-flex">
                                        <label>
                                            <img class="img-70 rounded me-2 cursor-pointer img-opacity object-fit-cover" alt=""
                                                 :src="imageSrc">
                                            <input type="file" name="logo" class="d-none" x-ref="input" @change="upload" accept="image/*">
                                        </label>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">لوگو سازمان</h5>
                                            <small class="text-muted">حجم مجاز تا 2 مگابایت | فایل های مجاز:
                                                (JPG,JPEG,WEBP,PNG)</small>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('logo')" class="mt-2"/>
                                    <button type="button" class="d-block btn btn-sm btn-danger-gradien w-100 my-2" @click="reset">بازنشانی تصویر</button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="name">نام سازمان
                                <sup class="text-danger">*</sup>
                            </label>
                            <input class="form-control" id="name" name="name" value="{{ old('name', $company->name) }}" type="text">
                            <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">بیو (توضیحات)</label>
                            <textarea id="bio" name="bio" class="form-control" rows="3">{{ old('bio', $company->bio) }}</textarea>
                            <x-input-error :messages="$errors->get('bio', $company->bio)" class="mt-2"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact_number">شماره تماس
                                <sup class="text-danger">*</sup>
                            </label>
                            <small class="text-muted d-block">شماره همراه یا شماره ثابت به همراه پیش شماره</small>
                            <input class="form-control" id="contact_number" dir="ltr" name="contact_number" value="{{ old('contact_number', $company->contact_number) }}"
                                   type="text">
                            <x-input-error :messages="$errors->get('contact_number')" class="mt-2"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">مدیریت و آدرس</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">آدرس دفتر مرکزی
                                    <sup class="text-danger">*</sup>
                                </label>
                                <small class="text-muted d-block">استان - شهر - خیابان اصلی - خیابان فرعی - پلاک - واحد</small>
                                <textarea id="address" name="address" class="form-control" rows="3">{{ old('address', $company->address) }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2"/>
                            </div>

                            @notRole(['manager'])
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="user_id">مدیر سازمان
                                    <sup class="text-danger">*</sup>
                                </label>
                                <x-partials.alpine.input.select-option name="user_id" :options="$managers->pluck('name' , 'id')->toArray()" :value="$company->user_id"/>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2"/>
                            </div>
                            @endnotRole

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="status">وضعیت
                                    <sup class="text-danger">*</sup>
                                </label>
                                <select class="form-select" name="status" id="status">
                                    <option value="0" @selected(old('status', $company->status) == 0)>غیر فعال</option>
                                    <option value="1" selected @selected(old('status', $company->status) == 1)>فعال</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-2">
            <button class="btn btn-primary" type="submit">ویرایش</button>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('previewImage', (input) => ({
                defalutImage: '{{ asset($company->logo ?? 'assets/images/custom/workplace-512px.png') }}',
                imageSrc: '',

                init() {
                    this.imageSrc = this.defalutImage;
                },

                upload() {
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imageSrc = e.target.result;
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                },

                reset() {
                    this.imageSrc = this.defalutImage;
                    input.value = '';
                }
            }));
        })
    </script>
@endpush
