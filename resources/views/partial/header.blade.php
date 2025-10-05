<!-- BEGIN #header -->
<div id="header" class="app-header">
    <!-- BEGIN mobile-toggler -->
    <div class="mobile-toggler">
        <button type="button" class="menu-toggler"
            @if (!empty($appTopNav) && !empty($appSidebarHide)) data-toggle="top-nav-mobile"
            @else data-toggle="sidebar-mobile" @endif>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <!-- END mobile-toggler -->

    <!-- BEGIN brand -->
    <div class="brand">
        <div class="desktop-toggler">
            <button type="button" class="menu-toggler"
                @if (empty($appSidebarHide)) data-toggle="sidebar-minify" @endif>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>

        {{-- Logo selon guard --}}
        @if(auth('admin')->check())
            <a href="{{ route('admin.dashboard') }}" class="brand-logo">
                <img src="/assets/img/logo.png" class="invert-dark" alt="Admin" height="48" />
            </a>
        @elseif(auth('web')->check())
            <a href="{{ route('company.dashboard') }}" class="brand-logo">
                <img src="/assets/img/logo.png" class="invert-dark" alt="Entreprise" height="48" />
            </a>
        @else
            <a href="/" class="brand-logo">
                <img src="/assets/img/logo.png" class="invert-dark" alt="PengMe" height="48" />
            </a>
        @endif
    </div>
    <!-- END brand -->

    <!-- BEGIN menu -->
    <div class="menu">
        <form class="menu-search" method="POST" name="header_search_form">

        </form>
        {{-- Notifications placeholder --}}
        <div class="menu-item dropdown">
            <a href="#" data-bs-toggle="dropdown" class="menu-link">
                <div class="menu-icon"><i class="fa fa-bell nav-icon"></i></div>
                <div class="menu-label"></div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-notification">
                <h6 class="dropdown-header text-body mb-1">Notifications</h6>
                <div class="p-2 text-muted small">Aucune notification</div>
            </div>
        </div>

        {{-- Profil / Déconnexion --}}
        @php
          $user = auth('admin')->check() ? auth('admin')->user() : (auth('web')->check() ? auth('web')->user() : null);
        @endphp

        @if($user)
        <div class="menu-item dropdown">
            <a href="#" data-bs-toggle="dropdown" class="menu-link">
                <div class="menu-img online">
                    <img src="/assets/img/user/user.jpg" alt="" class="ms-100 mh-100 rounded-circle" />
                </div>
                <div class="menu-text">
                    <span class="nav-link">Bonjour, <strong>{{ $user->name ?? $user->email }}</strong> !</span>
                </div>
            </a>

            <div class="dropdown-menu dropdown-menu-end me-lg-3">
                @if(auth('admin')->check())
                    <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                        Mon tableau admin <i class="fa fa-user-shield fa-fw ms-auto text-body text-opacity-50"></i>
                    </a>
                @elseif(auth('web')->check())
                    <a class="dropdown-item d-flex align-items-center" href="/profile">
                        Profil entreprise <i class="fa fa-user-circle fa-fw ms-auto text-body text-opacity-50"></i>
                    </a>
                @endif

                <div class="dropdown-divider"></div>

                @if(auth('admin')->check())
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion admin
                        </button>
                    </form>
                @elseif(auth('web')->check())
                    <form method="POST" action="{{ route('company.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endif
    </div>
    <!-- END menu -->
</div>
<!-- END #header -->
