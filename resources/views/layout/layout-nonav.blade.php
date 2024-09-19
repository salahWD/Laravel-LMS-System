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

  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="120x120" href="../../assets/images/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="../../assets/images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon/favicon-16x16.png">
  <link rel="manifest" href="../../assets/images/favicon/site.webmanifest">
  <link rel="mask-icon" href="../../assets/images/favicon/safari-pinned-tab.svg" color="{{ __('ffffff') }}">
  <meta name="msapplication-TileColor" content="{{ __('ffffff') }}">
  <meta name="theme-color" content="{{ __('ffffff') }}">

  <!-- Bootstrap 5.2.3 -->
  {{-- @if (app()->getLocale() == 'ar')
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-gXt9imSW0VcJVHezoNQsP+TNrjYXoGcrqBZJpry9zJt8PCQjobwmhMGaDHTASo9N" crossorigin="anonymous">
      @else
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    @endif --}}

  @yield('styles')

</head>

<body class="{{ app()->getLocale() }}">

  @yield('content')

  @yield('scripts')

</body>

</html>
