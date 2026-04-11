@php
    // Deteksi siapa yang sedang login
    if (Auth::guard('vendor')->check()) {
        $authUser  = Auth::guard('vendor')->user();
        $authRole  = 'Vendor';
        $editRoute = route('vendor.profile');
        $logoutRoute = route('vendor.logout');
        $logoutMethod = 'POST';
    } else {
        $authUser  = Auth::user();
        $authRole  = 'User';
        $editRoute = route('profile.edit');
        $logoutRoute = route('logout');
        $logoutMethod = 'POST';
    }
@endphp

<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <a class="navbar-brand brand-logo" href="/">
            <span style="color: #b66dff; font-weight: bold; font-size: 20px;">FRAMEWORK</span>
        </a>
        <a class="navbar-brand brand-logo-mini" href="/">
            <span style="font-size: 20px;">📚</span>
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button>

        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="nav-profile-img">
                        <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="image">
                        <span class="availability-status online"></span>
                    </div>
                    <div class="nav-profile-text">
                        <p class="mb-1 text-black">{{ $authUser->name }}</p>
                        <small class="text-muted">{{ $authRole }}</small>
                    </div>
                </a>
                <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                    <div class="dropdown-item py-2">
                        <i class="mdi mdi-account me-2 text-success"></i>
                        {{ $authUser->name }}
                    </div>
                    <div class="dropdown-divider"></div>

                    <a href="{{ $editRoute }}" class="dropdown-item">
                        <i class="mdi mdi-account-edit me-2 text-info"></i> Edit Profile
                    </a>
                    <div class="dropdown-divider"></div>

                    <form action="{{ $logoutRoute }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item"
                            style="border: none; background: none; width: 100%; text-align: left; cursor: pointer;">
                            <i class="mdi mdi-logout me-2 text-primary"></i> Logout
                        </button>
                    </form>
                </div>
            </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>