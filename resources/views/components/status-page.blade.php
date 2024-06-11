@props([
  'icon',
  'desc',
])

<style>
  .title {
    margin-top: 50px;
    margin-bottom: 20px;
  }
  .title.danger {
    color: rgb(66, 8, 8);
  }
  .title.warning {
    color: rgb(66, 52, 8);
  }
  .icon {
    max-width: 350px;
    margin: auto;
    color: rgb(123, 182, 206);
  }
  .icon svg {
    fill: currentColor;
    max-width: 100%;
    max-height: 100%;
  }
  p {
    font-size: 18px;
  }
  .btn {
    font-size: 20px;
    padding-right: 20px;
    padding-left: 20px;
    /* background-color:  rgb(123, 182, 206); */
    position: relative;
    margin-top: 20px;
  }
  .btn::after {
    content: "";
    z-index: -1;
    position: absolute;
    top: 50%;
    left: 50%;
    width: 250px;
    height: 250px;
    transition: all 0.3s;
    background-image: linear-gradient(#81e4fb, #2f5291);
    transform: translate(-50%, -50%) rotate(210deg);
    transform-origin: center center;
  }
  .btn.danger::after {
    background-image: linear-gradient(#fb8195, #912f3f);
  }
  .btn:hover {
    color: white;
  }
  .btn:hover::after {
    transform: translate(-50%, -50%) rotate(360deg);
  }
</style>

<div class="container">
  <div class="text-center">
    <h1 class="title {{ $attributes["level"] }}">{{ $attributes["title"] }}</h1>
    <div class="icon">
      {{ $icon }}
    </div>
    <p class="m-auto mt-3" style="max-width: 550px;">
      {{ $desc }}
    </p>
    <a href="{{ $attributes["link"] }}" class="btn {{ $attributes["level"] }}">{{ $attributes["btn"] }}</a>
  </div>
</div>
