@extends('01-layouts.master')

@php
    $prvTitle = $isUser ? 'لیست اشتراک کاربران' : 'لیست اشتراک سازمان ها';
@endphp
@section('title', 'اعطای اشتراک')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/tagify.css') }}">
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
                            <a href="{{ route('subscription-management.index', ['type' => request('type')]) }}">
                                {{ $prvTitle }}
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">اعطای اشتراک</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('subscription-management.store') }}" method="POST" class="row">
        @csrf

        <div class="card">
            <div class="card-header">
                <h5 class="m-b-0">اعطای اشتراک به {{ $isUser ? 'کاربران' : 'سازمان ها' }}</h5>
            </div>
            <div class="card-body">
                <div class="card-wrapper border rounded-3 row">
                    <div class="col-md-6">
                        <div class="mb-3" x-data="multiSelection($refs.input)">
                            <label class="form-label" for="entity_ids">انتخاب {{ $isUser ? 'کاربر' : 'سازمان' }}
                                <sup class="text-danger">*</sup>
                            </label>
                            <input name="entity_ids" id="entity_ids" x-ref="input" class="form-control"
                                   placeholder="انتخاب کنید...">
                        </div>
                        <x-input-error :messages="$errors->get('entity_ids')" class="mt-2"/>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="plan">طرح اشتراک
                            <sup class="text-danger">*</sup>
                        </label>
                        <select name="plan" id="plan" class="form-select">
                            @foreach($plans as $plan)
                                <option
                                    value="{{ $plan->id }}" @selected(old('plan') == $plan->id)>{{ $plan->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('plan')" class="mt-2"/>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="active-auto">آیا تمدید خودکار فعال شود؟
                            <sup class="text-danger">*</sup>
                        </label>
                        <select name="auto_renew" id="active-auto" class="form-select">
                            <option value="0" @selected(old('auto_renew', true) == 0)>خیر, غیــرفعال</option>
                            <option value="1" @selected(old('auto_renew') == 1)>بله, فعــال</option>
                        </select>
                        <x-input-error :messages="$errors->get('auto_renew')" class="mt-2"/>
                    </div>

                    <div class="col-md-6">
                        <div class="media mt-4">
                            <label class="col-form-label m-r-10">آیا مبلغ طرح از کیف پول {{ $isUser ? 'کاربر' : 'سازمان' }} برداشت شود؟</label>
                            <div class="media-body text-end icon-state">
                                <label class="switch">
                                    <input type="checkbox" name="withdraw_wallet" @checked(old('withdraw_wallet', true))><span class="switch-state"></span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('withdraw_wallet')" class="mt-2"/>
                        </div>
                    </div>

                    <input type="hidden" name="type" value="{{ request('type') }}">

                    <div class="col-12 mt-2 text-end">
                        <button class="btn btn-primary" type="submit">ثبت</button>
                    </div>

                </div>
            </div>
        </div>


    </form>

@endsection


@push('scripts')
    <script src="{{ asset('assets/js/select2/tagify.js') }}"></script>

    <script>
        window.addEventListener('alpine:init', () => {

            // none Subscribed Models Selection
            //------------------------------------------------------
            Alpine.data('multiSelection', (input) => ({
                lists: @js($noneSubscribedModels ?? []),
                oldValue: @js(old('entity_ids', '')),

                init() {
                    const oldValues = this.oldValue ?
                        this.oldValue.split(',').map(id => {
                            const item = this.lists.find(x => parseInt(x.value) === parseInt(id));
                            return item.name || 'نامشخص'
                        }) : [];
                    input.value = oldValues.toString()


                   new Tagify(input, {
                        tagTextProp: 'value',
                        whitelist: this.lists,
                        enforceWhitelist: true,
                        dropdown: {
                            enabled: 0,
                            maxItems: 10,
                            className: 'custom-dropdown',
                            closeOnSelect: false,
                            searchKeys: ['name']
                        },
                        templates: {
                            tag: function (tagData) {
                                return `<tag title="${tagData.name}"
                             contenteditable='false'
                             spellcheck='false'
                             class='tagify__tag'>
                            <x title="remove tag" class="tagify__tag__removeBtn"></x>
                            <div>
                                <span class="tagify__tag-text">${tagData.name}</span>
                            </div>
                        </tag>`
                            },
                            dropdownItem: function (tagData) {
                                return `<div ${this.getAttributes(tagData)}
                            class='tagify__dropdown__item'>
                            ${tagData.name}
                        </div>`
                            },
                            dropdownItemNoMatch() {
                                return `<div class='empty'>موردی یافت نشد.</div>`;
                            },
                        },
                        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(','),
                    })

                },


            }));
        })
    </script>
@endpush
