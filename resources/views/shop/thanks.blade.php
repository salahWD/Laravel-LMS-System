@extends('layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __(config("app.name")) . " | " . __("thank you") }}</title>
@endsection

@section("styles")
  <style>
    :root {
    }
  </style>
@endsection

@section("content")
  <div class="container">
    <h1>thank you</h1>
    <p class="lead">payment accepted successfully, your order is getting prepared and you will receive updates about it via email</p>
  </div>
@endsection
