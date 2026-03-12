<div class="sidebar-wrapper" data-layout="stroke-svg">
    <div>
        <div class="logo-wrapper"><a href="#"><img class="img-fluid logo_img"
                    src="{{ asset(getConfiguration('site_logo')) }}" alt=""></a>
            <div class="back-btn"><i class="fas fa-angle-left"></i></div>
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
                    src="{{ asset(getConfiguration('site_logo')) }}" alt=""></a></div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"><a href="#"><img class="img-fluid logo_icon"
                                src="{{ asset(getConfiguration('site_logo')) }}" alt=""></a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fas fa-angle-right ps-2"
                                aria-hidden="true"></i></div>
                    </li>
                    <li class="pin-title sidebar-main-title">
                        <div><h6>Pinned</h6></div>
                    </li>
                    <li class="sidebar-main-title">
                        <div><h6 class="lan-1">General</h6></div>
                    </li>

                    @can('dashboard.view')
                    <li class="sidebar-list" style="{{ request()->routeIs('index') ? 'background-color: #708090;' : '' }}">
                        <i class="fa fa-thumb-tack"></i>
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('index') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-board"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-board"></use>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @endcan

                    @canany(['inventory.view', 'inventory.forecast', 'recipe.view'])
                    <li class="sidebar-list {{ request()->routeIs('inventory.index', 'inventory.create', 'inventory.edit', 'inventory.forecast', 'recipe.index', 'recipe.edit') ? 'open' : '' }}">
                        <a class="sidebar-link sidebar-title" data-toggle="dropdown">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-gallery"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-gallery"></use>
                            </svg>
                            <span>Inventory</span>
                        </a>
                        <ul class="sidebar-submenu {{ request()->routeIs('inventory.index', 'inventory.create', 'inventory.edit', 'inventory.forecast', 'recipe.index', 'recipe.edit') ? 'd-block' : '' }}">

                            @can('inventory.view')
                            <li>
                                <a style="{{ request()->routeIs('inventory.index', 'inventory.create', 'inventory.edit') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('inventory.index') }}">Inventory</a>
                            </li>
                            @endcan

                            @can('inventory.forecast')
                            <li>
                                <a style="{{ request()->routeIs('inventory.forecast') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('inventory.forecast') }}">Forecast</a>
                            </li>
                            @endcan

                            @can('recipe.view')
                            <li>
                                <a style="{{ request()->routeIs('recipe.index', 'recipe.edit') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('recipe.index') }}">Recipes</a>
                            </li>
                            @endcan

                        </ul>
                    </li>
                    @endcanany

                    @can('menu_category.view')
                    <li class="sidebar-list {{ request()->routeIs('menu-category.index', 'menu-category.create', 'menu-category.edit', 'menu.index', 'menu.create', 'menu.edit') ? 'open' : '' }}">
                        <a class="sidebar-link sidebar-title" data-toggle="dropdown">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-gallery"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-gallery"></use>
                            </svg>
                            <span>Menu</span>
                        </a>
                        <ul class="sidebar-submenu {{ request()->routeIs('menu-category.index', 'menu-category.create', 'menu-category.edit', 'menu.index', 'menu.create', 'menu.edit') ? 'd-block' : '' }}">

                            @can('menu_category.view')
                            <li>
                                <a style="{{ request()->routeIs('menu-category.index', 'menu-category.create', 'menu-category.edit') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('menu-category.index') }}">Menu Category</a>
                            </li>
                            @endcan

                            @can('menu.view')
                            <li>
                                <a style="{{ request()->routeIs('menu.index', 'menu.create', 'menu.edit') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('menu.index') }}">Menu</a>
                            </li>
                            @endcan

                        </ul>
                    </li>
                    @endcan

                    @can('order.view')
                    <li class="sidebar-list {{ request()->routeIs('order.index', 'order.byTable', 'order.completed') ? 'open' : '' }}">
                        <a class="sidebar-link sidebar-title" data-toggle="dropdown">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-gallery"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-gallery"></use>
                            </svg>
                            <span>Orders</span>
                        </a>
                        <ul class="sidebar-submenu {{ request()->routeIs('order.index', 'order.byTable', 'order.completed') ? 'd-block' : '' }}">

                            <li>
                                <a style="{{ request()->routeIs('order.index', 'order.byTable') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('order.index') }}">Active Orders</a>
                            </li>

                            @can('order.view_completed')
                            <li>
                                <a style="{{ request()->routeIs('order.completed') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('order.completed') }}">Paid Orders</a>
                            </li>
                            @endcan

                        </ul>
                    </li>
                    @endcan
                    @can('qr_code.view')
                    <li class="sidebar-list" style="{{ request()->routeIs('qr-codes') ? 'background-color: #708090;' : '' }}">
                        <i class="fa fa-thumb-tack"></i>
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('qr-codes') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-board"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-board"></use>
                            </svg>
                            <span>QR Codes</span>
                        </a>
                    </li>
                    @endcan
                    @can('site_setting.view')
                    <li class="sidebar-list" style="{{ request()->routeIs('settings') ? 'background-color: #708090;' : '' }}">
                        <i class="fa fa-thumb-tack"></i>
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('settings') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-board"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-board"></use>
                            </svg>
                            <span>Site Settings</span>
                        </a>
                    </li>
                    @endcan
                    @can('user.view')
                    <li class="sidebar-list {{ request()->routeIs('user.index', 'role.index') ? 'open' : '' }}">
                        <a class="sidebar-link sidebar-title" data-toggle="dropdown">
                            <svg class="stroke-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#stroke-gallery"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('dashboard/assets/svg/icon-sprite.svg') }}#fill-gallery"></use>
                            </svg>
                            <span>User Management</span>
                        </a>
                        <ul class="sidebar-submenu {{ request()->routeIs('user.index', 'role.index') ? 'd-block' : '' }}">

                            <li>
                                <a style="{{ request()->routeIs('user.index', 'user.index') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('user.index') }}">User</a>
                            </li>

                            @can('role.view')
                            <li>
                                <a style="{{ request()->routeIs('role.index') ? 'background-color: #708090;' : '' }}"
                                    href="{{ route('role.index') }}">Roles</a>
                            </li>
                            @endcan

                        </ul>
                    </li>
                    @endcan

                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>