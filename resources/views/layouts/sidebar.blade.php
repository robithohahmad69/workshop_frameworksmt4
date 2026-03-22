<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile" />
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                    <span class="text-secondary text-small">{{ Auth::user()->email }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        
        <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
            <a class="nav-link" href="/">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        
        <li class="nav-item {{ Request::is('kategori*') ? 'active' : '' }}">
            <a class="nav-link" href="/kategori">
                <span class="menu-title">Kategori</span>
                <i class="mdi mdi-tag-multiple menu-icon"></i>
            </a>
        </li>
        
        <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
            <a class="nav-link" href="/buku">
                <span class="menu-title">Buku</span>
                <i class="mdi mdi-book-open-variant menu-icon"></i>
            </a>
        </li>

         <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
            <a class="nav-link" href="/barang">
                <span class="menu-title">Barang</span>
                <i class="mdi mdi-package-variant menu-icon"></i>
            </a>
        </li>

          <li class="nav-item {{ Request::is('tabel*') ? 'active' : '' }}">
            <a class="nav-link" href="/tabel">
                <span class="menu-title">Barangtables</span>
                <i class="mdi mdi-package-variant menu-icon"></i>
            </a>
        </li>
          <li class="nav-item {{ Request::is('datatables*') ? 'active' : '' }}">
            <a class="nav-link" href="/datatables">
                <span class="menu-title">Barangdatatables</span>
                <i class="mdi mdi-package-variant menu-icon"></i>
            </a>
        </li>

            {{-- Select --}}
        <li class="nav-item {{ Request::is('select') ? 'active' : '' }}">
            <a class="nav-link" href="/select">
                <span class="menu-title">Select</span>
                <i class="mdi mdi-form-dropdown menu-icon"></i>  {{-- ganti ini --}}
            </a>
        </li>

        {{-- Select2 --}}
        <li class="nav-item {{ Request::is('select2') ? 'active' : '' }}">
            <a class="nav-link" href="/select2">
                <span class="menu-title">Select2</span>
                <i class="mdi mdi-form-dropdown menu-icon"></i>  {{-- ganti ini --}}
            </a>
        </li>
        
  {{-- SESUDAH (benar) --}}
<li class="nav-item {{ Request::is('ajax/wilayahajax') ? 'active' : '' }}">
    <a class="nav-link" href="/ajax/wilayahajax">
        <span class="menu-title">Select Wilayah (AJAX)</span>
        <i class="mdi mdi-map-marker-radius menu-icon"></i>
    </a>
</li>
<li class="nav-item {{ Request::is('axios/wilayahaxios') ? 'active' : '' }}">
    <a class="nav-link" href="/axios/wilayahaxios">
        <span class="menu-title">Select Wilayah (Axios)</span>
        <i class="mdi mdi-map-marker-radius menu-icon"></i>
    </a>
</li>


{{-- SESUDAH (benar) --}}
<li class="nav-item {{ Request::is('ajax/kasirajax') ? 'active' : '' }}">
    <a class="nav-link" href="/ajax/kasirajax">
        <span class="menu-title">Kasir (AJAX)</span>
        <i class="mdi mdi-cash-register menu-icon"></i>
    </a>
</li>
<li class="nav-item {{ Request::is('axios/kasiraxios') ? 'active' : '' }}">
    <a class="nav-link" href="/axios/kasiraxios">
        <span class="menu-title">Kasir (Axios)</span>
        <i class="mdi mdi-cash-register menu-icon"></i>
    </a>
</li>

        
        <!-- Menu Edit Profile -->
        <li class="nav-item {{ Request::is('profile*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('profile.edit') }}">
                <span class="menu-title">Edit Profile</span>
                <i class="mdi mdi-account-circle menu-icon"></i>
            </a>
        </li>
    </ul>
</nav>