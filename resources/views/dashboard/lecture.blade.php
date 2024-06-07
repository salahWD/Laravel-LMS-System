@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . ($lecture->id ? __('Edit Lecture'): __('Create Lecture')) @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite("resources/css/dashboard/style.css")
@endsection

@section('content')

  <div class="container mt-4">
    <form method="POST"
      action="{{ isset($lecture->id) ? route("lecture_edit", $lecture->id) : route("lecture_create") }}"
      id="main_form"
      enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-12 col-xl-7">
          <div class="card card-body border-0 shadow mb-4">
            @if ((isset($lecture->id) && $lecture->is_yt_video()) || !isset($lecture->id))
              <label for="ytlink">
                <h2 class="h5 mb-4">{{ __('Youtube Video Import') }}</h2>
              </label>
              <div class="mb-3">
                <input
                    type="url"
                    class="form-control @error('ytlink') is-invalid @enderror"
                    id="ytlink"
                    name="ytlink"
                    data-ytid="{{ $lecture->yt_link_id() }}"
                    {{-- type="url" --}}
                    value="{{ old("ytlink") ?? $lecture->yt_link() }}"
                    placeholder="{{ __('Youtube Url') }}">
                @error('ytlink')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <hr>
            @endif
            <h2 class="h5 mb-4">{{ __('Lecture information') }}</h2>
            @if ($errors->any())
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <div class="tab-pane fade show active" id="ar-content" role="tabpanel" aria-labelledby="ar-content-tab">
              <div class="mb-3">
                <label for="title">{{ __('Title') }}</label>
                <input
                  class="form-control @error('title') is-invalid @enderror"
                  id="title"
                  name="title"
                  type="text"
                  value="{{ old("title") ?? $lecture?->title }}"
                  placeholder="{{ __('title of the lecture') }}">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label for="desc">{{ __('Description') }}</label>
                <textarea
                  class="form-control @error('description') is-invalid @enderror"
                  id="desc"
                  style="min-height: 80px;"
                  name="description"
                  placeholder="{{ __('description or any links used in the lecture') }}">{{ old("description") ?? $lecture?->description }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            @if (isset($courses) && count($courses) > 0)
              <div class="mb-3">
                <label for="course">{{ __('Course') }}</label>
                <select name="course" id="course" class="disabled form-select @error('course') is-invalid @enderror" @if(isset($selected_course)) disabled @endif>
                  <option value="">{{ __("-- select course --") }}</option>
                  @foreach ($courses as $course)
                    @if(isset($selected_course) && $selected_course->id == $course->id)
                      <option value="{{ $course->id }}" selected>{{ $course->title }}</option>
                    @else
                      <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endif
                  @endforeach
                </select>
                @error('course')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            @endif
            <div class="row align-items-center">
              {{-- <div class="col-md-6 mb-3">
                <label for="order">{{ __('Order') }}</label>
                <input
                  class="form-control @error('order') is-invalid @enderror"
                  id="order"
                  name="order"
                  type="number"
                  min="0"
                  max="5"
                  value="{{ old("order") ?? $lecture?->order }}"
                  placeholder="{{ __('order of the lecture') }}">
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div> --}}
              <div class="col-md-6 mb-3">
                  <label for="status">Status</label>
                  <select class="form-select mb-0" name="status" id="status" aria-label="status select">
                      <option value="0" @if($lecture->status != 1) selected @endif>private</option>
                      <option value="1" @if($lecture->status == 1) selected @endif>public</option>
                  </select>
              </div>
            </div>
            <hr>
            <button class="btn btn-gray-800 mt-2 animate-up-2" type="submit">{{ __('Save all') }}</button>
          </div>
        </div>
        <div class="col-12 col-xl-5">
          <div class="row">
            <div class="col-12 mb-4">
              <div class="card shadow border-0 text-center p-0">
                <div class="card-body pb-5">
                  <div class="ratio ratio-16x9" id="videoPreviewHolder">
                    <iframe
                      src="{{ $lecture->video_url() }}"
                      class="@if (!$lecture->is_yt_video()) d-none @endif"
                      id="YTpreview"
                      title="YouTube video player"
                      frameborder="0"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                      allowfullscreen></iframe>
                    <video
                      src="{{ $lecture->video_url() }}"
                      class="mw-100 border border-primary rounded shadow @if ($lecture->is_yt_video()) d-none @endif"
                      id="videoPreview"
                      controls="true"></video>
                  </div>
                  @if(!$lecture->is_yt_video() && $lecture->id == null)
                    <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('accepted formats') }}<bdi>: (.mp4)</bdi></p>
                  @endif
                  @if($lecture->id == null)
                    <hr>
                    <input type="file" class="d-none" name="video" id="lectureVideo">
                    <button type="button" onclick="document.getElementById('lectureVideo').click()" class="btn btn-sm btn-warning d-inline-flex mx-3 align-items-center">
                      <svg class="icon icon-xs" data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M4.5 4.5a3 3 0 0 0-3 3v9a3 3 0 0 0 3 3h8.25a3 3 0 0 0 3-3v-9a3 3 0 0 0-3-3H4.5ZM19.94 18.75l-2.69-2.69V7.94l2.69-2.69c.944-.945 2.56-.276 2.56 1.06v11.38c0 1.336-1.616 2.005-2.56 1.06Z"></path>
                      </svg>
                    </button>
                  @endif
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12 mb-4">
              <div class="card shadow border-0 text-center p-0">
                <div class="card-body pb-5">
                  <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('max size 2MB') }}</p>
                  <img src="{{ $lecture->image_url() }}" id="lectureImagePreview" class="rounded mx-auto m-0 lecture-thumbnail" alt="{{ __('Lecture Thumbnail') }}">
                  <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('recommended size is') }} <bdi>1200 x 628</bdi></p>
                  <hr>
                  <input type="file" class="d-none" name="image" id="lectureImage">
                  <button type="button" onclick="document.getElementById('lectureImage').click()" class="btn btn-sm btn-warning d-inline-flex mx-3 align-items-center">
                    <svg class="w-22px" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

@endsection

@section('jslibs')
  @if(Session::has('lecture-saved') && Session::get('lecture-saved'))
    <script src="{{ url("libs/dashboard/sweetalert2.all.min.js") }}"></script>
  @endif
@endsection

@section('scripts')
  <script>
    ytlink.addEventListener("input", function (e) {
      let link = $(this).val()
      let url
      try {
        url = new URL(link)
      } catch (error) {
        link = "https://" + link
        $(this).val(link)
        url = new URL("https://" + link)
      }
      let urlsearch = new URLSearchParams(url.search);
      let videoId = urlsearch.get("v");

      if (videoId.length == 11) {
        $.ajax({
          type: 'GET',
          url: `https://www.googleapis.com/youtube/v3/videos?part=id%2C+snippet&id=${videoId}&key={{ env("YT_API_KEY") }}`,
          crossDomain: true,
          processData: true,
          data: {},
          dataType: "jsonp",
          success: function (res) {
            $(title).val(res.items[0].snippet.title)
            $(desc).val(res.items[0].snippet.description);
            $(lectureImagePreview).attr("src", res.items[0].snippet.thumbnails.maxres.url)
            $(YTpreview).removeClass("d-none").attr("src", `https://www.youtube.com/embed/${videoId}`);
            $(videoPreview).addClass("d-none");
          }
        });
      }

    })
    lectureVideo.addEventListener("input", function (e) {
      const [file] = this.files
      if (file) {
        videoPreview.src = URL.createObjectURL(file)
      }
    })
    lectureImage.addEventListener("input", function (e) {
      const [file] = this.files
      if (file) {
        lectureImagePreview.src = URL.createObjectURL(file)
      }
    })
    $("#main_form").on("submit", function () {
      document.getElementById("course").removeAttribute("disabled")
    })
    @if(Session::has('lecture-saved') && Session::get('lecture-saved'))
      Swal.fire({
        title: "lecture Saved Successfully",
        customClass: {
          confirmButton: 'btn btn-success me-4',
        },
        buttonsStyling: false,
        inputAttributes: {
          autocapitalize: "off"
        },
        showCancelButton: false,
        confirmButtonText: "ok",
        showLoaderOnConfirm: false,
      })
    @endif
  </script>
@endsection
