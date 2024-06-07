@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('Reply to Message') @endphp
@endsection

@section('styles')
  @vite("resources/css/dashboard/style.css")
@endsection

@section('content')

  <div class="container mt-4">
    <form method="POST" action="{{ route("message_edit", $message->id) }}">
      @csrf
      <div class="row">
        <div class="col-12 col-xl-8">
          <div class="card card-body border-0 shadow mb-4">

            <h2 class="h5 mb-4">{{ __('Message Information') }}</h2>

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
              <p><b>{{ __('name') }}:</b> {{ $message->name }}</p>
              <p><b>{{ __('email') }}:</b> {{ $message->email }}</p>
              <p><b>{{ __('subject') }}:</b> {{ $message->subject }}</p>
              <b class="d-block mb-2">{{ __('message') }}:</b>
              <p> {{ $message->message }}</p>
              <hr>
              <label for="reply">{{ __('Reply') }}:</label>
              <textarea
                  class="form-control"
                  name="reply"
                  id="reply"
                  placeholder="{{ __('write your response') }}"
                  rows="3">{{ old('reply') }}</textarea>
            </div>


            <button class="btn btn-gray-800 mt-2 animate-up-2" type="submit">{{ __('Reply') }}</button>

          </div>
        </div>
        <div class="col-xl-4"></div>
      </div>
    </form>
  </div>

@endsection

@section('jslibs')
  @if(Session::has('message-saved') && Session::get('message-saved'))
    <script src="{{ url("libs/dashboard/sweetalert2.all.min.js") }}"></script>
  @endif
@endsection

@section('scripts')
  <script>
    @if(Session::has('message-saved') && Session::get('message-saved'))
      Swal.fire({
        title: "message Saved Successfully",
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
