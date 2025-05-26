@php
    use App\Enums\Wallet\TransactionType;
    use App\Enums\Wallet\TransactionStatus;
    $loadAlpineJs = true;
@endphp

@extends('01-layouts.master')

@section('title',$isUser ? 'کاربر' : 'سازمان' . "شارژ کیف پول ")

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
                            <a href="{{ route('wallet-management.show', $wallet->id) }}">
                                جزئیات کیف پول {{ $isUser ? 'کاربر' : 'سازمان' }}
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">شارژ کیف پول {{ $isUser ? 'کاربر' : 'سازمان' }}</li>
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
                                                @if($isUser)
                                                    <div class="media-size-email"><img class="me-3 rounded-circle"
                                                                                       src="{{ asset('assets/images/avtar/user.png') }}"
                                                                                       alt=""></div>
                                                    <div class="media-body">
                                                        <h6 class="f-w-600">{{ $entity->name }}</h6>
                                                        <p>{{ $entity->phone }} | {{ $entity->type['name'] }}</p>
                                                    </div>
                                                @else
                                                    <div class="media-size-email">
                                                        <img class="me-3 rounded-circle img-70 object-fit-cover"
                                                             src="{{ asset($entity->logo) }}" alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        <h6 class="f-w-600">{{ $entity->name }}</h6>
                                                        <p>{{ $entity->contact_number }}
                                                            | {{ $entity->manager->name }}</p>
                                                    </div>
                                                @endif
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
                                                    <a href="{{ route('wallet-management.show', $wallet) }}"
                                                       class="btn btn-primary-gradien d-flex justify-content-center btn-block w-100 mt-0">
                                                        <i class="me-2" data-feather="arrow-right"></i>
                                                        بــازگشت
                                                    </a>
                                                    <hr>
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
                    <div class="card">
                        <h5 class="card-header">افزایش موجودی کیف پول</h5>
                        <div class="card-body">
                            <div class="card-wrapper border rounded-3">
                                <form class="form-bookmark needs-validation"
                                      action="{{ route('wallet-management.store', $wallet->id) }}"
                                      method="POST"
                                      id="bookmark-form" novalidate>
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 mb-3 mt-0" x-data="{
                            init(){
                                new Cleave($refs.walletAmount,{
                                    numeral: true
                                })
                            }
                        }">
                                            <label for="wallet-amount">مبلغ مورد نظر جهت شارژ کیف پول (تومان)
                                                <sup class="text-danger fw-bold">*</sup>
                                            </label>
                                            <input class="form-control" id="wallet-amount" x-ref="walletAmount"
                                                   type="text" required
                                                   placeholder="مبلغ را وارد کنید..."
                                                   name="amount" value="{{ old('amount') }}"
                                                   autocomplete="off">
                                            <x-input-error :messages="$errors->get('amount')" class="mt-2"/>
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
                                onlinePaymentUrl: @js(route('wallet-management.send-to-gateway', $wallet->id)),
                                manualCredit: @js(route('wallet-management.store', $wallet->id))
                    }">
                                        @if(can('wallet-pay-gateway'))
                                            <button class="btn btn-secondary btn-sm me-2" id="Bookmark"
                                                    @click.prevent="$el.closest('form').action = onlinePaymentUrl; $el.closest('form').submit()"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="با کلیک بر روی این دکمه، به درگاه پرداخت آنلاین منتقل می‌شوید."
                                                    type="submit">
                                                <span class="">انتقال به درگاه پرداخت</span>
                                            </button>
                                        @endif

                                        @if(can('wallet-manual-credit'))
                                            <button class="btn btn-primary btn-sm" id="increase-wallet"
                                                    @click.prevent="$el.closest('form').action = manualCredit; $el.closest('form').submit()"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="با کلیک بر روی این دکمه، می‌توانید به صورت مستقیم و بدون نیاز به درگاه پرداخت، موجودی کیف پول موردنظر را افزایش دهید."
                                                    type="submit">
                                                <span class="">افزایش اعتبار</span>
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/cleave/cleave.min.js') }}"></script>
@endpush
