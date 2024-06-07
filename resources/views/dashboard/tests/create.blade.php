@extends('dashboard.layout')

@section('meta')
  @php
    $page_title = config("app.name") . " | " . isset($test) ? __('Edit Test') : __('Create Test');
  @endphp
@endsection

@section('styles')
  @vite("resources/css/dashboard/test-builder.css")
  @vite("resources/css/dashboard/style.css")
@endsection

@section('content')

  <form id="form" class="d-none" action="{{ route("test_store") }}" method="POST" enctype="multipart/form-data">
    @csrf
  </form>

  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
  <div class="container">
    <div class="card">
      <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
          <div class="card-body">
            <div class="mb-4">
              <label for="titleInput" class="form-label">Test title:</label>
              <input required form="form" type="text" name="title" id="titleInput" class="form-control" placeholder="Test Name">
            </div>
            @if ($courses != null && count($courses) > 0)
              <div class="mb-4">
                <label for="courses" class="form-label">Course:</label>
                <select form="form" name="course_id" id="courses" class="form-select" @if(isset($course) && $course != null) disabled @endif>
                  @if (isset($course) && $course->id != null)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                  @endif
                  <option value="">-- select a course --</option>
                  @foreach ($courses as $crs)
                    @if (isset($course) && $course != null && $course->id == $crs->id)
                      <option selected value="{{ $crs->id }}">{{ $crs->title }}</option>
                    @else
                      <option value="{{ $crs->id }}">{{ $crs->title }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            @endif
            {{-- <div class="mb-4">
              <input type="file" form="form" id="ImageInput" class="d-none" accept=".jpg,.png,.jpeg" name="thumbnail">
              <p>Add Image:</p>
              <img src="https://placehold.it/600x320?text=thumbnail" id="thumbnailPreview" alt="thumbnail preview image" class="m-auto d-block img-thumbnail" style="max-width: 600px">
              <button type="button" class="btn btn-secondary" onclick="ImageInput.click()">
                <i class="fa fa-camera"></i>
              </button>
            </div> --}}
            <button type="submit" form="form" class="btn btn-primary px-5">Create</button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-1"></div>
  </div>

@endsection

@section('scripts')
  <script>
    form.addEventListener("submit", function () {
      courses.removeAttribute("disabled");
    })

    let options = document.querySelectorAll(".option-card");

    options.forEach((opt) => {
      let input = document.getElementById(opt.dataset.target);
      opt.addEventListener("click", function () {
        if (this.classList.contains("active")) {
          this.classList.remove("active");
          input.removeAttribute("checked");
        } else {
          options.forEach((opt) => {
            opt.classList.remove("active");
            document.getElementById(opt.dataset.target).removeAttribute("checked");
          });
          this.classList.add("active");
          input.setAttribute("checked", "");
        }
      });
    });

    ImageInput.addEventListener("change", function () {
      const [file] = this.files;
      if (file) {
        thumbnailPreview.src = URL.createObjectURL(file);
      }
    })
  </script>
@endsection
