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
                <a href="{{ route('device.create') }}" class="btn btn-primary mb-4">+ ایجاد دستگاه</a>
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
                                    <th>شماره سیم کارت</th>
                                    <th>خریدار</th>
                                    <th>وضعیت</th>
                                    <th>متصل شده در</th>
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
                                        <td>{{ $device?->phone_number }}</td>
                                        <td>
                                            <a href="{{ route('user.show', $device->user_id) }}" target="_blank">
                                                {{ $device->user->name }}
                                            </a>
                                        </td>
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
                                                    <a class="dropdown-item "
                                                       href="{{ route('device.edit', $device->id) }}">ویرایش</a>
                                                    <a href="javascript:void(0)" class="dropdown-item"
                                                       @click.prevent="show = true">حذف</a>
                                                    <a href="{{ route('device.device-connection', $device->id) }}"
                                                       class="dropdown-item">دستورات دستگاه</a>
                                                    <a href="{{ route('device.show', $device->id) }}"
                                                       class="dropdown-item">نمایش موقعیت مکانی</a>

                                                </ul>
                                            </div>
                                            <x-partials.btns.confirm-rmv-btn
                                                url="{{ route('device.destroy', $device->id) }}"/>
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
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.bootstrap5.js')}}"></script>
    <script>
        $('#basic-1').DataTable({
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
