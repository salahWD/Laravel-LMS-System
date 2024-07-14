@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . $course->title . ' checkout' }}</title>
@endsection

@section('styles')
  @vite('resources/css/courses.css')
@endsection

@section('content')
  <!-- section main content -->
  <section class="main-content mt-5">
    <div class="container">

      <div class="row">
        <div class="col-md-6">
          <div class="courses__item courses__item-three shine__animate-item">
            <div class="row">
              <div class="col-12">
                <div class="courses__item-thumb text-center">
                  <img src="{{ $course->image_url() }}" alt="{{ $course->title }}">
                </div>
              </div>
              <div class="col-12">
                <div class="courses__item-content">
                  <div class="d-flex flex-column justify-content-between h-100">
                    <div class="top">
                      <ul class="courses__item-meta list-wrap mt-4 mb-3 gap-2 flex-nowrap">
                        <h3 class="title m-0">
                          {{ $course->title }}
                        </h3>
                        <li class="price p-0">
                          <span class="h3 m-0">
                            {{ $course->show_price() }}
                          </span>
                        </li>
                      </ul>
                      <p class="info">
                        {{ $course->description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div id="payment-form"></div>
        </div>
      </div>

    </div>
  </section>
@endsection

@section('scripts')
  <script>
    const CourseUrl = "{{ route('course_enroll', $course->id) }}";
    {{-- const clientSecretKey = "{{ $intent }}"; --}}
    const stripeKey = "{{ config('services.stripe.key') }}";
    {{-- const clientSecret = "{{ $intentId }}"; --}}
    const has_price = "{{ $course->price != null && $course->price > 0 }}";
  </script>
  @vite(['public/js/course-enroll.js'])
@endsection
