<x-profile.layout>

  @vite('resources/css/meetings.css')

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if (isset($error))
    <div class="alert alert-danger">
      <p class="lead">{{ $error }}</p>
    </div>
  @endif

  @if (isset($success))
    <div class="alert alert-success">
      <p class="lead">{{ $success }}</p>
    </div>
  @endif

  <div class="w-full pb-8 mt-8 sm:rounded-lg">
    <h2 class="pl-6 text-2xl font-bold sm:text-xl">My Meetings</h2>
  </div>

  <div class="container">

    @if ($meetings->count() > 0)
      <div class="row">
        @foreach ($meetings as $meeting)
          <div class="col-md-4">
            <x-meeting-card id="{{ $meeting->id }}" butonClass="modal-opener"
              title="{{ $meeting->appointment->title }}" notes="{{ $meeting->notes ?? 'no notes' }}"
              color="{{ $meeting->appointment->color }}" admin="{{ $meeting->appointment->author?->fullname() }}"
              date="{{ $meeting->day() }}" duration="{{ $meeting->appointment->show_duration() }}"
              meetingDate="{{ $meeting->appointment_date }}" googleCalendar="{{ $meeting->meeting_link }}"
              timezone="{{ $meeting->appointment->timezone }}" editable=true></x-meeting-card>
          </div>
        @endforeach
      </div>
    @else
      <div role="alert" class="custom-alert-1">
        <p class="font-bold text-capitalize" style="margin: 0px; font-weight: 700;">{{ __('warning') }}</p>
        <p style="margin: 0px;">{{ __('there is no :items to show', ['items' => __('appointments')]) }}.</p>
      </div>
    @endif

  </div>

  @if ($canceled_meetings->count() > 0)

    <div class="w-full pb-8 mt-8 sm:rounded-lg">
      <h2 class="pl-6 text-2xl font-bold sm:text-xl">Cancelled Meetings</h2>
    </div>
    <div class="container">
      <div class="row">
        @foreach ($canceled_meetings as $meeting)
          <div class="col-md-4">
            <x-meeting-card disabled=true style="filter: grayscale(1)" id="{{ $meeting->id }}"
              title="{{ $meeting->appointment->title }}" notes="{{ $meeting->notes ?? 'no notes' }}"
              color="{{ $meeting->appointment->color }}" admin="{{ $meeting->appointment->author?->fullname() }}"
              duration="{{ $meeting->appointment->show_duration() }}"></x-meeting-card>
          </div>
        @endforeach
      </div>
    </div>

  @endif

  @if ($past_meetings->count() > 0)

    <div class="w-full pb-8 mt-8 sm:rounded-lg">
      <h2 class="pl-6 text-2xl font-bold sm:text-xl">Past Meetings</h2>
    </div>
    <div class="container">
      <div class="row">
        @foreach ($past_meetings as $meeting)
          <div class="col-md-4">
            <x-meeting-card disabled=true style="filter: grayscale(1)" id="{{ $meeting->id }}"
              title="{{ $meeting->appointment->title }}" notes="{{ $meeting->notes ?? 'no notes' }}"
              color="{{ $meeting->appointment->color }}" admin="{{ $meeting->appointment->author?->fullname() }}"
              duration="{{ $meeting->appointment->show_duration() }}"></x-meeting-card>
          </div>
        @endforeach
      </div>
    </div>

  @endif

  <div class="container-sm">
    <div id="myModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <span class="close">&times;</span>
          <h2 class="title">Modal Header</h2>
        </div>
        <div class="modal-body">
          <form method="POST" data-url="{{ route('booked.appointmet.notes', '') }}" action="">
            @csrf
            <label for="notes" class="form-label">Notes:</label>
            <textarea name="notes" id="notes" cols="30" rows="5" class="notes form-control" placeholder="notes"></textarea>
          </form>
        </div>
        <div class="modal-footer">
          <button id="saveBtn" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const meetings = JSON.parse(`{!! $edit_meetings_data !!}`)
    let modal = document.getElementById("myModal");
    let btns = document.querySelectorAll(".modal-opener");
    let span = modal.querySelector(".close");

    btns.forEach(btn => {
      btn.onclick = function() {
        let meeting = meetings.filter(item => {
          return item.id == btn.dataset.id
        })
        if (meeting.length > 0) {
          meeting = meeting[0];
          modal.querySelector(".title").innerText = meeting.title;
          modal.querySelector(".notes").value = meeting.notes;
          saveBtn.addEventListener("click", function(e) {
            e.preventDefault();
            let form = document.getElementsByTagName("form")[0];
            form.action = `${form.dataset.url}/${meeting.id}`;
            form.submit();
          })
        }
        modal.style.display = "block";
      }
    });

    span.onclick = function() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>

</x-profile.layout>
