@extends('01-layouts.master')

@section('title', 'جزئیات اشتراک')

@push('styles')

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
                            <a href="{{ route('profile.wallet') }}">کیف پول من</a>
                        </li>
                        <li class="breadcrumb-item dana">جزئیات اشتراک</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5>جزئیات اشتراک شما</h5>
            </div>
            <div class="card-block row">
                <div class="col-sm-12 col-lg-12 col-xl-12">
                    <div class="table-responsive custom-scrollbar">
                        <table class="table text-nowrap">
                            <thead class="table-inverse">
                            <tr>
                                <th scope="col">طرح</th>
                                <th scope="col">تاریخ شروع</th>
                                <th scope="col">تاریخ انقضا</th>
                                <th scope="col">وضعیت</th>
                                <th scope="col">تمدید خودکار</th>
                                <th scope="col">لغو اشتراک</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th scope="row">{{ $subscription?->plan?->name }}</th>
                                <td>{{ jalaliDate($subscription->start_at, format: "%d %B %Y H:i") }}</td>
                                <td>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ number_format(dayCount($subscription->end_at)) }} روز باقی مانده است"
                                    >{{ jalaliDate($subscription->end_at, format: "%d %B %Y H:i") }}</span>
                                </td>
                                <td>
                                    <span @class(['badge dana fw-bold', "bg-{$subscription->status->badge()->color}"])>
                                        {{ $subscription->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <x-partials.alpine.change-status
                                        :status="(bool)$subscription->is_activated_automatically"
                                        :url="route('profile.subscription.toggle-auto-activation', $subscription->id)"/>
                                </td>
                                @isset($subscription->canceled_at)
                                    <td>{{ jalaliDate($subscription->canceled_at, format: "%d %B %Y H:i") }}</td>
                                    <td>
                                        {{ $subscription?->cancellation_reason }}
                                    </td>
                                @else
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger fw-bold" type="button"

                                        >
                                            <i data-feather="cancel" class="me-1"></i>
                                            لــغو اشتـــراک
                                        </button>
                                    </td>
                                @endisset
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('alpine:init', () => {

        })
    </script>
@endpush
