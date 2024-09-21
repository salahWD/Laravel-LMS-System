@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . __('Home') }}</title>
@endsection

@section('styles')
  @vite(['resources/csslibs/slick.css', 'resources/csslibs/simple-line-icons.css', 'resources/csslibs/style.css'])
@endsection

@section('content')
  <section class="hero-carousel mt-5" dir="ltr">
    <div class="row post-carousel-featured post-carousel">
      @foreach ($popular_categories as $category)
        <!-- post -->
        <div class="post featured-post-md">
          <div class="details clearfix">
            <a class='category-badge' href=''>{{ $category->translate(app()->getLocale(), true)->title }}</a>
            {{-- <h4 class="post-title"><a href=''>{{ $article->title }}</a></h4> --}}
            <ul class="meta list-inline mb-0">
              {{-- <li class="list-inline-item"><a href="">{{ $article->user->title }}</a></li> --}}
              <li class="list-inline-item">{{ $category->show_date() }}</li>
            </ul>
          </div>
          <a href='{{ route('category_show', $category->id) }}'>
            <div class="thumb rounded">
              <div class="inner data-bg-image" data-bg-image="{{ $category->image_url() }}"></div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </section>

  <!-- section main content -->
  <section class="main-content">
    <div class="container-xl">

      <div class="row gy-4">

        <div class="col-lg-8">

          <div class="row gy-4">

            @foreach ($articles as $article)
              <!-- post -->
              <div class="col-sm-6">
                <div class="post post-grid rounded bordered">
                  <div class="thumb top-rounded overflow-hidden">
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
                        href='{{ route('article_show', $article->id) }}'>{{ $article->translate(app()->getLocale(), true)->title }}</a>
                    </h5>
                    <p class="excerpt mb-0">{{ $article->translate(app()->getLocale(), true)->description }}</p>
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

          {!! $articles->links() !!}

        </div>
        <div class="col-lg-4">

          <!-- sidebar -->
          <div class="sidebar">
            <!-- widget about -->
            <div class="widget rounded">
              <div class="widget-about data-bg-image text-center" data-bg-image="{{ url('images/map-bg.png') }}">
                <img loading="lazy" src="{{ url('images/logo.png') }}" alt="{{ __('logo') }}" class="mb-4" />
                <p class="mb-4">{{ __('Everything that is important and useful in electronic engineering') }}</p>
                <ul class="social-icons list-unstyled list-inline mb-0">
                  <li class="list-inline-item"><a href="#"><i class="icon-social-facebook"></i></a></li>
                  <li class="list-inline-item"><a href="#"><i class="icon-social-instagram"></i></a></li>
                  <li class="list-inline-item"><a href="#"><i class="icon-social-youtube"></i></a></li>
                </ul>
              </div>
            </div>

            {{-- <!-- widget popular posts -->
            <div class="widget rounded">
              <div class="widget-header text-center">
                <h3 class="widget-title">{{ __('Popular Posts') }}</h3>
                <img loading="lazy" src="{{ url("images/wave.svg") }}" class="wave" alt="{{ __('wave') }}" />
              </div>
              <div class="widget-content">
                <!-- post -->
                <div class="post post-list-sm circle">
                  <div class="thumb circle">
                    <span class="number">1</span>
                    <a href='blog-single.html'>
                      <div class="inner">
                        <img loading="lazy" src="images/posts/tabs-1.jpg" alt="{{ __('post-title') }}" />
                      </div>
                    </a>
                  </div>
                  <div class="details clearfix">
                    <h6 class="post-title my-0"><a href='blog-single.html'>{{ __('3 Easy Ways To Make Your iPhone Faster') }}</a></h6>
                    <ul class="meta list-inline mt-1 mb-0">
                      <li class="list-inline-item">{{ __('29 March 2021') }}</li>
                    </ul>
                  </div>
                </div>
                <!-- post -->
                <div class="post post-list-sm circle">
                  <div class="thumb circle">
                    <span class="number">2</span>
                    <a href='blog-single.html'>
                      <div class="inner">
                        <img loading="lazy" src="images/posts/tabs-2.jpg" alt="{{ __('post-title') }}" />
                      </div>
                    </a>
                  </div>
                  <div class="details clearfix">
                    <h6 class="post-title my-0"><a href='blog-single.html'>{{ __('An Incredibly Easy Method That Works For
                        All') }}</a></h6>
                    <ul class="meta list-inline mt-1 mb-0">
                      <li class="list-inline-item">{{ __('29 March 2021') }}</li>
                    </ul>
                  </div>
                </div>
                <!-- post -->
                <div class="post post-list-sm circle">
                  <div class="thumb circle">
                    <span class="number">3</span>
                    <a href='blog-single.html'>
                      <div class="inner">
                        <img loading="lazy" src="images/posts/tabs-3.jpg" alt="{{ __('post-title') }}" />
                      </div>
                    </a>
                  </div>
                  <div class="details clearfix">
                    <h6 class="post-title my-0"><a href='blog-single.html'>{{ __('10 Ways To Immediately Start Selling
                        Furniture') }}</a></h6>
                    <ul class="meta list-inline mt-1 mb-0">
                      <li class="list-inline-item">{{ __('29 March 2021') }}</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div> --}}

            <!-- widget categories -->
            <div class="widget rounded">
              <div class="widget-header text-center">
                <h3 class="widget-title">{{ __('Explore Topics') }}</h3>
                <img loading="lazy" src="{{ url('images/wave.svg') }}" class="wave" alt="wave" />
              </div>
              <div class="widget-content">
                <ul class="list">
                  @foreach ($popular_categories as $cat)
                    <li><a
                        href="{{ route('category_show', $category->id) }}">{{ $cat->translate(app()->getLocale(), true)->title }}</a><span>({{ $cat->articles_count }})</span>
                    </li>
                  @endforeach
                </ul>
              </div>

            </div>

            {{-- <!-- widget newsletter -->
            <div class="widget rounded">
              <div class="widget-header text-center">
                <h3 class="widget-title">{{ __('Newsletter') }}</h3>
                <img loading="lazy" src="{{ url("images/wave.svg") }}" class="wave" alt="{{ __('wave') }}" />
              </div>
              <div class="widget-content">
                <span class="newsletter-headline text-center mb-3">{{ __('Join 70,000 subscribers!') }}</span>
                <form>
                  <div class="mb-2">
                    <input class="form-control w-100 text-center" placeholder="{{ __('Email address…') }}" type="email">
                  </div>
                  <button class="btn btn-default btn-full" type="submit">{{ __('Sign Up') }}</button>
                </form>
                <span class="newsletter-privacy text-center mt-3">By signing up, you agree to our <a href="#">{{ __('Privacy
                    Policy') }}</a></span>
              </div>
            </div> --}}

            {{-- <!-- widget post carousel -->
            <div class="widget rounded">
              <div class="widget-header text-center">
                <h3 class="widget-title">{{ __('Celebration') }}</h3>
                <img loading="lazy" src="{{ url("images/wave.svg") }}" class="wave" alt="{{ __('wave') }}" />
              </div>
              <div class="widget-content">
                <div class="post-carousel-widget">
                  <!-- post -->
                  <div class="post post-carousel">
                    <div class="thumb rounded">
                      <a class='category-badge position-absolute' href='category.html'>{{ __('How to') }}</a>
                      <a href='blog-single.html'>
                        <div class="inner">
                          <img loading="lazy" src="images/widgets/widget-carousel-1.jpg" alt="{{ __('post-title') }}" />
                        </div>
                      </a>
                    </div>
                    <h5 class="post-title mb-0 mt-4"><a href='blog-single.html'>{{ __('5 Easy Ways You Can Turn Future Into
                        Success') }}</a></h5>
                    <ul class="meta list-inline mt-2 mb-0">
                      <li class="list-inline-item"><a href="#">{{ __('Katen Doe') }}</a></li>
                      <li class="list-inline-item">{{ __('29 March 2021') }}</li>
                    </ul>
                  </div>
                  <!-- post -->
                  <div class="post post-carousel">
                    <div class="thumb rounded">
                      <a class='category-badge position-absolute' href='category.html'>{{ __('Various') }}</a>
                      <a href='blog-single.html'>
                        <div class="inner">
                          <img loading="lazy" src="images/widgets/widget-carousel-2.jpg" alt="{{ __('post-title') }}" />
                        </div>
                      </a>
                    </div>
                    <h5 class="post-title mb-0 mt-4"><a href='blog-single.html'>{{ __('Master The Art Of Nature With These 7
                        Tips') }}</a></h5>
                    <ul class="meta list-inline mt-2 mb-0">
                      <li class="list-inline-item"><a href="#">{{ __('Katen Doe') }}</a></li>
                      <li class="list-inline-item">{{ __('29 March 2021') }}</li>
                    </ul>
                  </div>
                  <!-- post -->
                  <div class="post post-carousel">
                    <div class="thumb rounded">
                      <a class='category-badge position-absolute' href='category.html'>{{ __('How to') }}</a>
                      <a href='blog-single.html'>
                        <div class="inner">
                          <img loading="lazy" src="images/widgets/widget-carousel-1.jpg" alt="{{ __('post-title') }}" />
                        </div>
                      </a>
                    </div>
                    <h5 class="post-title mb-0 mt-4"><a href='blog-single.html'>{{ __('5 Easy Ways You Can Turn Future Into
                        Success') }}</a></h5>
                    <ul class="meta list-inline mt-2 mb-0">
                      <li class="list-inline-item"><a href="#">{{ __('Katen Doe') }}</a></li>
                      <li class="list-inline-item">{{ __('29 March 2021') }}</li>
                    </ul>
                  </div>
                </div>
                <!-- carousel arrows -->
                <div class="slick-arrows-bot">
                  <button type="button" data-role="none" class="carousel-botNav-prev slick-custom-buttons"
                    aria-label="Previous"><i class="icon-arrow-left"></i></button>
                  <button type="button" data-role="none" class="carousel-botNav-next slick-custom-buttons"
                    aria-label="Next"><i class="icon-arrow-right"></i></button>
                </div>
              </div>
            </div> --}}

            <!-- widget tags -->
            <div class="widget rounded">
              <div class="widget-header text-center">
                <h3 class="widget-title">{{ __('Tag Clouds') }}</h3>
                <img loading="lazy" src="{{ url('images/wave.svg') }}" class="wave" alt="{{ __('wave') }}" />
              </div>
              <div class="widget-content">
                @foreach ($popular_tags as $tag)
                  {{-- <a href="{{ route('list_tag_articles', $tag->id) }}" class="tag"> --}}
                  <a href="{{ route('tag_view', $tag->slug) }}" class="tag">
                    {{ $tag->title }}
                  </a>
                @endforeach
                <a href="#" class="tag">{{ __('Electronics') }}</a>
                <a href="#" class="tag">{{ __('General') }}</a>
                <a href="#" class="tag">{{ __('Inspiration') }}</a>
                <a href="#" class="tag">{{ __('Mechanics') }}</a>
              </div>
            </div>

            <!-- widget -->
            <div class="widget no-container rounded text-md-center">
              <span class="spons-title">- {{ __('additional space') }} -</span>
              <a href="#" class="widget-spons">
                <img loading="lazy" src="{{ url('images/additional/widget.jpeg') }}"
                  alt="{{ __('additional space') }}" style="object-fit: cover; width: min(100%, 280px)" />
              </a>
            </div>

          </div>

        </div>

      </div>

    </div>
  </section>

  {{-- <!-- instagram feed -->
  <div class="instagram">
    <div class="container-xl">
      <!-- button -->
      <a href="#" class="btn btn-default btn-instagram">{{ __(' on Instagram') }} <bdi>@Walid_Isa</bdi></a>
      <!-- images -->
      <div class="instagram-feed d-flex flex-wrap">
        <div class="insta-item col-sm-2 col-6 col-md-2">
          <a href="#">
            <img loading="lazy" src="{{ url('images/insta/insta-1.jpg') }}" alt="{{ __('insta-title') }}" />
          </a>
        </div>
        <div class="insta-item col-sm-2 col-6 col-md-2">
          <a href="#">
            <img loading="lazy" src="{{ url('images/insta/insta-2.jpg') }}" alt="{{ __('insta-title') }}" />
          </a>
        </div>
        <div class="insta-item col-sm-2 col-6 col-md-2">
          <a href="#">
            <img loading="lazy" src="{{ url('images/insta/insta-3.jpg') }}" alt="{{ __('insta-title') }}" />
          </a>
        </div>
        <div class="insta-item col-sm-2 col-6 col-md-2">
          <a href="#">
            <img loading="lazy" src="{{ url('images/insta/insta-4.jpg') }}" alt="{{ __('insta-title') }}" />
          </a>
        </div>
        <div class="insta-item col-sm-2 col-6 col-md-2">
          <a href="#">
            <img loading="lazy" src="{{ url('images/insta/insta-5.jpg') }}" alt="{{ __('insta-title') }}" />
          </a>
        </div>
        <div class="insta-item col-sm-2 col-6 col-md-2">
          <a href="#">
            <img loading="lazy" src="{{ url('images/insta/insta-6.jpg') }}" alt="{{ __('insta-title') }}" />
          </a>
        </div>
      </div>
    </div>
  </div> --}}
@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script src="{{ url('js/popper.min.js') }}"></script>
  <script src="{{ url('js/bootstrap.min.js') }}"></script>
  <script src="{{ url('js/slick.min.js') }}"></script>
  <script src="{{ url('js/jquery.sticky-sidebar.min.js') }}"></script>
  <script src="{{ url('js/custom.js') }}"></script>
@endsection
