@extends('dashboard.layout')

@section('meta')
  @php
    $page_title = config('app.name') . ' | ' . (isset($test) ? __('Edit Test') : __('Create Test'));
    $no_footer_included = true;
  @endphp
  <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('styles')
  @vite('resources/css/dashboard/test-builder.css')
  @vite('resources/css/dashboard/style.css')
  <link rel="stylesheet" media="all" href="https://guppy.js.org/build/guppy-default.min.css" />
@endsection

@section('content')
  <div class="container">
    <div class="test-create-page">

      <div class="row p-0 m-0">

        <div class="col-4 order-2">
          <div class="sticky-top questions-sidebar mt-5">
            <b class="lead">{{ __('Question Types') }}:</b>
            <div class="nav flex-column rounded py-1 gap-1 mb-2">
              @foreach ($question_types as $type)
                <div class="shadow item mx-1 card flex-row align-items-center draggable-question overflow-hidden"
                  draggable="true" data-item="{{ $type['id'] }}" data-name="{{ $type['title'] }}">
                  <div class="icon-bg py-1 px-3 d-flex bg-clr-secondary align-items-center justify-content-center"
                    style="width: 60px;height: 45px;">
                    <i class="fa {{ $type['icon'] }}"></i>
                  </div>
                  <div class="card-body py-2 px-2 pe-3">
                    <h6 class="card-title my-1">{{ $type['title'] }}</h6>
                  </div>
                </div>
              @endforeach
            </div>
            <b class="lead">{{ __('Result Types') }}:</b>
            <div class="nav flex-column rounded py-1 gap-1 mb-2">
              @foreach ($result_types as $type)
                <div class="shadow item mx-1 card flex-row align-items-center draggable-result overflow-hidden"
                  @if ($type['id'] == 1 && $test->certificate != null) style="opacity: .25" draggable="false" @endif draggable="true"
                  data-item="{{ $type['id'] }}" data-name="{{ $type['title'] }}">
                  <div class="icon-bg py-1 px-3 d-flex bg-clr-info align-items-center justify-content-center"
                    style="width: 60px;height: 45px;">
                    <i class="fa {{ $type['icon'] }}"></i>
                  </div>
                  <div class="card-body py-2 px-2 pe-3">
                    <h6 class="card-title my-1">{{ $type['title'] }}</h6>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          <div id="alerts" class="d-flex justify-content-end position-fixed"
            style="z-index:999;left: 60px;bottom: 180px;width: 400px;"></div>
        </div>
        <div class="col" id="main">

          <!-- Modal -->
          <div class="modal fade" id="main_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
              <div class="modal-content">
                <div class="modal-header px-4">
                  <h5 class="modal-title" id="modal_title" data-title="{{ __('Text Question') }}">
                    {{ __('Text Question') }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                  <div class="row m-0">
                    <div class="col-4 pr-0 bg-light">
                      <div id="modal_sidebar" class="p-3" style="height: calc(100vh - 192px);overflow-y: auto;">
                      </div>
                    </div>
                    <div class="col-8 px-0" style="height: calc(100vh - 192px);overflow-y: auto;">
                      <div id="modal_preview" class="test-preview m-0 p-0">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                  <button type="button" id="submit_modal" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Test Title Modal -->
          <div class="modal fade" id="TitleEditModal" tabindex="-1" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">{{ __('Name Your Test') }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="mb-1" for="testTitle">{{ __('Test title:') }}</label>
                    <input type="text" name="title" value="{{ $test->title }}" id="testTitle" class="form-control"
                      placeholder="Test Title">
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                      <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                        id="editNameSubmit">{{ __('Save changes') }}</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="py-4">
            <div class="title mb-0">
              <h3 id="testTitleShow" class="test-title d-inline-block">{{ $test->title }}</h3>
              <button class="btn py-1 px-2 text-muted" data-bs-toggle="modal" data-bs-target="#TitleEditModal">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-primary" id="copy-link" data-value="{{ route('test_show', $test->id) }}">
                <span class="text">{{ __('copy') }}</span>
                <i class="fa fa-link"></i>
              </button>
            </div>
          </div>

          <div class="alert shadow alert-primary p-4" style="border-style: dashed" id="introInfo">
            <form id="intro-form">
              <div class="form-check form-switch p-0 my-0 d-flex gap-3">
                <label class="form-check-label" for="intro-switch">activate intro section:</label>
                <input class="form-check-input mx-0 float-none" name="has_intro" type="checkbox" role="switch"
                  id="intro-switch" @if ($test->has_intro()) checked @endif>
              </div>
              <div class="text-center" id="intro-holder">
                <div class="row">
                  <div class="col-md-7 d-flex flex-column align-items-center justify-content-center">
                    <h3 class="text-dark title my-3" data-target="introTitle-input" id="introTitle">
                      {{ $test->intro_title }}</h3>
                    <input style="display: none" id="introTitle-input" type="text" name="intro_title"
                      class="form-control mb-3" placeholder="intro title" value="{{ $test->intro_title }}">
                    <p class="text-dark lead mb-2" data-target="introDesc-input" id="introDesc">
                      {{ $test->intro_description }}</p>
                    <textarea style="display: none" type="text" id="introDesc-input" name="intro_desc" class="form-control"
                      placeholder="intro description">{{ $test->intro_description }}</textarea>
                    <button type="button" class="btn btn-primary py-2 px-3 mt-3" data-target="introButton-input"
                      id="introButton">{{ $test->intro_btn }}</button>
                    <input style="display: none" id="introButton-input" name="intro_btn" type="text"
                      class="form-control mb-3" placeholder="intro button" value="{{ $test->intro_btn }}">
                  </div>
                  <div class="col-md-5">
                    <button type="button" class="btn btn-secondary py-1 px-2 shadow mb-3"
                      onclick="introImage.click();">
                      <i class="fa fas fa-solid fa-image"></i>
                    </button>
                    <button type="button" id="introImageRemove"
                      class="btn btn-danger py-1 px-2 shadow mb-3 @if (!$test->has_intro_image()) d-none @endif">
                      <i class="fa fa-trash"></i>
                    </button>
                    <input type="file" name="intro_image" id="introImage" class="d-none" accept=".jpg,.jpeg,.png">
                    <div class="intro-image img-thumbnail @if (!$test->has_intro_image()) d-none @endif"
                      id="previewImageIntro">
                      <img class="rounded" src="{{ $test->intro_image_url() }}" alt="intro image">
                    </div>
                  </div>
                </div>
              </div>
              <button type="button" id="save-intro" class="btn btn-info sender-btn">save <i
                  class="fa fa-save"></i></button>
            </form>
          </div>
          <div id="contentItemsContainer" class="shadow alert bg-clr-secondary list-group questions mb-3 p-4"
            style="--bs-opacity:0.35; border-style: dashed" id="contentItemsContainer">
            <div id="contentRemovableInfo">
              <h4 class="title text-dark">{{ __('Questions') }}</h4>
              <p class="lead text-dark">{{ __('Drag & Drop test content here.') }}</p>
            </div>
          </div>

          <div class="shadow alert alert-info p-4 results" style="border-style: dashed" id="resultItemsContainer">
            <form id="result-form">
              <div id="result-holder">
                <div id="resultInvisiblity" style="transition: all 0.2s ease;">
                  <div class="row mb-2">
                    <div class="col-sm-6">
                      <label class="title text-dark" for="min_score">{{ __('minimum score') }}
                        ({{ __('total') }} {{ $test->totalScore() }}):</label>
                    </div>
                    <div class="col-sm-6">
                      <input value="{{ $test?->result?->min_score }}" type="number" max="{{ $test->totalScore() }}"
                        name="min_score" id="min_score" class="form-control"
                        placeholder="{{ __('minimum score to success') }}">
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-6">
                      <label class="title text-dark" for="min_percent">{{ __('minimum percentage') }}:</label>
                    </div>
                    <div class="col-sm-6">
                      <input value="{{ $test?->result?->min_percent }}" type="number" min="0"
                        name="min_percent" id="min_percent" class="form-control"
                        placeholder="{{ __('minimum percentage to success') }}">
                    </div>
                  </div>
                  @if ($test->course_id == null)
                    <div class="row mb-2">
                      <div class="col-sm-6">
                        <label class="title text-dark" for="max_attempts">{{ __('maximum attempts') }}:</label>
                      </div>
                      <div class="col-sm-6">
                        <input value="{{ $test?->result?->max_attempts }}" type="number" min="1"
                          name="max_attempts" id="max_attempts" class="form-control"
                          placeholder="{{ __('maximum attempts allowed') }}">
                      </div>
                    </div>
                  @endif
                  <div class="row mb-2">
                    <div class="col-sm-6">
                      <label class="title text-dark"
                        for="min_correct_questions">{{ __('minimum of (correctly answered questions)') }}:</label>
                    </div>
                    <div class="col-sm-6">
                      <input value="{{ $test?->result?->min_correct_questions }}" type="number" min="0"
                        name="min_correct_questions" id="min_correct_questions" class="form-control"
                        placeholder="{{ __('minimum (correct answered questions) to success') }}">
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-sm-6">
                      <label class="title text-dark" for="custom_note">{{ __('custom note') }}:</label>
                    </div>
                    <div class="col-sm-6">
                      <textarea name="custom_note" id="custom_note" placeholder="{{ __('this note will appear on success') }}"
                        class="form-control">{{ $test?->result?->note }}</textarea>
                    </div>
                  </div>
                </div>
                <button type="button" id="save-result" class="btn btn-info sender-btn">save <i
                    class="fa fa-save"></i></button>
                <hr>
                <div id="certificatesContainer">
                  <div id="certificateRemovableInfo" @if ($test->has_certificate()) class="d-none" @endif>
                    <h4 class="title text-dark">{{ __('Certificates') }}</h4>
                    <p class="lead text-dark">{{ __('Drag & Drop certificate elements here.') }}</p>
                  </div>
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    var questions_reorder = `{{ route('reorder_test_questions', $test->id) }}`;
    var edit_test_name_route = `{{ route('test_edit_name', $test->id) }}`;
    var ajax_test_intro = `{{ route('test_update', $test->id) }}`;
    var questions_ajax_route = `{{ route('add_question', $test->id) }}`;
    var question_ajax_route = `{{ url('app-request/question') }}`;
    var public_route = `{{ url('') }}`;
    var ajax_result = `{{ route('result', $test->id) }}`;
    var certificate_theme_route = `{{ url('/certificate/theme/') }}`;
    var certificate_ajax_route = `{{ url(app()->getLocale() . '/app-request/certificate/') }}`;
    var ajax_create_certificate_route = `{{ route('test_certificate', $test->id) }}`;
    var certificate_show_route = `{{ url(app()->getLocale() . '/dashboard/certificate/') }}`;
    var ajax_delete_certificate_route = `{{ route('test_certificate_delete', $test->id) }}`;
    var intro_image_delete = `{{ url('app-request/test/' . $test->id . '/image/delete') }}`;
    const LANG = "{{ app()->getLocale() }}";
    const testType = 1; // can remove it later (after checking it is not used)
    var questions_types = JSON.parse('{!! json_encode($question_types) !!}');
    var results_types = JSON.parse('{!! json_encode($result_types) !!}');
    var certificatesTypes = JSON.parse('{!! json_encode($certificates_types) !!}');
    var availableCertificates = JSON.parse('{!! json_encode($available_certificates) !!}');

    const answersStyles = [
      ["A", "#016fdf"],
      ["B", "#63d581"],
      ["C", "#2ad5c7"],
      ["D", "#877fed"],
      ["E", "#b064db"],
      ["F", "#dd61aa"],
      ["G", "#f05242"],
      ["H", "#50cbe5"],
      ["I", "#537af8"],
      ["J", "#d789ed"],
      ["K", "#5c7d9f"],
      ["L", "#f6bd50"],
      ["M", "#d75965"],
      ["N", "#5a53af"],
      ["O", "#d55899"],
      ["P", "#5b95d0"],
      ["Q", "#87b65d"],
      ["R", "#f08f65"],
      ["S", "#88b3e8"],
      ["T", "#bb2b76"],
      ["U", "#2bb0ee"],
      ["V", "#79aa75"],
      ["W", "#be8b75"],
      ["X", "#d697d2"],
      ["Y", "#4fb0a1"],
      ["Z", "#bed05b"],
    ];
    const __ = {
      "SETTING": "{{ __('SETTING') }}",
      "Add Image:": "{{ __('Add Image:') }}",
      "Answers:": "{{ __('Answers:') }}",
      "largest image size is: 2mb": "{{ __('largest image size is: 2mb') }}",
      "If answer to question is:": "{{ __('If answer to question is:') }}",
      "(you can add multiple answers)": "{{ __('(you can add multiple answers)') }}",
      "BUTTON SETTINGS:": "{{ __('BUTTON SETTINGS:') }}",
      "no options": "{{ __('no options') }}",
      "Label:": "{{ __('Label:') }}",
      "Settings:": "{{ __('Settings:') }}",
      "Upload Image": "{{ __('Upload Image') }}",
      "Allow multiple choices:": "{{ __('Allow multiple choices:') }}",
      "choose your calander": "{{ __('choose your calander') }}",
      "UTM SETTINGS:": "{{ __('UTM SETTINGS:') }}",
      "SEND DATA SETTINGS:": "{{ __('SEND DATA SETTINGS:') }}",
      "RESULT SETTINGS:": "{{ __('RESULT SETTINGS:') }}",
      "Result Description": "{{ __('Result Description') }}",
      "No Items To Show": "{{ __('No Items To Show') }}",
      "submit": "{{ __('submit') }}",
      "Submit": "{{ __('Submit') }}",
      "you can't edit this Question ):": "{{ __('you can\'t edit this Question ):') }}",
      "you can't edit this Result ):": "{{ __('you can\'t edit this Result ):') }}",
      "Question Deleted Successfully": "{{ __('Question Deleted Successfully') }}",
      "Somthing Went Wrong !!": "{{ __('Somthing Went Wrong !!') }}",
      "Certificate Deleted Successfully": "{{ __('Certificate Deleted Successfully') }}",
      "Question has been duplicated": "{{ __('Question has been duplicated') }}",
      "Result has been duplicated": "{{ __('Result has been duplicated') }}",
      "Button:": "{{ __('Button:') }}",
      "Add Answer": "{{ __('Add Answer') }}",
      "Add": "{{ __('Add') }}",
      "Question": "{{ __('Question') }}",
      "Form Fields": "{{ __('Form Fields') }}",
      "Text": "{{ __('Text') }}",
      "Image Or Video": "{{ __('Image Or Video') }}",
      "result": "{{ __('result') }}",
      "Video URL:": '{!! __("Video URL: <a target=\"_blank\" href=\"https://youtu.be/hi\">(How to add a video)</a>") !!}',
      "Video": '{{ __('Video') }}',
      "Image": '{{ __('Image') }}',
      "Enter A Youtube Video URL": "{{ __('Enter A Youtube Video URL') }}",
      "Button Label:": "{{ __('Button Label:') }}",
      "First Name": "{{ __('First Name') }}",
      "Email address": "{{ __('Email address') }}",
      "Make this field lead email field": "{{ __('Make this field lead email field') }}",
      "Last Name": "{{ __('Last Name') }}",
      "Email": "{{ __('Email') }}",
      "Long Answer": "{{ __('Long Answer') }}",
      "Short Answer": "{{ __('Short Answer') }}",
      "Checkbox": "{{ __('Checkbox') }}",
      "Dropdown": "{{ __('Dropdown') }}",
      "Label": "{{ __('Label') }}",
      "enter a label": "{{ __('enter a label') }}",
      "Placeholder": "{{ __('Placeholder') }}",
      "Enter Placeholder": "{{ __('Enter Placeholder') }}",
      "is required": "{{ __('is required') }}",
      "Button Link": "{{ __('Button Link') }}",
      "Enable skip form option:": "{{ __('Enable skip form option:') }}",
      "Enable Skip Question": "{{ __('Enable Skip Question') }}",
      "skip": "{{ __('skip') }}",
      "show Privacy Policy:": "{{ __('show Privacy Policy:') }}",
      "Enter a Placeholder": "{{ __('Enter a Placeholder') }}",
      "Checkbox options:": "{{ __('Checkbox options:') }}",
      "Dropdown options:": "{{ __('Dropdown options:') }}",
      "Number": "{{ __('Number') }}",
      "Dropdown": "{{ __('Dropdown') }}",
      "Checkbox": "{{ __('Checkbox') }}",
      "Phone Number": "{{ __('Phone Number') }}",
      "Long answer": "{{ __('Long answer') }}",
      "Short answer": "{{ __('Short answer') }}",
      "Please add an option": "{{ __('Please add an option') }}",
      "Last Name": "{{ __('Last Name') }}",
      "Email": "{{ __('Email') }}",
      "Field Is Required": "{{ __('Field Is Required') }}",
      "save": "{{ __('save') }}",
      "select": "{{ __('select') }}",
      "create": "{{ __('create') }}",
      "edit": "{{ __('edit') }}",
      "status": "{{ __('status') }}",
      "Questions Reordered Successfully": "{{ __('Questions Reordered Successfully') }}",
      "Test Updated Successfully": "{{ __('Test Updated Successfully') }}",
      "Certificate Updated Successfully": "{{ __('Certificate Updated Successfully') }}",
      "Question Updated Successfully": "{{ __('Question Updated Successfully') }}",
      "Certificate Added Successfully": "{{ __('Certificate Added Successfully') }}",
      "Question Added Successfully": "{{ __('Question Added Successfully') }}",
      "certificate description": "{{ __('certificate description') }}",
      "-- no options --": "{{ __('-- no options --') }}",
      "question title": "{{ __('question title') }}",
      "question description": "{{ __('question description') }}",
      "enter you answer here": "{{ __('enter you answer here') }}",
      "correct answer equation": "{{ __('correct answer equation') }}",
      "answer decimals": "{{ __('answer decimals') }}",
      "score": "{{ __('score') }}",
      "copy": "{{ __('copy') }}",
      "copied": "{{ __('copied') }}",
    };
  </script>
  <script src="{{ url('libs/dashboard/sweetalert2.all.min.js') }}"></script>
  <script src="{{ url('libs/dashboard/sortable.js') }}"></script>
  <script src="https://guppy.js.org//build/guppy.min.js"></script>
  <script src="{{ url('js/test-builder.js') }}"></script>
  <script>
    $(window).ready(function() {
      questionsReorderConfig() // questions reorder config
    });
    dragAndDropItemsHandler()
    hasIntroHandler()

    @if (count($test->questions) > 0)
      @foreach ($test->questions as $question)
        createQuestion('{{ $question->order }}', "{{ $question->title }}", '{{ $question->id }}',
          '{{ $question->type }}', "contentItemsContainer");
      @endforeach
    @endif

    @if ($test->certificate != null)
      createCertificateElement("{{ $test->certificate->title }}", "{{ $test->certificate->id }}");
    @endif
  </script>
@endsection
