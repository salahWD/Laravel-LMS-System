<!-- header -->
<header class="header-default shadow-sm">
  <nav class="navbar navbar-expand-lg">
    <div class="container-xl">
      <!-- site logo -->
      <a class='navbar-brand' href='{{ route("home") }}'><img src="{{ url("images/logo.png") }}" style="max-width: 75px;" alt="{{ __('logo') }}" /></a>

      <div class="collapse navbar-collapse">
        <!-- menus -->
        <ul class="navbar-nav mr-auto">
          <li class="nav-item @if(request()->segment(2) == '') active @endif">
            <a class='nav-link' href='{{ route("home") }}'>{{ __("Home") }}</a>
          </li>
          <li class="nav-item @if(request()->segment(2) == 'articles') active @endif">
            <a class='nav-link' href='{{ route("articles_show") }}'>{{ __('Articles') }}</a>
          </li>
          @if (isset($all_courses) && $all_courses != null)
            <li class="nav-item dropdown @if(request()->segment(2) == 'courses') active @endif">
              <a class="nav-link dropdown-toggle" href="{{ route("courses_show") }}">{{ __('Courses') }}</a>
              <ul class="dropdown-menu text-start">
                @foreach($all_courses as $course)
                  <li><a class='dropdown-item' href='{{ route("course_show", $course->id) }}'>{{ $course->title }}</a></li>
                @endforeach
              </ul>
            </li>
          @endif
          <li class="nav-item @if(request()->segment(2) == 'experiences') active @endif">
            <a class='nav-link' href=''>{{ __('Experiences') }}</a>
          </li>
          <li class="nav-item @if(request()->segment(2) == 'products') active @endif">
            <a class='nav-link' href='{{ route("shop") }}'>{{ __('shop') }}</a>
          </li>
          <li class="nav-item @if(request()->segment(2) == 'contact-us') active @endif">
            <a class='nav-link' href='{{ route("contact_us") }}'>{{ __('Contact') }}</a>
          </li>
        </ul>
      </div>

      <!-- header right section -->
      <div class="header-right">
        <!-- header buttons -->
        {{-- <div class="header-buttons">
          <button class="search icon-button">
            <i class="icon-magnifier" aria-hidden="true"></i>
          </button>
        </div> --}}
        @if (auth()->check())
          <a href="{{ route("profile.edit") }}" class="account d-flex align-items-center">
            <p class="fullname pe-2 m-0 me-2">{{ auth()->user()->fullname() }}</p>
            <img src="{{ auth()->user()->image_url() }}" alt="{{ auth()->user()->fullname() }}" class="rounded-circle">
          </a>
        @else
          <a class="login" href="{{ route("login") }}">
            {{ __("Login / Register") }}
          </a>
        @endif
      </div>
    </div>
  </nav>
</header>
