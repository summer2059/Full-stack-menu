<div class="sidebar-wrapper" data-layout="stroke-svg">
    <div>
        <div class="logo-wrapper "><a href="#"><img class="img-fluid logo_img"
                    src="{{ asset('dashboard/assets/images/logo/logo_light.png') }}" alt=""></a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar">
                <svg class="stroke-icon sidebar-toggle status_toggle middle">
                    <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#toggle-icon"></use>
                </svg>
                <svg class="fill-icon sidebar-toggle status_toggle middle">
                    <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-toggle-icon"></use>
                </svg>
            </div>
        </div>
        <div class="logo-icon-wrapper"><a href="#"><img class="img-fluid logo_icon"
                    src="{{ asset('dashboard/assets/images/logo/logo_light.png') }}" alt=""></a></div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"><a href="#"><img class="img-fluid logo_icon"
                                src="{{ asset('dashboard/assets/images/logo/logo_light.png') }}" alt=""></a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                aria-hidden="true"></i></div>
                    </li>
                    <li class="pin-title sidebar-main-title">
                        <div>
                            <h6>Pinned</h6>
                        </div>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                            <h6 class="lan-1">General</h6>
                        </div>
                    </li>
                    <li class="sidebar-list" style="{{ request()->routeIs('index') ? 'background-color: #708090;' : '' }}"><i class="fa fa-thumb-tack"> </i><a
                            class="sidebar-link sidebar-title link-nav" href="{{ route('index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-board"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-board"></use>
                            </svg><span>Dashboard </span></a>
                    </li>
                    <li class="sidebar-list" style="{{ request()->routeIs('blog.index') ? 'background-color: #708090;' : '' }}"><i class="fa fa-thumb-tack"> </i><a
                            class="sidebar-link sidebar-title link-nav" href="{{ route('blog.index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-board"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-board"></use>
                            </svg><span>Blog </span></a>
                    </li>

                    <li
                        class="sidebar-list {{ request()->routeIs('menu-category.index', 'menu-category.create', 'menu-category.edit', 'menu.index', 'menu.create', 'menu.edit') ? 'open' : '' }}">
                        <a class="sidebar-link sidebar-title" data-toggle="dropdown">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-gallery"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-gallery"></use>
                            </svg>
                            <span>Menu</span>
                        </a>
                        <ul
                            class="sidebar-submenu {{ request()->routeIs('menu-category.index', 'menu-category.create', 'menu-category.edit', 'menu.index', 'menu.create', 'menu.edit') ? 'd-block' : '' }}">
                            <li><a style="{{ request()->routeIs('menu-category.index', 'menu-category.create', 'menu-category.edit') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('menu-category.index') }}">Menu Category </a></li>
                            <li><a style="{{ request()->routeIs('menu.index', 'menu.create', 'menu.edit') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('menu.index') }}">Menu</a></li>
                        </ul>
                    </li>

                    <li class="sidebar-list" style="{{ request()->routeIs('settings') ? 'background-color: #708090;' : '' }}"><i class="fa fa-thumb-tack"> </i><a
                            class="sidebar-link sidebar-title link-nav" href="{{ route('settings') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-board"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-board"></use>
                            </svg><span>Site Settings </span></a>
                    </li>
                    
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
