@extends('01-layouts.master')

@section('title', 'تاریخچه اشتراک ها')

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
                            @if($isUser)
                                <a href="{{ route('profile.wallet') }}">کیف پول من</a>
                            @else
                                <a href="{{ route('company.index') }}">لیست سازمان های من</a>
                            @endif
                        </li>
                        <li class="breadcrumb-item dana">
                            @if($isUser)
                                <a href="{{ route('profile.subscription.index') }}">اشتراک فعال من</a>
                            @else
                                <a href="{{ route('profile.subscription.show', $wallet) }}">اشتراک فعال سازمان</a>
                            @endif
                        </li>

                        <li class="breadcrumb-item dana">تاریخچه اشتراک ها</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <x-partials.alert.success-alert/>
        <x-partials.alert.error-alert/>
        <x-partials.alert.info-alert/>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                @if($isUser)
                    <h5>تاریخچه اشتراک شما</h5>
                    <a href="{{ route('profile.subscription.show') }}">#اشتراک فعال من</a>
                @else
                    @php $company = $wallet?->walletable @endphp
                    <h5>تاریخچه اشتراک <a href="{{ route('company.show', $company->id) }}"
                                          class="h5 fw-bold txt-primary">{{ $company?->name }}</a></h5>
                    <a href="{{ route('profile.subscription.show', $wallet) }}">#اشتراک فعال سازمان</a>
                @endif
            </div>
            <div class="card-body row">
                <div class="col-sm-12 col-lg-12 col-xl-12">
                    <div class="table-responsive custom-scrollbar text-nowrap">
                        <table class="display" id="basic-1">
                            <thead>
                            <tr>
                                <th>طرح</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ انقضا</th>
                                <th>وضعیت</th>
                                <th>تمدید خودکار</th>
                                <th>تاریخ بازگشت وجه</th>
                                <th>تاریخ لغو</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($subscriptions as $subscription)
                                @php
                                    $isCanceled = $subscription->status->isCanceled();
                                @endphp
                                <tr>
                                    <th scope="row">{{ $subscription?->plan?->name }}</th>
                                    <td>{{ jalaliDate($subscription->start_at, format: "%d %B %Y H:i") }}</td>
                                    <td data-sort="{{ $subscription->created_at->toDateTimeString() }}">
                                        <span>{{ jalaliDate($subscription->end_at, format: "%d %B %Y H:i") }}</span>
                                    </td>
                                    <td>
                                    <span @class(['badge dana fw-bold', "bg-{$subscription->status->badge()->color}"])>
                                        {{ $subscription->status->label() }}
                                    </span>
                                    </td>
                                    <td>
                                   <span @class(['badge dana fw-bold',
                                                 'bg-success' => $subscription->auto_renew ,
                                                 'bg-danger' => !$subscription->auto_renew])>
                                       {{ $subscription->auto_renew ? 'فعال' : 'غیرفعال' }}
                                   </span>
                                    </td>
                                    <td>
                                        @php
                                            $isRefunded = $subscription?->cancellation?->refunded_at;
                                            $isRejected = !$isRefunded && $subscription?->cancellation?->status->isRejected();
                                        @endphp
                                        @if($isRefunded)
                                            <div class="d-flex flex-column">
                                                <small
                                                    class="text-muted">{{ !$subscription?->cancellation?->iban ? 'واریز به کیف پول' : 'واریز به شماره شبا' }}</small>
                                                <span>{{ jalaliDate($subscription->cancellation->refunded_at, format: '%d %B %Y, H:i') }}</span>
                                            </div>
                                        @elseif($isRejected)
                                            <div x-data="{show: false , showReason: false}">
                                                <div x-show="!showReason">
                                                <span class="badge bg-dark dana cursor-pointer"
                                                      @mouseenter="show = true"
                                                      @mouseleave="show = false"
                                                      @click="showReason = true"
                                                      x-text="show ? 'مشاهده دلیل' : 'درخواست لغو, رد شده'">درخواست لغو رد شده</span>
                                                </div>

                                                <div x-cloak x-show="showReason" @click="showReason = false"
                                                     class="cursor-pointer">
                                                    <small>{!! nl2br(e($subscription?->cancellation->rejection_reason)) !!}</small>
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @if($isCanceled)
                                        <td>{{ jalaliDate($subscription->canceled_at, format: "%d %B %Y H:i") }}</td>
                                    @else
                                        <td>
                                            <div x-data="subscriptionActions">
                                                <form
                                                    action="{{ route('profile.subscription.renew', $subscription->id) }}"
                                                    method="post" id="renew-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <button
                                                        class="btn btn-sm btn-warning-gradien fw-bold d-flex align-items-center"
                                                        type="button"
                                                        @click="showConfirmation"
                                                    >
                                                        <i data-feather="refresh-cw" class="me-1"
                                                           style="width: 18px"></i>
                                                        <span>تمــدیــد</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty

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

    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>

    <script>
        $('#basic-1').DataTable({
            order: [[4, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>
@endpush
