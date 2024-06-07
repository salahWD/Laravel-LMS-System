@extends('layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __(config("app.name")) . " | " . $article->title }}</title>
@endsection

@section('styles')
  <!-- STYLES -->
  @if (app()->getLocale() == "ar")
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
  @else
    @vite('resources/csslibs/bootstrap.min.css')
  @endif

  @vite([
    'resources/csslibs/slick.css',
    'resources/csslibs/simple-line-icons.css',
    'resources/csslibs/style.css',
    'resources/css/custom.css',
    'resources/css/editor.css',
  ])
@endsection

@section('content')

  <!-- section main content -->
  <section class="main-content mt-3">
    <div class="container-xl">

      <div class="p-4 mt-4 bg-light shadow">

        <!-- post single -->
        <div class="post post-single">
          <!-- post header -->
          <div class="post-header">
            <h1 class="title mt-0 mb-3">{{ $article->title }}</h1>
            <ul class="meta list-inline mb-0">
              <li class="list-inline-item"><a href="#"><img load="lazy" src="{{ $article->user->image_url() }}" class="rounded-circle author" alt="author"/>{{ $article->user->fullname() }}</a></li>
              @if ($article->category_id) <li class="list-inline-item"><a href="#">{{ $article->category?->title }}</a></li> @endif
              <li class="list-inline-item">{{ $article->created_at->format("Y-m-d") }}</li>
            </ul>
          </div>
          <!-- featured image -->
          <div class="featured-image">
            <img load="lazy" class="article-image" src="{{ $article->image_url() }}" alt="post-title" />
          </div>
          <!-- post content -->
          <div class="article-content post-content clearfix">
            {!! $article->content !!}
          </div>
          {{-- <div class="post-content clearfix">
            <p>The European languages are members of the same family. Their separate existence is a myth. For science, music, sport, etc, Europe uses the same <a href="#">vocabulary</a>. The languages only differ in their grammar, their pronunciation and their most common words.</p>

            <p>Everyone realizes why a new common language would be desirable: one could refuse to pay expensive translators. To achieve this, it <mark>would be</mark> necessary to have uniform grammar, pronunciation and more common words.</p>

            <figure class="figure">
              <img src="images/posts/post-lg-2.jpg" class="figure-img img-fluid rounded" alt="...">
              <figcaption class="figure-caption text-center">A caption for the above image.</figcaption>
            </figure>

            <p>The languages only differ in their grammar, their pronunciation and their most common words. Everyone realizes why a new common language would be desirable.</p>

            <img src="images/posts/single-sm-1.jpg" class="rounded alignleft" alt="...">
            <p>One could refuse to pay expensive translators. To achieve this, it would be necessary to have uniform grammar, pronunciation and more common words.</p>

            <p>If several languages coalesce, the grammar of the resulting language is more simple and regular than that of the individual languages. The new common language will be more simple and regular than the existing <a href="#">European languages</a>. It will be as simple as Occidental; in fact, it will be Occidental.</p>

            <p>A collection of textile samples lay spread out on the table - Samsa was a travelling salesman - and above it there hung a picture that he had recently cut out of an illustrated magazine and housed in a nice, gilded frame.</p>

            <h4>I should be incapable of drawing a single stroke</h4>

            <ul>
              <li>How about if I sleep a little bit</li>
              <li>A collection of textile samples lay spread out</li>
              <li>His many legs, pitifully thin compared with</li>
              <li>He lay on his armour-like back</li>
              <li> Gregor Samsa woke from troubled dreams</li>
            </ul>

            <p>To an English person, it will seem like simplified <a href="#">English</a>, as a skeptical Cambridge friend of mine told me what Occidental is. The European languages are members of the same family. Their separate existence is a myth. For science, music, sport, etc, Europe uses the same vocabulary.</p>
          </div> --}}
          <!-- post bottom section -->
          <div class="post-bottom">
            <div class="row d-flex align-items-center">
              <div class="col-md-7 col-12 text-center text-md-start">
                <!-- tags -->
                @if ($article->tags()->exists())
                  @foreach ($article->tags as $tag)
                    <a href="{{ route("tag_show", $tag->slug) }}" class="tag shadow bg-dark">#{{ $tag->title }}</a>
                  @endforeach
                @endif
              </div>
              <div class="col-md-5 col-12">
                <div class="about-author padding-30 rounded">
                  <div class="thumb">
                    <img load="lazy" src="{{ $article->user->image_url() }}" class="rounded-circle" alt="{{ $article->user->fullname() }}" />
                  </div>
                  <div class="details">
                    <h4 class="name"><a href="#">{{ $article->user->fullname() }}</a></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>

      <div class="spacer" data-height="50"></div>

      <!-- post comments -->
      <div class="comments bordered padding-30 rounded">

        <form id="comment-form" class="comment-form" method="post" action="{{ route("comment_article", $article->id) }}">

          <div class="messages"></div>

          <!-- section header -->
          <div class="section-header">
            <h3 class="section-title">{{ __("Comments") }}</h3>
            <img load="lazy" src="{{ url("images/wave.svg") }}" class="wave" alt="wave" />
          </div>
          <div class="row">

            <div class="column col-md-12">
              <!-- Comment textarea -->
              <div class="form-group">
                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="{{ __("leave a comment") }}..." required>{{ old("comment") }}</textarea>
                <div class="invalid-feedback"></div>
              </div>
            </div>
            @if(!auth()->check())
              <p class="dashed-note">{{ __("you need to register or login to comment") }}</p>
              <div class="column col-md-4">
                <div class="form-group">
                  <input type="text" class="form-control" name="username" id="username" placeholder="username" value="{{ old("username") }}" required>
                  <div class="invalid-feedback"></div>
                </div>
              </div>
              <div class="column col-md-4">
                <div class="form-group">
                  <input type="password" class="form-control" name="password" id="password" placeholder="password" required>
                  <div class="invalid-feedback"></div>
                </div>
              </div>
              <div class="column col-md-4">
                <div class="form-group">
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="{{ old("email") }}" required>
                  <div class="invalid-feedback"></div>
                </div>
              </div>
            @endif

          </div>

          <button type="submit" name="submit" id="submit" value="Submit" class="btn btn-default">{{ __("Submit") }}</button><!-- Submit Button -->

        </form>

        <hr class="my-5">

        <ul class="comments" id="comments-container">
          @foreach($comments as $comment)
            <!-- comment item -->
            <li class="comment rounded">
              <div class="thumb">
                <img load="lazy" class="rounded-circle" style="max-width: 80px;" src="{{ $comment->user->image_url() }}" alt="{{ $comment->user->fullname() }}" />
              </div>
              <div class="details">
                <h4 class="name"><a href="#">{{ $comment->user->fullname() }}</a></h4>
                <span class="date">{{ $comment->created_at->format("Y-m-d h:ma") }}</span>
                <p>{{ $comment->text }}</p>
                <button id="reply-{{ $comment->id }}-btn" data-id="{{ $comment->id }}" class="comment-reply-btn btn btn-default btn-sm">{{ __("Reply") }}</button>
              </div>
            </li>
          @endforeach
        </ul>
      </div>

    </div>
  </section>

@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script src="{{ url("js/jquery.min.js") }}"></script>
  <script src="{{ url("js/popper.min.js") }}"></script>
  <script src="{{ url("js/bootstrap.min.js") }}"></script>
  <script src="{{ url("js/slick.min.js") }}"></script>
  <script src="{{ url("js/jquery.sticky-sidebar.min.js") }}"></script>
  <script src="{{ url("js/custom.js") }}"></script>
  <script>

    $("#comment-form").on("submit", function (e) {
      e.preventDefault();

      let data = {
        "comment": $("#comment").val()
      }
      // if (typeof $("#comment").data("reply") != 'undefined') {
      //   data.reply = $("#comment").data("reply")
      // }
      if (typeof $("#username") != 'undefined') {
        data.username = $("#username").val()
      }
      if (typeof $("#email") != 'undefined') {
        data.email = $("#email").val()
      }
      if (typeof $("#password") != 'undefined') {
        data.password = $("#password").val()
      }

      $.ajax('{{ route("comment_article", $article->id) }}', {
        method: "POST",
        data: data,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (res) {
          console.log(res)
          if (res.done) {
            insertComment($("#comment").val(), res.comment)
          }
        },
        error: function (err) {
          Object.keys(err.responseJSON.errors).forEach(function (key) {
            $("#" + key).addClass("is-invalid")
            $("#" + key + " + .invalid-feedback").text(err.responseJSON.errors[key])
          })
        }
      })
    })

    $(".comment-reply-btn").on("click", function () {
      console.log($(this).data("id"))
      $("#comment").data("reply", $(this).data("id"))
    });

    function insertComment(text, data) {
      $("#comments-container").prepend(`
        <li class="comment rounded">
          <div class="thumb">
            <img load="lazy" class="rounded-circle" style="max-width: 80px;" src="${ data.user_image }" alt="${ data.fullname }" />
          </div>
          <div class="details">
            <h4 class="name">${ data.fullname }</h4>
            <span class="date">${ data.date }</span>
            <p>${ text }</p>
            <button id="reply-${ data.id }-btn" data-id="${ data.id }" class="comment-reply-btn btn btn-default btn-sm">{{ __("Reply") }}</button>
          </div>
        </li>
      `);
    }

  </script>
@endsection
