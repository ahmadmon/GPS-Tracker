@extends('01-layouts.master')

@section('title', 'لیست سازمان‌ ها')

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
                        <li class="breadcrumb-item dana">سازمان ها</li>
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
                <a href="{{ route('company.create') }}" class="btn btn-primary mb-4">+ ایجاد سازمان جدید</a>
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>لیست سازمان ها</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th>سازمان</th>
                                    <th>مدیر</th>
                                    <th>شماره تماس</th>
                                    <th>وضعیت</th>
                                    <th>تاریخ عضویت</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($companies as $company)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="currency-icon warning">
                                                    <img class="img-fluid" width="32" height="32" src="{{ $company->logo ?? asset('assets/images/custom/workplace-64px.png') }}" alt="">
                                                </div>
                                                <div> <a class="f-14 mb-0 f-w-500 c-light" href="{{ route('company.show', $company->id) }}">{{ $company->name }}</a>
                                                    <p class="c-o-light text-muted">{{ str($company?->address)->limit(35) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.show', $company->manager->id) }}" target="_blank">
                                                {{ $company->manager->name }}
                                            </a>
                                        </td>
                                        <td>{{ $company->contact_number }}</td>
                                        <td>
                                            @if($company->status)
                                                <span class="badge dana rounded-pill badge-success">فعال</span>
                                            @else
                                                <span class="badge dana rounded-pill badge-danger">غیرفعال</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ jalaliDate($company->created_at) }}</span>
                                        </td>
                                        <td x-data="{ show: false }">
                                            <div class="btn-group" x-cloak x-show="!show">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="icofont icofont-listing-box txt-dark"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-block text-center" style="">
                                                    <a class="dropdown-item"
                                                       href="{{ route('company.edit', $company->id) }}">ویرایش</a>
                                                    <a href="javascript:void(0)" class="dropdown-item"
                                                       @click.prevent="show = true">حذف</a>
                                                    <a class="dropdown-item"
                                                       href="{{ route('company.show', $company->id) }}">مشاهده جزئیات</a>
                                                </ul>
                                            </div>
                                            <x-partials.btns.confirm-rmv-btn
                                                url="{{ route('company.destroy', $company->id) }}"/>
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
            order: [[4, 'asc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
