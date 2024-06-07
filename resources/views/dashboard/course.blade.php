@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('Edit Course') @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite("resources/css/dashboard/style.css")
@endsection

@section('content')

  <div class="container mt-4">
    <div id="alerts" class="toast-container position-fixed p-3 d-flex flex-column gap-1"></div>
    <form method="POST" action="{{ isset($course->id) ? route("course_edit", $course->id) : route("course_create") }}" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-12 col-xl-7">
          <div class="card card-body border-0 shadow mb-4">
            <h2 class="h5 mb-4">{{ __('Course information') }}</h2>
            @if ($errors->any())
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            <div class="mb-3">
              <label for="title">{{ __('Title') }}</label>
              <input
                class="form-control @error('title') is-invalid @enderror"
                id="title"
                name="title"
                type="text"
                value="{{ old("title") ?? $course->title }}"
                placeholder="{{ __('title of the course') }}">
              @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label for="desc">{{ __('Description') }}</label>
              <textarea
                class="form-control @error('description') is-invalid @enderror"
                id="desc"
                style="min-height: 80px;"
                name="description"
                placeholder="{{ __('a brief description about the course') }}">{{ old("description") ?? $course->description }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            @if ($course->id == null)
              <div class="mb-3">
                <label for="yt-line">{{ __('Youtube Link') }}</label>
                <input
                  class="form-control @error('ytlink') is-invalid @enderror"
                  id="yt-line"
                  name="ytlink"
                  type="url"
                  value="{{ old("ytlink") }}"
                  placeholder="{{ __('playlist link to import it as a course') }}">
                @error('ytlink')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            @endif
            <div class="row align-items-center">
              <div class="col-md-4 col-sm-6 mb-3">
                <label for="price">{{ __('Price') }}</label>
                <input
                  class="form-control @error('price') is-invalid @enderror"
                  id="price"
                  name="price"
                  type="number"
                  min="0"
                  value="{{ old("price") ?? $course->price }}"
                  placeholder="{{ __('price of the course') }}">
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4 col-sm-6 mb-3">
                <label for="order">{{ __('Order') }}</label>
                <input
                  class="form-control @error('order') is-invalid @enderror"
                  id="order"
                  name="order"
                  type="number"
                  min="0"
                  max="5"
                  value="{{ old("order") ?? $course->order }}"
                  placeholder="{{ __('order of the course') }}">
                @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4 col-sm-6 mb-3">
                  <label for="status">Status</label>
                  <select class="form-select mb-0" name="status" id="status" aria-label="status select">
                      <option value="0" @if($course->status != 1) selected @endif>private</option>
                      <option value="1" @if($course->status == 1) selected @endif>public</option>
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
                  <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('max size 2MB') }}</p>
                  <img src="{{ $course->image_url() }}" id="courseImagePreview" class="rounded mx-auto m-0 course-thumbnail" alt="{{ __('course Image') }}">
                  <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('recommended size is 1200 x 628') }}</p>
                  <hr>
                  <input type="file" class="d-none" name="image" id="courseImage">
                  <button type="button" onclick="document.getElementById('courseImage').click()" class="btn btn-sm btn-warning d-inline-flex mx-3 align-items-center">
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

    @if ($course->id != null)
      <hr>
      <div class="card card-body border-0 shadow mb-4" id="lectures">
        <h2 class="dashed-title">
          {{ __("lectures") }}
        </h2>
        @if ($course->has_items())
          <div class="dropdown mb-4">
            <button data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
              <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
              {{ __('New Item') }}
            </button>
            <div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1" style="">
              <a class="dropdown-item d-flex align-items-center" href="{{ route("lecture_create_for", $course->id) }}">
                <svg class="dropdown-icon text-gray-400 me-2" data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <path clip-rule="evenodd" fill-rule="evenodd" d="M1.5 5.625c0-1.036.84-1.875 1.875-1.875h17.25c1.035 0 1.875.84 1.875 1.875v12.75c0 1.035-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 0 1 1.5 18.375V5.625Zm1.5 0v1.5c0 .207.168.375.375.375h1.5a.375.375 0 0 0 .375-.375v-1.5a.375.375 0 0 0-.375-.375h-1.5A.375.375 0 0 0 3 5.625Zm16.125-.375a.375.375 0 0 0-.375.375v1.5c0 .207.168.375.375.375h1.5A.375.375 0 0 0 21 7.125v-1.5a.375.375 0 0 0-.375-.375h-1.5ZM21 9.375A.375.375 0 0 0 20.625 9h-1.5a.375.375 0 0 0-.375.375v1.5c0 .207.168.375.375.375h1.5a.375.375 0 0 0 .375-.375v-1.5Zm0 3.75a.375.375 0 0 0-.375-.375h-1.5a.375.375 0 0 0-.375.375v1.5c0 .207.168.375.375.375h1.5a.375.375 0 0 0 .375-.375v-1.5Zm0 3.75a.375.375 0 0 0-.375-.375h-1.5a.375.375 0 0 0-.375.375v1.5c0 .207.168.375.375.375h1.5a.375.375 0 0 0 .375-.375v-1.5ZM4.875 18.75a.375.375 0 0 0 .375-.375v-1.5a.375.375 0 0 0-.375-.375h-1.5a.375.375 0 0 0-.375.375v1.5c0 .207.168.375.375.375h1.5ZM3.375 15h1.5a.375.375 0 0 0 .375-.375v-1.5a.375.375 0 0 0-.375-.375h-1.5a.375.375 0 0 0-.375.375v1.5c0 .207.168.375.375.375Zm0-3.75h1.5a.375.375 0 0 0 .375-.375v-1.5A.375.375 0 0 0 4.875 9h-1.5A.375.375 0 0 0 3 9.375v1.5c0 .207.168.375.375.375Zm4.125 0a.75.75 0 0 0 0 1.5h9a.75.75 0 0 0 0-1.5h-9Z"></path>
                </svg>
                {{ __("Lecture") }}
              </a>
              <div role="separator" class="dropdown-divider my-1"></div>
              <a href="{{ route("course_create_test", $course->id) }}" class="dropdown-item d-flex align-items-center">
                <svg class="dropdown-icon text-danger me-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path></svg>
                {{ __('Test') }}
              </a>
            </div>
          </div>
          {{-- <a href="{{ route("lecture_create_for", $course->id) }}" class="btn btn-primary me-auto mb-3">
            {{ __("add a lecture") }}
            <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
          </a> --}}
          <div class="row" id="sortableLectures" dir=ltr>
            @php $testCount = 0; @endphp
            @foreach($course->items_with_data as $item)
              @if ($item->is_lecture())
                <div class="col-md-4 mb-3">
                  <div data-id="{{ $item->id }}" data-order="{{ $item->order }}" class="lecture card bg-dark text-white" dir="{{ app()->getLocale() == "ar" ? "rtl": "ltr" }}">
                    <a href="{{ route("lecture_edit", $item->itemable->id) }}">
                      <img class="card-img lecture-image" src="{{ $item->itemable->image_url() }}" alt="{{ $item->itemable->title }}">
                    </a>
                    <div class="card-img-overlay lecture-overlay">
                      <div class="badge badge-lg bg-secondary order-badge">{{ $item->order - $testCount }}</div>
                      <a href="{{ route("lecture_edit", $item->itemable->id) }}">
                        <h5 class="card-title m-0">{{ $item->itemable->title }}</h5>
                      </a>
                      <small class="card-text text-sm">{{ $item->itemable->duration() }}</small>
                      <span class="lecture-handler d-block btn btn-primary">
                        <svg class="d-block icon icon-xs" data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path clip-rule="evenodd" fill-rule="evenodd" d="M12 5.25c1.213 0 2.415.046 3.605.135a3.256 3.256 0 0 1 3.01 3.01c.044.583.077 1.17.1 1.759L17.03 8.47a.75.75 0 1 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 0 0-1.06-1.06l-1.752 1.751c-.023-.65-.06-1.296-.108-1.939a4.756 4.756 0 0 0-4.392-4.392 49.422 49.422 0 0 0-7.436 0A4.756 4.756 0 0 0 3.89 8.282c-.017.224-.033.447-.046.672a.75.75 0 1 0 1.497.092c.013-.217.028-.434.044-.651a3.256 3.256 0 0 1 3.01-3.01c1.19-.09 2.392-.135 3.605-.135Zm-6.97 6.22a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.752-1.751c.023.65.06 1.296.108 1.939a4.756 4.756 0 0 0 4.392 4.392 49.413 49.413 0 0 0 7.436 0 4.756 4.756 0 0 0 4.392-4.392c.017-.223.032-.447.046-.672a.75.75 0 0 0-1.497-.092c-.013.217-.028.434-.044.651a3.256 3.256 0 0 1-3.01 3.01 47.953 47.953 0 0 1-7.21 0 3.256 3.256 0 0 1-3.01-3.01 47.759 47.759 0 0 1-.1-1.759L6.97 15.53a.75.75 0 0 0 1.06-1.06l-3-3Z"></path>
                        </svg>
                      </span>
                    </div>
                  </div>

                </div>
              @elseif ($item->is_test())
                @php $testCount++; @endphp

                <div class="col-md-4 mb-3">
                  <div data-id="{{ $item->id }}" data-order="{{ $item->order }}" class="test lecture card bg-dark text-white" dir="{{ app()->getLocale() == "ar" ? "rtl": "ltr" }}">
                    <a href="{{ route("test_build", $item->itemable->id) }}">
                      <img class="card-img lecture-image" src="{{ $item->itemable->image_url() }}" alt="{{ $item->itemable->title }}">
                    </a>
                    <div class="card-img-overlay lecture-overlay">
                      <div class="badge badge-lg bg-info order-badge">{{ $testCount }}</div>
                      <a href="{{ route("test_build", $item->itemable->id) }}">
                        <h5 class="card-title m-0">{{ $item->itemable->title }}</h5>
                      </a>
                      <span class="lecture-handler d-block btn btn-primary">
                        <svg class="d-block icon icon-xs" data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path clip-rule="evenodd" fill-rule="evenodd" d="M12 5.25c1.213 0 2.415.046 3.605.135a3.256 3.256 0 0 1 3.01 3.01c.044.583.077 1.17.1 1.759L17.03 8.47a.75.75 0 1 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 0 0-1.06-1.06l-1.752 1.751c-.023-.65-.06-1.296-.108-1.939a4.756 4.756 0 0 0-4.392-4.392 49.422 49.422 0 0 0-7.436 0A4.756 4.756 0 0 0 3.89 8.282c-.017.224-.033.447-.046.672a.75.75 0 1 0 1.497.092c.013-.217.028-.434.044-.651a3.256 3.256 0 0 1 3.01-3.01c1.19-.09 2.392-.135 3.605-.135Zm-6.97 6.22a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.752-1.751c.023.65.06 1.296.108 1.939a4.756 4.756 0 0 0 4.392 4.392 49.413 49.413 0 0 0 7.436 0 4.756 4.756 0 0 0 4.392-4.392c.017-.223.032-.447.046-.672a.75.75 0 0 0-1.497-.092c-.013.217-.028.434-.044.651a3.256 3.256 0 0 1-3.01 3.01 47.953 47.953 0 0 1-7.21 0 3.256 3.256 0 0 1-3.01-3.01 47.759 47.759 0 0 1-.1-1.759L6.97 15.53a.75.75 0 0 0 1.06-1.06l-3-3Z"></path>
                        </svg>
                      </span>
                    </div>
                  </div>

                </div>
              @endif
            @endforeach
          </div>
        @else
          <div class="alert alert-secondary mt-5" role="alert">
            <p class="lead m-0">{{ __('there are no lectures for this course') }}</p>
          </div>
          <a href="{{ route("lecture_create_for", $course->id) }}" class="btn btn-primary">
            {{ __("add a lecture") }}
            <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
          </a>
        @endif
      </div>
    @endif

  </div>

@endsection

@section('jslibs')
  <script src="{{ url("libs/dashboard/sortable.js") }}"></script>
  @if(Session::has('course-saved') && Session::get('course-saved'))
    <script src="{{ url("libs/dashboard/sweetalert2.all.min.js") }}"></script>
  @endif
@endsection

@section('scripts')
  <script>
    const alertsTimeOut = 2000
    courseImage.addEventListener("change", function (e) {
      const [file] = this.files
      if (file) {
        courseImagePreview.src = URL.createObjectURL(file)
      }
    })

    new Sortable(sortableLectures, {
      animation: 150,
      handle: '.lecture-handler',
      ghostClass: 'reordering',
      filter: '.filtered',
    });

    sortableLectures.addEventListener("drop", function (e) {

      if ($(e.srcElement).parents(".lecture").length > 0) {
        let orders = [];
        let testsCount = 0;
        $(sortableLectures).find(".lecture").each(function (i, item) {
          $(item).data("order", i + 1)
          if ($(item).hasClass("test")) {
            testsCount++;
          }
          $(item).find(".order-badge").text($(item).data("order") - testsCount)
          orders.push({"id": $(item).data('id'), "order": $(item).data('order')})
          $(item).addClass("filtered")
        })
        $(sortableLectures).find(".test").each(function (i, item) {
          $(item).find(".order-badge").text(i + 1)
        })
        reorderLectures(orders)
      }
    })

    function reorderLectures(orders) {
      $.ajax({
        method: "POST",
        url: '{{ route("courseitem_order") }}',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          "items": orders,
        },
        success: function(res) {
          if (res.result == 1) {
            successAlert('{{ __("Items Reordered Successfully") }}');
          }
        },
        complete: function() {
          $(sortableLectures).find(".lecture").each(function () {
            $(this).removeClass("filtered");
          })
        },
        error: function(res) {
          console.error(res);
        }
      });
    }

    function successAlert(text) {
      let id = Date.now();
      let toast = `
        <div id="${id}" class="mb-2 toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
              ${text}
              </div>
            <button type="button" onclick="$('#liveToast').fadeOut(450)" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>`
      $("#alerts").append(toast);
      let alert = $("#" + id);
      alert.fadeIn(350, function () {
        setTimeout(() => {
          alert.slideUp(350, () => {alert.remove()})
        }, alertsTimeOut);
      })
    }

    @if(Session::has('course-saved') && Session::get('course-saved'))
      Swal.fire({
        title: "Course Saved Successfully",
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
