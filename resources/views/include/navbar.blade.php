<!--start top header-->
<header class="top-header">
  @if (Auth::user()->level === '1')
    <nav class="navbar navbar-expand gap-3">
      <div class="mobile-menu-button">
          <i class="fas fa-bars"></i>
      </div>
      <div class="top-navbar-right ms-auto">

        <ul class="navbar-nav align-items-center ">    
          
          <li class="nav-item">
           <p class="mt-3">{{ Session::get('nama_perusahaan') }}</p>
          </li>

          <li class="nav-item dropdown dropdown-user-setting ">
            <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
              <div class="user-setting">
                <img src="{{ asset('assets/images/avatars/user.jpg') }}" class="user-img" alt="">
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <div class="d-flex flex-row align-items-center gap-2">
                  <img src="{{ asset('assets/images/avatars/user.jpg') }}"  class="rounded-circle" width="54" height="54">
                  <div class="">
                    <h6 class="mb-0 dropdown-user-name">
                      {{ Auth::user()->nama }}
                    </h6>
                    <small class="mb-0 dropdown-user-designation text-secondary">
                      Admin
                    </small>
                  </div>
                </div>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('view_profile') }}">
                  <div class="d-flex align-items-center">
                    <div class="">
                      <i class="fas fa-user-alt"></i>
                    </div>
                    <div class="ms-3"><span>Profile</span></div>
                  </div>
                </a>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('logout') }}">
                  <div class="d-flex align-items-center">
                    <div class="">
                      <i class="fas fa-power-off"></i>
                    </div>
                    <div class="ms-3">
                      <span>Log out</span>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
          </li>

        </ul>

      </div>
    </nav>
    @else
    <span class="d-block">
      {{ Session::get('nama_perusahaan') }}
    </span>
    @endif
  </header>
  <!--end top header-->