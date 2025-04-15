@php
    use App\Enums\Wallet\TransactionType;
    use App\Enums\Wallet\TransactionStatus;
    $loadAlpineJs = true;
@endphp

@extends('01-layouts.master')

@section('title', 'جزئیات کیف پول کاربر')

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
                        <li class="breadcrumb-item dana">جزئیات کیف پول کاربر</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>



    <div class="container-fluid">
        <div class="email-wrap bookmark-wrap">
            <div class="row">
                <div class="col-xl-3 box-col-6">
                    <div class="md-sidebar">
                        <a class="btn btn-primary md-sidebar-toggle" href="javascript:void(0)">فیلتر تراکنش ها</a>
                        <div class="md-sidebar-aside job-left-aside custom-scrollbar">
                            <div class="email-left-aside">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="email-app-sidebar left-bookmark task-sidebar">
                                            <div class="media">
                                                <div class="media-size-email"><img class="me-3 rounded-circle"
                                                                                   src="{{ asset('assets/images/avtar/user.png') }}"
                                                                                   alt=""></div>
                                                <div class="media-body">
                                                    <h6 class="f-w-600">{{ $user->name }}</h6>
                                                    <p>{{ $user->phone }} | {{ $user->type['name'] }}</p>
                                                </div>
                                            </div>
                                            <ul class="nav main-menu mt-2" role="tablist">
                                                <li class="nav-item effective-card">
                                                    <div class="card common-hover">
                                                        <div class="card-body p-2">
                                                            <a class="effect-card d-block p-3"
                                                               style="cursor:default;"
                                                               href="javascript:void(0)">
                                                                <div class="common-box1 common-align">
                                                                    <h5 class="d-block">موجودی:</h5>
                                                                </div>
                                                                <p class="mb-0 pt-2 fw-bolder h5">{{ persianPriceFormat($wallet->balance) }}</p>
                                                                <div class="go-corner">
                                                                    <div class="go-arrow"></div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li class="nav-item">
                                                    <a href="#"
                                                       class="btn btn-primary-gradien d-flex justify-content-center btn-block w-100 mt-0">
                                                        <i class="me-2" data-feather="plus-circle"></i>
                                                        شارژ کیف‌ پول
                                                    </a>
                                                    <hr>
                                                </li>

                                                <li class="nav-item">
                                                    <div class="mb-3">
                                                        <label for="search">جستجو</label>
                                                        <input type="text" id="search" class="form-control"
                                                               placeholder="بر اساس مبلغ..."
                                                               autocomplete="off">
                                                        <x-input-error :messages="$errors->get('search')" class="mt-2"/>
                                                    </div>
                                                </li>

                                                <li class="nav-item">
                                                    <div class="mb-3">
                                                        <label for="type">نــوع تراکنش</label>
                                                        <select id="type" class="form-select">
                                                            <option value="" selected>همه</option>
                                                            <option
                                                                value="{{ TransactionType::CREDIT }}">{{ TransactionType::CREDIT->label() }}</option>
                                                            <option
                                                                value="{{ TransactionType::DEBIT }}">{{ TransactionType::DEBIT->label() }}</option>
                                                        </select>
                                                        <x-input-error :messages="$errors->get('type')" class="mt-2"/>
                                                    </div>
                                                </li>

                                                <li class="nav-item">
                                                    <div class="mb-3">
                                                        <label for="status">وضعیت پــرداخت</label>
                                                        <select id="status" class="form-select">
                                                            <option value="" selected>همه</option>
                                                            <option
                                                                value="{{ TransactionStatus::SUCCESS }}">{{ TransactionStatus::SUCCESS->label() }}</option>
                                                            <option
                                                                value="{{ TransactionStatus::PENDING }}">{{ TransactionStatus::PENDING->label() }}</option>
                                                            <option
                                                                value="{{ TransactionStatus::FAILED }}">{{ TransactionStatus::FAILED->label() }}</option>
                                                        </select>
                                                        <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                                                    </div>
                                                </li>

                                                <li class="nav-item">
                                                    <div class="mb-3">
                                                        <label for="date">تاریــخ تراکنش</label>
                                                        <div class="input-group flatpicker-calender">
                                                            <div class="input-group flatpicker-calender">
                                                                <input class="form-control" id="date" type="date"
                                                                       x-data="dateFlatpickr($el)"
                                                                       placeholder="{{ jalaliDate(now()) }}">
                                                            </div>
                                                        </div>
                                                        <x-input-error :messages="$errors->get('date')" class="mt-2"/>
                                                    </div>
                                                </li>

                                                {{--                                                @if($this->hasFilters)--}}
                                                <li class="nav-item mt-2">
                                                    <button class="btn btn-primary btn-block w-100">
                                                        <span>
                                                            <i data-feather="filter" class="me-1"></i>
                                                            حذف فیلتــرها
                                                        </span>
                                                    </button>
                                                </li>
                                                {{--                                                @endif--}}


                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-9 col-md-12 box-col-12">
                    <x-partials.alert.success-alert/>
                    <x-partials.alert.error-alert/>

                    <div class="email-right-aside bookmark-tabcontent">
                        <div class="card email-body rounded-3">
                            <div class="ps-0">
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="pills-created" role="tabpanel"
                                         aria-labelledby="pills-created-tab">
                                        <div class="card mb-0">
                                            <div class="card-header d-flex">
                                                <h5 class="mb-0">
                                                    <i class="" data-feather="dollar-sign"></i>
                                                    لیست تراکنش های کاربر
                                                </h5>

                                                <div class="card-header-right">
                                                    {{--                                                    <a href="#" class="me-4"><i class="me-2" data-feather="printer"></i>پرینت</a>--}}
                                                    <i class="icofont icofont-minus minimize-card"></i>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="table-responsive custom-scrollbar text-nowrap">
                                                    <table class="display" id="basic-1">
                                                        <thead>
                                                        <tr>
                                                            <th>شماره تراکنش</th>
                                                            <th>نوع تراکنش</th>
                                                            <th>مبلغ تراکنش</th>
                                                            <th>وضعیت</th>
                                                            <th>تاریخ ایجاد</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @forelse($transactions as $transaction)
                                                            <tr>
                                                                <td class="text-muted">
                                                                    {{ $transaction->transaction_number }}
                                                                </td>
                                                                <td>
                                                                        <span
                                                                            class="badge common-align txt-{{ $transaction->typeDisplay['color'] }} rounded-pill badge-l-{{ $transaction->typeDisplay['color'] }} border border-{{ $transaction->typeDisplay['color'] }} dana fw-bold w-50">
                                                                            <i data-feather="{{ $transaction->typeDisplay['icon'] }}"
                                                                               class="me-1 stroke-{{ $transaction->typeDisplay['color'] }}"></i>
                                                                            {{ $transaction->type->label() }}
                                                                        </span>
                                                                    <div>
                                                                        <small class="text-muted"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="bottom"
                                                                               title="{{ $transaction?->description }}"
                                                                               class="project_name_0">{{ str($transaction?->description)->limit(30) }}</small>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                        <span
                                                                            class="fw-bold h6 txt-{{ $transaction->typeDisplay['color'] }}">{{ priceFormat($transaction->amount) }}
                                                                            <small style="font-size: 10px">تومان</small>
                                                                        </span>
                                                                </td>
                                                                <td>
                                                                    @php $isPending = (bool)$transaction->status->isPending(); @endphp
                                                                    @if(!$isPending)
                                                                        <span @class(["dana badge badge-{$transaction->statusDisplay['color']} stroke-{$transaction->statusDisplay['color']}"])>
                                                                                {{ $transaction->statusDisplay['label'] }}
                                                                            </span>
                                                                    @else
                                                                        <button
                                                                            x-data="{ label: @js($transaction->statusDisplay['label']) }"
                                                                            @mouseenter="label = 'پرداخت مجدد'"
                                                                            @mouseleave="label = @js($transaction->statusDisplay['label'])"
                                                                            type="button" @class(["cursor-pointer btn btn-sm btn-warning p-1"])>
                                                                            <span x-text="label"></span>
                                                                        </button>
                                                                    @endif

                                                                </td>
                                                                <td class="task-date">
                                                                    {{ jalaliDate($transaction->created_at, format: "%d %B %Y , H:i") }}
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-muted text-center">
                                                                    تراکنشی یافت نشد :(
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- // Date Picker assets  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
    <script src="{{ asset('assets/js/flat-pickr/jdate.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/flatpickr-jdate.js') }}"></script>
    <script src="{{ asset('assets/js/flat-pickr/l10n/fa-jdate.js') }}"></script>


    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script>
        $('#basic-1').DataTable({
            order: [[4, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>

    <script>
        window.addEventListener('alpine:init', () => {
            // Date picker
            //------------------------------------------------------
            Alpine.data('dateFlatpickr', (input) => ({
                flatpickrInstance: null,

                init() {
                    this.initializeFlatpickr();
                },

                initializeFlatpickr() {
                    this.flatpickrInstance = flatpickr(input, {
                        locale: "fa",
                        altInput: true,
                        altFormat: 'j F, Y',
                        dateFormat: 'Y-m-d',
                        defaultDate: input.value || null,
                        maxDate: "today",
                        disableMobile: true,
                        onClose: (selectedDates, dateStr) => {
                            console.log(selectedDates, dateStr);
                            if (selectedDates.length) {
                                // $wire.set('date', dateStr)
                            } else {
                                // $wire.removeFilters(['date']);
                            }
                        }
                    });
                }
            }))
        })
    </script>
@endpush
