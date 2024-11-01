@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . 'Courses' }}</title>
@endsection

@section('styles')
  @vite(['resources/csslibs/slick.css', 'resources/csslibs/simple-line-icons.css', 'resources/csslibs/style.css'])

  @vite(['resources/css/courses.css'])
@endsection

@section('content')
  <!-- section main content -->
  <section class="main-content mt-5">
    <div class="container">

      @if (auth()->check())
        <h2 class="title">{{ __('Hi, :name', ['name' => auth()->user()->fullname()]) }},
          {{ __("Let's Start Learning") }}</h2>
      @else
        <h2 class="title">{{ __("Let's Start Learning") }}</h2>
      @endif
      <hr>

      <div class="swiper-wrapper" aria-live="polite">
        @foreach ($courses as $course)
          <div class="swiper-slide swiper-slide-active style-mfekx" role="group" aria-label="1 / 5"
            data-swiper-slide-index="0">
            <div class="courses__item courses__item-two shine__animate-item">
              <div class="courses__item-thumb courses__item-thumb-two">
                <a href="{{ route('course_show', $course->id) }}" class="shine__animate-link">
                  <img src="{{ $course->image_url() }}" alt="{{ $course->title }}">
                </a>
              </div>
              <div class="courses__item-content courses__item-content-two">
                <ul class="courses__item-meta list-wrap">
                  {{-- <li class="courses__item-tag">
                  <a href="#">{{ $course->title }}</a>
                </li> --}}
                  <div class="courses__item-content-bottom">
                    <div class="author-two">
                      <a href="#">
                        <img src="{{ $course->user->image_url() }}"
                          alt="{{ $course->user->fullname() }}">{{ $course->user->fullname() }}
                      </a>
                    </div>
                  </div>
                  <li class="price">
                    {{-- <del>$29.00</del> --}}
                    {{ $course->show_price() }}
                  </li>
                </ul>
                <h5 class="title m-0"><a href="{{ route('course_show', $course->id) }}">{{ $course->title }}</a></h5>
              </div>
              <div class="courses__item-bottom-two">
                <ul class="list-wrap">
                  <li><i class="fa fa-book"></i>{{ $course->items()->count() }}</li>
                  <li><i class="fa fa-clock"></i>{{ $course->total_time() }}</li>
                  <li><i class="fas fa-graduation-cap"></i>{{ $course->students()->count() }}</li>
                </ul>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      @if (true)
        {!! $courses->links() !!}
      @endif


      {{-- <div class="spacer" data-height="50"></div> --}}

      {{-- @if ($category->articles)
      <div class="row">
        @foreach ($category->articles as $article)
          <!-- post -->
          <div class="col-md-4 col-sm-6">
            <div class="post post-grid rounded bordered">
              <div class="thumb top-rounded">
                @if ($article->category_id)
                  <a class='category-badge position-absolute' href='{{ route("category_show", $article->category_id) }}'>{{ $article->category->title }}</a>
                @endif
                <a href='{{ route("article_show", $article->id) }}'>
                  <div class="inner">
                    <img loading="lazy" class="article-image" src="{{ $article->image_url() }}" alt="{{ __('post-title') }}" />
                  </div>
                </a>
              </div>
              <div class="details">
                <ul class="meta list-inline mb-0">
                  <li class="list-inline-item"><a href="#">
                    <img loading="lazy" src="{{ $article->user?->image_url() }}" class="author rounded-circle" alt="{{ __('author') }}" />
                    {{ $article->user?->fullname() }}</a>
                  </li>
                  <li class="list-inline-item">{{ $article->created_at->format("Y-m-d") }}</li>
                </ul>
                <h5 class="post-title mb-3 mt-3"><a href='{{ route("article_show", $article->id) }}'>{{ $article->title }}</a></h5>
                <p class="excerpt mb-0">{{ $article->description }}</p>
              </div>
              <div class="post-bottom clearfix d-flex align-items-center" dir="ltr">
                <div class="social-share me-auto">
                  <button class="toggle-button icon-share"></button>
                  <ul class="icons list-unstyled list-inline mb-0">
                    <li class="list-inline-item"><a href="#"><i class="icon-social-facebook"></i></a></li>
                    <li class="list-inline-item"><a href="#"><i class="icon-social-instagram"></i></a></li>
                    <li class="list-inline-item"><a href="#"><i class="icon-social-youtube"></i></a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif --}}

    </div>
  </section>
@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script src="{{ url('js/popper.min.js') }}"></script>
  <script src="{{ url('js/bootstrap.min.js') }}"></script>
  <script src="{{ url('js/slick.min.js') }}"></script>
  <script src="{{ url('js/jquery.sticky-sidebar.min.js') }}"></script>
@endsection
