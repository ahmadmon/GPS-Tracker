@extends('01-layouts.master')

@section('title', 'لیست وسایل نقلیه')

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
                        <li class="breadcrumb-item dana">وسایل نقلیه</li>
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
                @if(can('create-vehicle'))
                    <a href="{{ route('vehicle.create') }}" class="btn btn-primary mb-4">+ ایجاد وسیله نقلیه</a>
                @endif
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>لیست وسایل نقلیه</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th>وسیله نقلیه</th>
                                    <th>پلاک</th>
                                    <th>دستگاه</th>
                                    @notRole(['user'])
                                    <th>کاربر</th>
                                    @endnotRole
                                    <th>وضعیت</th>
                                    <th>تاریخ ایجاد</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($vehicles as $vehicle)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $vehicle->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $vehicle->license_plate }}</td>
                                        <td>{{ $vehicle?->device?->name ?? 'تعریف نشده' }}</td>
                                        @notRole(['user'])
                                        <td>{{ $vehicle?->user?->name }}</td>
                                        @endnotRole
                                        <td>
                                            <x-partials.alpine.change-status :status="(bool)$vehicle->status" :url="route('vehicle.change-status',$vehicle->id)" />
                                        </td>
                                        <td>{{ jalaliDate($vehicle?->created_at) }}</td>
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
                                                    {{--                                                    <a class="dropdown-item" href="#">نمایش موقعیت</a>--}}
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
            order: [[5, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
