@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('Edit Article') @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
  @vite("resources/css/dashboard/style.css")
  @vite("resources/css/editor.css")
@endsection

@section('content')

  <div class="container mt-4">
    <form id="form" method="POST" action="{{ isset($article->id) ? route("article_edit", $article->id) : route("article_create") }}" enctype="multipart/form-data">
      @csrf
      <div class="row">
        <div class="col-12 col-xl-7">
          <div class="card card-body border-0 shadow mb-4">
            <h2 class="h5 mb-4">{{ __('Article information') }}</h2>
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
                <a class="nav-item nav-link active" id="ar-content-tab" data-bs-toggle="tab" href="#ar-content" role="tab" aria-controls="ar-content" aria-selected="true">{{ __("arabic") }}</a>
                <a class="nav-item nav-link" id="en-content-tab" data-bs-toggle="tab" href="#en-content" role="tab" aria-controls="en-content" aria-selected="false">{{ __("english") }}</a>
              </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
              <div class="tab-pane fade show active" id="ar-content" role="tabpanel" aria-labelledby="ar-content-tab">
                <div class="mb-3">
                  <label for="title">{{ __('Title') }}</label>
                  <input
                    class="form-control @error('title') is-invalid @enderror"
                    id="title"
                    name="title"
                    type="text"
                    value="{{ old("title") ?? $article->title }}"
                    placeholder="{{ __('title of the article') }}">
                  @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                  <label for="desc">{{ __('Description') }}</label>
                  <textarea
                    class="form-control @error('description') is-invalid @enderror"
                    id="desc"
                    style="min-height: 80px;"
                    name="description"
                    placeholder="{{ __('a brief description about the article') }}">{{ old("description") ?? $article->description }}</textarea>
                  @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="tab-pane fade" id="en-content" role="tabpanel" aria-labelledby="en-content-tab">
                <div class="mb-3">
                  <label for="title_en">{{ __('Title') }} {{ __("(en)") }}</label>
                  <input
                    class="form-control @error('title_en') is-invalid @enderror"
                    id="title_en"
                    name="title_en"
                    type="text"
                    value="{{ old("title_en") ?? $article->translateOrNew('en')->title }}"
                    placeholder="{{ __('title of the article') }}">
                  @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                  <label for="desc_en">{{ __('Description') }} {{ __("(en)") }}</label>
                  <textarea
                    class="form-control @error('description_en') is-invalid @enderror"
                    id="desc_en"
                    style="min-height: 80px;"
                    name="description_en"
                    placeholder="{{ __('a brief description about the article') }}">{{ old("description_en") ?? $article->translateOrNew('en')->description }}</textarea>
                  @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
            <!-- End of tab -->
            <div class="mb-4">
              <label for="cat">{{ __('Category') }}</label>
              <select
              class="form-select @error('category') is-invalid @enderror" id="cat" name="category">
                <option value="">{{ __('-- select a category --') }}</option>
                @foreach ($categories as $category)
                <option @if($category->id == $article->category_id) selected @endif value="{{ $category->id }}">{{ $category->title }}</option>
                @endforeach
              </select>
              @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
              <label for="tags">{{ __('Tags') }}</label>
              <input type="text" class="d-none" id="tags" name="tags" value="{{ $article->tags_text(old("tags")) }}">
              <div id="tags-input" class="form-control p-0">
                <div class="tags p-2"></div>
                <textarea class="input" placeholder="{{ __("type tags separated with comma (,)") }}"></textarea>
              </div>
              @error('tags')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <div class="mb-3">
                    <span class="h6 fw-bold">{{ __('Comment Section:') }}</span>
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" name="comment_status" type="checkbox" @if($article->comment_status == 2) checked @endif id="commentStatus">
                    <label class="form-check-label" id="commentStatusLabel" data-toggle="@if($article->comment_status == 2) {{ __("Closed") }} @else {{ __("Open") }} @endif" for="commentStatus">@if($article->comment_status == 2) {{ __("Open") }} @else {{ __("Closed") }} @endif</label>
                  </div>
                  @error('comment-status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <fieldset>
                    <legend class="h6">{{ __('Status') }}</legend>
                    <div class="d-flex gap-4">
                      <input class="d-none" type="radio" name="status" id="private" @if($article->status == 0) checked @endif value="0">
                      <button type="button" data-target="private" class="btn btn-primary @if($article->status == 0) active @endif status-buttons">
                        <span calss="fa text-white">
                          <svg class="w-22px" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"></path>
                          </svg>
                        </span>
                      </button>
                      <input class="d-none" type="radio" name="status" id="unlisted" @if($article->status == 1) checked @endif value="1">
                      <button type="button" data-target="unlisted" class="btn btn-warning @if($article->status == 1) active @endif status-buttons">
                        <span calss="fa text-white">
                          <svg class="w-22px" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"></path>
                          </svg>
                        </span>
                      </button>
                      <input class="d-none" type="radio" name="status" id="public" @if($article->status == 2) checked @endif value="2">
                      <button type="button" data-target="public" class="btn btn-info @if($article->status == 2) active @endif status-buttons">
                        <span calss="fa text-white">
                          <svg class="w-22px" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"></path>
                          </svg>
                        </span>
                      </button>
                    </div>
                  </fieldset>
                  @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
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
                  <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('max file size 2MB') }}</p>
                  <img src="{{ $article->image_url() }}" id="articleImagePreview" class="rounded mx-auto m-0 article-thumbnail" alt="{{ __('Article Image') }}">
                  <p class="lead mb-0 mt-2" style="font-size: 1rem;">{{ __('recommended size is') }} <bdi>1200 x 628</bdi></p>
                  <hr>
                  <input type="file" class="d-none" name="image" id="articleImage">
                  <button type="button" onclick="document.getElementById('articleImage').click()" class="btn btn-sm btn-warning d-inline-flex mx-3 align-items-center">
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
        <div class="col-12">
          <div class="row">
            <div class="col-12">
              <div class="card card-body border-0 shadow">
                <div class="d-flex justify-content-between align-items-center">
                  <h2 class="h4 m-0">{{ __('Article Content') }}</h2>
                  <button class="w-content btn btn-gray-800 m-0 animate-up-1" type="submit">{{ __('Save all') }}</button>
                </div>
                <hr class="my-3">
                <!-- Tab -->
                <nav>
                  <div class="nav nav-tabs mb-4" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="ar-article-content-tab" data-bs-toggle="tab" href="#ar-article-content" role="tab" aria-controls="ar-article-content" aria-selected="true">{{ __("arabic") }}</a>
                    <a class="nav-item nav-link" id="en-content-article-tab" data-bs-toggle="tab" href="#en-content-article" role="tab" aria-controls="en-content-article" aria-selected="false">{{ __("english") }}</a>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                  <div class="tab-pane fade show active" id="ar-article-content" role="tabpanel" aria-labelledby="ar-article-content-tab">
                    <input id="articleContent" type="hidden" name="content" value="{{ old("content") ?? (isset($article->id) ? $article->content : "") }}">
                    <trix-editor class="article-content" input="articleContent"></trix-editor>
                  </div>
                  <div class="tab-pane fade" id="en-content-article" role="tabpanel" aria-labelledby="en-content-article-tab">
                    <input id="articleContentEn" type="hidden" name="content_en" value="{{ old("content_en") ?? (isset($article->id) ? $article->translateOrNew('en')->content : "") }}">
                    <trix-editor class="article-content" input="articleContentEn"></trix-editor>
                  </div>
                </div>
                <!-- End of tab -->
                <button class="w-content btn btn-gray-800 mt-4 ms-auto w-fit px-5 animate-up-1" type="submit">{{ __('Save all') }}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

@endsection

@section('scripts')
  @if(Session::has('article-saved') && Session::get('article-saved'))
    <script src="{{ url("libs/dashboard/sweetalert2.all.min.js") }}"></script>
  @endif
  <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
  <script>
    let btns = document.querySelectorAll('.status-buttons');
    btns.forEach(el => {
      el.addEventListener("click", function () {
        btns.forEach(btn => btn.classList.remove("active"));
        this.classList.add("active");
        document.getElementById(el.dataset.target).setAttribute("checked", true);
      });
    });
    articleImage.addEventListener("change", function (e) {
      const [file] = this.files
      if (file) {
        articleImagePreview.src = URL.createObjectURL(file)
      }
    })

    $("#commentStatusLabel").on("click", function () {
      let txt = $(this).data("toggle")
      $(this).data("toggle", $(this).text())
      $(this).text(txt)
    })

    var HOST = "{{ route('article_attachment') }}"

    addEventListener("trix-attachment-add", function(event) {
      if (event.attachment.file) {
        uploadFileAttachment(event.attachment)
      }
    })

    function uploadFileAttachment(attachment) {
      uploadFile(attachment.file, setProgress, setAttributes)

      function setProgress(progress) {
        attachment.setUploadProgress(progress)
      }

      function setAttributes(attributes) {
        attachment.setAttributes(attributes)
      }
    }

    function uploadFile(file, progressCallback, successCallback) {
      var key = createStorageKey(file)
      var formData = createFormData(key, file)
      var xhr = new XMLHttpRequest()

      xhr.open("POST", HOST, true)
      xhr.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'))

      xhr.upload.addEventListener("progress", function(event) {
        var progress = event.loaded / event.total * 100
        progressCallback(progress)
      })

      xhr.addEventListener("load", function(event) {
        if (xhr.status == 200) {
          var attributes = {
            url: xhr.response,
            href: xhr.response + "?content-disposition=attachment"
          }
          successCallback(attributes)
        }
      })

      xhr.send(formData)
    }

    function createStorageKey(file) {
      var date = new Date()
      var day = date.toISOString().slice(0,10)
      var name = date.getTime() + "-" + file.name
      return [ "tmp", day, name ].join("/")
    }

    function createFormData(key, file) {
      var data = new FormData()
      data.append("key", key)
      data.append("Content-Type", file.type)
      data.append("file", file)
      return data
    }

    @if(Session::has('article-saved') && Session::get('article-saved'))
      Swal.fire({
        title: "Article Saved Successfully",
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

    $(window).ready(function () {
      $("#form").on("submit", function () {
        let arr = []
        $("#tags-input .tags .tag").each(function () {
          arr.push($(this).text().trim().toLowerCase());
        });
        $("#tags").val(arr.join(","))
      })
      window.shakingTags = [];
      function insertTag(text) {
        $("#tags-input .tags").append(`
          <span contenteditable="true" class="tag badge badge-sm py-1 bg-primary position-relative">${text}</span>
        `);
      }
      function processTags(txt) {
        let arr = $("#tags").val().split(",")
        if (!arr.includes(txt)) {
          insertTag(txt)
          arr.push(txt)
          document.querySelector("#tags").value = arr.join(",")
          document.querySelector("#tags-input .input").value = ""
        }else {
          $("#tags-input .tags .tag").each(function () {
            if ($(this).text() == document.querySelector("#tags-input .input").value) {
              $(this).addClass("jump-shake")
              window.shakingTags.push($(this))
            }
          })
          setTimeout(() => {
            window.shakingTags.forEach(el => {
              $(el).removeClass("jump-shake")
            })
          }, 800);
          document.querySelector("#tags-input .input").value = ""
        }
      }
      $("#tags-input .tags").on("click", function (e) {
        if (e.target !== this) {return;}
        $("#tags-input .input").focus();
      });
      $("#tags-input .input").on("input", function(e) {
        if (e.originalEvent.data == "," || ($("#tags-input .input").val().match(/\n/g)||[]).length) {
          if (e.originalEvent.data == ",") {
            $("#tags-input .input").val($("#tags-input .input").val().replace(/,/g, ""))
          }
          if (($("#tags-input .input").val().match(/\n/g)||[]).length) {
            $("#tags-input .input").val($("#tags-input .input").val().replace(/\n/g, ""))
          }
          processTags($("#tags-input .input").val())
        }
      });
      $("#tags").val().split(",").forEach(function (tag) {
        insertTag(tag)
      })
    })
  </script>
@endsection
