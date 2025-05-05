@extends('01-layouts.master')

@php
    $title = $isUser ? 'لیست اشتراک کاربران' : 'لیست اشتراک سازمان ها';
@endphp
@section('title', $title)

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
                        <li class="breadcrumb-item dana">{{ $title }}</li>
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
                    <a href="{{ route('subscription-management.create', ['type' => request('type')]) }}"
                       class="btn btn-primary mb-4">+ اعطای اشتراک</a>
                @endif
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th>دارنده اشتراک</th>
                                    <th>طرح</th>
                                    <th>تاریخ خرید اشتراک</th>
                                    <th>تاریخ انقضا</th>
                                    <th>وضعیت</th>
                                    <th>تمدید خودکار</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($subscriptions as $subscription)
                                    <tr>
                                        <td data-sort="{{ $subscription->created_at }}">
                                            <a class="f-14 mb-0 f-w-700"
                                               href="#">{{ $subscription->wallet?->walletable?->name }}</a>
                                        </td>
                                        <td>{{ $subscription->plan->name }}</td>
                                        <td>{{ jalaliDate($subscription->start_at, format: "%d %B %Y H:i") }}</td>
                                        <td>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                          title="{{ number_format(dayCount($subscription->end_at)) }} روز باقی مانده است"
                                    >{{ jalaliDate($subscription->end_at, format: "%d %B %Y H:i") }}</span>
                                        </td>
                                        <td>
                                            <span
                                                    class="badge badge-{{ $subscription->status->badge()->color }} dana rounded-pill">{{ $subscription->status->label() }}</span>
                                        </td>
                                        <td>
                                            <x-partials.alpine.change-status
                                                    :status="(bool)$subscription->is_activated_automatically"
                                                    :url="route('profile.subscription.toggle-auto-activation', $subscription->id)"/>
                                        </td>
                                        <td x-data="{ show: false }">
                                            <div class="btn-group" x-cloak x-show="!show">
                                                <button class="btn dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="icofont icofont-listing-box txt-dark"></i>
                                                </button>
                                                {{--                                                <ul class="dropdown-menu dropdown-block text-center" style="">--}}
                                                {{--                                                    --}}{{--                                                    @if(can('edit-vehicle'))--}}
                                                {{--                                                    <a class="dropdown-item "--}}
                                                {{--                                                       href="{{ route('subscription-plan.edit', $plan->slug) }}">ویرایش</a>--}}
                                                {{--                                                    --}}{{--                                                    @endif--}}

                                                {{--                                                    --}}{{--                                                    @if(can('delete-vehicle'))--}}
                                                {{--                                                    <a href="javascript:void(0)" class="dropdown-item"--}}
                                                {{--                                                       @click.prevent="show = true">حذف</a>--}}
                                                {{--                                                    --}}{{--                                                    @endif--}}
                                                {{--                                                </ul>--}}
                                            </div>

                                            {{--                                            @if(can('delete-vehicle'))--}}
                                            {{--                                            <x-partials.btns.confirm-rmv-btn--}}
                                            {{--                                                url="{{ route('subscription-plan.destroy', $plan->slug) }}"/>--}}
                                            {{--                                            @endif--}}
                                        </td>
                                    </tr>
                                @endforeach
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
            order: [[3, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
