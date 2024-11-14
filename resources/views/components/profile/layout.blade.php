@props([
    'meta' => null,
])
@extends('layout')

@section('meta')
  @if ($meta != null)
    {{ $meta }}
  @else
    <title>{{ __(config('app.name')) . ' | ' . __('Settings') }}</title>
  @endif
@endsection

@section('styles')
  <!-- STYLES -->
  @vite(['resources/css/home.css', 'resources/css/app.css'])
  @vite(['public/js/app.js'])
@endsection

@section('content')
  <div class="bg-white w-full flex flex-col gap-5 px-3 md:px-16 lg:px-28 md:flex-row text-[#161931]">
    <aside class="hidden py-4 md:w-1/3 lg:w-1/4 md:block">
      <div class="sticky flex flex-col gap-2 p-4 text-sm border-r border-indigo-100 top-12">

        <h2 class="pl-3 mb-4 text-2xl font-semibold">Settings</h2>

        <a href="{{ route('profile.edit') }}"
          class="flex items-center px-3 py-2.5 bg-white hover:text-blue-900 !border !border-transparent hover:!border-gray-800 rounded-full @if (request()->segment(3) == 'profile') !border-gray-800 text-blue-900 font-bold @else font-semibold @endif">
          Pubic Profile
        </a>
        <a href="{{ route('profile.settings') }}"
          class="flex items-center px-3 py-2.5 bg-white hover:text-blue-900 !border !border-transparent hover:!border-gray-800 rounded-full @if (request()->segment(3) == 'settings') !border-gray-800 text-blue-900 font-bold @else font-semibold @endif">
          Account Settings
        </a>
        <a href="{{ route('profile.certificates') }}"
          class="flex items-center px-3 py-2.5 bg-white hover:text-blue-900 !border !border-transparent hover:!border-gray-800 rounded-full @if (request()->segment(3) == 'certificates') !border-gray-800 text-blue-900 font-bold @else font-semibold @endif">
          My Certificates
        </a>
        <a href="{{ route('profile.meetings') }}"
          class="flex items-center px-3 py-2.5 bg-white hover:text-blue-900 !border !border-transparent hover:!border-gray-800 rounded-full @if (request()->segment(3) == 'meetings') !border-gray-800 text-blue-900 font-bold @else font-semibold @endif">
          My Meetings
        </a>

        @if(config("settings.shop_status"))
          <a href="{{ route('my_orders') }}"
            class="flex items-center px-3 py-2.5 bg-white hover:text-blue-900 !border !border-transparent hover:!border-gray-800 rounded-full @if (request()->segment(3) == 'orders') !border-gray-800 text-blue-900 font-bold @else font-semibold @endif">
            My Orders
          </a>
        @endif
      </div>
    </aside>
    <main class="w-full min-h-screen py-1 md:w-2/3 lg:w-3/4">
      <div class="p-2 md:p-4">

        {{ $slot }}

      </div>
    </main>
  </div>
@endsection


@section('scripts')
  <script src="{{ url('js/jquery.min.js') }}"></script>
@endsection
