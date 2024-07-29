@extends('layout')

@section('meta')
  <title>{{ __(config('app.name')) . ' | ' . $course->title }}</title>
@endsection

@section('styles')
  @vite('resources/css/courses.css')
@endsection

@section('content')
  <!-- section main content -->
  <section class="main-content mt-5">
    <div class="container">

      <div class="fixed-bottom p-4">
        <div class="position-relative">
          @if (session()->has('course_enrolled') && session()->get('course_enrolled'))
            <div id="liveToast" class="toast align-items-center text-white bg-primary border-0 position-absolute"
              role="alert" aria-live="assertive" aria-atomic="true"
              style="bottom: 0;{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 0;">
              <div class="d-flex">
                <div class="toast-body">
                  {{ __('Course has been successfully enrolled') }}.
                </div>
                <button type="button" onclick="$('#liveToast').fadeOut(450)"
                  class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
            </div>
          @endif
        </div>
      </div>

      <div class="courses__item courses__item-three shine__animate-item">
        <div class="courses__item-thumb">
          <img src="{{ $course->image_url() }}" alt="{{ $course->title }}">
        </div>
        <div class="courses__item-content">
          <div class="d-flex flex-column justify-content-between h-100">
            <div class="top">
              <ul class="courses__item-meta list-wrap">
                {{-- <p class="author">
                {{ __("By") }}
                <a href="#">
                  {{ $course->user->fullname() }}
                </a>
              </p> --}}
                <h3 class="title">
                  {{ $course->title }}
                </h3>
                <li class="price">
                  {{-- <del>
                  $29.00
                </del> --}}
                  <a>
                    {{ $course->show_price() }}
                  </a>
                </li>
              </ul>
              <p class="info">
                {{ $course->description }}
              </p>
            </div>
            <div class="courses__item-bottom mb-3">
              <div class="button">
                @if ($is_enrolled)
                  <a href="{{ route('lecture_show',auth()->user()->last_watched_of_course($course->id)) }}">
                    <span class="text">
                      {{ __('Continue learning') }}
                    </span>
                    <i class="flaticon-arrow-right">
                    </i>
                  </a>
                @else
                  <a href="{{ route('course_enroll', $course->id) }}">
                    <span class="text">
                      {{ __('Enroll Now') }}
                    </span>
                    <i class="flaticon-arrow-right">
                    </i>
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card card-body border-0 shadow mb-4">
        <div class="row">
          @if ($items->count() > 0)
            @foreach ($items as $item)
              <div class="col-md-4 mb-3 lecture">
                @if ($item->is_open && $is_enrolled)
                  <a
                    href="@if ($item->is_lecture()) {{ route('lecture_show', $item->itemable->id) }} @else {{ route('test_show', $item->itemable->id) }} @endif">
                    <div style="border-width: 2px;"
                      class="card bg-dark text-white @if ($item->is_watched) border-success @else border-primary @endif">
                      <img class="card-img lecture-img" src="{{ $item->itemable->image_url() }}"
                        alt="{{ $item->itemable->title }}">
                      <div class="card-img-overlay">
                        <h5 class="card-title m-0">{{ $item->itemable->title }}</h5>
                        <small class="card-text text-sm">{{ $item->itemable->get_date() }}</small>
                      </div>
                    </div>
                  </a>
                @else
                  <div style="border-width: 2px;"
                    class="card bg-dark text-white position-relative @if ($item->is_watched) border-success @else border-0 @endif">
                    <span class="lock-icon">
                      <i class="fa fa-lock"></i>
                    </span>
                    <img class="card-img lecture-img closed-lecture" src="{{ $item->itemable->image_url() }}"
                      alt="{{ $item->itemable->title }}">
                    <div class="card-img-overlay">
                      <h5 class="card-title m-0">{{ $item->itemable->title }}</h5>
                      <small class="card-text text-sm">{{ $item->itemable->get_date() }}</small>
                    </div>
                  </div>
                @endif
              </div>
            @endforeach
          @else
            <div class="card-body">
              <div class="my-4">
                <div role="alert"
                  style="border-style: solid; border-width: 0px;border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}-width: 4px; border-color: rgb(249 115 22); background-color: rgb(255 237 213); padding: 1rem; color: rgb(194 65 12);">
                  <p class="font-bold" style="margin: 0px; font-weight: 700;">{{ __('Not Finished Yet') }}</p>
                  <p style="margin: 0px;">
                    {{ __('we are working on this course and we are not done uploading the leactures and exams yet') }}.
                  </p>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

    </div>
  </section>
@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script src="{{ url('js/jquery.min.js') }}"></script>
  <script src="{{ url('js/popper.min.js') }}"></script>
  <script src="{{ url('js/bootstrap.min.js') }}"></script>
  <script src="{{ url('js/slick.min.js') }}"></script>
  <script src="{{ url('js/jquery.sticky-sidebar.min.js') }}"></script>
  @if (session()->has('course_enrolled') && session()->get('course_enrolled'))
    @php session()->forget('course_enrolled') @endphp
    <script>
      $(window).ready(function() {
        $("#liveToast").fadeIn(450, function() {
          setTimeout(() => {
            $("#liveToast").fadeOut(450, function() {
              $("#liveToast").remove()
            })
          }, 2500);
        })
      });
    </script>
  @endif
@endsection
