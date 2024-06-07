<nav class="navbar navbar-dark navbar-theme-primary px-4 col-12 d-lg-none">
  <a class="navbar-brand me-lg-5" href="#">
    <img class="navbar-brand-dark" src="{{ url("img/brand/light.svg") }}" alt="{{ __('Eletorial logo') }}" />
    <img class="navbar-brand-light" src="{{ url("img/brand/dark.svg") }}" alt="{{ __('Eletorial logo') }}" />
  </a>
  <div class="d-flex align-items-center">
    <button class="navbar-toggler d-lg-none collapsed" type="button" data-bs-toggle="collapse"
      data-bs-target="{{ __('sidebarMenu') }}" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<nav id="sidebarMenu" class="sidebar d-lg-block bg-gray-800 text-white collapse" data-simplebar>
  <div class="sidebar-inner px-4 pt-3">
    <div class="user-card d-flex d-md-none align-items-center justify-content-between justify-content-md-center pb-4">
      <div class="d-flex align-items-center">
        <div class="avatar-lg me-4">
          <img src="{{ url("img/team/profile-picture-3.jpg") }}" class="card-img-top rounded-circle border-white"
            alt="{{ __('Bonnie Green') }}">
        </div>
        <div class="d-block">
          <h2 class="h5 mb-3">Hi, {{ auth()->user()->username }}</h2>
          <a href="dashboard/examples/sign-in" tabindex="-1"
            class="btn btn-secondary btn-sm d-inline-flex align-items-center">
            <svg class="icon icon-xxs me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            {{ __('Sign Out') }}
          </a>
        </div>
      </div>
      <div class="collapse-close d-md-none">
        <a href="{{ __('sidebarMenu') }}" data-bs-toggle="collapse" data-bs-target="{{ __('sidebarMenu') }}" aria-controls="sidebarMenu"
          aria-expanded="true" aria-label="Toggle navigation">
          <svg class="icon icon-xs" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"></path>
          </svg>
        </a>
      </div>
    </div>
    <ul class="nav flex-column pt-3 pt-md-0">
      <li class="nav-item">
        <a href="{{ route("dashboard") }}" class="nav-link d-flex align-items-center">
          <span class="sidebar-icon">
            <img src="{{ url("img/brand/light.svg") }}" height="20" width="20" alt="{{ __('Eletorial Logo') }}">
          </span>
          <span class="mt-1 ms-1 sidebar-text">{{ __('Eletorial') }}</span>
        </a>
      </li>
      <li class="nav-item {{ (\Request::route()->getName() == 'dashboard') ? 'active' : '' }}">
        <a href="{{ route("dashboard") }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
              <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
            </svg>
          </span>
          <span class="sidebar-text">{{ __("Dashboard") }}</span>
        </a>
      </li>
      <li class="nav-item {{ (\Request::route()->getName() == 'users_manage') ? 'active' : '' }}">
        <a href="{{ route("users_manage") }}" class="nav-link">
          <span class="sidebar-icon">
            <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="20.000000pt" height="20.000000pt" viewBox="0 0 512.000000 512.000000" preserveAspectRatio="xMidYMid meet" style="padding-right: 6px;padding-left: 2px;box-sizing: border-box;">
              <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)" fill="currentColor" stroke="none">
                <path d="M1110 4694 c-84 -17 -229 -90 -305 -151 -162 -131 -260 -325 -272 -538 -8 -139 13 -239 79 -375 44 -91 62 -115 142 -195 76 -76 106 -99 186 -138 121 -59 207 -80 335 -80 136 0 262 31 361 88 39 23 42 28 64 112 44 170 156 357 277 460 l56 48 -6 90 c-14 205 -113 399 -272 528 -77 63 -220 133 -310 152 -87 18 -249 18 -335 -1z"/>
                <path d="M3670 4695 c-85 -19 -229 -91 -305 -152 -150 -122 -251 -310 -271 -508 l-6 -61 43 -29 c191 -125 352 -384 384 -612 6 -48 9 -52 48 -67 93 -35 173 -49 277 -49 133 0 217 20 340 80 80 39 110 62 186 138 80 80 98 104 142 195 66 136 87 236 79 375 -18 318 -228 583 -538 680 -75 24 -292 30 -379 10z"/>
                <path d="M2473 3930 c-184 -33 -357 -138 -468 -285 -63 -84 -95 -145 -127 -245 -19 -64 -23 -96 -23 -215 0 -154 14 -213 77 -338 214 -424 782 -541 1144 -235 277 234 350 610 181 933 -31 60 -65 103 -132 170 -101 101 -196 158 -325 195 -86 24 -247 34 -327 20z"/>
                <path d="M769 3140 c-379 -80 -671 -375 -749 -757 -19 -95 -20 -131 -18 -539 l3 -437 150 -43 c168 -49 419 -105 570 -129 100 -16 351 -45 391 -45 21 0 22 4 27 208 4 181 8 220 31 307 50 191 172 400 313 537 110 107 298 222 421 259 23 7 42 15 42 19 0 3 -14 19 -31 36 -119 113 -225 346 -245 540 l-7 64 -406 -1 c-352 0 -417 -3 -492 -19z"/>
                <path d="M3526 3138 c-3 -13 -8 -48 -11 -78 -17 -151 -95 -330 -201 -461 -36 -45 -64 -82 -62 -84 2 -1 29 -12 60 -23 268 -99 492 -301 623 -562 87 -174 113 -293 122 -557 6 -189 6 -193 27 -188 12 2 66 7 121 10 294 19 631 96 838 192 l77 36 0 430 c0 393 -2 437 -21 530 -77 385 -370 679 -753 758 -79 16 -138 19 -453 19 l-363 0 -4 -22z"/>
                <path d="M2177 2379 c-242 -25 -480 -153 -636 -341 -73 -87 -163 -261 -193 -373 -21 -76 -22 -103 -26 -556 l-3 -476 123 -36 c262 -77 483 -124 728 -157 199 -27 637 -38 814 -21 331 33 616 100 814 192 l84 39 -5 452 c-5 504 -8 529 -74 688 -140 333 -440 553 -802 590 -119 12 -709 11 -824 -1z"/>
              </g>
            </svg>
          </span>
          <span class="sidebar-text">{{ __("Users") }}</span>
        </a>
      </li>
      <li class="nav-item">
        <span
          class="nav-link d-flex justify-content-between align-items-center {{ !in_array(request()->segment(3), ["articles", "categories", "comments", "tags"]) ? 'collapsed' : '' }}"
          data-bs-toggle="collapse"
          data-bs-target="#submenu-articles"
          aria-expanded="{{ in_array(request()->segment(3), ["articles", "categories", "comments", "tags"]) ? 'true' : 'false' }}">
          <span>
            <span class="sidebar-icon">
              <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z"
                  clip-rule="evenodd"></path>
              </svg>
            </span>
            <span class="sidebar-text">{{ __('Posts') }}</span>
          </span>
          <span class="link-arrow">
            <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"></path>
            </svg>
          </span>
        </span>
        <div
          class="multi-level collapse
          {{ in_array(request()->segment(3), ["articles", "categories", "comments", "tags"]) ? 'show' : '' }}"
          role="list"
          id="submenu-articles">
          <ul class="flex-column nav">
            <li class="nav-item {{ (request()->segment(3) == 'articles') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route("articles_manage") }}">
                <span class="sidebar-text">{{ __('Articles') }}</span>
              </a>
            </li>
            <li class="nav-item {{ (request()->segment(3) == 'categories') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route("categories_manage") }}">
                <span class="sidebar-text">{{ __('Categories') }}</span>
              </a>
            </li>
            <li class="nav-item {{ (request()->segment(3) == 'comments') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route("comments_manage") }}">
                <span class="sidebar-text">{{ __('Comments') }}</span>
              </a>
            </li>
            <li class="nav-item {{ (request()->segment(3) == 'tags') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route("tags_manage") }}">
                <span class="sidebar-text">{{ __('Tags') }}</span>
              </a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <span
          class="nav-link d-flex justify-content-between align-items-center {{ !in_array(request()->segment(3), ["courses", "lectures", "tests", "certificates"]) ? 'collapsed' : '' }}"
          data-bs-toggle="collapse"
          data-bs-target="#submenu-teaching"
          aria-expanded="{{ in_array(request()->segment(3), ["courses", "lectures", "tests", "certificates"]) ? 'true' : 'false' }}">
          <span>
            <span class="sidebar-icon">
              <svg class="icon icon-xs me-2" data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z"></path>
                <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z"></path>
                <path d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z"></path>
              </svg>
            </span>
            <span class="sidebar-text">{{ __('Teaching') }}</span>
          </span>
          <span class="link-arrow">
            <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"></path>
            </svg>
          </span>
        </span>
        <div
          class="multi-level collapse
          {{ in_array(request()->segment(3), ["courses", "lectures", "tests", "certificates"]) ? 'show' : '' }}"
          role="list"
          id="submenu-teaching">
          <ul class="flex-column nav">
            <li class="nav-item {{ (request()->segment(3) == 'courses') ? 'active' : '' }}">
              <a href="{{ route("courses_manage") }}" class="nav-link">
                <span class="sidebar-text">{{ __('Courses') }}</span>
              </a>
            </li>
            <li class="nav-item {{ (request()->segment(3) == 'certificates') ? 'active' : '' }}">
              <a href="{{ route("certificates_manage") }}" class="nav-link">
                <span class="sidebar-text">{{ __('Certificate') }}</span>
              </a>
            </li>
            <li class="nav-item {{ (request()->segment(3) == 'lectures') ? 'active' : '' }}">
              <a href="{{ route("lectures_manage") }}" class="nav-link">
                <span class="sidebar-text">{{ __('Lectures') }}</span>
              </a>
            </li>
            <li class="nav-item {{ (request()->segment(3) == 'tests') ? 'active' : '' }}">
              <a href="{{ route("tests_manage") }}" class="nav-link">
                <span class="sidebar-text">{{ __('Tests') }}</span>
              </a>
            </li>
          </ul>
        </div>
      </li>

      <li class="nav-item">
        <span
          class="nav-link d-flex justify-content-between align-items-center {{ !in_array(request()->segment(3), ["products", "pcategories", "orders"]) ? 'collapsed' : '' }}"
          data-bs-toggle="collapse"
          data-bs-target="#submenu-products"
          aria-expanded="{{ in_array(request()->segment(3), ["products", "pcategories", "orders"]) ? 'true' : 'false' }}">
          <span>
            <span class="sidebar-icon">
              <svg class="icon icon-xs me-2" data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path clip-rule="evenodd" fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 0 0 4.25 22.5h15.5a1.875 1.875 0 0 0 1.865-2.071l-1.263-12a1.875 1.875 0 0 0-1.865-1.679H16.5V6a4.5 4.5 0 1 0-9 0ZM12 3a3 3 0 0 0-3 3v.75h6V6a3 3 0 0 0-3-3Zm-3 8.25a3 3 0 1 0 6 0v-.75a.75.75 0 0 1 1.5 0v.75a4.5 4.5 0 1 1-9 0v-.75a.75.75 0 0 1 1.5 0v.75Z"></path>
              </svg>
            </span>
            <span class="sidebar-text">{{ __('Products') }}</span>
          </span>
          <span class="link-arrow">
            <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"></path>
            </svg>
          </span>
        </span>
        <div
          class="multi-level collapse
          {{ in_array(request()->segment(3), ["products", "pcategories", "orders"]) ? 'show' : '' }}"
          role="list"
          id="submenu-products">
          <ul class="flex-column nav">
            <li class="nav-item {{ (request()->segment(3) == 'pcategories') ? 'active' : '' }}">
              <a href="{{ route("product_category_create") }}" class="nav-link">
                <span class="sidebar-text">{{ __('Categories') }}</span>
              </a>
            </li>
            <li class="nav-item {{ (request()->segment(3) == 'products') ? 'active' : '' }}">
              <a href="{{ route("products_manage") }}" class="nav-link">
                <span class="sidebar-text">{{ __('Products') }}</span>
              </a>
            </li>
            <li class="nav-item {{ (request()->segment(3) == 'orders') ? 'active' : '' }}">
              <a href="{{ route("orders_manage") }}" class="nav-link">
                <span class="sidebar-text">{{ __('orders') }}</span>
              </a>
            </li>
          </ul>
        </div>
      </li>


      <li class="nav-item {{ (request()->segment(3) == 'messages') ? 'active' : '' }}">
        <a href="{{ route("messages_manage") }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path clip-rule="evenodd" fill-rule="evenodd" d="M12 2.25c-2.429 0-4.817.178-7.152.521C2.87 3.061 1.5 4.795 1.5 6.741v6.018c0 1.946 1.37 3.68 3.348 3.97.877.129 1.761.234 2.652.316V21a.75.75 0 0 0 1.28.53l4.184-4.183a.39.39 0 0 1 .266-.112c2.006-.05 3.982-.22 5.922-.506 1.978-.29 3.348-2.023 3.348-3.97V6.741c0-1.947-1.37-3.68-3.348-3.97A49.145 49.145 0 0 0 12 2.25ZM8.25 8.625a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Zm2.625 1.125a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875-1.125a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Z"></path>
            </svg>
          </span>
          <span class="sidebar-text">{{ __('Messages') }}</span>
        </a>
      </li>

      <li class="nav-item {{ (request()->segment(3) == 'settings') ? 'active' : '' }}">
        <a href="{{ route("dashboard_settings") }}" class="nav-link">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                clip-rule="evenodd"></path>
            </svg>
          </span>
          <span class="sidebar-text">{{ __('Settings') }}</span>
        </a>
      </li>

      <li role="separator" class="dropdown-divider mt-4 mb-3 border-gray-700"></li>
      <li class="nav-item">
        <a href="https://t.me/salahbakhash"
          class="nav-link d-flex align-items-center">
          <span class="sidebar-icon">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                clip-rule="evenodd"></path>
            </svg>
          </span>
          <span class="sidebar-text">{{ __("Want Help") }} <span class="badge badge-sm bg-secondary ms-1 text-gray-800">{{ __('call me') }}</span></span>
        </a>
      </li>
      {{-- <li class="nav-item">
        <a href="dashboard/upgrade-to-pro"
          class="btn btn-secondary d-flex align-items-center justify-content-center btn-upgrade-pro">
          <span class="sidebar-icon d-inline-flex align-items-center justify-content-center">
            <svg class="icon icon-xs me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                clip-rule="evenodd"></path>
            </svg>
          </span>
          <span>{{ __('Upgrade to Pro') }}</span>
        </a>
      </li> --}}
    </ul>
  </div>
</nav>
