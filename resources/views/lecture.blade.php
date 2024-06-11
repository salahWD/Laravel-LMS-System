@extends('layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __(config("app.name")) . " | " . $current_lecture->title }}</title>
@endsection

@section('styles')
  @vite('resources/css/courses.css')
  @vite('resources/css/video-player.css')
@endsection

@section('content')

<!-- section main content -->
<section class="main-content mt-5 lecture-page">
  <div class="container">
    <div class="row">
      <sidebar class="sidebar col-md-3">
        <h2 class="m-auto">{{ __('Course content') }}</h2>
        {{-- ===========  Sections Feature in the future  =========== --}}
        {{-- <div class="accordion" id="lecturesAccordion">
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                Course Section Title
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#lecturesAccordion">
              <div class="accordion-body">
                <strong>Section details should be here.</strong> conseder these are the section details <code>.some code to show off</code>, and to the end we go.
              </div>
            </div>
          </div>
        </div> --}}
        <ul class="list-group mt-4">
          @if($all_items)
            @foreach($all_items as $i => $item)
              <li class="list-group-item @if($item->is_current) list-group-item-primary @endif">
                @if ($item->is_open)
                  <a href="@if ($item->is_lecture())
                    {{ route("lecture_show", $item->itemable_id) }}
                  @else
                    {{ route("test_show", $item->itemable_id) }}
                  @endif">
                @endif
                <div class="d-flex w-100 @if ($item->is_lecture() && $item->duration != null) justify-content-between @else justify-content-start @endif align-items-center">
                  <span class="badge @if($item->is_last_watched || ($item->is_current && $item->order == 1)) bg-primary @elseif($item->is_open) bg-success @else bg-dark @endif p-2 py-1 me-2">
                    @if($item->is_open || $item->is_current)
                      {{ $i + 1 }}
                    @else
                      <i class="fa fa-sm fa-lock"></i>
                    @endif
                  </span>
                  {{ $item->itemable->title }}
                  @if ($item->itemable->duration != null)
                    <span class="badge bg-dark d-inline-block ms-auto">{{ $item->itemable->duration }}</span>
                  @endif
                </div>
                @if ($item->is_open)
                  </a>
                @endif
              </li>
            @endforeach
          @endif
        </ul>
      </sidebar>
      <section class="lecture col-md-9">

        @if ($current_lecture->is_yt_video())
          <div class="ratio ratio-16x9" id="videoPreviewHolder" data-video="{{ $current_lecture->video_url() }}">
            <div id="videoPlayer"></div>
            {{-- <iframe
            src="{{ $current_lecture->video_url() }}"
            title="YouTube video player"
            frameborder="0"
            id="videoPlayer"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen></iframe> --}}
          </div>
        @else
          <div id="video-holder" class="video stop" style="direction: ltr">
              <div class="ratio ratio-16x9" id="videoPreviewHolder">
                <video id="video">
                  <source src="{{ $current_lecture->video_url() }}" type="video/mp4">
                  Your browser does not support the video tag.
                </video>
              </div>


            <div class="bg"></div>
            <div class="controls">
              <div id="line" class="watch-line">
                <div id="watched-thumb" class="watched-thumb"></div>
                <div class="line-holder">
                  <div class="line"></div>
                  <div id="loaded-line" class="loaded-line"></div>
                  <div id="hovered-line" class="hovered-line"></div>
                  <div id="watched-line" class="watched-line"></div>
                </div>
              </div>
              <div class="options">
                <div class="actions">
                  <div id="start-stop" class="start-stop option">
                    <i class="fa fa-solid fa-play"></i>
                  </div>
                  <div id="volume" class="volume low option">
                    <i id="mute-control" class="fa fa-volume-up"></i>
                    <div class="input">
                      <input min="0" value="50" max="100" type="range" id="volume-level">
                      <div class="unactive-area"></div>
                      <div id="input-thumb" class="thumb"></div>
                      <span id="input-area" class="active-area"></span>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-1 time-stamp ms-3">
                    <span id="current-time"></span> /
                    <span id="time-stamp"></span>
                  </div>
                </div>
                <div class="info">
                  <!-- <div class="setting option">
                    <i class="fa fa-solid fa-gear"></i>
                  </div> -->
                  <div id="screen-btn" class="full-screen option">
                    <i class="fa fa-solid fa-expand"></i>
                  </div>
                </div>
              </div>
            </div>
            <span class="card forward">
              <i class="fa fa-solid fa-forward"></i>
            </span>
            <span class="card backward">
              <i class="fa fa-solid fa-backward"></i>
            </span>
            <div class="card stop">
              <i class="fa fa-solid fa-play"></i>
            </div>
            <div class="card start">
              <i class="fa fa-solid fa-pause"></i>
            </div>
          </div>
        @endif

        <div class="text-center">
          @if(!empty($current_lecture->next_item()))
            @if($current_lecture->next_item()->is_lecture())
              <a id="next-lecture" href="{{ route("lecture_show", $current_lecture->next_item()->itemable_id) }}" class="btn btn-lg border btn-outline-success mt-4 m-auto">{{ __("next lecture") }}</a>
            @else
              <a id="next-lecture" href="{{ route("test_show", $current_lecture->next_item()->itemable_id) }}" class="btn btn-lg border btn-outline-success mt-4 m-auto">{{ __("take a test") }}</a>
            @endif
          @else
            <a id="next-lecture" href="{{ route("courses_show") }}" class="btn btn-lg border btn-outline-success mt-4 m-auto">{{ __("congratulations, you have finished the course") }}</a>
          @endif
        </div>

      </section>
    </div>

  </div>
</section>

@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script>
    const doneUrl = `{{ route("lecture_done_ajax", $current_lecture->id) }}`;
    const playerNeeded = {{ !$current_lecture->is_yt_video() ? "true" : "false" }};
  </script>
  <script src="{{ url("js/jquery.min.js") }}"></script>
  <script src="{{ url("js/popper.min.js") }}"></script>
  <script src="{{ url("js/bootstrap.min.js") }}"></script>
  <script src="{{ url("js/slick.min.js") }}"></script>
  <script src="{{ url("js/jquery.sticky-sidebar.min.js") }}"></script>
  <script src="{{ url("js/custom.js") }}"></script>
  <script src="{{ url("js/video-player.js") }}"></script>

  @if ($current_lecture->is_yt_video())
  <script src="{{ url("js/yt-player.js") }}"></script>
  @endif
@endsection
