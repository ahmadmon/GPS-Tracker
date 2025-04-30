@extends('01-layouts.master')

@section('title', 'جزئیات اشتراک')

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
                            <a href="{{ route('wallet-management.show', auth()->user()->wallet->id) }}">کیف پول من</a>
                        </li>
                        <li class="breadcrumb-item dana">جزئیات اشتراک</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

    </div>
@endsection

@push('scripts')
    <script>
        window.addEventListener('alpine:init', () => {

        })
    </script>
@endpush
