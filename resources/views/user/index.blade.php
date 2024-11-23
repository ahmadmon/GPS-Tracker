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
                                    <th>تاریخ عضویت</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $user->name }}</span>
                                                <small
                                                    class="text-muted">{{ $user->type['name'] }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>
                                            @if($user->status)
                                                <span class="badge dana rounded-pill badge-success">فعال</span>
                                            @else
                                                <span class="badge dana rounded-pill badge-danger">غیرفعال</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ jalaliDate($user->created_at) }}</span>
                                        </td>
                                        <td x-data="{ show: false }">
                                            <div class="btn-group" x-cloak x-show="!show">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="icofont icofont-listing-box txt-dark"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-block text-center" style="">
                                                    <a class="dropdown-item"
                                                       href="{{ route('user.edit', $user->id) }}">ویرایش</a>
                                                    <a href="javascript:void(0)" class="dropdown-item"
                                                       @click="show = true">حذف</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('user.show', $user->id) }}">مشاهده جزئیات</a>
                                                </ul>
                                            </div>
                                            <x-partials.btns.confirm-rmv-btn
                                                url="{{ route('user.destroy', $user->id) }}"/>
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

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.bootstrap5.js')}}"></script>

    <script>
        $('#basic-1').DataTable({
            order: [[3, 'asc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
