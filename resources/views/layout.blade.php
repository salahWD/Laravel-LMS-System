<!--
  =========================================================
  * Eletorial - custom design and development for (the master of electronics) eng walid isa
  =========================================================

  * Copyright 2024 eletorial

  * Designed and coded by salah bakhash (https://github.com/salahWD)

  =========================================================
-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/x-icon" href="/favicon.png">
  @yield('meta')
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>{{ config('app.name') }}</title>
  <meta name="title" content="CES CONTENT">
  <meta name="author" content="Walid Isa">
  <meta name="description" content="CES CONTENT">
  <meta name="keywords" content="CES CONTENT" />
  <link rel="canonical" href="CES MAIN PAGE">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="CES CONTENT">
  <meta property="og:title" content="CES CONTENT TITLE">
  <meta property="og:description" content="CES CONTENT">
  <meta property="og:image" content="CES CONTENT">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="CES CONTENT">
  <meta property="twitter:title" content="CES CONTENT TITLE">
  <meta property="twitter:description" content="CES CONTENT">
  <meta property="twitter:image" content="CES CONTENT">

  @if (app()->getLocale() == 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
      integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLzUCCUXOY2j/LSvXYuG6Bqs43ALlhIqAJVRb" crossorigin="anonymous">
  @else
    @vite('resources/csslibs/bootstrap.min.css')
  @endif

  @if (!isset($no_header_footer) || !$no_header_footer)
    @vite(['resources/csslibs/slick.css', 'resources/csslibs/simple-line-icons.css', 'resources/csslibs/style.css', 'resources/csslibs/all.min.css', 'resources/css/custom.css'])
  @endif

  @yield('styles')

</head>

<body dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="{{ app()->getLocale() }}">

  <!-- preloader -->
  {{-- <div id="preloader">
      <div class="preloader">
        <i></i>
        <i></i>
        <i></i>
        <i></i>
        <i></i>
        <i></i>
        <i></i>
      </div>
    </div> --}}

  <div class="site-wrapper d-lg-none">
    <div class="main-overlay"></div>
  </div>

  @if (!isset($no_header_footer) || !$no_header_footer)
    @include('layout.header')
  @endif

  @yield('content')

  @if (!isset($no_header_footer) || !$no_header_footer)
    @include('layout.footer')
    @include('layout.search-popup')
  @endif

  @yield('scripts')

</body>

</html>
