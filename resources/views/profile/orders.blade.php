<x-profile.layout>

  <div class="w-full px-6 pb-8 mt-8 sm:rounded-lg">
    <h2 class="pl-6 text-2xl font-bold sm:text-xl">My Orders</h2>

    <div class="grid mx-auto mt-10">
        <div class="w-full px-4 mx-auto pb-16 overflow-hidden rounded-lg">
        <div class="block border border-gray-400 rounded-lg bg-white shadow">
          <div class="p-6">
            <div class="flex flex-col">
              <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 sm:px-4 lg:px-6">
                  <div class="overflow-hidden">
                    <table class="min-w-full text-sm font-light text-left">
                      <thead
                        class="text-base font-medium text-gray-900 border-b ">
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
                        @foreach($orders as $order)
                          <tr class="border-b">
                            <td class="px-3 py-4 whitespace-nowrap">
                              <a class="tect-blue-500 underline" href="{{ route('order_tracking', $order->token) }}">#{{ $order->token }}</a>
                            </td>
                            <td class="px-3 py-4">
                              <a href="{{ route('order_tracking', $order->token) }}">{{ $order->get_title() }}</a>
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap">{{ $order->calc_quantity() }}</td>
                            <td class="px-3 py-4 whitespace-nowrap">{{ config('cart.currency') }}{{ $order->calc_price() }}</td>
                            <td class="px-3 py-4 whitespace-nowrap">
                              <span class="badge bg-{{ $order->status_class() }} text-sm font-light px-2.5 py-0.5 rounded-full">{{ $order->status_title() }}</span>
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
          <div
            class="px-6 py-3">
            <div class="flex items-center justify-center px-6 py-3">
              {!! $orders->links() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</x-profile.layout>
