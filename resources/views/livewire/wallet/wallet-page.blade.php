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
                                                                <p class="mb-0 pt-2 fw-bolder h5">{{ priceFormat($wallet->amount) }}
                                                                    تومان</p>
                                                                <div class="go-corner">
                                                                    <div class="go-arrow"></div>
                                                                </div>
                                                            </a></div>
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
                        <div class="card email-body radius-left">
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
                                                            <tr>
                                                                <td>
                                                                    <h6 class="task_title_0">Client meeting</h6>
                                                                    <p class="project_name_0">General</p>
                                                                </td>
                                                                <td>
                                                                    <p class="task_desc_0">Lorem Ipsum is simply dummy
                                                                        text of the printing and typesetting industry.
                                                                        Lorem Ipsum has been</p>
                                                                </td>
                                                                <td><a class="me-2" href="#"><i data-feather="link"></i></a><a
                                                                        href="#"><i data-feather="more-horizontal"></i></a>
                                                                </td>
                                                                <td><a href="#"><i data-feather="trash-2"></i></a></td>
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
