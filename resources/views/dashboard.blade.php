@extends('dashboard.layout')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('Dashboard') @endphp
@endsection

@section('styles')
  <style>
    .ct-series-a .ct-bar:nth-child(1) {
      stroke: #1f2937
    }

    .ct-series-a .ct-bar:nth-child(2) {
      stroke: #e5e7eb
    }

    .ct-series-a .ct-bar:nth-child(3) {
      stroke: #f0bc74
    }

    .ct-series-a .ct-bar:nth-child(4) {
      stroke: #2361ce
    }

    .ct-series-a .ct-bar:nth-child(5) {
      stroke: #e11d48
    }
  </style>
@endsection

@section('content')
  <div class="container">
    <div class="py-4">
      <div class="dropdown">
        <button class="btn btn-gray-800 d-inline-flex align-items-center me-2 dropdown-toggle" data-bs-toggle="dropdown"
          aria-haspopup="true" aria-expanded="false">
          <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          {{ __('New Item') }}
        </button>
        <div class="dropdown-menu dashboard-dropdown dropdown-menu-start mt-2 py-1">
          <a class="dropdown-item d-flex align-items-center" href="{{ route('user_create') }}">
            <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path
                d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z">
              </path>
            </svg>
            {{ __('Add User') }}
          </a>
          <a class="dropdown-item d-flex align-items-center" href="{{ route('article_create') }}">
            <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path
                d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z">
              </path>
            </svg>
            {{ __('Write An Article') }}
          </a>
          {{-- <a class="dropdown-item d-flex align-items-center" href="#">
            <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path
                d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z">
              </path>
              <path d="M9 13h2v5a1 1 0 11-2 0v-5z"></path>
            </svg>
            {{ __('Upload Files') }}
          </a>
          <a class="dropdown-item d-flex align-items-center" href="#">
            <svg class="dropdown-icon text-gray-400 me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"></path>
            </svg>
            {{ __('Preview Security') }}
          </a>
          <div role="separator" class="dropdown-divider my-1"></div>
          <a class="dropdown-item d-flex align-items-center" href="#">
            <svg class="dropdown-icon text-danger me-2" fill="currentColor" viewBox="0 0 20 20"
              xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd"
                d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                clip-rule="evenodd"></path>
            </svg>
            {{ __('Upgrade to Pro') }}
          </a> --}}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body">
            <div class="row d-block d-xl-flex align-items-center">
              <div
                class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0">
                  <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z">
                    </path>
                  </svg>
                </div>
                <div class="d-sm-none">
                  <h2 class="h5">{{ __('Students') }}</h2>
                  <h3 class="fw-extrabold mb-1">{{ $students_count }}</h3>
                </div>
              </div>
              <div class="col-12 col-xl-7 px-xl-0">
                <div class="d-none d-sm-block">
                  <h2 class="h6 text-gray-400 mb-0">{{ __('Students') }}</h2>
                  <h3 class="fw-extrabold mb-2">{{ $students_count }}</h3>
                </div>
                <small class="d-flex align-items-center text-gray-500">
                  {{ \Carbon\Carbon::now()->subMonths(1)->format('M d') }} - {{ \Carbon\Carbon::now()->format('M d') }}
                </small>
                <div class="small d-flex mt-1">
                  <div>Since last month <svg class="icon icon-xs text-success" fill="currentColor" viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path>
                    </svg><span class="text-success fw-bolder">22%</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body">
            <div class="row d-block d-xl-flex align-items-center">
              <div
                class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                <div class="icon-shape icon-shape-secondary rounded me-4 me-sm-0">
                  <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                      d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                      clip-rule="evenodd"></path>
                  </svg>
                </div>
                <div class="d-sm-none">
                  <h2 class="fw-extrabold h5">{{ __('Revenue') }}</h2>
                  <h3 class="mb-1">$ ???</h3>
                </div>
              </div>
              <div class="col-12 col-xl-7 px-xl-0">
                <div class="d-none d-sm-block">
                  <h2 class="h6 text-gray-400 mb-0">{{ __('Revenue') }}</h2>
                  <h3 class="fw-extrabold mb-2">$ ???</h3>
                </div>
                <small class="d-flex align-items-center text-gray-500">
                  {{ \Carbon\Carbon::now()->subMonths(1)->format('M d') }} - {{ \Carbon\Carbon::now()->format('M d') }}
                </small>
                <div class="small d-flex mt-1">
                  <div>Since last month <svg class="icon icon-xs text-danger" fill="currentColor" viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                    </svg><span class="text-danger fw-bolder">?%</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-xl-4 mb-4">
        <div class="card border-0 shadow">
          <div class="card-body">
            <div class="row d-block d-xl-flex align-items-center">
              <div
                class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                <div class="icon-shape icon-shape-tertiary rounded me-4 me-sm-0">
                  <svg class="icon" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                      d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd"></path>
                  </svg>
                </div>
                <div class="d-sm-none">
                  <h2 class="fw-extrabold h5"> {{ __('Bounce Rate') }}</h2>
                  <h3 class="mb-1">{{ $totalBounceRate * 100 ?? '47.6' }}%</h3>
                </div>
              </div>
              <div class="col-12 col-xl-7 px-xl-0">
                <div class="d-none d-sm-block">
                  <h2 class="h6 text-gray-400 mb-0"> {{ __('Bounce Rate') }}</h2>
                  <h3 class="fw-extrabold mb-2">{{ $totalBounceRate * 100 ?? '47.6' }}%</h3>
                </div>
                <small class="text-gray-500">
                  {{ __('Feb 1 - Apr 1') }}
                </small>
                <div class="small d-flex mt-1">
                  <div>Since last month <svg class="icon icon-xs text-success" fill="currentColor" viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path>
                    </svg><span class="text-success fw-bolder">4%</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-12 col-xl-8">
        <div class="row">
          <div class="col-12 mb-4">
            <div class="card border-0 shadow">
              <div class="card-header">
                <div class="row align-items-center">
                  <div class="col">
                    <h2 class="fs-5 fw-bold mb-0">{{ __('Most Page visits') }}</h2>
                  </div>
                  <div class="col text-end">
                    <a href="https://analytics.google.com/analytics/web/"
                      class="btn btn-sm btn-primary">{{ __('Go To Google Analytics') }}</a>
                  </div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th class="border-bottom" scope="col">{{ __('Page name') }}</th>
                      <th class="border-bottom" scope="col">{{ __('Page Views') }}</th>
                      <th class="border-bottom" scope="col">{{ __('Avg Time Spent') }}</th>
                      <th class="border-bottom" scope="col">{{ __('Bounce rate') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($topPages as $data)
                      <tr>
                        <th class="text-gray-900" scope="row">
                          {{ $data['pagePath'] ?? 'N/A' }}
                        </th>
                        <td class="fw-bolder text-gray-500">
                          {{ $data['pageViews'] ?? 'N/A' }}
                        </td>
                        <td class="fw-bolder text-gray-500">
                          {{ $data['avgTimeSpent'] ?? 'N/A' }}s
                        </td>
                        <td class="fw-bolder text-gray-500">
                          <div class="d-flex">
                            {{ $data['bounceRate'] * 100 ?? 'N/A' }}%
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          {{--
            <div class="col-12 col-xxl-6 mb-4">
              <div class="card border-0 shadow">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                  <h2 class="fs-5 fw-bold mb-0">{{ __('Team members') }}</h2>
                  <a href="#" class="btn btn-sm btn-primary">{{ __('See all') }}</a>
                </div>
                <div class="card-body">
                  <ul class="list-group list-group-flush list my--3">
                    <li class="list-group-item px-0">
                      <div class="row align-items-center">
                        <div class="col-auto">
                          <!-- Avatar -->
                          <a href="#" class="avatar">
                            <img class="rounded" alt="{{ __('Image placeholder') }}"
                              src="../../images/team/profile-picture-1.jpg">
                          </a>
                        </div>
                        <div class="col-auto ms--2">
                          <h4 class="h6 mb-0">
                            <a href="#">{{ __('Chris Wood') }}</a>
                          </h4>
                          <div class="d-flex align-items-center">
                            <div class="bg-success dot rounded-circle me-1"></div>
                            <small>{{ __('Online') }}</small>
                          </div>
                        </div>
                        <div class="col text-end">
                          <a href="#" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
                            <svg class="icon icon-xxs me-2" fill="currentColor" viewBox="0 0 20 20"
                              xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Invite') }}
                          </a>
                        </div>
                      </div>
                    </li>
                    <li class="list-group-item px-0">
                      <div class="row align-items-center">
                        <div class="col-auto">
                          <!-- Avatar -->
                          <a href="#" class="avatar">
                            <img class="rounded" alt="{{ __('Image placeholder') }}"
                              src="../../images/team/profile-picture-2.jpg">
                          </a>
                        </div>
                        <div class="col-auto ms--2">
                          <h4 class="h6 mb-0">
                            <a href="#">{{ __('Jose Leos') }}</a>
                          </h4>
                          <div class="d-flex align-items-center">
                            <div class="bg-warning dot rounded-circle me-1"></div>
                            <small>{{ __('In a appointment') }}</small>
                          </div>
                        </div>
                        <div class="col text-end">
                          <a href="#" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
                            <svg class="icon icon-xxs me-2" fill="currentColor" viewBox="0 0 20 20"
                              xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Message') }}
                          </a>
                        </div>
                      </div>
                    </li>
                    <li class="list-group-item px-0">
                      <div class="row align-items-center">
                        <div class="col-auto">
                          <!-- Avatar -->
                          <a href="#" class="avatar">
                            <img class="rounded" alt="{{ __('Image placeholder') }}"
                              src="../../images/team/profile-picture-3.jpg">
                          </a>
                        </div>
                        <div class="col-auto ms--2">
                          <h4 class="h6 mb-0">
                            <a href="#">{{ __('Bonnie Green') }}</a>
                          </h4>
                          <div class="d-flex align-items-center">
                            <div class="bg-danger dot rounded-circle me-1"></div>
                            <small>{{ __('Offline') }}</small>
                          </div>
                        </div>
                        <div class="col text-end">
                          <a href="#" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
                            <svg class="icon icon-xxs me-2" fill="currentColor" viewBox="0 0 20 20"
                              xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Message') }}
                          </a>
                        </div>
                      </div>
                    </li>
                    <li class="list-group-item px-0">
                      <div class="row align-items-center">
                        <div class="col-auto">
                          <!-- Avatar -->
                          <a href="#" class="avatar">
                            <img class="rounded" alt="{{ __('Image placeholder') }}"
                              src="../../images/team/profile-picture-4.jpg">
                          </a>
                        </div>
                        <div class="col-auto ms--2">
                          <h4 class="h6 mb-0">
                            <a href="#">{{ __('Neil Sims') }}</a>
                          </h4>
                          <div class="d-flex align-items-center">
                            <div class="bg-danger dot rounded-circle me-1"></div>
                            <small>{{ __('Offline') }}</small>
                          </div>
                        </div>
                        <div class="col text-end">
                          <a href="#" class="btn btn-sm btn-secondary d-inline-flex align-items-center">
                            <svg class="icon icon-xxs me-2" fill="currentColor" viewBox="0 0 20 20"
                              xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd"
                                d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
                                clip-rule="evenodd"></path>
                            </svg>
                            {{ __('Message') }}
                          </a>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="col-12 col-xxl-6 mb-4">
              <div class="card border-0 shadow">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                  <h2 class="fs-5 fw-bold mb-0">{{ __('Progress track') }}</h2>
                  <a href="#" class="btn btn-sm btn-primary">{{ __('See tasks') }}</a>
                </div>
                <div class="card-body">
                  <!-- Project 1 -->
                  <div class="row mb-4">
                    <div class="col-auto">
                      <svg class="icon icon-sm text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd"
                          d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                          clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div class="col">
                      <div class="progress-wrapper">
                        <div class="progress-info">
                          <div class="h6 mb-0">{{ __('Rocket - SaaS Template') }}</div>
                          <div class="small fw-bold text-gray-500"><span>75 %</span></div>
                        </div>
                        <div class="progress mb-0">
                          <div class="progress-bar bg-success" role="progressbar" aria-valuenow="75" aria-valuemin="0"
                            aria-valuemax="100" style="width: 75%;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Project 2 -->
                  <div class="row align-items-center mb-4">
                    <div class="col-auto">
                      <svg class="icon icon-sm text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd"
                          d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                          clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div class="col">
                      <div class="progress-wrapper">
                        <div class="progress-info">
                          <div class="h6 mb-0">{{ __('Themesberg - Design System') }}</div>
                          <div class="small fw-bold text-gray-500"><span>60 %</span></div>
                        </div>
                        <div class="progress mb-0">
                          <div class="progress-bar bg-success" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                            aria-valuemax="100" style="width: 60%;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Project 3 -->
                  <div class="row align-items-center mb-4">
                    <div class="col-auto">
                      <svg class="icon icon-sm text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd"
                          d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                          clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div class="col">
                      <div class="progress-wrapper">
                        <div class="progress-info">
                          <div class="h6 mb-0">{{ __('Homepage Design in Figma') }}</div>
                          <div class="small fw-bold text-gray-500"><span>45 %</span></div>
                        </div>
                        <div class="progress mb-0">
                          <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0"
                            aria-valuemax="100" style="width: 45%;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Project 4 -->
                  <div class="row align-items-center mb-3">
                    <div class="col-auto">
                      <svg class="icon icon-sm text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd"
                          d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                          clip-rule="evenodd"></path>
                      </svg>
                    </div>
                    <div class="col">
                      <div class="progress-wrapper">
                        <div class="progress-info">
                          <div class="h6 mb-0">{{ __('Backend for Themesberg v2') }}</div>
                          <div class="small fw-bold text-gray-500"><span>34 %</span></div>
                        </div>
                        <div class="progress mb-0">
                          <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="34" aria-valuemin="0"
                            aria-valuemax="100" style="width: 34%;"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          --}}
        </div>
      </div>
      <div class="col-12 col-xl-4">
        <div class="col-12 px-0 mb-4">
          <div class="card border-0 shadow">
            <div class="card-header d-flex flex-row align-items-center flex-0 border-bottom">
              <div class="d-block">
                <div class="h6 fw-normal text-gray mb-2">{{ __('Total Users') }}</div>
                <h2 class="h3 fw-extrabold">{{ $totalUsers ?? $students_count }}</h2>
                <div class="small mt-2">
                  <svg class="icon icon-xs text-success" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                      d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                      clip-rule="evenodd"></path>
                  </svg>
                  <span class="text-success fw-bold">{{ $totalBounceRate * 100 ?? '18.2' }}%</span>
                </div>
              </div>
              <div class="d-block ms-auto">
                @foreach ($topCountries as $i => $country)
                  <div class="d-flex align-items-center text-end mb-2">
                    <span
                      class="dot rounded-circle bg-{{ ['gray-800', 'gray-200', 'secondary', 'info', 'danger'][$i] }} me-2"></span>
                    <span class="fw-normal small">{{ $country['country'] }}</span>
                  </div>
                @endforeach
              </div>
            </div>
            <div class="card-body p-2">
              <div class="ct-chart-ranking ct-golden-section ct-series-a"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card bg-yellow-100 border-0 shadow">
          <div class="card-header d-sm-flex flex-row align-items-center flex-0">
            <div class="d-block mb-3 mb-sm-0">
              <div class="fs-5 fw-normal mb-2">{{ __('Traffic Value') }}</div>
              <h2 class="fs-3 fw-extrabold">{{ $totalUsers }} {{ __('visitor') }}</h2>
            </div>
          </div>
          <div class="card-body p-2">
            <div class="line-chart ct-chart-sales-value ct-double-octave ct-series-g"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('jslibs')
  <script>
    var dailyTraffic = JSON.parse(
      `{!! json_encode([array_column($dailyTraffic, 'date'), array_column($dailyTraffic, 'sessions')]) !!}`);
    var topCountries = JSON.parse(
      `{!! json_encode([array_column($topCountries, 'country'), array_column($topCountries, 'percentage')]) !!}`);
  </script>
  <script src="{{ url('libs/dashboard/smooth-scroll.polyfills.min.js') }}"></script>
  <script src="{{ url('libs/dashboard/sweetalert2.all.min.js') }}"></script>
  <script src="{{ url('libs/dashboard/notyf.min.js') }}"></script>
  <script src="{{ url('libs/dashboard/simplebar.min.js') }}"></script>
  <script src="{{ url('libs/dashboard/chartist.min.js') }}"></script>
  <script src="{{ url('libs/dashboard/chartist-plugin-tooltip.min.js') }}"></script>
@endsection
