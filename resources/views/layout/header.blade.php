@php

  $links = [
      'Home' => [
          'url' => route('home'),
          'name' => '',
      ],
      'Articles' => [
          'url' => route('articles_show'),
          'name' => 'articles',
      ],
      'Courses' => null,
  ];
  if (config('settings.shop_status')) {
      $links['shop'] = [
          'url' => route('shop'),
          'name' => 'products',
      ];
  }
  $links['Contact'] = [
      'url' => route('contact_us'),
      'name' => 'contact-us',
  ];
  if (isset($all_courses) && $all_courses != null) {
      $links['Courses'] = [
          'url' => route('courses_show'),
          'name' => 'courses',
          'items' => $all_courses,
      ];
  }

@endphp
<!-- header -->
<header class="header-default shadow-sm">
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <!-- site logo -->
      <a class='navbar-brand d-none d-lg-inline' href='{{ route('home') }}'><img src="{{ url('images/logo.png') }}"
          style="max-width: 75px;" alt="{{ __('logo') }}" /></a>

      <div class="collapse navbar-collapse">
        <!-- menus -->
        <ul class="navbar-nav mr-auto">
          @foreach ($links as $key => $data)
            @if (isset($data['items']))
              <li class="nav-item dropdown @if (request()->segment(2) == $data['name']) active @endif">
                <a class="nav-link dropdown-toggle" href="{{ $data['url'] }}">{{ __($key) }}</a>
                <ul class="dropdown-menu text-start">
                  @foreach ($all_courses as $course)
                    <li><a class='dropdown-item' href='{{ route('course_show', $course->id) }}'>{{ $course->title }}</a>
                    </li>
                  @endforeach
                </ul>
              </li>
            @else
              <li class="nav-item @if (request()->segment(2) == $data['name']) active @endif">
                <a class='nav-link' href='{{ $data['url'] }}'>{{ __($key) }}</a>
              </li>
            @endif
          @endforeach
          {{-- <li class="nav-item @if (request()->segment(2) == 'experiences') active @endif">
                        <a class='nav-link' href=''>{{ __('Experiences') }}</a>
                    </li> --}}
        </ul>
      </div>

      <button class="burger-menu icon-button d-lg-none">
        <span class="burger-icon"></span>
      </button>

      <!-- header right section -->
      <div class="header-right">
        <!-- header buttons -->
        {{-- <div class="header-buttons">
          <button class="search icon-button">
            <i class="icon-magnifier" aria-hidden="true"></i>
          </button>
        </div> --}}
        @if (auth()->check())
          <a href="{{ route('profile.edit') }}" class="account d-flex align-items-center">
            <p class="fullname pe-2 m-0 me-2">{{ auth()->user()->fullname() }}</p>
            <img src="{{ auth()->user()->image_url() }}" alt="{{ auth()->user()->fullname() }}"
              class="rounded-circle">
          </a>
        @else
          <a class="login" href="{{ route('login') }}">
            {{ __('Login / Register') }}
          </a>
        @endif
      </div>
    </div>
  </nav>
</header>

<div class="canvas-menu position-left d-flex align-items-end flex-column d-lg-none">
  <!-- close button -->
  <button type="button" class="btn-close" aria-label="Close"></button>

  <!-- logo -->
  <div class="logo">
    <a href='{{ route('home') }}'><img src="{{ url('images/logo.png') }}" style="max-width: 75px;"
        alt="{{ __('logo') }}" /></a>
  </div>

  <!-- menu -->
  <nav>
    <ul class="vertical-menu">
      @foreach ($links as $key => $data)
        @if (isset($data['items']))
          <li class="@if (request()->segment(2) == $data['name']) active @endif">
            <a href="{{ $data['url'] }}">{{ __($key) }}</a>
            <i class="icon-arrow-down switch"></i>
            <ul class="submenu">
              @foreach ($all_courses as $course)
                <li>
                  <a href="{{ route('course_show', $course->id) }}">{{ $course->title }}</a>
                </li>
              @endforeach
            </ul>
          </li>
        @else
          <li class="@if (request()->segment(2) == $data['name']) active @endif">
            <a href='{{ $data['url'] }}'>{{ __($key) }}</a>
          </li>
        @endif
      @endforeach

    </ul>
  </nav>

</div>
