@php use App\Facades\Acl; @endphp
@extends('01-layouts.master')

@section('title', 'ثبت‌نام کاربر جدید')

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
                            <a href="{{ route('user.index') }}">
                                لیست کاربران
                            </a>
                        </li>
                        <li class="breadcrumb-item dana">ثبت‌نام کاربر جدید</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('user.store') }}" method="POST" class="row" autocomplete="off">
        @csrf

        <!-- User Info -->
        <div @class(['col-xl-4' => can('user-permissions'),
                      'col-12' => !can('user-permission')
                       ])>
            <div class="card">
                <h5 class="card-header">اطلاعات کاربر</h5>
                <div class="card-body">

                    <div class="col-12 mb-3">
                        <label class="form-label" for="name">نام و نام‌خانوادگی
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="name" name="name" value="{{ old('name') }}" type="text">
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>


                    <div class="col-12 mb-3">
                        <label class="form-label" for="phone">شماره تماس
                            <sup class="text-danger">*</sup>
                        </label>
                        <input class="form-control" id="phone" dir="ltr" name="phone" value="{{ old('phone') }}"
                               type="text">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
                    </div>

                    {{--                    <div class="col-12 mb-3">--}}
                    {{--                        <label class="form-label" for="password">رمز عبور</label>--}}
                    {{--                        <div class="input-group" x-data="{ show: false }">--}}
                    {{--                            <span class="input-group-text list-light-dark cursor-pointer" @click="show = !show"--}}
                    {{--                                  x-text="show ? 'مخفی' : 'نمایش'">نمایش</span>--}}
                    {{--                            <input class="form-control" dir="ltr" :type="show ? 'text' : 'password'"--}}
                    {{--                                   value="{{ $password ?? '' }}" autocomplete="new-password" id="password" disabled>--}}
                    {{--                        </div>--}}
                    {{--                        <x-input-error :messages="$errors->get('password')" class="mt-2"/>--}}
                    {{--                    </div>--}}

                    <div class="col-12 mb-3">
                        <label class="form-label" for="password">رمز عبور</label>
                        <div class="input-group" x-data="{ show: false }">
                            <span class="input-group-text list-light-dark cursor-pointer" @click="show = !show"
                                  x-text="show ? 'مخفی' : 'نمایش'">نمایش</span>
                            <input class="form-control" dir="ltr" :type="show ? 'text' : 'password'"
                                   value="{{ old('password') }}" name="password" autocomplete="new-password"
                                   id="password">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2"/>
                    </div>


                    <div x-data="{ selected: @js(old('user_type', 0)) }">

                        <div class="col-12 mb-3">
                        <label class="form-label" for="user_type">نوع کاربر
                            <sup class="text-danger">*</sup>
                        </label>
                            <select class="form-select" name="user_type" id="user_type" x-model="selected">
                            <option value="0" selected @selected(old('user_type') == 0)>کاربر</option>
                            <option value="1" @selected(old('user_type') == 1)>ادمین</option>
                                @notRole(['manager'])
                            <option value="2" @selected(old('user_type') == 2)>سوپر ادمین</option>
                                @endnotRole
                            <option value="3" @selected(old('user_type') == 3)>مدیر سازمان</option>
                        </select>
                        <x-input-error :messages="$errors->get('user_type')" class="mt-2"/>
                            @if(can('user-permissions'))
                                <div class="d-block">
                                    <small class="text-muted">لطفا نام کاربری و نقش را مشابه هم انتخاب کنید.</small>
                                </div>
                            @endif
                        </div>

                        <div class="col-12 mb-3" x-cloak x-show="[0,1,3].includes(parseInt(selected))">
                        <label class="form-label" for="user_id">عضو سازمان
                            <sup class="text-danger">*</sup>
                        </label>
                            @php
                                $options = $companies->mapWithKeys(fn($item) => [$item->id => implode(' - ', [$item->name , $item?->manager?->name])])->toArray();
                            @endphp
                            <x-partials.alpine.input.select-option :$options
                                                               name="company_id"/>
                        <x-input-error :messages="$errors->get('company_id')" class="mt-2"/>
                    </div>

                    </div>


                    <div class="col-12 mb-3">
                        <label class="form-label" for="status">وضعیت
                            <sup class="text-danger">*</sup>
                        </label>
                        <select class="form-select" name="status" id="status">
                            <option value="0" @selected(old('status') == 0)>غیر فعال</option>
                            <option value="1" selected @selected(old('status') == 1)>فعال</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2"/>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary" type="submit">ایــــجاد</button>
                    </div>
                </div>
            </div>
        </div>
        @if(can('user-permissions'))

            <!-- User Permissions -->
            <div class="col-xl-8">
                <div class="card">
                    <h5 class="card-header">نقش و مجوزها</h5>
                    <div class="card-body">
                        <div class="card-wrapper border rounded-3 h-100 checkbox-checked mb-4">
                            <div class="sub-title dana d-flex align-items-center justify-content-start">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24">
                                    <path fill="#1d1b1b"
                                          d="M15 21h-2a2 2 0 0 1 0-4h2v-2h-2a4 4 0 0 0 0 8h2Zm8-2a4 4 0 0 1-4 4h-2v-2h2a2 2 0 0 0 0-4h-2v-2h2a4 4 0 0 1 4 4"/>
                                    <path fill="#1d1b1b"
                                          d="M14 18h4v2h-4zm-7 1a6 6 0 0 1 .09-1H3v-1.4c0-2 4-3.1 6-3.1a8.6 8.6 0 0 1 1.35.125A5.95 5.95 0 0 1 13 13h5V4a2.006 2.006 0 0 0-2-2h-4.18a2.988 2.988 0 0 0-5.64 0H2a2.006 2.006 0 0 0-2 2v14a2.006 2.006 0 0 0 2 2h5.09A6 6 0 0 1 7 19M9 2a1 1 0 1 1-1 1a1.003 1.003 0 0 1 1-1m0 4a3 3 0 1 1-3 3a2.996 2.996 0 0 1 3-3"/>
                                </svg>
                                <span class="ms-2">انتخاب نقش </span>
                            </div>
                            <div class="my-1">
                                <x-input-error :messages="$errors->get('role')"/>
                            </div>
                            @php
                                if(Acl::hasRole(['manager'])){
                                       $roles = $roles->reject(fn($role) => in_array($role->title,['developer', 'super-admin']));
                                }
                                    $defaultRoleId = $roles->first()->id;
                            @endphp
                            <div class="d-flex align-items-center justify-content-between flex-wrap"
                                 x-data="{ role: @json( (int)old('role', $defaultRoleId) ) }">
                                @foreach($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" id="role-{{ $role->id }}"
                                               value="{{ $role->id }}"
                                               :checked="parseInt(role) === {{ (int)$role->id }}"
                                               @change="$dispatch('updated-role', { roleName: '{{ $role->title }}' })"
                                               type="radio" name="role">
                                        <label class="form-check-label cursor-pointer" for="role-{{ $role->id }}"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               data-bs-title="{{ $role->description }}"
                                        >{{ $role->persian_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card-wrapper border rounded-3 h-100 checkbox-checked">
                            <div class="sub-title dana d-flex align-items-center justify-content-start">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 48 48">
                                    <g fill="none" stroke="#1d1b1b" stroke-linecap="round" stroke-width="4">
                                        <path stroke-linejoin="round"
                                              d="M20 10H6a2 2 0 0 0-2 2v26a2 2 0 0 0 2 2h36a2 2 0 0 0 2-2v-2.5"/>
                                        <path d="M10 23h8m-8 8h24"/>
                                        <circle cx="34" cy="16" r="6" stroke-linejoin="round"/>
                                        <path stroke-linejoin="round"
                                              d="M44 28.419C42.047 24.602 38 22 34 22s-5.993 1.133-8.05 3"/>
                                    </g>
                                </svg>
                                <span class="ms-2">انتخاب دسترسی ها </span>
                            </div>

                            <div x-data="permissionsList" class="row">
                                <div class="col-12"
                                     @updated-role.window="handleDispatch($event.detail.roleName)">
                                    <x-input-error :messages="$errors->get('permissions')"/>
                                    <x-input-error :messages="$errors->get('permissions.*')"/>
                                </div>
                                <div class="col-12 mb-3 d-flex justify-content-start">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary d-flex align-items-center me-3"
                                            @click="selectAll()">
                                        <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                             viewBox="0 0 24 24">
                                            <mask id="lineMdCheckAll0">
                                                <g fill="none" stroke="#fff" stroke-dasharray="24"
                                                   stroke-dashoffset="24"
                                                   stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                    <path d="M2 13.5l4 4l10.75 -10.75">
                                                        <animate fill="freeze" attributeName="stroke-dashoffset"
                                                                 dur="0.4s"
                                                                 values="24;0"/>
                                                    </path>
                                                    <path stroke="#000" stroke-width="6" d="M7.5 13.5l4 4l10.75 -10.75">
                                                        <animate fill="freeze" attributeName="stroke-dashoffset"
                                                                 begin="0.4s" dur="0.4s" values="24;0"/>
                                                    </path>
                                                    <path d="M7.5 13.5l4 4l10.75 -10.75">
                                                        <animate fill="freeze" attributeName="stroke-dashoffset"
                                                                 begin="0.4s" dur="0.4s" values="24;0"/>
                                                    </path>
                                                </g>
                                            </mask>
                                            <rect width="24" height="24" fill="#7366FF" mask="url(#lineMdCheckAll0)"/>
                                        </svg>
                                        <span>انتخاب همه</span>
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger d-flex align-items-center"
                                            @click="deselectAll()">
                                        <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                             viewBox="0 0 32 32">
                                            <path fill="#FB1506"
                                                  d="M16 3.667C9.19 3.667 3.667 9.187 3.667 16S9.19 28.333 16 28.333c6.812 0 12.333-5.52 12.333-12.333S22.813 3.667 16 3.667m0 3c1.85 0 3.572.548 5.024 1.48L8.147 21.024A9.26 9.26 0 0 1 6.667 16c0-5.146 4.187-9.333 9.333-9.333m0 18.666a9.27 9.27 0 0 1-5.024-1.48l12.876-12.877A9.26 9.26 0 0 1 25.332 16c0 5.146-4.186 9.333-9.332 9.333"/>
                                        </svg>
                                        <span>انتخاب هیچکدام</span>
                                    </button>
                                    <hr>
                                </div>


                                @foreach($permissions as $group => $permission)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check checkbox checkbox-solid-dark mb-0 learning-header">
                                            <input class="form-check-input cursor-pointer"
                                                   value="{{ $group }}"
                                                   :checked="permissions.length > 0 && permissions.every(permission => selectedPermissions.includes(permission.id))"
                                                   @change="toggleGroup('{{ $group }}')"
                                                   id="{{ $group }}" type="checkbox">
                                            <label class="form-check-label"
                                                   for="{{ $group }}"> {{ $group }}</label>
                                        </div>
                                        <div class="ms-3">
                                            @foreach($permission as $item)
                                                <div class="">
                                                    <input class="form-check-input cursor-pointer"
                                                           value="{{ $item['id'] }}"
                                                           name="permissions[]"
                                                           :checked="selectedPermissions.includes({{ (int)$item['id'] }})"
                                                           @change="togglePermission({{ (int)$item['id'] }})"
                                                           id="{{ $item['id'] }}" type="checkbox">
                                                    <label class="form-check-label text-muted cursor-pointer"
                                                           for="{{ $item['id'] }}"> {{ $item['persian_name'] }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif
    </form>

@endsection

@push('scripts')
    <script>
        window.addEventListener('alpine:init', () => {
            Alpine.data('permissionsList', () => ({
                permissions: @json($permissions),
                selectedPermissions: @json(old('permissions', [])),

                init() {
                    if (this.selectedPermissions.length === 0) {
                        this.handleDispatch('{{ $roles->firstWhere('id', old('role', $defaultRoleId))->title ?? null }}')
                    }

                    if (this.selectedPermissions.length > 0) {
                        this.selectedPermissions = this.selectedPermissions.flat().map(item => parseInt(item));
                    }
                },

                selectAll() {
                    this.selectedPermissions = Object.values(this.permissions).flat().map(permission => permission.id)
                },

                deselectAll() {
                    this.selectedPermissions = [];
                },

                toggleGroup(group) {
                    const groupIds = this.permissions[group].map(permission => permission.id)
                    if (groupIds.every(id => this.selectedPermissions.includes(id))) {
                        this.selectedPermissions = this.selectedPermissions.filter(id => !groupIds.includes(id));
                    } else {
                        this.selectedPermissions = [...new Set([...this.selectedPermissions, ...groupIds])];
                    }
                },

                togglePermission(id) {
                    if (this.selectedPermissions.includes(id)) {
                        this.selectedPermissions = this.selectedPermissions.filter(permissionId => permissionId !== id);
                    } else {
                        this.selectedPermissions.push(id);
                    }
                },

                handleDispatch($roleName) {
                    switch ($roleName) {
                        case 'super-admin':
                            this.selectAll();
                            break;
                        case 'manager':
                            this.selectAll();
                            break;
                        case 'developer':
                            this.selectAll();
                            break;
                        case 'user':
                            this.selectedPermissions = [32, 33, 37, 38, 39, 40, 41, 42, 44, 57, 58, 59, 60, 61, 62];
                            break
                        case 'admin':
                            this.selectedPermissions = [32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 57, 58, 59, 60, 61, 62, 63];
                            break;
                        default:
                            this.deselectAll();
                    }
                }

            }))
        })
    </script>
@endpush
