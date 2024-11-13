@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . $tag->title @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite('resources/css/dashboard/style.css')
@endsection

@section('content')
  <div class="container mt-4">
    <form method="POST" action="{{ route('tag_edit', $tag->id) }}">
      @csrf
      <div class="row">
        <div class="col-12 col-xl-7">
          <div class="card card-body border-0 shadow mb-4">
            <h2 class="h5 mb-4">{{ __('Tag information') }}</h2>
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
              <input class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                type="text" value="{{ old('title') ?? $tag->title }}"
                placeholder="{{ __('how tag will be displayed') }}">
              @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="slug">{{ __('Slug') }}</label>
              <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" type="text"
                value="{{ old('slug') ?? $tag->slug }}" placeholder="{{ __('how the tag will appear in url') }}">
              @error('slug')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="desc">{{ __('Description') }}</label>
              <textarea class="form-control @error('description') is-invalid @enderror" id="desc" style="min-height: 80px;"
                name="description" placeholder="description won't be displayed to users">{{ old('description') ?? $tag->description }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <hr>
            <button class="btn btn-gray-800 mt-2 animate-up-2" type="submit">{{ __('Save all') }}</button>
          </div>
        </div>
        <div class="col-12 col-xl-5">
          <div class="row">
            <div class="col-12 mb-4">
              <div class="card shadow border-0 text-center p-0">
                <h5 class="card-header">Recent Articles For ({{ $tag->title }})</h5>
                <div class="card-body pb-5">
                  @foreach ($articles as $i => $article)
                    <a href="{{ route('article_edit', $article->id) }}">{{ $article->title }}</a>
                    @if ($i != $articles->count() - 1)
                      <hr>
                    @endif
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
@endsection

@section('scripts')
  @if (Session::has('tag-saved') && Session::get('tag-saved'))
    <script src="{{ url('libs/dashboard/sweetalert2.all.min.js') }}"></script>
  @endif
  <script>
    @if (Session::has('tag-saved') && Session::get('tag-saved'))
      Swal.fire({
        title: "Tag Saved Successfully",
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
