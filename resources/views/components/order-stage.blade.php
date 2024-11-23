@props(['stage', 'order'])

@if ($order->stage > $stage)
  <i class="text-success text-center" style="width: 40px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512" height="40">
      <path
        d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
    </svg>
  </i>
@elseif ($order->stage == $stage)
  <form method="POST" action="{{ route('order_edit', $order->id) }}" id="update-form">
    @csrf
    <input type="hidden" name="stage" value="{{ $stage }}">
    <button type="submit" class="btn btn-success text-white"><i class="fa fa-check"></i> done</button>
  </form>
@else
  <i class="text-warning text-center" style="width: 40px;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" fill="currentColor" height="46">
      <path
        d="M176 432c0 44.1-35.9 80-80 80s-80-35.9-80-80 35.9-80 80-80 80 35.9 80 80zM25.3 25.2l13.6 272C39.5 310 50 320 62.8 320h66.3c12.8 0 23.3-10 24-22.8l13.6-272C167.4 11.5 156.5 0 142.8 0H49.2C35.5 0 24.6 11.5 25.3 25.2z" />
    </svg>
  </i>
@endif
