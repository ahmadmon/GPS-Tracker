@php use App\Facades\Acl; @endphp
@extends('01-layouts.master')

@section('title', 'خرید اشتراک')

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
                        <li class="breadcrumb-item dana">خرید اشتراک</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="header-faq">
                    <h5 class="txt-primary">فعالســازی اشتــراک
                        برای {{ $isUser ? "شما" : $wallet->walletable?->name }}
                    </h5>
                    <div class="d-flex align-items-center mt-2">
                        <img src="{{ asset('assets/images/custom/subscription.png') }}" width="40" height="40"
                             alt="">
                        <h1 class="mb-0 mt-4 ms-2">طـــرح موردنظر خود را انتخاب کنید!</h1>
                    </div>
                </div>

                @if($isUser)
                    @role(['manager'])
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <div data-feather="alert-circle" class="me-1"></div>
                        <div>
                            <h5 class="txt-light">مدیر محترم، برای خرید اشتراک برای سازمان خود، لطفاً مراحل زیر را دنبال
                                فرمایید:</h5>
                            <ol class="list-unstyled p-2">
                                <li class="txt-light">به قسمت <a href="{{ route('company.index') }}"
                                                                 class="alert-link text-dark text-decoration-underline">لیست
                                        سازمان‌ها</a> مراجعه کنید.
                                </li>
                                <li class="txt-light">در قسمت عملیات، گزینه <span class="text-dark alert-link">خرید اشتراک</span>
                                    کلیک کنید.
                                </li>
                                <li class="txt-light">پلن اشتراک مورد نظر را انتخاب کرده و خرید را تکمیل کنید.</li>
                            </ol>
                        </div>
                    </div>
                    @endrole
                @endif
            </div>

            <x-partials.alert.success-alert/>
            <x-partials.alert.error-alert/>
            <div class="col-md-7 xl-60">
                <div class="faq-wrap">
                    <div class="col-lg-12">
                        <div class="default-according style-1 faq-accordion">
                            <!-- Question 1 -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link ps-0 dana" data-bs-toggle="collapse"
                                                data-bs-target="#ques-1" aria-expanded="true"
                                                aria-controls="ques-1"><i data-feather="help-circle"></i>سامانه چگونه
                                            کار می‌کند؟
                                        </button>
                                    </h5>
                                </div>
                                <div class="collapse show" id="ques-1" aria-labelledby="ques-1"
                                     data-bs-parent="#accordionoc">
                                    <div class="card-body">
                                        با استفاده از سامانه، شما می‌توانید دستگاه‌های GPS خود را وارد کرده و آن‌ها را
                                        فعال کنید. بعد از فعال‌سازی، به راحتی می‌توانید دستگاه خود را روی نقشه آنلاین
                                        ردیابی کنید و مسیرهایی که طی کرده‌اید را در بازه‌های زمانی مشخص مشاهده کنید.
                                        علاوه بر این، اگر مدیر سازمان هستید، می‌توانید سازمان‌های مختلف ایجاد کنید و
                                        زیرمجموعه‌های خود را مدیریت کنید.
                                    </div>
                                </div>
                            </div>
                            <!-- Question 2 -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed ps-0 dana" data-bs-toggle="collapse"
                                                data-bs-target="#ques-2" aria-expanded="false" aria-controls="ques-2"><i
                                                data-feather="help-circle"></i> چگونه اشتراک خود را تمدید یا لغو کنم؟
                                        </button>
                                    </h5>
                                </div>
                                <div class="collapse" id="ques-2" aria-labelledby="ques-2"
                                     data-bs-parent="#accordionoc">
                                    <div class="card-body">
                                        اگر گزینه تمدید خودکار را فعال کرده باشید و موجودی کیف پول شما کافی باشد، اشتراک
                                        شما به طور خودکار تمدید می‌شود. در غیر این صورت، باید اشتراک خود را به صورت دستی
                                        تمدید کنید. برای لغو اشتراک هم می‌توانید از بخش جزئیات اشتراک اقدام کنید.
                                    </div>
                                </div>
                            </div>
                            <!-- Question 3 -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed ps-0 dana" data-bs-toggle="collapse"
                                                data-bs-target="#ques-3" aria-expanded="false" aria-controls="ques-3"><i
                                                data-feather="help-circle"></i> اگر اشتراک من تمام شود، چه اتفاقی
                                            می‌افتد؟
                                        </button>
                                    </h5>
                                </div>
                                <div class="collapse" id="ques-3" aria-labelledby="ques-3"
                                     data-bs-parent="#accordionoc">
                                    <div class="card-body">
                                        یک روز قبل از انقضای اشتراک شما، از طریق پیامک و اعلان در سامانه به شما اطلاع
                                        داده می‌شود. اگر تمدید خودکار را فعال کرده باشید و موجودی کیف پول کافی باشد،
                                        اشتراک به صورت خودکار تمدید می‌شود. در غیر این صورت، دسترسی شما به سامانه پس از
                                        پایان اشتراک قطع خواهد شد.
                                    </div>
                                </div>
                            </div>
                            <!-- Question 4 -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed ps-0 dana" data-bs-toggle="collapse"
                                                data-bs-target="#ques-4" aria-expanded="false" aria-controls="ques-4"><i
                                                data-feather="help-circle"></i>اگر از اشتراک خود راضی نباشم، می‌توانم
                                            وجه خود را پس بگیرم؟
                                        </button>
                                    </h5>
                                </div>
                                <div class="collapse" id="ques-4" aria-labelledby="ques-4"
                                     data-bs-parent="#accordionoc">
                                    <div class="card-body">
                                        بله! اگر تا 48 ساعت از استفاده از سامانه و اشتراک خود راضی نباشید، می‌توانید با
                                        تیم پشتیبانی تماس بگیرید و درخواست عودت وجه کنید. ما همیشه آماده کمک به شما
                                        هستیم!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5 xl-40">
                <form action="{{ route('profile.subscription.subscribe', $wallet) }}" method="post"
                      id="handle-plan">
                    @csrf
                    <x-input-error :messages="$errors->get('plan')" class="mt-2"/>

                    <section class="mega-horizontal">
                        @foreach($plans as $plan)
                            <div class="card height-equal">
                                <div class="media p-20">
                                    <div class="form-check radio radio-primary m-0 w-100">
                                        <input class="form-check-input"
                                               @checked(old('plan', $loop->first)) id="radio-{{ $plan->slug }}"
                                               type="radio"
                                               name="plan" value="{{ $plan->id }}">
                                        <label class="form-check-label mb-0 w-100" for="radio-{{ $plan->slug }}"><span
                                                class="media-body megaoption-space"><span class="mt-0 mega-title-badge">{{ $plan->name }}<span
                                                        class="badge bg-secondary pull-right digits dana">{{ priceFormat($plan->price) }} تومان</span></span><span>{!! e(nl2br($plan?->description)) !!}</span></span></label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </section>
                    <div class="d-flex mt-2">
                        <div class="text-end icon-state">
                            <label class="switch mb-0">
                                <input type="checkbox" name="auto_renew"><span
                                    class="switch-state bg-primary"></span>
                            </label>
                        </div>
                        <label class="col-form-label m-l-10">تمدید خودکار اشتراک</label>
                    </div>
                    <x-input-error :messages="$errors->get('auto_renew')" class="mt-2"/>
                </form>
            </div>
            <div class="col-12">
                <div class="d-flex justify-content-end" x-data="handleSubscription">
                    <button type="button"
                            @click="showConfirmation"
                            class="btn btn-primary btn-block d-flex justify-content-around align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M23 4a3 3 0 0 0-3-3H4a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h1a1 1 0 1 0 0-2H4a1 1 0 0 1-1-1V8h18v6a1 1 0 0 1-1 1h-1a1 1 0 1 0 0 2h1a3 3 0 0 0 3-3zm-2 2V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v2z"
                                  fill="#FFFF"/>
                            <path
                                d="M13 22a1 1 0 1 1-2 0v-5.593L9.707 17.7a1 1 0 1 1-1.414-1.414l3-2.994a1 1 0 0 1 1.413.001l2.999 3a1 1 0 1 1-1.414 1.413L13 16.417z"
                                fill="#FFFF"/>
                        </svg>

                        <span class="ms-2">برداشت از کیف پول</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('alpine:init', () => {
            Alpine.data('handleSubscription', () => ({
                showConfirmation() {
                    Swal.fire({
                        title: "تایید برداشت از کیف پول",
                        text: "شما در حال برداشت از کیف پول خود جهت خرید اشتراک هستید. آیا مطمئنید که می‌خواهید این تراکنش را انجام دهید؟",
                        icon: "warning",
                        showCancelButton: true,
                        reverseButtons: true,
                        cancelButtonText: "لغو",
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "تایید و ادامه"
                    }).then((result) => {
                        if (result.value) {
                            document.getElementById('handle-plan').submit()
                        }
                    });
                }
            }))
        })
    </script>
@endpush
