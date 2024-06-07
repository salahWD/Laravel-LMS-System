@extends('layout')

@section('meta')
  <title>{{ __(config("app.name")) . " | " . __("Contact Us") }}</title>
@endsection

@section('styles')
  @vite([
    'resources/csslibs/style.css',
    'resources/css/contact.css',
  ])
@endsection

@section('content')

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 text-center my-5">
        @if (auth()->check())
          <h2 class="heading-section">{{ __('Welcome, :name', ['name' => auth()->user()->fullname()]) }}</h2>
        @else
          <h2 class="heading-section">{{ __('Welcome') }}</h2>
        @endif
      </div>
    </div>
    <div class="position-relative">
      @if(session()->has('message-sent') && session()->get('message-sent'))
        <div id="liveToast" class="toast align-items-center text-white bg-primary border-0 position-absolute" role="alert" aria-live="assertive" aria-atomic="true" style="top: -120px;{{ app()->getLocale() == "ar" ? "left": "right"}}: 0;">
          <div class="d-flex">
            <div class="toast-body">
              {{ __("Message sent seccessfully")}}.
            </div>
            <button type="button" onclick="$('#liveToast').fadeOut(450)" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      @endif
    </div>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="wrapper">
          <div class="row no-gutters">
            <div class="col-md-6">
              <div class="contact-wrap w-100 p-lg-5 p-4">
                <h3 class="mb-4">{{ __('Send us a message') }}</h3>
                <div id="form-message-warning" class="mb-4"></div>
                <div id="form-message-success" class="mb-4">
                  {{ __('Your message was sent, thank you!') }}
                </div>
                @if ($errors->any())
                  <div class="alert alert-danger rounded">
                    <ul>
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif
                <form method="POST" action="{{ route("contact_us") }}" id="contactForm" name="contactForm" class="contactForm">
                  <input type="hidden" id="g-recaptcha-check" name="g-recaptcha-check" />
                  @csrf
                  <div class="row">
                    @if (!auth()->check())
                      <div class="col-md-12">
                        <div class="form-group">
                          <input type="text" class="form-control @error('name') is-invalid @endif" name="name" id="name" value="{{ old('name') }}" placeholder="{{ __("Name") }}">
                          @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="form-group">
                          <input type="email" class="form-control @error('email') is-invalid @endif" name="email" id="email" value="{{ old('email') }}" placeholder="{{ __("Email") }}">
                          @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                      </div>
                    @endif
                    <div class="col-md-12">
                      <div class="form-group">
                        <input type="text" class="form-control @error('subject') is-invalid @endif" name="subject" id="subject" value="{{ old('subject') }}" placeholder="{{ __("Subject") }}">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <textarea name="message" class="form-control @error('message') is-invalid @endif" id="message" cols="30" rows="6" placeholder="{{ __("Message") }}">{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <button type="button" onclick="onClick(event)" class="@error('g-recaptcha-check') is-invalid @endif btn btn-primary">{{ __("Send Message") }}</button>
                        <div class="submitting"></div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
            </div>
            <div class="col-md-6 d-flex align-items-stretch">
              <div class="info-wrap w-100 p-lg-5 p-4 img">
                <h3>{{ __("Contact us") }}</h3>
                <p class="mb-4">{{ __("We're open for any suggestion or just to have a chat") }}</p>
                <div class="dbox w-100 d-flex align-items-start">
                  <div class="icon d-flex align-items-center justify-content-center">
                    <span class="fa fa-map-marker"></span>
                  </div>
                  <div class="text pl-3">
                    <p><span>{{ __("Address") }}:</span> address should / be here</p>
                  </div>
                </div>
                <div class="dbox w-100 d-flex align-items-center">
                  <div class="icon d-flex align-items-center justify-content-center">
                    <span class="fa fa-phone"></span>
                  </div>
                  <div class="text pl-3">
                    <p><span>{{ __("Phone") }}:</span> <a href="tel://1234567920">+ 1235 2355 98</a></p>
                  </div>
                </div>
                <div class="dbox w-100 d-flex align-items-center">
                  <div class="icon d-flex align-items-center justify-content-center">
                    <span class="fa fa-paper-plane"></span>
                  </div>
                  <div class="text pl-3">
                    <p><span>{{ __("Email") }}:</span> <a href="mailto:info@yoursite.com">info@yoursite.com</a></p>
                  </div>
                </div>
                <div class="dbox w-100 d-flex align-items-center">
                  <div class="icon d-flex align-items-center justify-content-center">
                    <span class="fa fa-globe"></span>
                  </div>
                  <div class="text pl-3">
                    <p><span>{{ __("Website") }}</span> <a href="#">yoursite.com</a></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script src="https://www.google.com/recaptcha/api.js?render={{ config("services.recaptcha.site_key") }}"></script>
  <script src="{{ url("js/jquery.min.js") }}"></script>
  <script src="{{ url("js/popper.min.js") }}"></script>
  <script src="{{ url("js/bootstrap.min.js") }}"></script>
  <script src="{{ url("js/slick.min.js") }}"></script>
  <script src="{{ url("js/jquery.sticky-sidebar.min.js") }}"></script>
  <script src="{{ url("js/custom.js") }}"></script>
  <script>
    function onClick(e) {
        e.preventDefault();
        grecaptcha.ready(function() {
          grecaptcha.execute('{{ config("services.recaptcha.site_key") }}', {action: 'submit'}).then(function(token) {
            document.getElementById("g-recaptcha-check").value = token;
            document.getElementById("contactForm").submit();
          });
        });
      }
  </script>
  @if(session()->has('message-sent') && session()->get('message-sent'))
    @php session()->forget('message-sent') @endphp
    <script>
      $(window).ready(function () {
        $("#liveToast").fadeIn(450, function () {
          setTimeout(() => {
            $("#liveToast").fadeOut(450, function () {
              $("#liveToast").remove()
            })
          }, 2500);
        })
      });
    </script>
  @endif
@endsection
