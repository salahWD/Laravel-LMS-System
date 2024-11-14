<x-profile.layout>

  <div class="w-full pb-8 mt-8 sm:rounded-lg">
    <h2 class="pl-6 text-2xl font-bold sm:text-xl">My Orders</h2>
  </div>
  <div class="container">
    @if ($orders->count() > 0)
      <div class="grid mx-auto">
        <div class="w-full mx-auto pb-16 overflow-hidden rounded-lg">
          <div class="block border border-gray-400 rounded-lg bg-white shadow">
            <div class="p-6">
              <div class="flex flex-col">
                <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                  <div class="inline-block min-w-full py-2 sm:px-4 lg:px-6">
                    <div class="overflow-hidden">
                      <table class="min-w-full text-sm font-light text-left">
                        <thead class="text-base font-medium text-gray-900 border-b ">
                          <tr>
                            <th scope="col" class="px-6 py-4">ID</th>
                            <th scope="col" class="px-6 py-4">Order Title</th>
                            <th scope="col" class="px-6 py-4">Items</th>
                            <th scope="col" class="px-6 py-4">Price</th>
                            <th scope="col" class="px-6 py-4">Status</th>
                            <th scope="col" class="px-6 py-4">Date</th>
                          </tr>
                        </thead>
                        <tbody class="font-medium text-gray-800">
                          @foreach ($orders as $order)
                            <tr class="border-b">
                              <td class="px-3 py-4 whitespace-nowrap">
                                <a class="tect-blue-500 underline"
                                  href="{{ route('order_tracking', $order->token) }}">#{{ $order->token }}</a>
                              </td>
                              <td class="px-3 py-4">
                                <a href="{{ route('order_tracking', $order->token) }}">{{ $order->get_title() }}</a>
                              </td>
                              <td class="px-3 py-4 whitespace-nowrap">{{ $order->calc_quantity() }}</td>
                              <td class="px-3 py-4 whitespace-nowrap">
                                {{ config('cart.currency') }}{{ $order->calc_price() }}</td>
                              <td class="px-3 py-4 whitespace-nowrap">
                                <span
                                  class="badge bg-{{ $order->status_class() }} text-sm font-light px-2.5 py-0.5 rounded-full">{{ $order->status_title() }}</span>
                              </td>
                              <td class="px-3 py-4 whitespace-nowrap">{{ $order->get_date() }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-6 py-3">
              <div class="flex items-center justify-center px-6 py-3">
                {!! $orders->links() !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    @else
      <div class="bg-white shadow w-100">
        <div style="border:2px solid rgb(229, 231, 232);border-radius:8px;margin-top: 20px;">
          <div class="flex flex-row-reverse flex-wrap">
            <img src="{{ url('images/orders-image.png') }}" alt="no orders image"
              class="box-border align-middle w-fit object-cover flex-shrink md:max-w-[41%]" />
            <div class="grow-0 md:max-w-[58.33%] md:basis-[58.33%] m-0 flex items-center">
              <div class="p-8">
                <h2
                  style="max-width:100%;margin:0px 0px 16px;padding:0px;font-size:28px;line-height:36px;font-family:'Source Sans Pro', Arial, sans-serif;-webkit-font-smoothing:antialiased;margin-top:0px;margin-bottom:16px;box-sizing:border-box;font-weight:600;color:rgb(31, 31, 31);font:600 28px / 36px 'Source Sans Pro', Arial, sans-serif;letter-spacing:-0.1px;">
                  Order what you need</h2>
                <p class="m-0 text-base leading-6 font-Source" style="color:rgb(31, 31, 31);">
                  We offer starter packages, kits, and beginner-friendly PCBs—perfect for launching your electronics
                  journey</p>
                <a href="{{ route('shop') }}" class="mt-3 btn btn-primary">Our Products</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>

</x-profile.layout>
