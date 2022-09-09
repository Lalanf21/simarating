<!--start sidebar -->

<aside class="sidebar-wrapper" data-simplebar="true">
  <div class="sidebar-header">
      <div>
        {{-- <h4 class="logo-text">PPL</h4> --}}
      </div>
      <div class="toggle-icon ms-auto">
          <i class="fas fa-bars"></i>
      </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
      <li class="{{ set_active('dashboard') }}">
        <a href="{{ route('dashboard') }}">
            <div class="parent-icon">
              <i class="fas fa-th"></i>
            </div>
            <div class="menu-title">Dashboard</div>
          </a>
        </li>
        
        {{-- modul admin --}}
        
      @if (Auth::user()->level === '1')
      <li class="menu-label">Master data</li>
      <li>
          <a class="has-arrow" href="javascript:;">
            <div class="parent-icon">
              <i class="fas fa-database"></i>
            </div>
          <div class="menu-title">Master data</div>
          </a>
          <ul>
            <li class="{{ set_active('master-mitra') }}"> 
              <a href="{{ route('master-mitra') }}">
                Company partner
              </a>
            </li>
            <li class="{{ set_active('master-addon') }}"> 
              <a href="{{ route('master-addon') }}">
                Add-on room
              </a>
            </li>
            <li class="{{ set_active('master_user_co_working') }}"> 
              <a href="{{ route('master_user_co_working') }}">
                Co-working user
              </a>
            </li>
          </ul>
      </li>

      <li class="menu-label">Transactional data</li>
      <li class="{{ set_active('schedule') }}">
          <a href="{{ route('schedule') }}">
            <div class="parent-icon">
              <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="menu-title">Reservation</div>
          </a>
      </li>
      
      <li class="menu-label">Setting</li>
      <li class="{{ set_active('setting-kapasitas') }}"> 
          <a href="{{ route('setting-kapasitas') }}">
            <div class="parent-icon">
              <i class="fas fa-cog"></i>
            </div>
            <div class="menu-title">Capacity</div>
          </a>
      </li>

      <li class="{{ set_active('setting-users') }}">
          <a href="{{ route('setting-users') }}">
            <div class="parent-icon">
              <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="menu-title">Users</div>
          </a>
      </li>

      {{-- <li class="menu-label">Laporan</li>
      <li>
          <a class="has-arrow" href="javascript:;">
            <div class="parent-icon">
              <i class="fas fa-file-export"></i>
            </div>
            <div class="menu-title">Data laporan</div>
          </a>
          <ul>
            <li> 
              <a href="{{ route('laporan_booking') }}">
                Laporan booking
              </a>
            </li>
            <li> 
              <a href="{{ route('laporan_user_co_working') }}">
                Laporan user co-working
              </a>
            </li>
          </ul>
      </li> --}}
      {{-- end modul admin --}}
      @else
      {{-- modul pengguna --}}
      <li>
          <hr class="dropdown-divider">
      </li>
      <li class="{{ set_active('user-booking') }}">
        <a href="{{ route('user-booking') }}">
          <div class="parent-icon">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <div class="menu-title">Reservation</div>
        </a>
      </li>
      <li>
          <hr class="dropdown-divider">
      </li>
      <li class="{{ set_active('view_profile') }}">
        <a href="{{ route('view_profile') }}">
          <div class="parent-icon">
            <i class="fas fa-user"></i>
          </div>
        <div class="menu-title">Profile</div>
        </a>
      </li>
      {{-- <li>
        <a href="{{ route('riwayat-booking') }}">
          <div class="parent-icon">
            <i class="fas fa-history"></i>
          </div>
          <div class="menu-title">Riwayat booking</div>
        </a>
      </li> --}}
      {{-- end modul pengguna --}}
      @endif
      <li>
          <hr class="dropdown-divider">
      </li>
      <li>
          <a href="{{ route('logout') }}">
              <div class="parent-icon">
                <i class="fas fa-power-off"></i>
              </div>
              <div class="menu-title">Log out</div>
          </a>
      </li>
    
    </ul>
    <!--end navigation-->
  </aside>
  <!--end sidebar -->

  @if (Auth::user()->level === '2')
  {{-- menu for mobile --}}
  <nav class="navbar p-0 navbar-dark fixed-bottom border-top bg-primary navbar-expand d-lg-none d-xl-none">
    <ul class="navbar-nav nav-justified w-100">
      <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link {{ set_active('dashboard', 'active') }}">
          <i class="fas fa-th fa-2x"></i>
          <span class="small d-block">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('user-booking') }}" class="nav-link {{ set_active('user-booking', 'active') }}">
          <i class="fas fa-clipboard-list fa-2x"></i>
          <span class="small d-block">Reservation</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('view_profile') }}" class="nav-link {{ set_active('view_profile', 'active') }}">
          <i class="fas fa-user fa-2x"></i>
          <span class="small d-block">Profile</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('logout') }}" class="nav-link">
          <i class="fas fa-power-off fa-2x"></i>
          <span class="small d-block">Log out</span>
        </a>
      </li>
    </ul>
  </nav>
  @endif
  {{-- end menu for mobile --}}