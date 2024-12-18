@extends('layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __(config('app.name')) . ' | ' . $tag->title }}</title>
@endsection

@section('styles')
  @vite(['resources/csslibs/slick.css', 'resources/csslibs/simple-line-icons.css', 'resources/csslibs/style.css', 'resources/css/custom.css'])
@endsection

@section('content')
  <!-- section main content -->
  <section class="main-content mt-3">
    <div class="container">

      <div class="p-4 mt-4 bg-light shadow">
        <section class="section">
          <div class="container">
            <div class="row">
              <div class="col-md-2"></div>
              <div class="col-md-8">
                <div class="h-100 d-flex flex-column justify-content-between text-center">
                  <div class="top">
                    <p class="lead mb-0">{{ __('Tag') }}</p>
                    <h2 class="title mt-1 mb-2">{{ $tag->title }}</h2>
                    <hr>
                    <p class="lead text-black">{{ $tag->description }}</p>
                  </div>
                  <div class="h4 m-0"><span class="badge bg-primary">{{ __('articles') }}:
                      {{ $tag?->articles?->count() ?? 0 }}</span></div>
                </div>
              </div>
              <div class="col-md-2"></div>
            </div>
          </div>
        </section>
      </div>

      <div class="spacer" data-height="50"></div>

      @if ($tag->articles)
        <div class="row">
          @foreach ($tag->articles as $article)
            <!-- post -->
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="post post-grid rounded bordered">
                <div class="thumb top-rounded">
                  @if ($article->category_id)
                    <a class='category-badge position-absolute'
                      href='{{ route('category_show', $article->category_id) }}'>{{ $article->category->title }}</a>
                  @endif
                  <a href='{{ route('article_show', $article->id) }}'>
                    <div class="inner">
                      <img loading="lazy" class="article-image" src="{{ $article->image_url() }}"
                        alt="{{ __('post-title') }}" />
                    </div>
                  </a>
                </div>
                <div class="details">
                  <ul class="meta list-inline mb-0">
                    <li class="list-inline-item"><a href="#">
                        <img loading="lazy" src="{{ $article->user?->image_url() }}" class="author rounded-circle"
                          alt="{{ __('author') }}" />
                        {{ $article->user?->fullname() }}</a>
                    </li>
                    <li class="list-inline-item">{{ $article->created_at->format('Y-m-d') }}</li>
                  </ul>
                  <h5 class="post-title mb-3 mt-3"><a
                      href='{{ route('article_show', $article->id) }}'>{{ $article->title }}</a></h5>
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
      @endif

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
