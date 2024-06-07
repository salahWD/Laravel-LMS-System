<!doctype html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $subject }}</title>
    <style media="all" type="text/css">

    body {
      font-family: Helvetica, sans-serif;
      -webkit-font-smoothing: antialiased;
      font-size: 16px;
      line-height: 1.3;
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
      background-color: #f4f5f6;
      margin: 0;
      padding: 0;
    }

    .container {
      margin: 0 auto !important;
      max-width: 600px;
      padding: 0;
      padding-top: 24px;
      width: 600px;
    }

    .content {
      box-sizing: border-box;
      display: block;
      margin: 0 auto;
      max-width: 600px;
      padding: 0;
    }

    .main {
      background: #ffffff;
      border: 1px solid #eaebed;
      border-radius: 16px;
      width: 100%;
    }

    .wrapper {
      box-sizing: border-box;
      padding: 24px;
    }

    .footer {
      clear: both;
      margin-top: 32px;
      text-align: center;
      width: 100%;
    }

    .footer td,
    .footer p,
    .footer span,
    .footer a {
      color: #9a9ea6;
      font-size: 16px;
      text-align: center;
    }

    p {
      font-family: Helvetica, sans-serif;
      font-size: 16px;
      font-weight: normal;
      margin: 0;
      margin-bottom: 16px;
    }

    a {
      color: #3487fc;
      text-decoration: underline;
    }

    .btn {
      box-sizing: border-box;
      width: fit-content;
      padding-bottom: 16px;
      background-color: #ffffff;
      border: solid 2px #3487fc;
      border-radius: 4px;
      box-sizing: border-box;
      color: #3487fc;
      cursor: pointer;
      display: block;
      font-size: 16px;
      font-weight: bold;
      margin: 0 auto 16px;
      padding: 12px 24px;
      text-decoration: none;
      text-transform: capitalize;
    }

    .btn-primary {
      background-color: #3487fc;
      border-color: #3487fc;
      color: #ffffff;
    }

    @media all {

      .btn-primary:hover {
        background-color: #002136 !important;
        border-color: #002136 !important;
      }
    }

    .text-muted {
      color: #9a9ea6;
    }

    .text-c {
      text-align: center;
    }

    .text-r {
      text-align: right;
    }

    .text-l {
      text-align: left;
    }

    .link {
      color: #3487fc !important;
      text-decoration: underline !important;
    }

    .mt0 {
      margin-top: 0;
    }

    .mb0 {
      margin-bottom: 0;
    }

    .preheader {
      color: transparent;
      display: none;
      height: 0;
      max-height: 0;
      max-width: 0;
      opacity: 0;
      overflow: hidden;
      mso-hide: all;
      visibility: hidden;
      width: 0;
    }

    .powered-by a {
      text-decoration: none;
    }

    @media only screen and (max-width: 640px) {
      .main p,
      .main td,
      .main span {
        font-size: 16px !important;
      }
      .wrapper {
        padding: 8px !important;
      }
      .content {
        padding: 0 !important;
      }
      .container {
        padding: 0 !important;
        padding-top: 8px !important;
        width: 100% !important;
      }
      .main {
        border-left-width: 0 !important;
        border-radius: 0 !important;
        border-right-width: 0 !important;
      }
      .btn {
        font-size: 16px !important;
        max-width: 100% !important;
        width: 100% !important;
      }
    }
    @media all {
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass,
      .ExternalClass p,
      .ExternalClass span,
      .ExternalClass font,
      .ExternalClass td,
      .ExternalClass div {
        line-height: 100%;
      }
      .apple-link a {
        color: inherit !important;
        font-family: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-decoration: none !important;
      }
      #MessageViewBody a {
        color: inherit;
        text-decoration: none;
        font-size: inherit;
        font-family: inherit;
        font-weight: inherit;
        line-height: inherit;
      }
    }
    </style>
  </head>
  <body dir={{ app()->getLocale() == "ar" ? "rtl" : "rtl" }}>
    <p>&nbsp;</p>
    <div class="container">
      <div class="content">
        <span class="preheader">{{ substr($text, 0, 30) }}@if(strlen($text) > 30)...@endif</span>
        <div class="main">
          <div class="wrapper">
            <p><b>{{ __("name") }}:</b> {{ $fullname }}</p>
            <b>{{ __("content") }}:</b>
            <p>{{ $text }}</p>
            <a class="btn btn-primary" href="{{ route("messages_manage") }}" target="_blank">{{ __("See The Message") }}</a>
            <p class="text-muted">{{ $phrase }}</p>
          </div>
        </div>
        <div class="footer">
          <p class="apple-link">{{ __("Thank you for reaching to us and contacting us" . ", " . config("app.name")) }}</p>
          <p class="content-block powered-by">
            {{ __("developed by") }} <a href="https://github.com/salahwd">salah</a>
          </p>
        </div>

      </div>
    </div>
    <p>&nbsp;</p>
  </body>
</html>
