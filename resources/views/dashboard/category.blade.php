@extends('dashboard.layout')

@section('meta')
    @php $page_title = config("app.name") . " | " . __('Edit Category') @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
    @vite('resources/css/dashboard/style.css')
@endsection

@section('content')
    <div class="container mt-4">
        <form method="POST"
            action="{{ isset($category->id) ? route('category_edit', $category->id) : route('category_create') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-12 col-xl-7">
                    <div class="card card-body border-0 shadow mb-4">
                        <h2 class="h5 mb-4">{{ __('Category information') }}</h2>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <!-- Tab -->
                        <nav>
                            <div class="nav nav-tabs mb-4" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="ar-content-tab" data-bs-toggle="tab"
                                    href="#ar-content" role="tab" aria-controls="ar-content"
                                    aria-selected="true">{{ __('arabic') }}</a>
                                <a class="nav-item nav-link" id="en-content-tab" data-bs-toggle="tab" href="#en-content"
                                    role="tab" aria-controls="en-content" aria-selected="false">{{ __('english') }}</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="ar-content" role="tabpanel"
                                aria-labelledby="ar-content-tab">
                                <div class="mb-3">
                                    <label for="title">{{ __('Title') }}</label>
                                    <input class="form-control @error('title') is-invalid @enderror" id="title"
                                        name="title" type="text" value="{{ old('title') ?? $category->title }}"
                                        placeholder="{{ __('title of the category') }}">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="desc">{{ __('Description') }}</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="desc" style="min-height: 80px;"
                                        name="description" placeholder="{{ __('a brief description about the category') }}">{{ old('description') ?? $category->description }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="tab-pane fade" id="en-content" role="tabpanel" aria-labelledby="en-content-tab">
                                <div class="mb-3">
                                    <label for="title_en">{{ __('Title') }} {{ __('(en)') }}</label>
                                    <input class="form-control @error('title_en') is-invalid @enderror" id="title_en"
                                        name="title_en" type="text"
                                        value="{{ old('title_en') ?? $category->translateOrNew('en')->title }}"
                                        placeholder="{{ __('title of the category') }}">
                                    @error('title_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="desc_en">{{ __('Description') }} {{ __('(en)') }}</label>
                                    <textarea class="form-control @error('description_en') is-invalid @enderror" id="desc_en" style="min-height: 80px;"
                                        name="description_en" placeholder="{{ __('a brief description about the category') }}">{{ old('description_en') ?? $category->translateOrNew('en')->description }}</textarea>
                                    @error('description_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <!-- End of tab -->
                        @if (isset($no_order))
                            {{-- just to use else on isset --}}
                        @else
                            <div class="mb-3">
                                <label for="order">{{ __('Order') }}</label>
                                <input class="form-control @error('order') is-invalid @enderror" id="order"
                                    name="order" type="number" min="0" max="5"
                                    value="{{ old('order') ?? $category->order }}"
                                    placeholder="{{ __('order of the category') }}">
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <hr>
                        @endif
                        <button class="btn btn-gray-800 mt-2 animate-up-2" type="submit">{{ __('Save all') }}</button>
                    </div>
                </div>
                <div class="col-12 col-xl-5">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card shadow border-0 text-center p-0">
                                <div class="card-body pb-5">
                                    <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('max size 2MB') }}</p>
                                    <img src="{{ $category->image_url() }}" id="categoryImagePreview"
                                        class="rounded mx-auto m-0 category-thumbnail" alt="{{ __('category Image') }}">
                                    <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('recommended size is') }}
                                        <bdi>1200 x 628</bdi>
                                    </p>
                                    <hr>
                                    <input type="file" class="d-none" name="image" id="categoryImage">
                                    <button type="button" onclick="document.getElementById('categoryImage').click()"
                                        class="btn btn-sm btn-warning d-inline-flex mx-3 align-items-center">
                                        <svg class="w-22px" data-slot="icon" fill="none" stroke-width="1.5"
                                            stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z">
                                            </path>
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
    @if (Session::has('category-saved') && Session::get('category-saved'))
        <script src="{{ url('libs/dashboard/sweetalert2.all.min.js') }}"></script>
    @endif
@endsection

@section('scripts')
    <script>
        categoryImage.addEventListener("change", function(e) {
            const [file] = this.files
            if (file) {
                categoryImagePreview.src = URL.createObjectURL(file)
            }
        })
        @if (Session::has('category-saved') && Session::get('category-saved'))
            Swal.fire({
                title: "Category Saved Successfully",
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
