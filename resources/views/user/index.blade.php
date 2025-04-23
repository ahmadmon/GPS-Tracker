@extends('01-layouts.master')

@section('title', 'لیست کاربران')

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
                        <li class="breadcrumb-item dana">کاربران</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        <x-partials.alert.success-alert/>
        <x-partials.alert.error-alert/>
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                <a href="{{ route('user.create') }}" class="btn btn-primary mb-4">+ ثبت‌نام کاربر جدید</a>
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>لیست کاربران</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th>کاربر</th>
                                    <th>شماره تماس</th>
                                    <th>وضعیت</th>
                                    @role(['super-admin','admin'])
                                    <th>موجودی کیف پول</th>
                                    @endrole
                                    <th>تاریخ عضویت</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('user.show', $user->id) }}">
                                                    <span class="fw-bold">{{ $user->name }}</span>
                                                </a>
                                                @notRole(['manager'])
                                                <small
                                                        class="text-muted">{{ $user->type['name'] }}</small>
                                                @endnotRole
                                            </div>
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>
                                            <x-partials.alpine.change-status :status="(bool)$user->status" :url="route('user.change-status',$user->id)" />
                                        </td>
                                        @role(['super-admin', 'admin'])
                                        <td data-sort="{{ $user?->wallet?->balance }}">
                                            <a href="{{ route('wallet-management.show', $user->wallet) }}" target="_blank">
                                                <strong>{{ priceFormat($user?->wallet?->balance) }} تومان</strong>
                                            </a>
                                        </td>
                                        @endrole
                                        <td data-sort="{{ $user->created_at->toDateTimeString() }}">
                                            <span class="text-muted">{{ jalaliDate($user->created_at) }}</span>
                                        </td>
                                        <td x-data="{ show: false }">
                                            <div class="btn-group" x-cloak x-show="!show">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="icofont icofont-listing-box txt-dark"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-block text-center" style="">
                                                    @if(can('edit-user'))
                                                        <a class="dropdown-item"
                                                           href="{{ route('user.edit', $user->id) }}">ویرایش</a>
                                                    @endif
                                                    @if(can('delete-user'))
                                                        <a href="javascript:void(0)" class="dropdown-item"
                                                           @click="show = true">حذف</a>
                                                    @endif
                                                    @if(can('show-user'))
                                                        <a class="dropdown-item"
                                                           href="{{ route('user.show', $user->id) }}">مشاهده جزئیات</a>
                                                    @endif
                                                </ul>
                                            </div>
                                            @if(can('delete-user'))
                                                <x-partials.btns.confirm-rmv-btn
                                                        url="{{ route('user.destroy', $user->id) }}"/>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">داده ای یافت نشد.</td>
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
        $sort = auth()->user()->hasRole(['user', 'manager']) ? 3 : 4;
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
