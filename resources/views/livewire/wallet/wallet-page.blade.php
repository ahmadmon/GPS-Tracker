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
                                                                <p class="mb-0 pt-2 fw-bolder h5">{{ persianPriceFormat($wallet->amount) }}</p>
                                                                <div class="go-corner">
                                                                    <div class="go-arrow"></div>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>

                                                <li class="nav-item">
                                                    <button class="badge-light-primary btn-block btn-mail w-100 mt-0"
                                                            type="button" data-bs-toggle="modal"
                                                            data-bs-target="#addFundsModal"><i class="me-2"
                                                                                               data-feather="plus-circle"></i>
                                                        افزایش موجودی
                                                    </button>
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
                                                    <a href="#" class="me-4"><i class="me-2" data-feather="printer"></i>پرینت</a>
                                                    <i class="icofont icofont-minus minimize-card"></i>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="taskadd">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            @forelse($myTransactions as $transaction)
                                                                <tr>
                                                                    <td>
                                                                    <span
                                                                            class="badge common-align txt-{{ $transaction->type->badge()['color'] }} rounded-pill badge-l-{{ $transaction->type->badge()['color'] }} border border-{{ $transaction->type->badge()['color'] }} dana fw-bold w-50">
                                                                        <i data-feather="plus-circle"
                                                                           class="me-1 stroke-{{ $transaction->type->badge()['color'] }}"></i>
                                                                        {{ $transaction->type->label() }}
                                                                    </span>
                                                                        <p class="project_name_0">{{ substr($transaction->description,30) }}</p>
                                                                    </td>
                                                                    <td>
                                                                        <h5 class="fw-bold txt-{{ $transaction->type->badge()['color'] }}">{{ priceFormat($transaction->amount) }} </h5>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge badge-{{ $transaction->status->badge()['color'] }} stroke-{{ $transaction->status->badge()['color'] }} dana">{{ $transaction->status->label() }}</span>
                                                                    </td>
                                                                    <td class="task-date">
                                                                        {{ jalaliDate($transaction->created_at, format: "%d %B %Y , H:i") }}
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr>
                                                                    <p class="text-muted text-center">موجودی یافت نشد.
                                                                    </p>
                                                                </tr>
                                                            @endforelse

                                                            <tr>
                                                                <td>
                                                                    <span
                                                                            class="badge common-align txt-danger rounded-pill badge-l-danger border border-danger dana fw-bold w-50">
                                                                        <i data-feather="minus-circle"
                                                                           class="me-1 stroke-danger"></i>
                                                                        برداشت
                                                                    </span>
                                                                    <p class="project_name_0">پاره ای از
                                                                        توضیحاتثیثثبدنثیصثیصصثزصث...</p>
                                                                </td>
                                                                <td>
                                                                    <h5 class="fw-bold txt-danger">200,000 تومان</h5>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                            class="badge badge-success stroke-success dana">موفقیت آمیز</span>
                                                                </td>
                                                                <td class="task-date">
                                                                    18 فروردین ۱۴۰۴ , 22:12:30
                                                                </td>
                                                            </tr>
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
                                                    <a href="#" class="me-4"><i class="me-2" data-feather="printer"></i>پرینت</a>
                                                    <i class="icofont icofont-minus minimize-card"></i>
                                                </div>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="taskadd">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <tr>
                                                                <td>
                                                                    <span
                                                                            class="badge common-align txt-success rounded-pill badge-l-success border border-success dana fw-bold w-50">
                                                                        <i data-feather="plus-circle"
                                                                           class="me-1 stroke-success"></i>
                                                                        واریز
                                                                    </span>
                                                                    <p class="project_name_0">پاره ای از
                                                                        توضیحاتثیثثبدنثیصثیصصثزصث...</p>
                                                                </td>
                                                                <td>
                                                                    <h5 class="fw-bold txt-success">450,000 تومان</h5>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                            class="badge badge-success stroke-success dana">موفقیت آمیز</span>
                                                                </td>
                                                                <td class="task-date">
                                                                    12 فروردین ۱۴۰۴ , 12:45:36
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <span
                                                                            class="badge common-align txt-danger rounded-pill badge-l-danger border border-danger dana fw-bold w-50">
                                                                        <i data-feather="minus-circle"
                                                                           class="me-1 stroke-danger"></i>
                                                                        برداشت
                                                                    </span>
                                                                    <p class="project_name_0">پاره ای از
                                                                        توضیحاتثیثثبدنثیصثیصصثزصث...</p>
                                                                </td>
                                                                <td>
                                                                    <h5 class="fw-bold txt-danger">200,000 تومان</h5>
                                                                </td>
                                                                <td>
                                                                    <span
                                                                            class="badge badge-success stroke-success dana">موفقیت آمیز</span>
                                                                </td>
                                                                <td class="task-date">
                                                                    18 فروردین ۱۴۰۴ , 22:12:30
                                                                </td>
                                                            </tr>
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
        <x-partials.modals.add-funds-modal/>
    </div>

</div>

@assets
<!-- Page js-->
<script src="{{ asset('assets/js/cleave/cleave.min.js') }}"></script>
@endassets

@script
<script>
    const modal = document.getElementById('addFundsModal');

    if (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            const amountInput = document.getElementById('wallet-amount');

            if (amountInput) {
                console.log(amountInput)
                amountInput.focus()
                amountInput.select()
            }
        })
    }
</script>
@endscript
