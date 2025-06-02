@extends('01-layouts.master')

@section('title', 'مدیریت زیر مجموعه ها')

@push('styles')

@endpush

@section('content')
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
                            <li class="breadcrumb-item dana">
                                <a href="{{ route('company.index') }}">
                                    لیست سازمان ها
                                </a>
                            </li>
                            <li class="breadcrumb-item dana">
                                <a href="{{ route('company.show', $company) }}">
                                    جزئیات سازمان
                                </a>
                            </li>
                            <li class="breadcrumb-item dana">مدیریت زیر مجموعه</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6">
                <livewire:company.add-subsets :$company wire:key="{{ randomKey(15) }}"/>
            </div>
            <div class="col-xl-6">
                <livewire:company.remove-subsets :$company wire:key="{{ randomKey(16) }}"/>
            </div>
        </div>
    </div>
@endsection
