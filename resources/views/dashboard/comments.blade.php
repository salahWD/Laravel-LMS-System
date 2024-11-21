@extends('dashboard.layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @php $page_title = config("app.name") . " | " . __('Manage Comments') @endphp
@endsection

@section('styles')
  @vite('resources/css/dashboard/style.css')
@endsection

@section('content')
  <div class="container">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
      <div class="d-block mb-4 mb-md-0">
        <x-breadcrumb page="comments" is-route=1 url="comments_manage" />
        <h2 class="h4">{{ __('All Comments') }}</h2>
        <p class="mb-0">{{ __('manage all comments from one place.') }}</p>
      </div>
    </div>
    <div class="table-settings mb-4">
      <div class="row align-items-center justify-content-between">
        <div class="col col-md-6 col-lg-3 col-xl-4">
          <div class="input-group me-2 me-lg-3 fmxw-400">
            <span class="input-group-text">
              <svg class="icon icon-xs" x-description="Heroicon name: solid/search" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd"
                  d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                  clip-rule="evenodd"></path>
              </svg>
            </span>
            <input type="text" class="form-control" placeholder="{{ __('Search Comments') }}">
          </div>
        </div>
        <div class="col-4 col-md-2 col-xl-1 ps-md-0 text-end">
          <div class="dropdown">
            <button class="btn btn-link text-dark dropdown-toggle dropdown-toggle-split m-0 p-1" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <svg class="icon icon-sm" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd"
                  d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                  clip-rule="evenodd"></path>
              </svg>
              <span class="visually-hidden">{{ __('Toggle Dropdown') }}</span>
            </button>
            <div class="dropdown-menu dropdown-menu-xs dropdown-menu-end pt-0 pb-0">
              <a class="dropdown-item fw-bold disabled bg-gray-200">Rows: {{ config('settings.tables_row_count') }}</a>
              <a class="dropdown-item rounded-bottom" href="{{ route('dashboard_settings') }}">{{ __('change') }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card card-body border-0 shadow table-wrapper table-responsive overflow-visible">
      <table class="table table-hover">
        <thead>
          <tr>
            <th class="border-gray-200">{{ __('Auther') }}</th>
            <th class="border-gray-200">{{ __('Comment') }}</th>
            <th class="border-gray-200">{{ __('Response To') }}</th>
            <th class="border-gray-200">{{ __('Submitted On') }}</th>
            <th class="border-gray-200">{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody id="comments-table">
          @foreach ($comments as $comment)
            <!-- Item -->
            <tr class="fw-normal comment-row" id="comment-{{ $comment->id }}" data-id="{{ $comment->id }}">
              <td class="p-1">
                <div class="d-flex flex-column align-items-center">
                  <img class="rounded profile-pick" style="object-fit: contain;" src="{{ $comment->user->image_url() }}"
                    alt="{{ __('user image') }}">
                  {{ $comment->user->username }}
                </div>
              </td>
              <td class="wrap">
                {{ $comment->text }}
              </td>
              <td class="wrap">
                {{ $comment->response_to() }}
              </td>
              <td>
                {{ $comment->created_at->format('Y-m-d ga') }}
              </td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-sm btn-primary approve-btn" data-id="{{ $comment->id }}">
                    <span class="fa w-22px">
                      <svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z">
                        </path>
                      </svg>
                    </span>
                    {{ __('Approve') }}
                  </button>
                  <hr class="my-0">
                  <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $comment->id }}">
                    <span class="fa w-22px">
                      <svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                        </path>
                      </svg>
                    </span>
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="card-footer px-3 border-0 d-flex flex-column flex-lg-row align-items-center justify-content-between">
        {!! $comments->links() !!}
      </div>
    </div>
  </div>
@endsection

@section('jslibs')
  <script src="{{ url('libs/dashboard/sweetalert2.all.min.js') }}"></script>
@endsection

@section('scripts')
  <script>
    const page_id = {{ $comments->currentPage() == 0 ? 1 : $comments->currentPage() }};
    const table = $("#comments-table");

    $(".comment-row .approve-btn").each(function() {
      $(this).on("click", function() {
        commentApprove($(this))
      })
    })
    $(".comment-row .delete-btn").each(function() {
      $(this).on("click", function() {
        commentDelete($(this))
      })
    })

    function commentApprove(el) {
      $.ajax(`{{ url('app-request/comments') }}/${ el.data("id") }/approve`, {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
          page: page_id,
        },
        success: function(data) {
          if (data.result) {
            $("#comment-" + el.data("id")).fadeOut(300, function() {
              $(this).remove()
              if (data.next_comment) {
                insertComment(data.next_comment)
              }
            })
          }
        },
        error: (error) => console.log(error)
      })
    }

    function commentDelete(el) {

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
            const Url = `{{ url('app-request/comments') }}/${ el.data("id") }/delete`;
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
            $("#comment-" + el.data("id")).fadeOut(300, function() {
              $(this).remove()
              if (result.value.next_comment) {
                insertComment(result.value.next_comment)
              }
            })
          }
        }
      });

    }

    function insertComment(data) {
      table.append(`
        <tr class="fw-normal comment-row" id="comment-${ data.id }" data-id="${ data.id }">
          <td class="p-1">
            <div class="d-flex flex-column align-items-center">
              <img class="rounded profile-pick" style="object-fit: contain;" src="${ data.image }" alt="{{ __('user image') }}">
              ${ data.username }
            </div>
          </td>
          <td class="wrap">
            ${ data.text }
          </td>
          <td class="wrap">
            ${ data.response_to }
          </td>
          <td>
            ${ data.created_at }
          </td>
          <td>
            <div class="btn-group">
              <button class="btn btn-sm btn-primary approve-btn" id="comment-${ data.id }-approve" data-id="${ data.id }">
                <span class="fa w-22px">
                  <svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"></path>
                  </svg>
                </span>
                {{ __('Approve') }}
              </button>
              <hr class="my-0">
              <button class="btn btn-sm btn-danger delete-btn" id="comment-${ data.id }-delete" data-id="${ data.id }">
                <span class="fa w-22px">
                  <svg data-slot="icon" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"></path>
                  </svg>
                </span>
              </button>
            </div>
          </td>
        </tr>
      `)
      $(`#comment-${ data.id }-approve`).on("click", function() {
        commentApprove($(this))
      })
      $(`#comment-${ data.id }-delete`).on("click", function() {
        commentDelete($(this))
      })
    }
  </script>
@endsection
