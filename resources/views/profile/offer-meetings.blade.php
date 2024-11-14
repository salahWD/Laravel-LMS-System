@extends('layout')

@section('meta')
  <title>{{ '@' . $author->username . ' | ' . __('Meetings') }}</title>
@endsection

@section('styles')
  @vite('resources/css/meetings.css')
@endsection

@section('content')

  <div class="container">

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (isset($error))
      <div class="alert alert-danger">
        <p class="lead">{{ $error }}</p>
      </div>
    @endif

    @if (isset($success))
      <div class="alert alert-success">
        <p class="lead">{{ $success }}</p>
      </div>
    @endif

    <img src="{{ $author->image_url() }}" alt="" class="rounded-circle object-fit-cover m-auto mt-5 mb-2 d-block"
      style="max-width: 150px;">

    <div class="w-full pb-8 mt-8 sm:rounded-lg text-center">
      <h2 class="pl-6 text-2xl font-bold sm:text-xl">My Meetings</h2>
    </div>

    @if ($meetings->count() > 0)
      <div class="row">
        @foreach ($meetings as $meeting)
          <div class="col-lg-4 col-md-6">
            <x-meeting-card id="{{ $meeting->id }}" butonClass="modal-opener" title="{{ $meeting->title }}"
              href="{{ route('appointment_booking', $meeting->url) }}" notes="{{ $meeting->description }}"
              color="{{ $meeting->color }}" duration="{{ $meeting->show_duration() }}" selectable=true></x-meeting-card>
          </div>
        @endforeach
      </div>
    @else
      <div role="alert" class="custom-alert-1">
        <p class="font-bold text-capitalize" style="margin: 0px; font-weight: 700;">{{ __('warning') }}</p>
        <p style="margin: 0px;">{{ __('there is no :items to show', ['items' => __('appointments')]) }}.</p>
      </div>
    @endif

  </div>

@endsection

@section('scripts')
  <script src="{{ url('js/jquery.min.js') }}"></script>
@endsection
