<html lang="en">
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <style>
        {!! file_get_contents(resource_path('views/certificates/css/reset-lib.css'), true) !!}
        {!! file_get_contents(resource_path('views/certificates/css/default.css'), true) !!}
      </style>
      <title>certificate</title>
  </head>
  <body>
    <div id="certificate-preview">
      <div class="container">
        <div class="logo">
          An Organization
        </div>
        <div class="marquee" id="certificate-title">
          {{ $data['title'] }}
        </div>
        <div class="assignment">
          This certificate is presented to
        </div>
        <div class="person">
          {{ $data['user'] }}
        </div>
        <div class="reason" id="certificate-desc">
          {!! $data['description'] !!}
        </div>
      </div>
    </div>
  </body>
</html>
