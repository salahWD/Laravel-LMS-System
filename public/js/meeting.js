let btn = $(".on-off-btn");
btn.each(function () {
  $(this).on("click", function () {
    $(this).toggleClass(["btn-outline-gray-800", "btn-outline-gray-500"]);
    if ($(this).hasClass("btn-outline-gray-800")) {
      $(this).text("{{ __('turn off ') }}");
    } else {
      $(this).text("{{ __('turn on ') }}");
    }
  });
});

function removeTimeWindow(id) {
  $("#" + id).remove();
}

function createTimeWindow(args, data = null) {
  let defaults = { day: null, count: 0, removeBtn: false };
  let params = { ...defaults, ...args };
  let customId =
    Math.random().toString(36).substring(2) +
    Math.random().toString(36).substring(2);
  let timeRangeParams = { id: "", name: params.day, count: params.count };
  if (data != null) {
    timeRangeParams.time = data;
  }
  let removeBtn = `<button onclick="$('#${customId}').remove()" class="btn btn-sm text-secondary px-0 ps-sm-2 mt-2 mt-sm-0 ms-sm-1 col-sm-2" type="button">Remove<span class="d-sm-none"> time above</span> </button>`;
  return `<div class="mt-minus-first mt-3" id="${customId}">
            <div class="d-flex align-items-center flex-wrap flex-sm-nowrap col">
              ${TimeRange(timeRangeParams)}
              ${params.removeBtn ? removeBtn : ""}
            </div>
          </div>`;
}

export function createExcludeDate(date = null) {
  let customId =
    Math.random().toString(36).substring(2) +
    Math.random().toString(36).substring(2);
  let dayEl = $("#excluded-dates-box");
  let name = `excluded_days`;
  let count = dayEl.find(".excluded-date").length;
  let timeRangeParams = { id: "", name: name, count: count };
  let day;
  if (date != null) {
    let times = date.split("|");
    day = times[0];
    timeRangeParams.time = extractTimeFromTimestamp(times[1]);
  }
  let el = `
  <div class="p-3 border-bottom excluded-date" id="${customId}">
    <div class="d-flex align-items-center flex-wrap col">
      <input class="form-control me-md-2 mb-3 mb-md-0 col-12 col-md" type="date" name="${name}[times][${count}][day]" ${
    date != null ? `value="${day}"` : ""
  }>
      <div class="d-flex flex-wrap flex-md-nowrap align-items-center">
        ${TimeRange(timeRangeParams)}
        <div class="col-12 col-md mt-2 mt-md-0 ms-0 ms-md-3 text-end">
          <button onclick="$('#${customId}').remove()" class="btn btn-sm btn-link link-danger w-auto p-0" type="button">Remove</button>
        </div>
      </div>
    </div>
  </div>`;

  $("#excluded-dates-box div:last").before(el);
}

function createOptions(selected = 0, count = 60, withZero = true) {
  let el = "";
  for (let i = withZero ? 0 : 1; i < count; i++) {
    el =
      el +
      `<option value="${i < 10 ? "0" + i : i}" ${
        i == selected ? "selected" : ""
      }>
        ${i < 10 ? "0" + i : i}
      </option>`;
  }
  return el;
}

function TimeRange(args) {
  let defaults = {
    id: "no-id-given",
    name: null,
    count: 0,
    time: {
      from_hour: "01",
      from_minute: "00",
      from_format: "am",
      to_hour: "06",
      to_minute: "00",
      to_format: "pm",
    },
  };
  let params = { ...defaults, ...args };
  let time = params.time;
  return `<div class="d-flex align-items-center" id="${params.id}">
          <div class="input-group" style="min-width: 160px;">
          <select class="form-control" ${
            params.name != null &&
            `name="${params.name}[times][${params.count}][from_hour]"`
          }>
              ${createOptions(time.fromHour, 12, false)}
            </select>
            <span class="input-group-text bg-white" style="padding: 0px 3px;">:</span>
            <select class="form-control" ${
              params.name != null &&
              `name="${params.name}[times][${params.count}][from_minut]"`
            }>
              ${createOptions(time.fromMinute, 60)}
            </select>
            <select class="form-control" ${
              params.name != null &&
              `name="${params.name}[times][${params.count}][from_format]"`
            }>
              <option ${
                time.fromFormat == "am" && "selected"
              } value="am">AM</option>
              <option ${
                time.fromFormat == "pm" && "selected"
              } value="pm">PM</option>
            </select>
          </div>
          <div class="mx-2 text-secondary small">-</div>
          <div class="input-group" style="min-width: 160px;">
          <select class="form-control" ${
            params.name != null &&
            `name="${params.name}[times][${params.count}][to_hour]"`
          }>
              ${createOptions(time.toHour, 12, false)}
          </select>
          <span class="input-group-text bg-white" style="padding: 0px 3px;">:</span>
          <select class="form-control" ${
            params.name != null &&
            `name="${params.name}[times][${params.count}][to_minut]"`
          }>
              ${createOptions(time.toMinute, 60)}
            </select>
            <select class="form-control" ${
              params.name != null &&
              `name="${params.name}[times][${params.count}][to_format]"`
            }>
              <option ${
                time.toFormat == "am" && "selected"
              } value="am">AM</option>
              <option ${
                time.toFormat == "pm" && "selected"
              } value="pm">PM</option>
            </select>
          </div>
        </div>`;
}

function checkDayAvailability(item) {
  let day = item.parents(".day-element");
  if (item.prop("checked")) {
    day.find(".disable-if-no-day").removeClass("d-none").addClass("d-flex");
    day.find(".unavailable").addClass("d-none").removeClass("d-block");
  } else {
    day.find(".disable-if-no-day").addClass("d-none").removeClass("d-flex");
    day.find(".unavailable").removeClass("d-none").addClass("d-block");
  }
}

export function addAvailableTime(dayItem, data = null) {
  let name = `available_days[${dayItem.data("day")}]`;
  let count = dayItem.find(".time-windows").children().length;
  dayItem
    .find(".time-windows")
    .append(
      createTimeWindow({ day: name, count: count, removeBtn: count > 0 }, data)
    );
}

export function extractTimeFromTimestamp(timestamp) {
  let stamps = timestamp.split("-");
  let fromTime = stamps[0].split(":");
  let toTime = stamps[1].split(":");
  return {
    fromHour: fromTime[0],
    fromMinute: fromTime[1],
    fromFormat: fromTime[2],
    toHour: toTime[0],
    toMinute: toTime[1],
    toFormat: toTime[2],
  };
}

$(".add-window-btn").each(function () {
  $(this).on("click", function () {
    let item = $(this).parents(".day-element");
    addAvailableTime(item);
  });
});

$(".day-available-check").each(function () {
  checkDayAvailability($(this));
  $(this).on("input", function () {
    checkDayAvailability($(this));
  });
});

function handleExcludeCheck() {
  if ($("#overrides").prop("checked")) {
    $("#excluded-dates").removeClass("d-none");
    $("#excluded-dates [name]").each(function () {
      $(this).attr("name", $(this).data("name"));
    });
  } else {
    $("#excluded-dates").addClass("d-none");
    $("#excluded-dates [name]").each(function () {
      $(this).data("name", $(this).attr("name"));
      $(this).attr("name", "");
    });
  }
}

$("#overrides").on("input", handleExcludeCheck);
handleExcludeCheck();

$("#add-excluded-date-btn").on("click", function () {
  createExcludeDate();
});
