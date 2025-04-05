@extends('01-layouts.master')

@section('title', 'پروفایل کاربری')

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
                        <li class="breadcrumb-item dana">کیف پول</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

@endpush
