var months = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];
var days = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];

var event = {};

// Calendar
document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = document.getElementById("calendar");
  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    businessHours: {
      // days of week. an array of zero-based day of week integers (0=Sunday)
      daysOfWeek: allAvailableTimes.map((item) => {
        if (item.key != "excluded") {
          return item.key;
        }
      }),
    },
    height: "auto",
    selectable: true,
    select: function (info) {
      var currentDay = new Date();
      var daySelected = info.start;

      if (daySelected > currentDay) {
        var timeDiv = document.getElementById("available-times-div");

        while (timeDiv.firstChild) {
          timeDiv.removeChild(timeDiv.lastChild);
        }

        var h4 = document.createElement("h4");
        var h4node = document.createTextNode(
          days[daySelected.getDay()] +
            ", " +
            months[daySelected.getMonth()] +
            " " +
            daySelected.getDate()
        );
        h4.appendChild(h4node);
        timeDiv.appendChild(h4);

        let availableTimes = allAvailableTimes.filter((value, key) => {
          return value.key == daySelected.getDay();
        });

        if (availableTimes.length > 0) {
          availableTimes.forEach((timeWindow) => {
            const windows = parseTimeWindows(timeWindow);
            // Time Buttons
            windows.forEach((slot) => {
              var timeSlot = document.createElement("div");
              timeSlot.classList.add("time-slot");
              timeSlot.dataset.time = slot.start;

              var timeBtn = document.createElement("button");

              var btnNode = document.createTextNode(slot.start);
              timeBtn.classList.add("time-btn");

              timeBtn.appendChild(btnNode);
              timeSlot.appendChild(timeBtn);

              timeDiv.appendChild(timeSlot);

              // When time is selected
              window.last = null;
              timeBtn.addEventListener("click", function () {
                if (window.last != null) {
                  last.parentNode.removeChild(last.parentNode.lastChild);
                }
                var confirmBtn = document.createElement("button");
                var confirmTxt = document.createTextNode("Confirm");
                confirmBtn.classList.add("confirm-btn");
                confirmBtn.appendChild(confirmTxt);
                this.parentNode.appendChild(confirmBtn);
                let time = this.textContent;

                confirmBtn.addEventListener("click", function () {
                  // event.date =
                  //   days[daySelected.getDay()] +
                  //   ", " +
                  //   months[daySelected.getMonth()] +
                  //   " " +
                  //   daySelected.getDate();
                  const localDate = new Date(
                    `${
                      months[daySelected.getMonth()]
                    } ${daySelected.getDate()}, ${daySelected.getFullYear()} ${convertTo24Hour(
                      time
                    )}`
                  );
                  let dateInUTC = new Date(
                    localDate.toLocaleString("en-US", {
                      timeZone:
                        Intl.DateTimeFormat().resolvedOptions().timeZone,
                    })
                  );

                  let form = document.createElement("form");
                  form.method = "POST";
                  form.action = AppointmentsUrl;

                  let csrf = document.createElement("input");
                  csrf.name = "_token";
                  csrf.value = document
                    .querySelector("meta[name='csrf-token']")
                    .getAttribute("content");
                  form.appendChild(csrf);
                  form.style = "display: none;";

                  let dateInput = document.createElement("input");
                  dateInput.name = "date";
                  // dateInput.value = dateInUTC;
                  dateInput.value = dateInUTC.toISOString();
                  console.log(dateInUTC.toISOString());
                  // dateInput.value = toLocalISOString(
                  //   // 2024-07-03T01:00:00
                  //   dateInUTC,
                  //   Intl.DateTimeFormat().resolvedOptions().timeZone
                  // );
                  form.appendChild(dateInput);

                  // let timezoneInput = document.createElement("input");
                  // timezoneInput.name = "timezone";
                  // timezoneInput.value =
                  //   Intl.DateTimeFormat().resolvedOptions().timeZone;
                  // form.appendChild(timezoneInput);

                  if (has_price) {
                    let token = document.createElement("input");
                    token.name = "intentId";
                    token.value = clientSecret;
                    form.appendChild(token);
                  }

                  document.body.appendChild(form);

                  if (!has_price) {
                    form.submit();
                    return true;
                  } else {
                    document
                      .getElementById("pages")
                      .classList.add("show-last-page");

                    let h1 = dateInUTC.getHours();
                    let m1 = dateInUTC.getMinutes();
                    let f1 = dateInUTC.getHours() >= 12 ? "pm" : "am";
                    let text = `${h1 > 9 ? h1 : "0" + h1}:${
                      m1 > 9 ? m1 : "0" + m1
                    } ${f1}`;

                    dateInUTC.setMinutes(
                      dateInUTC.getMinutes() + AppointmentDuration
                    );

                    let h2 = dateInUTC.getHours();
                    let m2 = dateInUTC.getMinutes();
                    let f2 = dateInUTC.getHours() >= 12 ? "pm" : "am";
                    text = `${text} - ${h2 > 9 ? h2 : "0" + h2}:${
                      m2 > 9 ? m2 : "0" + m2
                    } ${f2}`;

                    document.getElementById(
                      "event-time-stamp"
                    ).innerText = `${text} ${
                      months[daySelected.getMonth()]
                    } ${daySelected.getDate()}, ${daySelected.getFullYear()}`;
                    window.bookingForm = form;
                  }
                });
                window.last = timeBtn;
              });
            });
          });

          let m = daySelected.getMonth();
          let day = daySelected.getDate();
          const date1 = `${daySelected.getFullYear()}-${
            m >= 9 ? m + 1 : "0" + (m + 1)
          }-${day > 9 ? day : "0" + day}T00:00:00${getTimezoneOffset(
            Intl.DateTimeFormat().resolvedOptions().timeZone
          )}`;

          fetch(
            AppointmentsUrl +
              "/unavailable?date=" +
              encodeURI(new Date(date1).toISOString()),
            {
              method: "GET",
              headers: {
                "Content-Type": "application/json",
              },
            }
          )
            .then((response) => response.json())
            .then((dates) => {
              if (dates?.excluded?.length > 0 || dates?.booked?.length > 0) {
                let timeSlots = [];
                availableTimes.forEach((timeWindow) => {
                  const windows = parseTimeWindows(timeWindow, new Date(date1));
                  timeSlots.push(...windows);
                });

                if (dates?.booked?.length > 0) {
                  // Convert datesArray to Date objects
                  const dateObjects = dates.booked.map((dateString) => {
                    const userTimezone =
                      Intl.DateTimeFormat().resolvedOptions().timeZone;

                    // Step 1: Parse the ISO 8601 string into a Date object
                    const dateInAppointmentTimezone = new Date(dateString);

                    // Formatter to convert the time to the user's timezone
                    const userTimezoneFormatter = new Intl.DateTimeFormat(
                      "en-US",
                      {
                        timeZone: userTimezone,
                        year: "numeric",
                        month: "2-digit",
                        day: "2-digit",
                        hour: "2-digit",
                        minute: "2-digit",
                        second: "2-digit",
                        hour12: false, // 24-hour format
                      }
                    );
                    const formattedUserTimeParts =
                      userTimezoneFormatter.formatToParts(
                        dateInAppointmentTimezone
                      );

                    const convertedUserTime = `${formattedUserTimeParts[4].value}-${formattedUserTimeParts[0].value}-${formattedUserTimeParts[2].value} ${formattedUserTimeParts[6].value}:${formattedUserTimeParts[8].value}:${formattedUserTimeParts[10].value}`;

                    console.log(
                      "Original Time (Appointment's Timezone):",
                      dateString
                    );
                    console.log(
                      "Converted Time in User's Timezone:",
                      convertedUserTime
                    );

                    const dateEnd = new Date(convertedUserTime);
                    dateEnd.setMinutes(
                      dateEnd.getMinutes() + AppointmentDuration
                    );
                    console.log({
                      start: new Date(convertedUserTime),
                      end: dateEnd,
                    });

                    return {
                      start: new Date(convertedUserTime),
                      end: dateEnd,
                    };
                  });

                  // Filter time windows
                  const filteredTimeWindows = timeSlots.filter((window) => {
                    const windowStart = new Date(window.date);
                    // const windowStart = new Date(window.date);
                    const windowEnd = new Date(window.date);
                    windowEnd.setMinutes(
                      windowStart.getMinutes() + AppointmentDuration
                    );

                    // console.log(windowStart, windowEnd);
                    // Check if windowStart or windowEnd is within an hour of any date in datesArray
                    return dateObjects.some((date) => {
                      return (
                        (windowStart >= date.start &&
                          windowStart <= date.end) ||
                        (windowEnd >= date.start && windowEnd <= date.end) ||
                        (windowStart <= date.start && windowEnd >= date.end)
                      );
                    });
                  });

                  if (filteredTimeWindows.length > 0) {
                    filteredTimeWindows.forEach((desableTime) => {
                      let el = document.querySelector(
                        `#available-times-div .time-slot[data-time="${desableTime.start}"] button`
                      );
                      el.setAttribute("disabled", true);
                      el.parentElement.classList.add("disabled");
                    });
                  }
                }

                if (dates?.excluded?.length > 0) {
                  // Convert datesArray to Date objects
                  const dateObjects = dates.excluded.map((dateString) => {
                    return {
                      start: new Date(dateString.from),
                      end: new Date(dateString.to),
                    };
                  });

                  // Filter time windows
                  const filteredTimeWindows = timeSlots.filter((window) => {
                    const windowStart = new Date(window.date);
                    // const windowStart = new Date(window.date);
                    const windowEnd = new Date(window.date);
                    windowEnd.setMinutes(
                      windowStart.getMinutes() + AppointmentDuration
                    );

                    // console.log(windowStart, windowEnd);
                    // Check if windowStart or windowEnd is within an hour of any date in datesArray
                    return dateObjects.some((date) => {
                      return (
                        (windowStart >= date.start &&
                          windowStart <= date.end) ||
                        (windowEnd >= date.start && windowEnd <= date.end) ||
                        (windowStart <= date.start && windowEnd >= date.end)
                      );
                    });
                  });

                  if (filteredTimeWindows.length > 0) {
                    filteredTimeWindows.forEach((desableTime) => {
                      let el = document.querySelector(
                        `#available-times-div .time-slot[data-time="${desableTime.start}"] button`
                      );
                      el.setAttribute("disabled", true);
                      el.parentElement.classList.add("disabled");
                    });
                  }
                }
              }
            })
            .catch((error) => console.error("Error:", error));
        } else {
          let timeSlot = document.createElement("div");
          let text = document.createTextNode("No Available Time To Book");
          timeSlot.appendChild(text);
          timeSlot.classList.add("alert", "alert-warning");

          timeDiv.appendChild(timeSlot);
        }

        var containerDiv = document.getElementsByClassName("container")[0];
        containerDiv.classList.add("time-div-active");

        document.getElementById("calendar-section").style.flex = "2";

        timeDiv.style.display = "initial";
        calendar.updateSize();
      } else {
        calendar.unselect();
        // alert("Sorry that date has already past. Please select another date.");
      }
    },
  });
  calendar.render();
});

document
  .getElementById("back-to-booking")
  .addEventListener("click", function () {
    document.getElementById("pages").classList.remove("show-last-page");
  });

/*
  ================================================
  ===============  Helper Funtions ===============
  ================================================
*/

function parseTimeWindows(timeRange, date = null) {
  const [start, end] = [
    timeRange.from_date.substring(2),
    timeRange.to_date.substring(2),
  ];
  const localTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

  let tempDate; // 2024-07-02T
  if (date) {
    tempDate = toLocalISOString(date, localTimezone);
  } else {
    // will take first 11 digit of the string to make the day and month and year (we will parse the time so the date is not important)
    tempDate = toLocalISOString(new Date(), localTimezone);
  }

  let startTimeWindow = new Date(`${tempDate.substring(0, 11)}${start}`);
  let endTimeWindow = new Date(`${tempDate.substring(0, 11)}${end}`);

  startTimeWindow = new Date(
    startTimeWindow.toLocaleString("en-US", {
      timeZone: localTimezone,
    })
  );
  endTimeWindow = new Date(
    endTimeWindow.toLocaleString("en-US", {
      timeZone: localTimezone,
    })
  );

  const timeWindows = []; // will be returned

  let currentDate = new Date(startTimeWindow);
  if (endTimeWindow < currentDate) {
    let tmp = endTimeWindow;
    endTimeWindow = currentDate;
    currentDate = tmp;
  }

  while (currentDate <= endTimeWindow) {
    const availableEnd = new Date(currentDate);
    availableEnd.setMinutes(availableEnd.getMinutes() + AppointmentDuration); // the end of the time window (start time + duration)

    // if the meeting ends after the timeWindow Ends it will end at the same time
    if (availableEnd > endTimeWindow) {
      availableEnd.setTime(endTimeWindow.getTime());
    }

    // if (availableStart < endTimeWindow) {
    if (currentDate < endTimeWindow) {
      timeWindows.push({
        // start: formatTime(availableStart),
        date: currentDate.toISOString(),
        start: formatTime(currentDate),
        end: formatTime(availableEnd),
      });
    }

    // Move currentDate 45 minutes ahead: 30 minutes available time + 15 minutes break
    // the number of minuts is just an example
    currentDate.setMinutes(
      currentDate.getMinutes() + AppointmentDuration + AppointmentBufferZone
    );
  }

  return timeWindows;
}

function convertTo24Hour(time) {
  const [timePart, modifier] = time.split(" ");
  // let [hours, minutes] = timePart.split(":");
  let [hours, minutes] = timePart.split(":");

  if (hours === "12") {
    hours = "00";
  }
  if (modifier.toLowerCase() === "pm") {
    hours = parseInt(hours, 10) + 12;
  }
  return `${hours.toString().padStart(2, "0")}:${minutes.padStart(2, "0")}`;
}

function convertTo24HourReturnUnites(time) {
  const [timePart, modifier] = time.split(" ");
  let [hours, minutes] = timePart.split(":");
  hours = parseInt(hours, 10);
  minutes = parseInt(minutes, 10);
  if (modifier.toLowerCase() === "pm" && hours < 12) {
    hours += 12;
  }
  if (modifier.toLowerCase() === "am" && hours === 12) {
    hours = 0;
  }
  return { hours, minutes };
}

function formatTime(date) {
  const hours = date.getHours();
  const minutes = date.getMinutes();
  const ampm = hours >= 12 ? "pm" : "am";
  const formattedHours = hours % 12 === 0 ? 12 : hours % 12;
  return `${formattedHours.toString().padStart(2, "0")}:${minutes
    .toString()
    .padStart(2, "0")} ${ampm}`;
}

/* ==========================================================
   ==================  Checkout Section  ====================
   ========================================================== */

if (has_price) {
  const stripe = Stripe(stripeKey);

  let elements;

  $(window).ready(function () {
    initialize();
    checkStatus();
  });

  document
    .querySelector("#payment-form")
    .addEventListener("submit", handleSubmit);

  async function initialize() {
    elements = stripe.elements({ clientSecret: clientSecretKey });

    const paymentElement = elements.create("payment");

    paymentElement.mount("#payment-element");
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    fetch(AppointmentsUrl, {
      method: "POST",
      body: new FormData(window.bookingForm),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log(data);
      })
      .catch((error) => console.error("Error:", error));

    const { error } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: successUrl,
      },
    });

    if (error) {
      if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
      } else {
        showMessage("An unexpected error occurred.");
      }
      setLoading(false);
    }
  }

  // Fetches the payment intent status after payment submission
  async function checkStatus() {
    const clientSecret = new URLSearchParams(window.location.search).get(
      "payment_intent_client_secret"
    );

    if (!clientSecret) {
      return;
    }

    const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

    switch (paymentIntent.status) {
      case "succeeded":
        showMessage("Payment succeeded!");
        break;
      case "processing":
        showMessage("Your payment is processing.");
        break;
      case "requires_payment_method":
        showMessage("Your payment was not successful, please try again.");
        break;
      default:
        showMessage("Something went wrong.");
        break;
    }
  }

  // ------- UI helpers -------
  function showMessage(messageText) {
    const messageContainer = document.querySelector("#payment-message");

    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;

    setTimeout(function () {
      messageContainer.classList.add("hidden");
      messageContainer.textContent = "";
    }, 7000);
  }

  // Show a spinner on payment submission
  function setLoading(isLoading) {
    if (isLoading) {
      // Disable the button and show a spinner
      document.querySelector("#submitBtn").disabled = true;
      document.querySelector("#spinner").classList.remove("hidden");
      document.querySelector("#button-text").classList.add("hidden");
    } else {
      document.querySelector("#submitBtn").disabled = false;
      document.querySelector("#spinner").classList.add("hidden");
      document.querySelector("#button-text").classList.remove("hidden");
    }
  }
}

function toLocalISOString(date, timezone) {
  const options = {
    timeZone: timezone,
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  };

  const formatter = new Intl.DateTimeFormat("en-GB", options);
  const parts = formatter.formatToParts(date);
  const isoString = `${parts.find((p) => p.type === "year").value}-${
    parts.find((p) => p.type === "month").value
  }-${parts.find((p) => p.type === "day").value}T${
    parts.find((p) => p.type === "hour").value
  }:${parts.find((p) => p.type === "minute").value}:${
    parts.find((p) => p.type === "second").value
  }`;

  return isoString;
}

function convertTZ(date, tzString) {
  return new Date(
    (typeof date === "string" ? new Date(date) : date).toLocaleString("en-US", {
      timeZone: tzString,
    })
  );
}

function getTimezoneOffset(timezone) {
  const date = new Date();
  const dateInTimezone = date.toLocaleString("en-US", { timeZone: timezone });
  const offsetDate = new Date(dateInTimezone);

  const offset = offsetDate.getTimezoneOffset();

  const offsetHours = Math.floor(Math.abs(offset) / 60);
  const offsetMinutes = Math.abs(offset) % 60;

  const sign = offset <= 0 ? "+" : "-";

  // Format the offset as "+HH:MM" or "-HH:MM"
  const formattedOffset = `${sign}${String(offsetHours).padStart(
    2,
    "0"
  )}:${String(offsetMinutes).padStart(2, "0")}`;

  return formattedOffset;
}
