@extends('01-layouts.master')

@section('title', 'لیست طرح اشتراک')

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
                        <li class="breadcrumb-item dana">طرح اشتراک</li>
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
                    <a href="{{ route('subscription-plan.create') }}" class="btn btn-primary mb-4">+ تعریف طرح
                        اشتراک</a>
                @endif
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>لیست طرح اشتراک</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th>نام</th>
                                    <th>قیمت</th>
                                    <th>مدت زمان</th>
                                    <th>نوع</th>
                                    <th>وضعیت</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td data-sort="{{ $plan->created_at }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    <a class="f-14 mb-0 f-w-500 c-light"
                                                       href="{{ route('subscription-plan.show', $plan->slug) }}">{{ $plan->name }}</a>
                                                    <p class="c-o-light text-muted cursor-pointer"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       data-bs-title="{{ $plan?->description }}">{{ str($plan?->description)->limit(35) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ priceFormat($plan->price) }} تومان</td>
                                        <td>{{ $plan->is_lifetime ? 'مادام‌العمر' : $plan?->duration }}</td>
                                        <td>
                                            <span class="badge badge-{{ $plan->type->badge()->color }} dana rounded-pill">{{ $plan->type->badge()->name }}</span>
                                        </td>
                                        <td>
                                            <x-partials.alpine.change-status :status="(bool)$plan->status"
                                                                             :url="route('subscription-plan.change-status',$plan->slug)"/>
                                        </td>
                                        <td x-data="{ show: false }">
                                            <div class="btn-group" x-cloak x-show="!show">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="icofont icofont-listing-box txt-dark"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-block text-center" style="">
                                                    {{--                                                    @if(can('edit-vehicle'))--}}
                                                    <a class="dropdown-item "
                                                       href="{{ route('subscription-plan.edit', $plan->slug) }}">ویرایش</a>
                                                    {{--                                                    @endif--}}

                                                    {{--                                                    @if(can('delete-vehicle'))--}}
                                                    <a href="javascript:void(0)" class="dropdown-item"
                                                       @click.prevent="show = true">حذف</a>
                                                    {{--                                                    @endif--}}
                                                </ul>
                                            </div>

                                            {{--                                            @if(can('delete-vehicle'))--}}
                                            <x-partials.btns.confirm-rmv-btn
                                                url="{{ route('subscription-plan.destroy', $plan->slug) }}"/>
                                            {{--                                            @endif--}}
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
            order: [[5, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
