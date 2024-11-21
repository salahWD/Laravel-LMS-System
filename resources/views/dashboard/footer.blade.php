<footer class="bg-white rounded shadow p-5 mb-4 mt-4">
  <div class="row">
    <div class="col-12 col-md-4 col-xl-6 mb-4 mb-md-0">
      <p class="mb-0 text-center text-lg-start">© 2023-<span class="current-year"></span> <a class="text-primary fw-normal"
          href="https://github.com/salahWD" target="_blank">{{ __('Salah Bakhash') }}</a></p>
    </div>
    <div class="col-12 col-md-8 col-xl-6 text-center text-lg-start">
      <!-- List -->
      <ul class="list-inline list-group-flush list-group-borderless text-md-end mb-0">
        <li class="list-inline-item px-0 px-sm-2">
          <a href="{{ route('dashboard_settings') }}">{{ __('Settings') }}</a>
        </li>
        <li class="list-inline-item px-0 px-sm-2">
          <a href="{{ route('home') }}">{{ __('Home') }}</a>
        </li>
      </ul>
    </div>
  </div>
</footer>
