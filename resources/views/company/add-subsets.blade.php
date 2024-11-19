@extends('01-layouts.master')

@section('title', 'افزودن زیر مجموعه')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/jquery.dataTables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    {{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/test/jquery.dataTables.css') }}">--}}
    {{--    <link rel="stylesheet" type="text/css" href="{{ asset('assets/test/dataTables.bootstrap5.css') }}">--}}
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/test/select.bootstrap5.css') }}">
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
                            <a href="{{ route('company.index') }}">
                                لیست سازمان ها
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">افزودن زیر مجموعه</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('company.store') }}" method="POST" class="row" autocomplete="off"
          enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 card-no-border">
                        <h4>لیست کاربران</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar text-nowrap">
                            <table class="display" id="basic-1">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>کاربر</th>
                                    <th>شماره تماس</th>
                                    <th>تاریخ عضویت</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $user->name }}</span>
                                                <small
                                                    class="text-muted">{{ $user->user_type ? 'ادمین' : 'کاربر' }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $user->phone }}</td>
                                        <td>
                                            <span class="text-muted">{{ jalaliDate($user->created_at) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">داده ای یافت نشد.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-12 mt-2">
            <button class="btn btn-primary" type="submit">افزودن</button>
        </div>
    </form>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables1.js') }}"></script>
    <script src="{{ asset('assets/js/datatable/datatables/dataTables.bootstrap5.js')}}"></script>

    {{--    <script src="{{ asset('assets/test/jquery.dataTables.min.js') }}"></script>--}}
    {{--    <script src="{{ asset('assets/test/dataTables1.js')}}"></script>--}}
    {{--    <script src="{{ asset('assets/test/dataTables.bootstrap5.js')}}"></script>--}}
        <script src="{{ asset('assets/test/dataTables.select.js')}}"></script>
        <script src="{{ asset('assets/test/select.bootstrap5.js')}}"></script>

    <script>
        $('#basic-1').DataTable({
            columnDefs: [
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }
            ],
            order: [[3, 'asc']],
            language: {
                url: "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json"
            },
            select: {
                style: 'os',
                selector: 'td:first-child'
            }
        });

    </script>
@endpush
