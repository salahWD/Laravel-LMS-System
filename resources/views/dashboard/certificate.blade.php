@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('Edit Certificate') @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite("resources/css/dashboard/style.css")
@endsection

@section('content')

  <div class="container mt-4 certificate">
    <div id="alerts" class="toast-container position-fixed p-3 d-flex flex-column gap-1"></div>
    <form method="POST" action="{{ isset($certificate) ? route("certificate_edit", $certificate->id) : route("certificate_create") }}">
      @csrf
      <div class="row">
        <div class="col-12 col-xl-7">
          <div class="card card-body border-0 shadow mb-4">
            <h2 class="h5 mb-4">{{ __('Certificate information') }}</h2>
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
                value="{{ old("title") ?? $certificate->title ?? "" }}"
                placeholder="{{ __('title of the certificate') }}">
              @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label for="description">{{ __('description') }}</label>
              <textarea
                class="form-control @error('description') is-invalid @enderror"
                id="description"
                name="description"
                placeholder="{{ __('description of the certificate') }}">{{ old("description") ?? $certificate->description ?? "" }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label for="themes">{{ __("Themes") }}</label>
              <select class="form-select mb-0" name="theme" id="themes" aria-label="themes select">
                @if (count($themes) > 0)
                  @foreach ($themes as $theme)
                  <option value="{{ $theme }}" @if(isset($certificate) && $certificate->template == $theme) selected @endif>{{ $theme }}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="row align-items-center">
              <div class="col-sm-6 mb-3">
                <label for="price">{{ __('Price') }}</label>
                <input
                  class="form-control @error('price') is-invalid @enderror"
                  id="price"
                  name="price"
                  type="number"
                  min="0"
                  value="{{ old("price") ?? (isset($certificate) ? $certificate->price : "") }}"
                  placeholder="{{ __('price of the certificate') }}">
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-sm-6 mb-3">
                  <label for="status">{{ __("Status") }}</label>
                  <select class="form-select mb-0" name="status" id="status" aria-label="status select">
                      <option value="0" @if(isset($certificate) && $certificate->status != 1) selected @endif>{{ __("private") }}</option>
                      <option value="1" @if(isset($certificate) && $certificate->status == 1) selected @endif>{{ __("public") }}</option>
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
                  {{-- <img src="https://placehold.it/400" id="themesPreview" class="rounded mx-auto m-0"> --}}
                  <iframe class="certificate-preview" id="themesPreview" src="{{ isset($certificate) ? route("certificate_show", $certificate->id) : route("certificate_theme_show", "default") }}#toolbar=0&navpanes=0&scrollbar=0" frameborder="0"></iframe>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>

  </div>

@endsection

{{-- @section('jslibs')
  @if(Session::has('certificate-saved') && Session::get('certificate-saved'))
    <script src="{{ url("libs/dashboard/sweetalert2.all.min.js") }}"></script>
  @endif
@endsection --}}

@section('scripts')
  <script>
    // const themesArr = JSON.parse('{!! json_encode($themes) !!}')

    // const alertsTimeOut = 2000
    themes.addEventListener("change", function (e) {
      themesPreview.src = `{{ url(app()->getLocale() . "/certificate/theme") }}/${themes.value}#toolbar=0&navpanes=0&scrollbar=0`
    })

    // function successAlert(text) {
    //   let id = Date.now();
    //   let toast = `
    //     <div id="${id}" class="mb-2 toast align-items-center text-white bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    //       <div class="d-flex">
    //         <div class="toast-body">
    //           <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
    //           ${text}
    //           </div>
    //         <button type="button" onclick="$('#liveToast').fadeOut(450)" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    //       </div>
    //     </div>`
    //   $("#alerts").append(toast);
    //   let alert = $("#" + id);
    //   alert.fadeIn(350, function () {
    //     setTimeout(() => {
    //       alert.slideUp(350, () => {alert.remove()})
    //     }, alertsTimeOut);
    //   })
    // }

    // @if(Session::has('certificate-saved') && Session::get('certificate-saved'))
    //   Swal.fire({
    //     title: "Certificate Saved Successfully",
    //     customClass: {
    //       confirmButton: 'btn btn-success me-4',
    //     },
    //     buttonsStyling: false,
    //     inputAttributes: {
    //       autocapitalize: "off"
    //     },
    //     showCancelButton: false,
    //     confirmButtonText: "ok",
    //     showLoaderOnConfirm: false,
    //   })
    // @endif
  </script>
@endsection
