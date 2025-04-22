@extends('01-layouts.master')

@section('title', 'اطلاعات سازمان')

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
                            <a href="{{ route('company.index') }}">
                                لیست سازمان ها
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">اطلاعات سازمان</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <div class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <h5>اطلاعات کلی</h5>
                        @if(can('edit-company'))
                            <a href="{{ route('company.edit', $company->id) }}"
                               class="btn btn-sm btn-primary">ویرایش</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="profile-title">
                            <div class="d-flex">
                                <img class="img-70 rounded me-2 object-fit-cover" alt=""
                                     src="{{ asset($company->logo ?? 'assets/images/custom/workplace-64px.png') }}">
                                @notRole(['manager'])
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $company->name }}</h5>
                                    <strong class="text-muted">مدیر:
                                        <a href="{{ route('user.show', $company->manager->id) }}"
                                           target="_blank">{{ $company->manager->name }}</a>
                                    </strong>
                                </div>
                                @endnotRole
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">بیو (توضیحات)</label>
                        <textarea id="bio" class="form-control disabled" disabled
                                  rows="3">{{ $company?->bio ?? 'فاقد بیو (توضیحات)' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="contact_number">شماره تماس </label>
                        <input class="form-control disabled" disabled id="contact_number" dir="ltr"
                               value="{{ $company->contact_number }}"
                               type="text">
                    </div>

                    <div class="col-12 mb-3">
                        <label for="address" class="form-label">آدرس دفتر مرکزی
                        </label>
                        <textarea id="address" class="form-control disabled" disabled
                                  rows="3">{{ $company->address }}</textarea>
                    </div>


                    <div class="mb-3">
                        <label class="form-label" for="status">وضعیت
                        </label>
                        @if($company->status)
                            <div>
                                <span class="badge bg-success w-100 dana py-2 fw-bold">فــعــال</span>
                            </div>
                        @else
                            <div>
                                <span class="badge bg-danger w-100 dana py-2 fw-bold">غیر فــعــال</span>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <x-partials.alert.success-alert/>
            <div class="card">
                <div class="card-header">
                    <div class="card-title mb-0 d-flex justify-content-between align-items-center">
                        <h4>زیرمجموعه های سازمان</h4>
                        @if(can('manage-subsets'))
                            <a href="{{ route('company.manage-subsets', $company->id) }}"
                               class="btn btn-sm btn-primary">
                                مدیریت زیرمجموعه ها</a>
                        @endif
                    </div>
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
                                @if(can('manage-subsets'))
                                    <th>عملیات</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($company->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $user->name }}</span>
                                            @notRole(['manager'])
                                            <small
                                                class="text-muted">{{ $user->type['name'] }}</small>
                                            @endnotRole
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
                                    <td data-sort="{{ $user->created_at->toDateTimeString() }}">
                                        <span class="text-muted">{{ jalaliDate($user->created_at) }}</span>
                                    </td>
                                    @if(can('manage-subsets'))
                                        <td x-data="{ show: false }">
                                            <div class="btn-group" x-cloak x-show="!show">
                                                <a href="javascript:void(0)" class="btn btn-sm btn-danger"
                                                   @click="show = true">حذف از لیست</a>
                                            </div>
                                            <x-partials.btns.confirm-rmv-btn
                                                url="{{ route('company.remove-subsets', [$company->id,$user->id]) }}"/>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr class="bg-transparent">
                                    <td colspan="5" class="text-center">داده ای یافت نشد.</td>
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

    <script>
        $('#basic-1').DataTable({
            order: [[3, 'asc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
