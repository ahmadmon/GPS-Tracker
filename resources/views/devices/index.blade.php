@extends('01-layouts.master')

@section('title', 'لیست دستگاه')

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
                        <li class="breadcrumb-item dana">دستگاه ها</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        <x-partials.alert.success-alert/>
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                @if(can('create-device'))
                    <a href="{{ route('device.create') }}" class="btn btn-primary mb-4">+ ایجاد دستگاه</a>
                @endif

                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>لیست دستگاه ها</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th>نام</th>
                                    <th>مدل</th>
                                    <th>برند</th>
                                    <th>شماره سیم کارت</th>
                                    <th>وسیله نقلیه</th>
                                    @notRole(['user'])
                                    <th>کاربر</th>
                                    @endnotRole
                                    <th>وضعیت</th>
                                    <th>تاریخ ایجاد</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($devices as $device)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $device->name }}</span>
                                                <small class="text-muted">{{ $device->serial }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $device->model }}</td>
                                        <td>
                                            <span @class(['badge dana',
                                                          'badge-dark' => $device->brand->isSinotrack(),
                                                          'badge-warning' => $device->brand->isWanWay(),
                                                          'badge-info' => $device->brand->isConcox(),
                                                        ])>
                                                {{ $device->brand }}
                                            </span>
                                        </td>
                                        <td>{{ $device?->phone_number }}</td>
                                        <td>
                                            @if(can('edit-vehicle'))
                                            <a href="{{ route('vehicle.edit', $device?->vehicle_id) }}" target="_blank">
                                                {{ $device->vehicle?->name }}
                                                <small class="text-muted d-block">{{ $device->vehicle?->license_plate }}</small>
                                            </a>
                                            @else
                                                <span>
                                                    {{ $device->vehicle?->name }}
                                                    <small class="text-muted d-block">{{ $device->vehicle?->license_plate }}</small>
                                                </span>
                                            @endif
                                        </td>
                                        @notRole(['user'])
                                        <td>
                                            <a href="{{ route('user.show', $device->user_id) }}" target="_blank">
                                                {{ $device->user->name }}
                                            </a>
                                        </td>
                                        @endrole
                                        <td>
                                           <x-partials.alpine.change-status :status="(bool)$device->status" :url="route('device.change-status',$device->id)" />
                                        </td>
                                        <td data-sort="{{ $device->created_at->toDateTimeString() }}">{{ jalaliDate($device?->created_at,time:true) }}</td>
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
                                                           class="dropdown-item">تنظیمات دستگاه</a>
                                                    @endif
                                                    {{--                                                    <a href="{{ route('device.show', $device->id) }}"--}}
                                                    {{--                                                       class="dropdown-item">نمایش موقعیت مکانی</a>--}}

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
            </div>
            <!-- Zero Configuration  Ends-->
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script>
        $('#basic-1').DataTable({
            order: [[7, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
