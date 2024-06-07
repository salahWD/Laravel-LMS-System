@extends('dashboard.layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php $page_title = config("app.name") . " | " . __('Manage Tests') @endphp
@endsection

@section('styles')
  @vite("resources/css/dashboard/style.css")
@endsection

@section('content')

  <div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <x-breadcrumb page="test" is-route=1 url="tests_manage" />
        <h2 class="h4">{{ __('All Tests') }}</h2>
        <p class="mb-0">{{ __('manage all tests from one place.') }}</p>
      </div>
      <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route("test_create") }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
          <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
          {{ __('New Test') }}
        </a>
      </div>
    </div>
    <div class="table-settings mb-4">
      <div class="row align-items-center justify-content-between">
          <div class="col col-md-6 col-lg-3 col-xl-4">
              <div class="input-group me-2 me-lg-3 fmxw-400">
                  <span class="input-group-text">
                      <svg class="icon icon-xs" x-description="Heroicon name: solid/search" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                          <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                      </svg>
                  </span>
                  <input type="text" class="form-control" placeholder="{{ __('Search Tests') }}">
              </div>
          </div>
          <div class="col-4 col-md-2 col-xl-1 ps-md-0 text-end">
              <div class="dropdown">
                  <button class="btn btn-link text-dark dropdown-toggle dropdown-toggle-split m-0 p-1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path></svg>
                      <span class="visually-hidden">{{ __('Toggle Dropdown') }}</span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-xs dropdown-menu-end pb-0">
                      <span class="small ps-3 fw-bold text-dark">{{ __('Show') }}</span>
                      <a class="dropdown-item d-flex align-items-center fw-bold" href="#">10 <svg class="icon icon-xxs ms-auto" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></a>
                      <a class="dropdown-item fw-bold" href="#">20</a>
                      <a class="dropdown-item fw-bold rounded-bottom" href="#">30</a>
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="card card-body border-0 shadow table-wrapper table-responsive overflow-visible">
      <table class="table table-hover" id="tests-table">
        <thead>
          <tr>
            <th class="border-gray-200">{{ __('Image') }}</th>
            <th class="border-gray-200">{{ __('Title') }}</th>
            <th class="border-gray-200">{{ __('Course') }}</th>
            <th class="border-gray-200">{{ __('Order') }}</th>
            <th class="border-gray-200">{{ __('Status') }}</th>
            <th class="border-gray-200">{{ __('Publish Date') }}</th>
            <th class="border-gray-200">{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($tests as $test)
            <!-- Item -->
            <tr class="fw-normal test-row" id="test-{{ $test->id }}" data-id="{{ $test->id }}">
              <td class="p-1" style="vertical-align:middle">
                <a href="{{ route("test_build", $test->id) }}">
                  <img class="rounded-1 profile-pick" src="{{ $test->image_url() }}" alt="">
                </a>
              </td>
              <td class="text-end-dots pe-0 wrap">
                <a href="{{ route("test_build", $test->id) }}">{{ $test->title }}</a>
              </td>
              <td>{{ $test->course?->title ?? "- - -" }}</td>
              <td>{{ $test->order ?? "-" }}</td>
              <td><span class="fa w-16px">{!! $test->status_icon() !!}</span></td>
              <td>{{ $test->get_date() }}</td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-link text-dark dropdown-toggle dropdown-toggle-split m-0 py-0 p-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="icon icon-sm">
                      <span class="fa fa-ellipsis-h icon-dark"></span>
                    </span>
                    <span class="visually-hidden">{{ __('Toggle Dropdown') }}</span>
                  </button>
                  <div class="dropdown-menu py-0">
                    <a class="dropdown-item" href="{{ route("test_build", $test->id) }}">
                      <span class="fa me-1">
                        <svg class="me-2" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                        </svg>
                      </span>
                      {{ __('Edit') }}
                    </a>
                    <a class="dropdown-item" href="{{ route("test_report", $test->id) }}">
                      <span class="fa me-1">
                        <svg class="me-2" data-slot="icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path clip-rule="evenodd" fill-rule="evenodd" d="M2.25 13.5a8.25 8.25 0 0 1 8.25-8.25.75.75 0 0 1 .75.75v6.75H18a.75.75 0 0 1 .75.75 8.25 8.25 0 0 1-16.5 0Z"></path>
                          <path clip-rule="evenodd" fill-rule="evenodd" d="M12.75 3a.75.75 0 0 1 .75-.75 8.25 8.25 0 0 1 8.25 8.25.75.75 0 0 1-.75.75h-7.5a.75.75 0 0 1-.75-.75V3Z"></path>
                        </svg>
                      </span>
                      {{ __('Stats') }}
                    </a>
                    <hr class="my-0">
                    <button class="dropdown-item text-danger rounded-bottom delete-btn" id="test-{{ $test->id }}-delete" data-id="{{ $test->id }}">
                      <span class="fa me-2">
                        <svg class="me-2" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                        </svg>
                      </span>
                      {{ __('Remove') }}
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
        {!! $tests->links() !!}
      </div>
    </div>
  </div>

@endsection


@section('jslibs')
  <script src="{{ url("libs/dashboard/sweetalert2.all.min.js") }}"></script>
@endsection

@section('scripts')
  <script>
    const page_id = {{ $tests->currentPage() == 0 ? 1: $tests->currentPage() }};
    const table = $("#tests-table");

    $(".test-row .delete-btn").each(function () {
      $(this).on("click", function () {
        lectureDelete($(this))
      })
    })

    function lectureDelete(el) {

      Swal.fire({
        title: "Are You Sure ?",
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-primary me-4'
        },
        reverseButtons: true,
        buttonsStyling: false,
        showCancelButton: true,
        confirmButtonText: "Delete",
        preConfirm: async (violation) => {
          try {
            const Url = `{{ url("app-request/tests") }}/${ el.data("id") }/delete`;
            let data = new FormData()
            data.append("page", page_id)
            const response = await fetch(Url, {
              method: "POST",
              body: data,
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            })
            if (!response.ok) {
              return Swal.showValidationMessage(`
                ${JSON.stringify(await response.json())}
              `)
            }
            return response.json()
          } catch (error) {
            Swal.showValidationMessage(`
              Request failed: ${error}
            `)
          }
        },
      }).then((result) => {
        if (result.isConfirmed) {
          if (result.value.result) {
            $('#test-' + el.data("id")).fadeOut(300, function () {
              $(this).remove()
              if (result.value.next_test) {
                insertCategory(result.value.next_test)
              }
            })
          }
        }
      });

    }

    function insertCategory(data) {
      table.append(`
        <tr class="fw-normal test-row" id="test-${ data.id }" data-id="${ data.id }">
          <td class="p-1" style="vertical-align:middle">
            <a href="{{ url("dashboard/tests") }}/${data.id}">
              <img class="rounded-1 profile-pick" src="${ data.image }" alt="">
            </a>
          </td>
          <td class="text-end-dots pe-0 wrap">
            <a href="{{ url("dashboard/tests") }}/${data.id}">${ data.title }</a>
          </td>
          <td>${ data.course_title ? data.course_title : "- - -" }</td>
          <td><span class="fa w-16px">${ data.status_icon }</span></td>
          <td>${ data.date }</td>
          <td>
            <div class="btn-group">
              <button class="btn btn-link text-dark dropdown-toggle dropdown-toggle-split m-0 py-0 p-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="icon icon-sm">
                  <span class="fa fa-ellipsis-h icon-dark"></span>
                </span>
                <span class="visually-hidden">{{ __('Toggle Dropdown') }}</span>
              </button>
              <div class="dropdown-menu py-0">
                <a class="dropdown-item" href="{{ url("dashboard/tests") }}/${data.id}">
                  <span class="fa me-1">
                    <svg class="me-2" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"></path>
                    </svg>
                  </span>
                  {{ __('Edit') }}
                </a>
                <hr class="my-0">
                <button class="dropdown-item text-danger rounded-bottom delete-btn" id="test-${ data.id }-delete" data-id="${ data.id }">
                  <span class="fa me-2">
                    <svg class="me-2" data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                    </svg>
                  </span>
                  {{ __('Remove') }}
                </button>
              </div>
            </div>
          </td>
        </tr>
      `)
      $(`#test-${ data.id }-delete`).on("click", function () {
        lectureDelete($(this))
      })
    }

  </script>
@endsection
