@php use App\Models\User; @endphp
@extends('01-layouts.master')

@section('title', 'لیست درخواست های لغو اشتراک')

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
                        <li class="breadcrumb-item dana">لیست درخواست های لغو اشتراک</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid">
        <x-partials.alert.success-alert/>
        <div class="row">
            <!-- Zero Configuration  Starts-->
            <div class="col-sm-12">
                @if(can('create-vehicle'))
                    <a href="{{ route('subscription-cancellation.create') }}" class="btn btn-primary mb-4">+ لغو
                        اشتراک</a>
                @endif
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>لیست درخواست های لغو اشتراک</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th>درخواست دهنده</th>
                                    <th>طرح اشتراک</th>
                                    <th>شماره شبا</th>
                                    <th>مبلغ بازگشتی</th>
                                    <th>وضعیت</th>
                                    <th>تاریخ لغو</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cancellationRequests as $cancellation)
                                    @php
                                        $wallet = $cancellation->subscription->wallet;
                                        $walletable = $wallet->walletable;
                                        $plan = $cancellation->subscription->plan;
                                        $isUser = $walletable instanceof User;
                                    @endphp
                                    <tr>
                                        <td data-sort="{{ $cancellation->created_at }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <div>
                                                    <a class="f-14 mb-0 f-w-500 c-light"
                                                       href="{{ route($isUser ? 'user.show' : 'company.show', $walletable) }}">{{ $walletable->name }}</a>
                                                    <p class="c-o-light text-muted cursor-pointer"
                                                       data-bs-toggle="tooltip" data-bs-placement="top"
                                                       data-bs-title="{{ $cancellation?->reason }}">{{ str($cancellation?->reason)->limit(35) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $plan->name }}</td>
                                        <td>{{ $cancellation->iban }}</td>
                                        <td>{{ priceFormat($cancellation->refund_amount) }} تومان</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $cancellation->status->badge()->color }} dana rounded-pill">{{ $cancellation->status->badge()->name }}</span>
                                        </td>
                                        <td>{{ jalaliDate($cancellation->canceled_at, format: "%d %B %Y H:i") }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <form
                                                    action="{{ route('subscription-cancellation.update', $cancellation) }}"
                                                    method="post">
                                                    @csrf @method('PUT')
                                                    <button class="btn btn-sm btn-success"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            data-bs-title="تایید کردن">
                                                        <i data-feather="check" width="10" height="10"></i>
                                                    </button>
                                                </form>

                                                <div data-bs-toggle="tooltip" data-bs-placement="top" title="رد کردن">
                                                    <button class="btn btn-sm btn-dark ms-1"
                                                            x-data
                                                            @click="$dispatch('cancellation-request-id', {id: '{{ $cancellation->id }}'})"
                                                            data-bs-target="#rejection-reason-modal"
                                                            data-bs-toggle="modal">
                                                        <i data-feather="slash" width="10" height="10"></i>
                                                    </button>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Zero Configuration  Ends-->
        </div>
    </div>

    <x-partials.modals.rejection-reason/>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>

    <script>
        $('#basic-1').DataTable({
            order: [[5, 'desc']],
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            }
        });
    </script>

    <script>
        window.addEventListener('alpine:init', () => {
            Alpine.data('rejectionReason', () => ({
                reason: '',
                id: null,
                required: false,

                submitForm() {
                    if (!this.reason.trim()) {
                        this.required = true;
                        return false;
                    }
                    this.$refs.rejectionReasonForm.submit();
                }

            }))
        })
    </script>
@endpush
