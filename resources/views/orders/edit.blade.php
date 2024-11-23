@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('Manage User') @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite('resources/css/dashboard/user-edit.css')
@endsection

@section('content')

  <div class="container">

    <div class="row justify-content-center mt-4">
      <div class="col-12 col-xl-9">
        <div class="card shadow border-0 p-4 p-md-5 position-relative">
          <div class="mb-6 d-flex align-items-center justify-content-center">
            <h2 class="h1 mb-0">Order #{{ $order->token }}</h2><span
              class="badge badge-lg bg-{{ $order->status_class() }} ms-4">{{ $order->status_title() }}</span>
          </div>
          <div class="row justify-content-between">
            <div class="col-sm">
              <h5>Client Information:</h5>
              <div>
                <ul class="list-group simple-list">
                  <li class="list-group-item fw-normal">{{ $order?->user?->fullname() }}</li>
                  <li class="list-group-item fw-normal">
                    <a class="fw-bold text-primary" href="#">{{ $order?->user?->email }}</a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="col-sm col-lg-6">
              <dl class="d-flex justify-content-between">
                <strong>Invoice ID:</strong>
                <span>#{{ $order->token }}</span>
              </dl>
              <dl class="d-flex justify-content-between">
                <strong>Date Issued:</strong>
                <span>{{ $order->get_date(true) }}</span>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-lg-4">
      <div class="col-12 col-xl-6">
        <div class="card card-body border-0 shadow mb-4">
          @if ($order->products->count() > 0)
            @foreach ($order->products as $i => $prod)
              <div class="d-flex justify-content-between">
                <div class="d-flex gap-2">
                  <img src="{{ $prod->main_image_url() }}" alt="Diamond Watch image"
                    class="object-position-center rounded" style="width:90px;aspect-ratio:1;object-fit:cover">
                  <div class="py-3">
                    <h6 class="title">{{ $prod->title }}</h6>
                  </div>
                </div>
                <h3 class="quantity d-flex align-items-center px-4"
                  style="border-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}:0.0625rem solid #37415147 !important;">
                  {{ $prod->pivot->quantity }}
                </h3>
              </div>
              <hr>
            @endforeach
          @else
          @endif
          <div class="mt-4 w-100">
            <table class="table table-clear">
              <tbody>
                <tr>
                  <td class="left"><strong>Subtotal</strong></td>
                  <td class="right">{{ $payment->amount / 100 }}</td>
                </tr>
                <tr>
                  <td class="left"><strong>VAT</strong></td>
                  <td class="right">{{ $payment->vat }}</td>
                </tr>
                <tr>
                  <td class="left"><strong>Shipping</strong></td>
                  <td class="right">{{ config('cart.currency') . config('cart.shipping_amound') }}
                  </td>
                </tr>
                <tr>
                  <td class="left"><strong>Total</strong></td>
                  <td class="right">
                    <strong>{{ $payment->amount / 100 + config('cart.shipping_amound') }}</strong>
                  </td>
                </tr>
              </tbody>
            </table>
            @if ($order->stage == 2)
              <form id="refund-and-cancel" action="{{ route('order_refund', $order->id) }}" method="POST">
                @csrf
                @method('delete')
                <button class="mt-4 btn btn-sm btn-danger d-inline-flex align-items-center" data-bs-toggle="tooltip"
                  data-bs-placement="top" title="{{ __('Refund Order') }}">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" class="icon icon-xs me-2"
                    fill="currentColor">
                    <path
                      d="M535 41c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l64 64c4.5 4.5 7 10.6 7 17s-2.5 12.5-7 17l-64 64c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l23-23L384 112c-13.3 0-24-10.7-24-24s10.7-24 24-24l174.1 0L535 41zM105 377l-23 23L256 400c13.3 0 24 10.7 24 24s-10.7 24-24 24L81.9 448l23 23c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0L7 441c-4.5-4.5-7-10.6-7-17s2.5-12.5 7-17l64-64c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9zM96 64H337.9c-3.7 7.2-5.9 15.3-5.9 24c0 28.7 23.3 52 52 52l117.4 0c-4 17 .6 35.5 13.8 48.8c20.3 20.3 53.2 20.3 73.5 0L608 169.5V384c0 35.3-28.7 64-64 64H302.1c3.7-7.2 5.9-15.3 5.9-24c0-28.7-23.3-52-52-52l-117.4 0c4-17-.6-35.5-13.8-48.8c-20.3-20.3-53.2-20.3-73.5 0L32 342.5V128c0-35.3 28.7-64 64-64zm64 64H96v64c35.3 0 64-28.7 64-64zM544 320c-35.3 0-64 28.7-64 64h64V320zM320 352a96 96 0 1 0 0-192 96 96 0 1 0 0 192z" />
                  </svg>
                  Cancel Order & Refund
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
      <div class="col-12 col-xl-6">
        <div class="d-flex flex-column gap-3 w-100">
          <div class="card card-body border-0 shadow">
            <div class="d-flex justify-content-between align-items-center" id="stage-1">
              <div>
                <h2 class="h5 mb-1">Processing</h2>
                <p class="lorem mb-0 pe-4">Lorem ipsum dolor sit amet.</p>
              </div>
              <x-order-stage :order="$order" :stage="1"></x-order-stage>
            </div>
          </div>
          <div class="card card-body border-0 shadow">
            <div class="d-flex justify-content-between align-items-center" id="stage-2">
              <div>
                <h2 class="h5 mb-1">Shipping</h2>
                <p class="lorem mb-0 pe-4">Lorem ipsum dolor sit amet.</p>
              </div>
              <x-order-stage :order="$order" :stage="2"></x-order-stage>
            </div>
          </div>
          <div class="card card-body border-0 shadow">
            <div class="d-flex justify-content-between align-items-center" id="stage-3">
              <div>
                <h2 class="h5 mb-1">Delivery</h2>
                <p class="lorem mb-0 pe-4">Lorem ipsum dolor sit amet.</p>
              </div>
              <i class="text-warning text-center" style="width: 40px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" fill="currentColor" height="46">
                  <path
                    d="M176 432c0 44.1-35.9 80-80 80s-80-35.9-80-80 35.9-80 80-80 80 35.9 80 80zM25.3 25.2l13.6 272C39.5 310 50 320 62.8 320h66.3c12.8 0 23.3-10 24-22.8l13.6-272C167.4 11.5 156.5 0 142.8 0H49.2C35.5 0 24.6 11.5 25.3 25.2z" />
                </svg>
              </i>
            </div>
          </div>
          <div class="card card-body border-0 shadow">
            <div class="d-flex justify-content-between align-items-center" id="stage-4">
              <div>
                <h2 class="h5 mb-1">Receve The Order</h2>
                <p class="lorem mb-0 pe-4">Lorem ipsum dolor sit amet.</p>
              </div>
              <i class="text-warning text-center" style="width: 40px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512" fill="currentColor" height="46">
                  <path
                    d="M176 432c0 44.1-35.9 80-80 80s-80-35.9-80-80 35.9-80 80-80 80 35.9 80 80zM25.3 25.2l13.6 272C39.5 310 50 320 62.8 320h66.3c12.8 0 23.3-10 24-22.8l13.6-272C167.4 11.5 156.5 0 142.8 0H49.2C35.5 0 24.6 11.5 25.3 25.2z" />
                </svg>
              </i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('jslibs')
  <script src="{{ url('libs/dashboard/sweetalert2.all.min.js') }}"></script>
@endsection

@section('scripts')
  <script>
    let updateStage = document.getElementById('update-form');
    @if ($order->stage == 2)
      let refundForm = document.getElementById('refund-and-cancel');

      refundForm.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
          title: "Will Cancel The Order & Refund The Payment ?",
          confirmButtonText: `Cancel & Refund`,
          customClass: {
            confirmButton: 'bg-danger text-white',
          },
          focusCancel: true,
          buttonsStyling: true,
          showCancelButton: true,
          showLoaderOnConfirm: false,
        }).then((result) => {
          if (result.isConfirmed) {
            refundForm.submit();
          }
        });
      });
    @endif
    updateStage.addEventListener('submit', function(e) {
      e.preventDefault();
      console.log(updateStage)
      Swal.fire({
        title: "Are You Sure ?",
        confirmButtonText: "Yes",
        customClass: {
          confirmButton: 'bg-success text-white',
        },
        focusCancel: false,
        buttonsStyling: true,
        showCancelButton: true,
        showLoaderOnConfirm: false,
      }).then((result) => {
        if (result.isConfirmed) {
          updateStage.submit();
        }
      });
    });
  </script>
@endsection
