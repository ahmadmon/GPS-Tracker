@php use Carbon\Carbon; @endphp
@extends('01-layouts.master')

@section('title', 'لیست حصارهای جغرافیایی')

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
                        <li class="breadcrumb-item dana">حصارهای جغرافیایی</li>
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
                @if(can('create-geofence'))
                    <a href="{{ route('geofence.create') }}" class="btn btn-primary mb-4">+ ایجاد حصار جدید</a>
                @endif
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>لیست حصارهای جغرافیایی</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th>نام</th>
                                    <th>نوع</th>
                                    @notRole(['user'])
                                    <th>مختص به کاربر</th>
                                    @endnotRole
                                    <th>مختص به دستگاه</th>
                                    <th>وضعیت</th>
                                    <th>زمان فعالیت حصار</th>
                                    <th>تاریخ ایجاد</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($geofences as $geofence)
                                    <tr>
                                        <td>
                                            <div>
                                                @if(can('edit-geofence'))
                                                    <a class="f-14 mb-0 f-w-500 c-light"
                                                       href="{{ route('geofence.edit', $geofence->id) }}">{{ $geofence->name }}</a>
                                                @else
                                                    <span class="f-14 mb-0 f-w-500 c-light">{{ $geofence->name }}</span>
                                                @endif
                                                <p class="c-o-light text-muted"
                                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                                   data-bs-title="{{ $geofence->description ?? ' ' }}"
                                                >{{ str($geofence?->description)->limit(35) }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            @if($geofence->type)
                                                <span class="badge dana rounded-pill badge-primary">ورود</span>
                                            @else
                                                <span class="badge dana rounded-pill badge-warning">خروج</span>
                                            @endif
                                        </td>
                                        @notRole(['user'])
                                        <td>
                                            <a href="{{ route('user.show', $geofence?->user->id) }}" target="_blank">
                                                {{ $geofence?->user->name ?? '-' }}
                                            </a>
                                        </td>
                                        @endnotRole
                                        <td>
                                            <a href="{{ route('device.show', $geofence->device->id) }}" target="_blank">
                                                {{ $geofence->device->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <x-partials.alpine.change-status :status="(bool)$geofence->status"
                                                                             :url="route('geofence.change-status',$geofence->id)"/>
                                        </td>
                                        <td>
                                            @if(isset($geofence->start_time) && isset($geofence->end_time))
                                                از {{ Carbon::parse($geofence?->start_time)->format('H:i') }}
                                                الی {{ Carbon::parse($geofence?->end_time)->format('H:i') }}
                                            @else
                                                فاقد محدودیت زمانی
                                            @endif
                                        </td>
                                        <td data-sort="{{ $geofence->created_at->toDateTimeString() }}">
                                            <span class="text-muted">{{ jalaliDate($geofence->created_at) }}</span>
                                        </td>
                                        <td x-data="{ show: false }">
                                            <div class="btn-group" x-cloak x-show="!show">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="icofont icofont-listing-box txt-dark"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-block text-center" style="">
                                                    @if(can('edit-geofence'))

                                                        <a class="dropdown-item"
                                                           href="{{ route('geofence.edit', $geofence->id) }}">ویرایش</a>
                                                    @endif

                                                    @if(can('delete-geofence'))
                                                        <a href="javascript:void(0)" class="dropdown-item"
                                                           @click.prevent="show = true">حذف</a>
                                                    @endif
                                                </ul>
                                            </div>
                                            <x-partials.btns.confirm-rmv-btn
                                                url="{{ route('geofence.destroy', $geofence->id) }}"/>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">داده ای یافت نشد.</td>
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

    @php
        $sort = auth()->user()->hasRole(['user']) ? 5 : 6;
    @endphp

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>

    <script>
        $('#basic-1').DataTable({
            order: [[{{ $sort }}, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
