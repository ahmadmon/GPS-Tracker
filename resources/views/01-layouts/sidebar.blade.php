<div class="sidebar-wrapper" sidebar-layout="stroke-svg">
    <div>
        <div class="logo-wrapper"><a href="index.html"><img class="img-fluid for-light"
                    src="../assets/images/logo/logo.png" alt=""><img class="img-fluid for-dark"
                    src="../assets/images/logo/logo_dark.png" alt=""></a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle"
                    data-feather="grid"> </i></div>
        </div>
        <div class="logo-icon-wrapper"><a href="index.html"><img class="img-fluid"
                    src="../assets/images/logo/logo-icon.png" alt=""></a></div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i
                                class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    <li class="pin-title sidebar-main-title">
                        <div>
                            <h6>Pinned</h6>
                        </div>
                    </li>
                    <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a
                            class="sidebar-link sidebar-title" href="{{ route('home') }}"
                            target="_blank">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>داشبورد</span></a></li>
                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a @class(['sidebar-link sidebar-title link-nav', 'active' => Route::is('device.*')]) href="{{ route('device.index') }}">
                            <i data-feather="cpu"></i>
                            <span>دستگاه ها</span>
                            <div class="according-menu">
                                <i class="fa-solid fa-angle-right"></i>
                            </div>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <i class="fa-solid fa-thumbtack"></i>
                        <a @class(['sidebar-link sidebar-title link-nav', 'active' => Route::is('vehicle.*')]) href="{{ route('vehicle.index') }}">
                            <i data-feather="truck"></i>
                            <span>وسایل نقلیه</span>
                            <div class="according-menu">
                                <i class="fa-solid fa-angle-right"></i>
                            </div>
                        </a>
                    </li>

                    <li class="sidebar-list">
                        <i class="fa fa-thumb-tack"></i>
                        <a class="sidebar-link sidebar-title" href="#">
                            <i data-feather="users"></i>
                            <span>کاربران</span></a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('user.index') }}"><span>لیست کاربران</span></a></li>
                            <li><a href="{{ route('user.create') }}"><span>ایجاد کاربر جدید</span></a></li>
                        </ul>
                    </li>
                    <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a
                                class="sidebar-link sidebar-title" href="#">
                            <i data-feather="life-buoy"></i>
                            <span>سازمان</span></a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('company.index') }}"><span>لیست سازمان ها</span></a></li>
                            <li><a href="{{ route('company.create') }}"><span>ایجاد سازمان جدید</span></a></li>
                        </ul>
                    </li>
                    <li class="sidebar-list"><i class="fa fa-thumb-tack"></i><a
                            class="sidebar-link sidebar-title" href="#">
                            <i data-feather="octagon"></i>
                            <span>حصار جغرافیایی</span></a>
                        <ul class="sidebar-submenu">
                            <li><a href="{{ route('geofence.index') }}"><span>لیست حصار‌های جغرافیایی</span></a></li>
                            <li><a href="{{ route('geofence.create') }}"><span>ایجاد حصار جدید</span></a></li>
                        </ul>
                    </li>

                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
