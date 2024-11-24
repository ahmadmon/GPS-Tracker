<div class="page-header mb-4">
    <div class="header-wrapper row m-0">
{{--        <form class="form-inline search-full col" action="#" method="get">--}}
{{--            <div class="form-group w-100">--}}
{{--                <div class="Typeahead Typeahead--twitterUsers">--}}
{{--                    <div class="u-posRelative">--}}
{{--                        <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text"--}}
{{--                               placeholder="جستجو ..." name="q" title="" autofocus>--}}
{{--                        <div class="spinner-border Typeahead-spinner" role="status"><span--}}
{{--                                class="sr-only">درحال بارگذاری...</span></div>--}}
{{--                        <i class="close-search" data-feather="x"></i>--}}
{{--                    </div>--}}
{{--                    <div class="Typeahead-menu"></div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </form>--}}
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"><a href="{{ route('home') }}"><img class="img-fluid" width="40px"
                                                                         src="{{ asset('assets/images/logo/samfa-logo.png') }}"
                                                                         alt="سمفا - سامانه هوشمند ردیابی GPS"></a></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
            </div>
        </div>
        <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
            <div class="notification-slider">
                <div class="d-flex h-100">
                    <h6 class="mb-0 f-w-400"><span class="f-light">سمفا - سامانه هوشمند ردیابی GPS</span></h6>
                </div>
            </div>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">
                {{-- <li class="language-nav">
                    <div class="translate_wrapper">
                        <div class="current_lang">
                            <div class="lang"><i class="flag-icon flag-icon-us"></i><span class="lang-txt">EN
                                </span></div>
                        </div>
                        <div class="more_lang">
                            <div class="lang selected" data-value="en"><i
                                    class="flag-icon flag-icon-us"></i><span class="lang-txt">English<span>
                                        (US)</span></span></div>
                            <div class="lang" data-value="de"><i class="flag-icon flag-icon-de"></i><span
                                    class="lang-txt">Deutsch</span></div>
                            <div class="lang" data-value="es"><i class="flag-icon flag-icon-es"></i><span
                                    class="lang-txt">Español</span></div>
                            <div class="lang" data-value="fr"><i class="flag-icon flag-icon-fr"></i><span
                                    class="lang-txt">Français</span></div>
                            <div class="lang" data-value="pt"><i class="flag-icon flag-icon-pt"></i><span
                                    class="lang-txt">Português<span> (BR)</span></span></div>
                            <div class="lang" data-value="cn"><i class="flag-icon flag-icon-cn"></i><span
                                    class="lang-txt">简体中文</span></div>
                            <div class="lang" data-value="ae"><i class="flag-icon flag-icon-ae"></i><span
                                    class="lang-txt">لعربية <span> (ae)</span></span></div>
                        </div>
                    </div>
                </li> --}}
{{--                <li> <span class="header-search">--}}
{{--                        <svg>--}}
{{--                            <use href="{{ asset('assets/svg/icon-sprite.svg#search') }}"></use>--}}
{{--                        </svg></span></li>--}}
{{--                <li class="onhover-dropdown">--}}
{{--                    <svg>--}}
{{--                        <use href="{{ asset('assets/svg/icon-sprite.svg#star') }}"></use>--}}
{{--                    </svg>--}}
{{--                    <div class="onhover-show-div bookmark-flip">--}}
{{--                        <div class="flip-card">--}}
{{--                            <div class="flip-card-inner">--}}
{{--                                <div class="front">--}}
{{--                                    <h6 class="f-18 mb-0 dropdown-title">دسترسی سریع</h6>--}}
{{--                                    <ul class="bookmark-dropdown">--}}
{{--                                        <li>--}}
{{--                                            <div class="row">--}}
{{--                                                <div class="col-4 text-center">--}}
{{--                                                    <div class="bookmark-content">--}}
{{--                                                        <a href="{{ route('map') }}">--}}
{{--                                                            <div class="bookmark-icon"><i data-feather="map"></i>--}}
{{--                                                            </div>--}}
{{--                                                            <span class="dana text-dark">نقشه</span>--}}
{{--                                                        </a>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-4 text-center">--}}
{{--                                                    <div class="bookmark-content">--}}
{{--                                                        <a href="{{ route('device.create') }}">--}}
{{--                                                            <div class="bookmark-icon"><i data-feather="cpu"></i>--}}
{{--                                                            </div>--}}
{{--                                                            <span class="dana text-dark">ایجاد دستگاه</span>--}}
{{--                                                        </a>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-4 text-center">--}}
{{--                                                    <div class="bookmark-content">--}}
{{--                                                        <a href="{{ route('vehicle.create') }}">--}}
{{--                                                            <div class="bookmark-icon"><i data-feather="truck"></i>--}}
{{--                                                            </div>--}}
{{--                                                            <span class="dana text-dark">ایجاد وسیله نقلیه</span>--}}
{{--                                                        </a>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </li>--}}

{{--                                    </ul>--}}
{{--                                </div>--}}
{{--                                <div class="back">--}}
{{--                                    <ul>--}}
{{--                                        <li>--}}
{{--                                            <div class="bookmark-dropdown flip-back-content">--}}
{{--                                                <input type="text" placeholder="search..." aria-label="search">--}}
{{--                                            </div>--}}
{{--                                        </li>--}}
{{--                                        <li><a class="f-w-700 d-block flip-back" id="flip-back"--}}
{{--                                               href="javascript:void(0)">برگشت</a></li>--}}
{{--                                    </ul>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </li>--}}
                <li>
                    <div class="mode">
                        <svg>
                            <use href="{{ asset('assets/svg/icon-sprite.svg#moon') }}"></use>
                        </svg>
                    </div>
                </li>
{{--                <li class="onhover-dropdown">--}}
{{--                    <div class="notification-box">--}}
{{--                        <svg>--}}
{{--                            <use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>--}}
{{--                        </svg>--}}
{{--                        <span class="badge rounded-pill badge-secondary">4 </span>--}}
{{--                    </div>--}}
{{--                    <div class="onhover-show-div notification-dropdown">--}}
{{--                        <h6 class="f-18 mb-0 dropdown-title">اعلان‌ها </h6>--}}
{{--                        <ul>--}}
{{--                            <li class="b-l-primary border-4">--}}
{{--                                <p>پردازش تحویل <span class="font-danger">10 دقیقه.</span></p>--}}
{{--                            </li>--}}
{{--                            <li class="b-l-success border-4">--}}
{{--                                <p>سفارش کامل شد<span class="font-success">1 ساعت</span></p>--}}
{{--                            </li>--}}
{{--                            <li class="b-l-secondary border-4">--}}
{{--                                <p>بلیت‌های تولید شده<span class="font-secondary">3 ساعت</span></p>--}}
{{--                            </li>--}}
{{--                            <li class="b-l-warning border-4">--}}
{{--                                <p>تحویل کامل<span class="font-warning">6 ساعت</span></p>--}}
{{--                            </li>--}}
{{--                            <li><a class="f-w-700" href="#">بررسی همه</a></li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </li>--}}
                <li class="profile-nav onhover-dropdown pe-0 py-0">
                    <div class="media profile-media"><img class="b-r-10"
                                                          src="{{ asset('assets/images/avtar/user.png') }}"
                                                          alt="">
                        <div class="media-body"><span>{{ auth()->user()?->name }}</span>
                            <p class="mb-0">{{ auth()->user()->roles->first()?->persian_name }}<i
                                    class="middle fa fa-angle-down"></i></p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="{{ route('profile.index') }}"><i data-feather="user"></i><span>حساب </span></a>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="post" class="d-inline">
                                @csrf
                                <a onclick="$(this).parent().submit()" type="button"><i
                                        data-feather="log-out"> </i><span>خروج از سامانه</span></a>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
{{--        <script class="result-template" type="text/x-handlebars-template">--}}
{{--            <div class="ProfileCard u-cf">--}}
{{--                <div class="ProfileCard-avatar">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"--}}
{{--                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"--}}
{{--                         class="feather feather-airplay m-0">--}}
{{--                        <path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path>--}}
{{--                        <polygon points="12 15 17 21 7 21 12 15"></polygon>--}}
{{--                    </svg>--}}
{{--                </div>--}}
{{--                <div class="ProfileCard-details">--}}
{{--                    <div class="ProfileCard-realName">name</div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </script>--}}
{{--        <script class="empty-template" type="text/x-handlebars-template">--}}
{{--            <div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down,--}}
{{--                yikes!--}}
{{--            </div></script>--}}
    </div>
</div>

