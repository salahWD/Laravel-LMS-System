@extends('dashboard.layout')

@section('meta')
  <meta name="csrf-token" content="{{ CSRF_TOKEN()}}">
@endsection

@section('styles')
  @vite("resources/css/dashboard/test-report.css")
@endsection

@section("content")
  <div class="container pb-5">

    <div class="actions d-flex justify-content-between">
      <div class="reports-types">
        <div class="btn-group" role="group">
          <button type="button" data-page="insight" class="btn page-btn border">{{ __('Insights') }}</button>
          <button type="button" data-page="responses" class="btn page-btn border active btn-white">{{ __('Responses') }}</button>
        </div>
      </div>
      {{-- <div class="date-range">
        <input type="date" class="form-control" id="">
      </div> --}}
    </div>

    <div class="page" id="insight">

      <div class="@if($questions->count() > 0) mb-2 @endif gap-4 d-flex w-100">
        <div class="card flex-shrink-0 flex-grow-1">
          <div class="card-body text-center">
            <h3 class="card-title" id="views-counter">{{ $test->unique_attempts_count() }}</h3>
            <p class="lead">{{ __('Students') }}</p>
          </div>
        </div>
        <div class="card flex-shrink-0 flex-grow-1">
          <div class="card-body text-center">
            <h3 class="card-title" id="responses-counter">{{ $responses }}</h3>
            <p class="lead">{{ __('Submissions') }}</p>
          </div>
        </div>
        <div class="card flex-shrink-0 flex-grow-1">
          <div class="card-body text-center">
            <h3 class="card-title" id="completed-counter">%{{ round($completed / ($responses == 0 ? 1 : $responses) * 100) }}</h3>
            <p class="lead">{{ __('Completion Rate') }}</p>
          </div>
        </div>
      </div>

      <div class="@if($questions->count() > 0) d-none @endif no-date-range-selected card border text-center p-4 mb-3" id="no-date-range">
        {{-- <h3 class="title">{{ __('Select date range') }}</h3> --}}
        <h3 class="title">{{ __('No Reports !!') }}</h3>
        <p class="">{{ __('there is no reports until now') }}.</p>
        {{-- <p class="">{{ __('Please select a date range above in order to load Insights data') }}.</p> --}}
        <img class="img img-floud" src="{{ url("images/no-data-reports.svg") }}" alt="">
      </div>

      <div id="questions" class="d-flex flex-column">
      </div>

    </div>

    <div class="page" id="responses">
      <div class="row">
        <div class="col-4">
          <p><b id="total-responses-count">{{ $responses }}</b> {{ __('total responses')}}</p>
          <div class="card border py-4 px-3">
            <input type="text" id="search-input" placeholder="{{ __('Search By Email') }}" class="form-control">
            <hr>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="select-all-responses">
              <label class="form-check-label" for="select-all-responses">
                {{ __('Select all') }} ({{$test->testAttempts_count ?? 0}})
              </label>
            </div>
            @if ($test->testAttempts->count() > 0)
              <div class="responses">
                <?php $trigger = 1;?>

                @foreach ($test->testAttempts as $submission)
                  <div
                      id="submission-{{ $submission->id }}"
                      {{-- data-name="{{ $submission->getOriginal(app()->getLocale() . "_title") ?? "Unknown" }}" --}}
                      data-name="{{ $submission->get_lead() ?? __("Unknown") }}"
                      data-time="{{ $submission->created_at->format('H:i:s') }}"
                      data-date="{{ $submission->created_at->format('d.m.Y') }}"
                      class="response @if ($trigger) active @endif py-1">
                    <?php $trigger = 0;?>
                    <div class="form-check m-0">
                      <input class="form-check-input submission-check" type="checkbox" data-submission-id="{{ $submission->id }}">
                      <p class="m-0 form-check-label d-block text-primary">
                        {{ $submission->get_lead() ?? __("Unknown") }}
                      </p>
                      <small class="text-secondary">{{ $submission->created_at }}</small>
                    </div>
                  </div>
                @endforeach
              </div>
              <a id="export-link" data-link="{{ route("test_export_data", $test->id) }}" href="{{ route("test_export_data", $test->id) }}" class="btn w-100 mt-3 btn-primary">{{ __('Export') }} <i class="fa fa-export"></i></a>
            @else
              <small class="text-secondary">{{ __('No responses available') }}</small>
            @endif
          </div>
        </div>
        <div class="col-8">
          <p>{{ __('Response preview for') }}: <b id="preview-name">Unknown</b>
            <span class="info">
              <i class="fa fa-calendar"></i>
              <span id="preview-date">07.22.2023</span>
            </span>
            <span class="info">
              <i class="fa fa-clock-o"></i>
              <span id="preview-time">01:08 PM (UTC)</span>
            </span>
          </p>
          <div id="preview-responses" class="card no-responses-card">
            <div class="center">
              <img class="img img-floud" src="{{ url("images/no-data-reports.svg") }}" alt="">
              <h4 class="h5">{{ __('No data available') }}</h4>
              <small class="text-secondary">{{ __('Share your content to start collecting data') }}.</small>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
@endsection


@section('scripts')
  <script>
    const lang = "{{ app()->getLocale() }}";
    const getResponseUrl = "{{ url(app()->getLocale() . '/app-request/attempts/') }}";
    const __ = {
      "Continued": "{{ __('Continued') }}",
      "Submissions": "{{ __('Submissions') }}",
      "Answered": "{{ __('Answered') }}",
      "Answers": "{{ __('Answers') }}",
      "Responses": "{{ __('Responses') }}",
      "Shown Result:": "{{ __('Shown Result:') }}",
      "Result": "{{ __('Result') }}",
    };
  </script>

  <script type="text/javascript" src="{{ url("js/report.js") }}"></script>
  <script>

    let selectedItems = [];
    $(".responses .response .submission-check").each(function () {
      $(this).on("input", function () {

        if ($(this).prop("checked") == true) {
          selectedItems.push($(this).data("submission-id"));
        }else {
          const index = selectedItems.indexOf($(this).data("submission-id"));
          if (index > -1) {
            selectedItems.splice(index, 1);
          }
        }

        $("#export-link").attr("href", $("#export-link").data("link") + "?exports=" + selectedItems.toString());
      });
    })
    $(".responses .response .form-check").each(function () {
      $(this).click(function () {

        let id = $(this).find(".submission-check").first().data("submission-id");
        activeResponse(id);
        $("#submission-" + id).addClass("active");
        $("#submission-" + id).siblings().removeClass("active");

      });
    })

    let activeResponseEl = $(".responses .response.active .submission-check").first();
    activeResponseEl.click();

    // activeResponse(activeResponseEl.data("submission-id"));

    $(".page-btn").each(function () {
      $(this).click(function () {
        let btn = $(this);
        btn.prop("disabled", true);
        setTimeout(() => {
          btn.prop("disabled", false);
        }, 1000);
        activePage($(this).data("page"));
      })
    })
    activePage("responses");

    @foreach ($questions as $i => $question)

      createQuestion(
          {{ $i + 1 }},
          "{{ $question->title }}",
          {{ $question->type }},
          {{ $question->test_entries_count }},
          @if ($question->type == 5)
          JSON.parse('{!! $question->answers()->withCount("entries")->get()->makeHidden(["text"]) !!}')
          @else
          JSON.parse('{!! $question->answers()->withCount("entries")->get() !!}')
          @endif
      );

    @endforeach

    @if ($result != null)
      createResultStatistics("{{ $result->title }}");
    @endif

    $("#select-all-responses").click(function () {
      if ($(this).prop("checked") == true) {
        $(".responses .response input.form-check-input").prop("checked", true).each(function () {
          selectedItems.push($(this).data("submission-id"));
          $("#export-link").attr("href", $("#export-link").data("link") + "?exports=" + selectedItems.toString());
        });
      }else {
        $(".responses .response input.form-check-input").prop("checked", false).each(function () {
          selectedItems = [];
          $("#export-link").attr("href", $("#export-link").data("link") + "?exports=" + selectedItems.toString());
        });
      }
    });

    $("#export-link").click(function (e) {
      if (selectedItems.length > 0) {
        $("#export-link").attr("href", $("#export-link").data("link") + "?exports=" + selectedItems.toString());
      }else {
        e.preventDefault();
      }
    });

  </script>
@endsection
