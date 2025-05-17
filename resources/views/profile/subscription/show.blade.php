@php
    $loadAlpineJs = false;
@endphp
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
                            @if($isUser)
                                <a href="{{ route('profile.wallet') }}">کیف پول من</a>
                            @else
                                <a href="{{ route('company.index') }}">لیست سازمان های من</a>
                            @endif
                        </li>
                        <li class="breadcrumb-item dana">جزئیات اشتراک</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <x-partials.alert.success-alert/>
        <x-partials.alert.error-alert/>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                @if($isUser)
                    <h5>جزئیات اشتراک شما</h5>
                    <a href="{{ route('profile.subscription.history') }}">#تاریخچه اشتراک ها</a>
                @else
                    @php $company = $subscription->wallet?->walletable @endphp
                    <h5>جزئیات اشتراک <a href="{{ route('company.show', $company->id) }}"
                                         class="h5 fw-bold txt-primary">{{ $company?->name }}</a></h5>
                    <a href="{{ route('profile.subscription.history', $subscription->wallet_id) }}">#تاریخچه اشتراک
                        ها</a>
                @endif
            </div>
            <div class="card-block row">
                <div class="col-sm-12 col-lg-12 col-xl-12">
                    <div class="table-responsive custom-scrollbar">
                        <table class="table text-nowrap">
                            @php
                                $canRenew = !$subscription->status->isActive() || ($subscription->end_at <= now()->addDays(2));
                                $canCancel = !$subscription->cancellation()->exists();
                                $showActions = $canCancel || $canRenew;
                                $isCanceled = $subscription->status->isCanceled();
                            @endphp
                            <thead class="table-inverse">
                            <tr>
                                <th scope="col">طرح</th>
                                <th scope="col">تاریخ شروع</th>
                                <th scope="col">تاریخ انقضا</th>
                                <th scope="col">وضعیت</th>
                                <th scope="col">تمدید خودکار</th>
                                @if($showActions)
                                    <th scope="col">{{ $isCanceled ? 'تاریخ لغو' : 'عملیات اشتراک' }}</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th scope="row">{{ $subscription?->plan?->name }}</th>
                                <td>{{ jalaliDate($subscription->start_at, format: "%d %B %Y H:i") }}</td>
                                <td>
                                    <span data-bs-toggle="tooltip" data-bs-placement="top"
                                          title="{{ number_format(dayCount($subscription->end_at)) }} روز باقی مانده است"
                                    >{{ jalaliDate($subscription->end_at, format: "%d %B %Y H:i") }}</span>
                                </td>
                                <td>
                                    <span @class(['badge dana fw-bold', "bg-{$subscription->status->badge()->color}"])>
                                        {{ $subscription->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <x-partials.alpine.change-status
                                        :status="(bool)$subscription->auto_renew"
                                        :url="route('profile.subscription.toggle-auto-activation', $subscription->id)"/>
                                </td>
                                @if($isCanceled)
                                    <td>{{ jalaliDate($subscription->canceled_at, format: "%d %B %Y H:i") }}</td>
                                @else
                                    @if($showActions)
                                        <td class="btn-group" x-data="subscriptionActions">
                                            @if($canRenew)
                                                <form
                                                    action="{{ route('profile.subscription.renew', $subscription->id) }}"
                                                    method="post" id="renew-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <button
                                                        class="btn btn-sm btn-outline-warning fw-bold d-flex align-items-center"
                                                        type="button"
                                                        @click="showConfirmation"
                                                    >
                                                        <i data-feather="refresh-cw" class="me-1"
                                                           style="width: 18px"></i>
                                                        <span>تمــدیــد</span>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($canCancel)
                                                <button
                                                    class="btn btn-sm btn-outline-danger fw-bold d-flex align-items-center"
                                                    type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#subscriptionCancellation"
                                                >
                                                    <i data-feather="slash" class="me-1" style="width: 18px"></i>
                                                    <span>لـــغو</span>
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                @endif
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <livewire:components.cancellation-modal :$subscription/>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('alpine:init', () => {
            Alpine.data('subscriptionActions', () => ({
                showConfirmation() {
                    Swal.fire({
                        title: "تایید برداشت از کیف پول",
                        text: "شما در حال برداشت از کیف پول خود جهت تمدید اشتراک هستید. آیا مطمئنید که می‌خواهید این تراکنش را انجام دهید؟",
                        icon: "warning",
                        showCancelButton: true,
                        reverseButtons: true,
                        cancelButtonText: "لغو",
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "تایید و ادامه"
                    }).then((result) => {
                        if (result.value) {
                            document.getElementById("renew-form").submit();
                        }
                    });
                }
            }))
        })
    </script>
@endpush
