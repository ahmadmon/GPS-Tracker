@php
    use App\Enums\Wallet\TransactionType;
    use App\Enums\Wallet\TransactionStatus
@endphp
<div>

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
                        <li class="breadcrumb-item dana">کیف پول من</li>
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

                                                <li class="nav-item" wire:ignore>
                                                    <button class="badge-light-primary btn-block btn-mail w-100 mt-0"
                                                            type="button" data-bs-toggle="modal"
                                                            data-bs-target="#addFundsModal"><i class="me-2"
                                                                                               data-feather="plus-circle"></i>
                                                        افزایش موجودی
                                                    </button>
                                                    <hr>
                                                </li>

                                                <li class="nav-item">
                                                    <div class="mb-3">
                                                        <label for="search">جستجو</label>
                                                        <input type="text" id="search" class="form-control"
                                                               wire:model.live.debounce.850ms="search"
                                                               placeholder="بر اساس مبلغ..."
                                                               autocomplete="off">
                                                        <x-input-error :messages="$errors->get('search')" class="mt-2"/>
                                                    </div>
                                                </li>

                                                <li class="nav-item">
                                                    <div class="mb-3">
                                                        <label for="type">نــوع تراکنش</label>
                                                        <select id="type" class="form-select" wire:model.change="type">
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
                                                        <select id="status" class="form-select"
                                                                wire:model.change="status">
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
                                                    <div class="mb-3" x-data="dateFlatpickr($refs.date)">
                                                        <label for="date">تاریــخ تراکنش</label>
                                                        <div class="input-group flatpicker-calender">
                                                            <div class="input-group flatpicker-calender" wire:ignore>
                                                                <input class="form-control" id="date" type="date"
                                                                       wire:model.live="date" x-ref="date">
                                                            </div>
                                                        </div>
                                                        <x-input-error :messages="$errors->get('date')" class="mt-2"/>
                                                    </div>
                                                </li>


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
                                                    لیست تراکنش های من
                                                </h5>

                                                <div class="card-header-right">
                                                    {{--                                                    <a href="#" class="me-4"><i class="me-2" data-feather="printer"></i>پرینت</a>--}}
                                                    <i class="icofont icofont-minus minimize-card"></i>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="taskadd">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            @forelse($myTransactions as $transaction)
                                                                <tr wire:key="{{ $transaction->id }}">
                                                                    <td wire:ignore>
                                                                        <span
                                                                            class="badge common-align txt-{{ $transaction->typeDisplay['color'] }} rounded-pill badge-l-{{ $transaction->typeDisplay['color'] }} border border-{{ $transaction->typeDisplay['color'] }} dana fw-bold w-50">
                                                                            <i data-feather="{{ $transaction->typeDisplay['icon'] }}"
                                                                               class="me-1 stroke-{{ $transaction->typeDisplay['color'] }}"></i>
                                                                            {{ $transaction->type->label() }}
                                                                        </span>
                                                                        <div wire:ignore>
                                                                            <p data-bs-toggle="tooltip"
                                                                               data-bs-placement="top"
                                                                               title="{{ $transaction?->description }}"
                                                                               class="project_name_0">{{ str($transaction?->description)->limit(30) }}</p>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="fw-bold txt-{{ $transaction->typeDisplay['color'] }}">{{ priceFormat($transaction->amount) }}
                                                                            <small style="font-size: 10px">تومان</small>
                                                                        </h5>
                                                                    </td>
                                                                    <td>
                                                                        @php $isPending = (bool)$transaction->status->isPending(); @endphp
                                                                        @if(!$isPending)
                                                                            <span @class(["dana badge badge-{$transaction->statusDisplay['color']} stroke-{$transaction->statusDisplay['color']}"])>
                                                                                {{ $transaction->statusDisplay['label'] }}
                                                                            </span>
                                                                        @else
                                                                            <button
                                                                                wire:click="retryPayment({{ $transaction->id }})"
                                                                                x-data="{ label: @js($transaction->statusDisplay['label']) }"
                                                                                @mouseenter="label = 'پرداخت مجدد'"
                                                                                @mouseleave="label = @js($transaction->statusDisplay['label'])"
                                                                                type="button" @class(["cursor-pointer btn btn-sm btn-outline-warning p-1"])>
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

                    @if($isManager)
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
                                                        لیست تراکنش های سازمان های شما
                                                    </h5>

                                                    <div class="card-header-right">
                                                        {{--                                                    <a href="#" class="me-4"><i class="me-2" data-feather="printer"></i>پرینت</a>--}}
                                                        <i class="icofont icofont-minus minimize-card"></i>
                                                    </div>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div class="taskadd">
                                                        <div class="table-responsive">
                                                            @php $shownCompanies = [] @endphp
                                                            <table class="table">
                                                                @forelse($companiesTransactions as $transaction)
                                                                    <tr wire:key="{{ $transaction->id }}">
                                                                        <td>
                                                                    <span
                                                                        class="badge common-align txt-{{ $transaction->typeDisplay['color'] }} rounded-pill badge-l-{{ $transaction->typeDisplay['color'] }} border border-{{ $transaction->typeDisplay['color'] }} dana fw-bold w-50">
                                                                        <i data-feather="{{ $transaction->typeDisplay['icon'] }}"
                                                                           class="me-1 stroke-{{ $transaction->typeDisplay['color'] }}"></i>
                                                                        {{ $transaction->type->label() }}
                                                                    </span>
                                                                            <div wire:ignore>
                                                                                <p data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="{{ $transaction?->description }}"
                                                                                   class="project_name_0">{{ str($transaction?->description)->limit(30) }}</p>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <a href="{{ route('company.show', $transaction->source_id) }}"
                                                                               target="_blank">
                                                                                {{ $transaction->source?->name ?? '-' }}
                                                                            </a>
                                                                        </td>
                                                                        <td>
                                                                            <h5 class="fw-bold txt-{{ $transaction->typeDisplay['color'] }}">{{ priceFormat($transaction->amount) }}
                                                                                <small
                                                                                    style="font-size: 10px">تومان</small>
                                                                            </h5>
                                                                        </td>
                                                                        <td>
                                                                            <span
                                                                                class="badge badge-{{ $transaction->statusDisplay['color'] }} stroke-{{ $transaction->statusDisplay['color'] }} dana">{{ $transaction->statusDisplay['label'] }}</span>
                                                                        </td>
                                                                        <td class="task-date">
                                                                            {{ jalaliDate($transaction->created_at, format: "%d %B %Y , H:i") }}
                                                                        </td>
                                                                        <td wire:ignore>
                                                                            @if(!in_array($transaction->source_id,$shownCompanies))
                                                                                @php $shownCompanies[] = $transaction->source_id; @endphp

                                                                                <p class="txt-linkedin"
                                                                                   data-bs-toggle="tooltip"
                                                                                   data-bs-placement="top"
                                                                                   title="موجودی کیف پول">
                                                                                    {{ persianPriceFormat($transaction->source->wallet->balance ?? '-') }}
                                                                                </p>
                                                                            @else

                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6" class="text-muted text-center">
                                                                            تراکنشی یافت نشد :(
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
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
                    @endif
                </div>
            </div>
        </div>
        <x-partials.modals.add-funds-modal :$isManager :$user/>
    </div>

</div>

@assets
<!-- Page js-->
<script src="{{ asset('assets/js/cleave/cleave.min.js') }}"></script>

<!-- // Date Picker assets  -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flatpickr/flatpickr.min.css') }}">
<script src="{{ asset('assets/js/flat-pickr/jdate.js') }}"></script>
<script src="{{ asset('assets/js/flat-pickr/flatpickr-jdate.js') }}"></script>
<script src="{{ asset('assets/js/flat-pickr/l10n/fa-jdate.js') }}"></script>
@endassets

@script
<script>
    const modal = document.getElementById('addFundsModal');

    if (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            const amountInput = document.getElementById('wallet-amount');

            if (amountInput) {
                amountInput.focus()
                amountInput.select()
            }
        })
    }


    // Date picker
    //------------------------------------------------------
    Alpine.data('dateFlatpickr', (input) => ({
        flatpickrInstance: null,

        init() {
            this.initializeFlatpickr();

            // $wire.on('locationUpdated', () => {
            //     if (this.flatpickrInstance) {
            //         this.flatpickrInstance.destroy();
            //     }
            //
            //     this.initializeFlatpickr();
            // });
        },

        initializeFlatpickr() {
            this.flatpickrInstance = flatpickr(input, {
                locale: "fa",
                altInput: true,
                altFormat: 'Y/m/d',
                maxDate: "today",
                disableMobile: true,
                placeholder: @js(jalaliDate(now(), format: 'Y/m/d')),
                onClose: (selectedDate, dateStr) => {
                    console.log(selectedDate,dateStr)
                    // $wire.set('dateTimeRange', dateStr);
                }
            });
        }
    }))
</script>
@endscript
