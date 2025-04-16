@php
    use App\Enums\Wallet\TransactionType;
    use App\Enums\Wallet\TransactionStatus;
    $loadAlpineJs = true;
@endphp

@extends('01-layouts.master')

@section('title', "شارژ کیف پول {$type}")

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
                            <a href="{{ route('wallet-management.show', $walletId) }}">
                                جزئیات کیف پول {{ $type }}
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">شارژ کیف پول {{ $type }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <div class="card-wrapper border rounded-3">
                <form class="form-bookmark needs-validation" action="{{ route('wallet-management.store', $walletId) }}"
                      method="POST"
                      id="bookmark-form" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3 mt-0" x-data="{
                            init(){
                                new Cleave($refs.walletAmount,{
                                    numeral: true
                                })
                            }
                        }">
                            <label for="wallet-amount">مبلغ مورد نظر جهت شارژ کیف پول (تومان)
                                <sup class="text-danger fw-bold">*</sup>
                            </label>
                            <input class="form-control" id="wallet-amount" x-ref="walletAmount" type="text" required
                                   placeholder="مبلغ را وارد کنید..."
                                   name="amount" value="{{ old('amount') }}"
                                   autocomplete="off">
                            <x-input-error :messages="$errors->get('amount')" class="mt-2"/>
                        </div>


                        <div class="col-12 col-md-6 mb-3 my-0">
                            <label for="wallet-description">نوع تراکنش</label>
                            @php
                                $types = [
                                    TransactionType::CREDIT->value => TransactionType::CREDIT->label(),
                                    TransactionType::DEBIT->value => TransactionType::DEBIT->label(),
                                ];
                                $oldType = old('type');
                            @endphp
                            <select name="type" id="wallet-type" class="form-select">
                                @foreach($types as $value => $label)
                                    <option
                                        value="{{ $value }}" @selected(old('type', $oldType) == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2"/>
                        </div>

                        <div class="col-12 mb-3 my-0">
                            <label for="wallet-description">توضیحات</label>
                            <textarea class="form-control" autocomplete="off"
                                      id="wallet-description"
                                      name="description"
                                      placeholder="مثلاً: واریز برای استفاده از خرید اشتراک ماهانه...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2"/>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end" x-data="{
                                onlinePaymentUrl: @js(route('wallet-management.send-to-gateway', $walletId)),
                                manualCredit: @js(route('wallet-management.store', $walletId))
                    }">
                        <button class="btn btn-secondary btn-sm me-2" id="Bookmark"
                                @click.prevent="$el.closest('form').action = onlinePaymentUrl; $el.closest('form').submit()"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="با کلیک بر روی این دکمه، به درگاه پرداخت آنلاین منتقل می‌شوید."
                                type="submit">
                                <span class="">انتقال به درگاه پرداخت</span>
                        </button>

                        <button class="btn btn-primary btn-sm" id="increase-wallet"
                                @click.prevent="$el.closest('form').action = manualCredit; $el.closest('form').submit()"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="با کلیک بر روی این دکمه، می‌توانید به صورت مستقیم و بدون نیاز به درگاه پرداخت، موجودی کیف پول {{ $type }} را افزایش دهید."
                                type="submit">
                                <span class="">افزایش اعتبار</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/cleave/cleave.min.js') }}"></script>
@endpush
