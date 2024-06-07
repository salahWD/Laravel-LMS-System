@extends('layout')

@section('meta')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ __(config("app.name")) . " | " . $current_test->title }}</title>
@endsection

@section('styles')
  @vite('resources/css/taking-test.css')
@endsection

@section('content')

<div class="container">

  <div class="test-bg-color"></div>

  <div class="website-logo-container">
    <img class="website-logo" src="{{ url("images/logo.png") }}" alt="logo">
  </div>

  <div class="test-content">
    <div class="questions">

      @if($current_test->has_intro())
        <div class="intro item active" id="test-intro">
          <div class="content text-center">
            <h1 class="title">{{ $current_test->intro_title }}</h1>
            <p class="lead desc">{{ $current_test->intro_description }}</p>

            @if($current_test->has_intro_image())
              <img src="{{ $current_test->intro_image_url() }}" class="img-fluid rounded shadow-lg question-img d-block m-auto">
            @endif
            <button
                class="btn nex-btn btn-primary"
                id="intro-btn"
                data-target="{{ $questions->first() != null ? "question-".$questions->first()->id : "no-result"; }}">{{ $current_test->intro_btn }}</button>
          </div>
        </div>
      @endif

      @foreach($questions as $i => $question)
        <div
            class="question
            {{ ["text-question", "image-question", "form", "media", "equation"][$question->type - 1] }}
            item no-answer-yet @if(!$current_test->has_intro() && $i == 0) active @endif"
            id="question-{{ $question->id }}"
            data-id="{{ $question->id }}"
            data-type="{{ $question->type }}"
            @if ($question->type == 5)
            data-score="{{ $question->answers->first()->score }}"
            @endif
            data-order="{{ $question->order }}"
            style="order: {{ $question->order }}">
          <?php $letter = 'A'; ?>
          <div class="content">
            <h1 class="title question-title">{{ $question->title }}</h1>
            <p class="lead question-desc">{{ $question->description }}</p>

            @if($question->image != null)
              <img src="{{ $question->image_url() }}" class="img-fluid question-img">
            @endif

            @if($question->video != null)
              <div class="embed-responsive embed-responsive-16by9">
                <iframe class="yt-aspect embed-responsive-item" src="{{ $question->video }}"></iframe>
              </div>
            @endif

            @if(in_array($question->type, [1, 2]))

              <div class="answers @if($question->type == 2) img-answers @endif my-4" id="answers-holder-{{$i}}" data-multi="{{ $question->is_multi_select }}">

                @foreach ($question->answers as $x => $answer)

                  <div
                      @if($question->is_multi_select != 1)
                        @if($i >= count($questions) - 1) data-target="end"
                        @else data-target="question-{{ $questions[$i + 1]->id }}" @endif
                      @endif
                      data-score="{{ $answer->score ?? 0}}"
                      data-id="{{ $answer->id }}"
                      id="answer-{{ $answer->id }}"
                      class="answer @if ($question->type == 2) answer card border-0 @else border @endif">
                  <div class="highlight"></div>
                  @if ($question->type == 2)
                    <img class="img card-img-top" src="{{ $answer->image_url() }}" width="200" height="200">
                  @else
                    <div class="answer-letter">{{ $letter++ }}</div>
                  @endif
                  <p class="text m-0">{{ $answer->text }}</p>
                </div>

                @endforeach
              </div>
            @endif

            @if($question->type == 3)

              <form class="form disabled-form" id="fields-holder-{{$i}}">
                @foreach ($question->fields as $x => $field)

                  <div class="input-box">

                    @if($field->type != 13)
                      <label class="form-label" for="form-{{$i}}-input-{{$x}}">{{ $field->label }} @if($field->is_required == 1) * @endif</label>
                    @endif

                    @if(in_array($field->type, [1, 2, 3, 4, 6]))
                      <input
                          {{ $field->is_required == 1 ? "required" : "" }}
                          class="view-input"
                          type="{{ $field->type_name() }}"
                          data-type="{{$field->type}}" data-id="{{ $field->id }}"
                          id="form-{{$i}}-input-{{$x}}"
                          placeholder="{{ $field->placeholder != 'undefined' ? $field->placeholder : __("write here") }}">
                    @elseif($field->type == 5)
                      <textarea
                          {{ $field->is_required == 1 ? "required" : "" }}
                          class="view-input"
                          data-type="{{$field->type}}" data-id="{{ $field->id }}"
                          id="form-{{$i}}-input-{{$x}}"
                          placeholder="{{ $field->placeholder != 'undefined' ? $field->placeholder : __("write here") }}"
                          ></textarea>
                    @elseif($field->type == 7)
                      @foreach($field->options as $y => $option)
                        @if($field->is_multiple_chooseing == 1)
                          <div class="holder">
                            <label class="form-label" for="form-{{$i}}-input-{{$x}}-option-{{$y}}">{{ $option->value }}</label>
                            <input
                              {{ $field->is_required == 1 ? "required" : "" }}
                              name="field[{{$field->id}}][]"
                              value="{{ $option->value }}"
                              class="input-option"
                              data-type="{{$field->type}}" data-id="{{ $field->id }}"
                              id="form-{{$i}}-input-{{$x}}-option-{{$y}}"
                              type="checkbox">
                          </div>
                        @else
                          <div class="holder">
                            <label class="form-label" for="form-{{$i}}-input-{{$x}}-option-{{$y}}">{{ $option->value }}</label>
                            <input
                              {{ $field->is_required == 1 ? "required" : "" }}
                              name="field[{{$field->id}}]"
                              value="{{ $option->value }}"
                              class="input-option d-none"
                              data-type="{{$field->type}}" data-id="{{ $field->id }}"
                              id="form-{{$i}}-input-{{$x}}-option-{{$y}}"
                              type="radio">
                            <span onclick="document.getElementById('form-{{$i}}-input-{{$x}}-option-{{$y}}').click()" class="checkmark" style="border-color: {{$current_test->border_color}}"></span>
                          </div>
                        @endif
                      @endforeach
                    @elseif($field->type == 8)
                      <select class="view-input" data-type="{{$field->type}}" data-id="{{ $field->id }}" id="form-{{$i}}-input-{{$x}}"
                        {{ $field->is_required == 1 ? "required" : "" }}>
                          @foreach($field->options as $option)
                            <option value="{{ $option->id }}">{{ $option->value }}</option>
                          @endforeach
                      </select>
                    @endif

                  </div>

                @endforeach
              </form>
            @endif

            @if($question->type == 5)
              <textarea id="question-{{ $question->id }}-answer" placeholder="{{ __("write your answer here") }}" class="answer_val form-control"></textarea>
            @endif

            @if(!in_array($question->type, [1, 2]) || $question->is_multi_select == 1)

              @if($question->type == 3 || $question->type == 5)
                <div class="btns-row mt-3">
                  <button
                    class="btn btn-primary nex-btn"
                    @if($i >= count($questions) - 1) data-target="end"
                    @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ $question->button_label ?? __("Submit") }}</button>
                  @if($question->is_skippable != null && $question->is_skippable == 1)
                    <button
                      class="btn skip"
                      @if($i >= count($questions) - 1) data-target="end"
                      @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ __("Skip") }}</button>
                  @endif
                  <div class="error-box d-inline-block" style="float: {{ app()->getLocale() == "ar" ? "left" : "right" }}"></div>
                </div>
              @else
                <button
                  class="btn nex-btn btn-primary"
                  @if($i >= count($questions) - 1) data-target="end"
                  @else data-target="question-{{ $questions[$i + 1]->id }}" @endif>{{ $question->button_label ?? __("Submit") }}</button>
              @endif


            @endif

          </div>
        </div>
      @endforeach

      @if(isset($result) && !empty($result))

        <div
            class="result item py-lg-4 px-lg-5 py-3 px-2 m-auto"
            id="result"
            data-min-score="{{ $result->min_score }}"
            data-min-percent="{{ $result->min_percent }}"
            data-min-correct="{{ $result->min_correct_questions }}"
            data-max-attempts="{{ $result->max_attempts }}">
          <div class="content">

            <div class="body d-flex flex-column justify-content-center rounded">
              <h3 class="title mb-4 failed-title">{{ __("Try Again Later") }}</h3>
              <h3 class="title mb-4 success-title">{{ __("Congratulation, You Succeed") }}</h3>
              <div class="score-panel rounded mb-3" id="score-panel">
                <span>{{ __('your total score is') }}</span>:<span id="result-score">??</span>
              </div>
              @if ($current_test->has_certificate())
                <p class="lead success-p mb-3" style="display: none" id="certificate-noti">{{ __("You have obtained a new certificate") }} (<span id="certificate-name">{{ $current_test->certificate->title }}</span>)</p>
              @endif
              <span class="desc">
                {{ $result?->note }}
              </span>
              <div class="d-flex justify-content-center gap-4">
                <a
                  class="btn btn-primary"
                  @if(!empty($current_test->next_item()))
                    @if($current_test->next_item()->is_lecture())
                      href="{{ route("lecture_show", $current_test->next_item()->itemable_id) }}"
                    @else
                      href="{{ route("test_show", $current_test->next_item()->itemable_id) }}"
                    @endif
                  @else
                    href="#"
                  @endif
                  type="button">{{ __('Continue') }}</a>
                <a href="{{ route("test_show", $current_test->id) }}" style="display: none" class="btn btn-danger">{{ __("Try Again") }}</a>
                @if ($current_test->has_certificate())
                  <a href="{{ route("certificate_download", $current_test->certificate->id) }}" style="display: none" id="certificate-download" class="btn btn-success">{{ __("Download Certificate") }}</a>
                @endif
              </div>
            </div>
          </div>
        </div>

      @endif

      <div class="question item" id="no-result" data-type="1">
        <div class="content">
          <div class="body d-flex flex-column justify-content-center rounded">
            <span class="title">No Result ):</span>
            <span class="desc mb-2">these answers has no result</span>
          </div>
        </div>
      </div>


      <div class="watermark website-logo-container @if(app()->getLocale() == "en") flex-row-reverse @endif">
        @if($current_test->can_go_back == 1)
          <div class="steps-btns @if(app()->getLocale() != "en") flex-row-reverse @endif" id="steps-btns">
            <button id="back-ques-btn" class="btn-primary btn border border-secondary opacity-50"><i class="fa fa-solid fa-arrow-left"></i></button>
            <button id="forward-ques-btn" class="btn-primary btn border border-secondary opacity-50"><i class="fa fa-solid fa-arrow-right"></i></button>
          </div>
        @endif
      </div>


    </div>
  </div>
</div>

@endsection

@section('scripts')
  <!-- JAVA SCRIPTS -->
  <script>
    const FormRoute = "{{ route('attempt_form_entry', $current_test->id) }}";
    const AnswersRoute = "{{ route('attempt_answering', $current_test->id) }}";
    const submissionRoute = "{{ route('attempt_create') }}";
    const testId  = "{{ $current_test->id }}";
    const __ = {
      "required fields are not filled": "{{ __('required fields are not filled') }}",
      "your score is less then": "{{ __('your score is less then') }}",
      "try again later": "{{ __('try again later') }}",
      "your overall correct answered questions are less then": "{{ __('your overall correct answered questions are less then') }}",
      "your overall percent is less then": "{{ __('your overall percent is less then') }}",
    };
  </script>
  {{-- @vite([
    'resources/js/jquery.min.js',
    'resources/js/bootstrap.min.js',
    'resources/js/taking-test.js',
  ]) --}}
  <script src="{{ url("js/jquery.min.js") }}"></script>
  <script src="{{ url("js/bootstrap.min.js") }}"></script>
  <script src="{{ url("js/taking-test.js") }}"></script>
@endsection
