<div class="sidebar-wrapper" sidebar-layout="stroke-svg">
    <div>
        <div class="logo-wrapper">
            <a href="{{ route('home') }}">
                <img class="img-fluid for-light"
                     src="{{ asset('assets/images/logo/samfa-logo.png') }}" width="40" alt="سمفا - سامانه هوشمند ردیابی GPS">
                <img class="img-fluid for-dark"
                     src="{{ asset('assets/images/logo/samfa-logo.png') }}" width="40" alt="سمفا - سامانه هوشمند ردیابی GPS">
            </a>
            <div class="back-btn">
                <i class="fa fa-angle-left"></i>
            </div>
            <div class="toggle-sidebar">
                <i class="status_toggle middle sidebar-toggle"
                   data-feather="grid"> </i>
            </div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="{{ route('home') }}">
                <img class="img-fluid"
                     src="{{ asset('assets/images/logo/samfa-logo.png') }}" width="40" alt="">
            </a>
        </div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>بازگشت</span><i
                                    class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    @notRole(['manager'])
                    <li class="sidebar-list">
                        <a
                                @class(['sidebar-link sidebar-title link-nav', 'active' => Route::is('home')]) href="{{ route('home') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>داشبورد</span>
                        </a>
                    </li>
                    @endnotRole
                    @if(can('show-map'))
                    <li class="sidebar-list">
                        <a @class(['sidebar-link sidebar-title link-nav', 'active' => Route::is('map')]) href="{{ route('map') }}">
                            <i data-feather="map"></i>
                            <span>نقشه</span>
                            <div class="according-menu">
                                <i class="fa-solid fa-angle-right"></i>
                            </div>
                        </a>
                    </li>
                    @endif
                    @if(can('devices-list'))
                        <li class="sidebar-list">
                            <a @class(['sidebar-link sidebar-title link-nav', 'active' => Route::is('device.*')]) href="{{ route('device.index') }}">
                                <i data-feather="cpu"></i>
                                <span>دستگاه ها</span>
                                <div class="according-menu">
                                    <i class="fa-solid fa-angle-right"></i>
                                </div>
                            </a>
                        </li>
                    @endif

                    @if(can('vehicles-list'))
                        <li class="sidebar-list">
                            <a @class(['sidebar-link sidebar-title link-nav', 'active' => Route::is('vehicle.*')]) href="{{ route('vehicle.index') }}">
                                <i data-feather="truck"></i>
                                <span>وسایل نقلیه</span>
                                <div class="according-menu">
                                    <i class="fa-solid fa-angle-right"></i>
                                </div>
                            </a>
                        </li>
                    @endif

                    @notRole(['user'])
                    @if(can('users-list'))
                        <li class="sidebar-list">
                            <a @class(['sidebar-link sidebar-title', 'active' => Route::is('user.*')]) href="javascript:void(0)">
                                <i data-feather="users"></i>
                                <span>کاربران</span></a>
                            <ul class="sidebar-submenu">
                                <li><a href="{{ route('user.index') }}"><span>لیست کاربران</span></a></li>
                                @if(can('create-company'))
                                    <li><a href="{{ route('user.create') }}"><span>ایجاد کاربر جدید</span></a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @endnotRole
                    @notRole(['user'])
                    @if(can('companies-list'))
                        <li class="sidebar-list">
                            <a @class(['sidebar-link sidebar-title', 'active' => Route::is('company.*')]) href="javascript:void(0)">
                                <i data-feather="life-buoy"></i>
                                <span>سازمان</span></a>
                            <ul class="sidebar-submenu" @style(['display: block' => Route::is('company.index', 'company.create')])>
                                <li>
                                    <a @class(['active' => Route::is('company.index')]) href="{{ route('company.index') }}"><span>لیست سازمان ها</span></a>
                                </li>
                                @if(can('create-company'))
                                    <li>
                                        <a @class(['active' => Route::is('company.create')]) href="{{ route('company.create') }}"><span>ایجاد سازمان جدید</span></a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @endnotRole
                    @if(can('geofences-list'))
                        <li class="sidebar-list">
                            <a @class(['sidebar-link sidebar-title', 'active' => Route::is('geofence.*')]) href="javascript:void(0)">
                                <i data-feather="octagon"></i>
                                <span>حصار جغرافیایی</span></a>
                            <ul class="sidebar-submenu">
                                <li><a href="{{ route('geofence.index') }}"><span>لیست حصار‌های جغرافیایی</span></a>
                                </li>
                                @if(can('create-geofence'))
                                    <li><a href="{{ route('geofence.create') }}"><span>ایجاد حصار جدید</span></a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
