@extends('01-layouts.master')

@section('title', 'جزئیات کاربر')


@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endpush

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
                        <li class="breadcrumb-item dana">جزئیات کاربر</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <!-- User Info -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="d-flex align-items-center">
                        <i data-feather="user" style="width: 20px; padding-bottom: 5px" class="me-1"></i>
                        <span>اطلاعات کاربر</span>
                    </h5>
                    @if(can('edit-user'))
                        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-primary">ویرایش</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="card-wrapper border rounded-3">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="name">نام و نام‌خانوادگی
                            </label>
                            <input class="form-control" id="name" value="{{ $user->name }}" type="text" disabled>
                        </div>


                        <div class="col-12 mb-3">
                            <label class="form-label" for="phone">شماره تماس
                            </label>
                            <input class="form-control" id="phone" dir="ltr" value="{{ $user->phone }}"
                                   type="text" disabled>
                        </div>


                        <div class="col-12 mb-3">
                            <label class="form-label" for="user_type">سازمان
                            </label>
                            <input class="form-control" id="user_type"
                                   value="{{ implode(' - ',$user->joinedCompanies()->pluck('name')->toArray()) }}"
                                   type="text" disabled>
                        </div>

                        @notRole(['manager'])
                        <div class="col-12 mb-3">
                            <label class="form-label" for="user_type">نوع کاربر
                            </label>
                            <input class="form-control" id="user_type"
                                   value="{{ $user->type['name'] }}"
                                   type="text" disabled>
                        </div>
                        @endnotRole


                        <div class="col-12 mb-3">
                            <label class="form-label" for="status">وضعیت
                            </label>
                            <input class="form-control" id="status"
                                   value="{{ $user->status ? 'فعال' : 'غیر فعال' }}"
                                   type="text" disabled>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- User Devices -->
            <div class="card mb-4">
                <div class="card-header pb-0 card-no-border">
                    <h5 class="d-flex justify-content-between align-items-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <i data-feather="cpu" style="width: 20px; padding-bottom: 5px" class="me-1"></i>
                            <span>دستگاه های کاربر</span>
                        </div>
                        @if(can('create-device'))
                            <a href="{{ route('device.create') }}" class="btn btn-sm btn-primary">+ ایجاد دستگاه
                                جدید</a>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="display" id="basic-1">
                            <thead>
                            <tr>
                                <th>نام</th>
                                <th>مدل</th>
                                <th>شماره سیم کارت</th>
                                <th>وضعیت</th>
                                <th>متصل شده در</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($user->devices as $device)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $device->name }}</span>
                                            <small class="text-muted">{{ $device->serial }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $device->model }}</td>
                                    <td>{{ $device?->phone_number }}</td>
                                    <td>
                                        @if($device->status)
                                            <span class="badge dana rounded-pill badge-success">فعال</span>
                                        @else
                                            <span class="badge dana rounded-pill badge-danger">غیرفعال</span>
                                        @endif
                                    </td>
                                    <td>{{ jalaliDate($device?->connected_at,time:true) }}</td>
                                    <td x-data="{ show: false }">
                                        <div class="btn-group" x-cloak x-show="!show">
                                            <button class="btn dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="icofont icofont-listing-box txt-dark"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-block text-center" style="">
                                                @if(can('edit-device'))
                                                    <a class="dropdown-item "
                                                       href="{{ route('device.edit', $device->id) }}">ویرایش</a>
                                                @endif
                                                @if(can('delete-device'))
                                                    <a href="javascript:void(0)" class="dropdown-item"
                                                       @click.prevent="show = true">حذف</a>
                                                @endif
                                                @if(can('device-settings'))

                                                    <a href="{{ route('device.device-setting', $device->id) }}"
                                                       class="dropdown-item">دستورات دستگاه</a>
                                                @endif
                                                {{--                                                <a href="{{ route('device.show', $device->id) }}"--}}
                                                {{--                                                   class="dropdown-item">نمایش موقعیت مکانی</a>--}}

                                            </ul>
                                        </div>
                                        @if(can('delete-device'))
                                            <x-partials.btns.confirm-rmv-btn
                                                url="{{ route('device.destroy', $device->id) }}"/>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">داده ای یافت نشد.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- User Vehicles -->
            <div class="card">
                <div class="card-header pb-0 card-no-border">
                    <h5 class="d-flex justify-content-between align-items-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <i data-feather="truck" style="width: 20px; padding-bottom: 5px" class="me-1"></i>
                            <span>وسایل نقلیه کاربر</span>
                        </div>
                        @if(can('create-vehicle'))
                            <a href="{{ route('vehicle.create') }}" class="btn btn-sm btn-primary">+ ایجاد وسیله نقلیه
                                جدید</a>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive custom-scrollbar text-nowrap">
                        <table class="display" id="basic-2">
                            <thead>
                            <tr>
                                <th>وسیله نقلیه</th>
                                <th>پلاک</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($user->vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $vehicle->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $vehicle->license_plate }}</td>
                                    <td>
                                        @if($vehicle->status)
                                            <span class="badge dana rounded-pill badge-success">فعال</span>
                                        @else
                                            <span class="badge dana rounded-pill badge-danger">غیرفعال</span>
                                        @endif
                                    </td>
                                    <td x-data="{ show: false }">
                                        <div class="btn-group" x-cloak x-show="!show">
                                            <button class="btn dropdown-toggle" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="icofont icofont-listing-box txt-dark"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-block text-center" style="">
                                                @if(can('edit-vehicle'))
                                                    <a class="dropdown-item "
                                                       href="{{ route('vehicle.edit', $vehicle->id) }}">ویرایش</a>
                                                @endif
                                                @if(can('delete-vehicle'))
                                                    <a href="javascript:void(0)" class="dropdown-item"
                                                       @click.prevent="show = true">حذف</a>
                                                @endif
                                            </ul>
                                        </div>
                                        @if(can('delete-vehicle'))
                                            <x-partials.btns.confirm-rmv-btn
                                                url="{{ route('vehicle.destroy', $vehicle->id) }}"/>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">داده ای یافت نشد.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.bootstrap5.js')}}"></script>
    <script>
        $('#basic-1').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });

        $('#basic-2').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
