function create(el, attributes, content = "") {
  if (el == false) {
    return false;
  }
  let element = document.createElement(el);
  for (var key in attributes) {
    element.setAttribute(key, attributes[key]);
  }
  element.innerHTML = content;
  return element;
}

function createQuestion(order, title, type, answeredAnswers, items) {
  let card = create("div", {
    class: "card bg-white border px-3 py-4 my-1",
  });
  document.getElementById("questions").append(card);

  card.append(
    create(
      "h5",
      {
        class: "title",
      },
      `${order}. ${title}`
    )
  );
  let acationText = [1, 2].includes(type)
    ? __[`Answered`]
    : type == 3
    ? __["Submissions"]
    : type == 6
    ? __["Shown Result"]
    : __["Continued"];
  card.append(
    create(
      "small",
      { class: "my-2" },
      `<b>${answeredAnswers}</b> ${acationText}`
    )
  );

  if (items != null && type < 4) {
    if (type == 3) {
      items.forEach((field) => {
        let progress = create(
          "div",
          {
            class: "progress py-3 px-4 border w-75",
            role: "progressbar",
            style: "height: 50px; background: rgb(246, 248, 250);",
          },
          field.text
        );

        let holder = create("div", {
          class: "mt-1 d-flex w-100 align-items-center",
        });
        holder.append(progress);

        card.append(holder);
      });
    } else {
      let SelectedAnswersCount = items.reduce((acc, i) => {
        return parseInt(acc) + parseInt(i.entries_count);
      }, 0);

      SelectedAnswersCount =
        SelectedAnswersCount == 0 ? 1 : SelectedAnswersCount;

      card.append(create("p", {}, __[`Answers:`]));

      items.forEach((answer) => {
        let progress = create("div", {
          class: "progress border w-100",
          role: "progressbar",
          style: "height: 50px; background: rgb(246, 248, 250);",
        });

        progress.append(
          create(
            "div",
            {
              class: "progress-bar overflow-visible text-start text-dark",
              style: `width: ${Math.round(
                (answer.entries_count / SelectedAnswersCount) * 100
              )}%`,
            },
            `<p class="my-0">${answer.text}</p>`
          )
        );

        let holder = create("div", {
          class: "mt-1 d-flex w-100 align-items-center",
        });
        holder.append(progress);
        card.append(holder);

        let percentage = create(
          "span",
          { class: "px-2 lead pb-1 responses-count" },
          `%${Math.round((answer.entries_count / SelectedAnswersCount) * 100)}`
        );
        holder.append(percentage);

        let responses = create(
          "span",
          { class: "pb-1 flex-shrink-0", style: "padding-left: 15px" },
          `<b>${answer.entries_count}</b> ${__["Responses"]}`
        );
        holder.append(responses);
      });
    } // if type != 3
  }
}

function createResultStatistics(title) {
  let card = create("div", {
    class: "card bg-white border px-3 py-4 my-1",
  });
  document.getElementById("questions").append(card);

  card.append(
    create(
      "h5",
      {
        class: "title",
      },
      `${__["Result"]}: (${title})`
    )
  );
}

function activePage(selector) {
  $(`.page-btn[data-page="${selector}"]`)
    .removeClass("btn-white")
    .addClass("active")
    .siblings()
    .addClass("btn-white")
    .removeClass("active");
  $(".page").fadeOut(150);
  $("#" + selector).fadeIn(150);
}

function createEnrty(i, info) {
  let card = $("#preview-responses");

  let entry = create("div", {
    class: "entry",
  });
  card.append(entry);

  entry.append(
    create("h3", { class: "title" }, i + 1 + ". " + info["question"]["title"])
  );
  if (info != null && info.answers_value != null) {
    if (info?.question?.type != null && info?.question?.type == 5) {
      info.answers_value.forEach((answer) => {
        entry.append(
          create(
            "div",
            { class: "answer" },
            `<b>equation:</b> ${answer["formula"]}`
          )
        );
      });
    } else {
      info.answers_value.forEach((answer) => {
        entry.append(create("div", { class: "answer" }, answer["text"]));
      });
    }
  }
  if (info != null && info.variables_values != null) {
    info.variables_values.forEach((variable) => {
      entry.append(
        create(
          "div",
          { class: "answer variable" },
          `${variable["title"]} = ${variable["pivot"]["value"]}`
        )
      );
    });
  }
  if (
    info != null &&
    info.answers_value != null &&
    info?.question?.type != null &&
    info?.question?.type == 5 &&
    info?.answers_value[0]?.pivot?.value != null
  ) {
    entry.append(
      create(
        "div",
        { class: "answer" },
        `<b>answer:</b> ${info.answers_value[0].pivot.value}`
      )
    );
  }
}

function createResult(result) {
  let card = $("#preview-responses");

  let entry = create("div", {
    class: "entry",
  });
  card.append(entry);

  entry.append(create("h3", { class: "title" }, __["Shown Result:"]));
  if (result != null && typeof result !== "undefined") {
    entry.append(create("div", { class: "answer" }, result));
  }
}

function createFormEnrty(i, info) {
  let card = $("#preview-responses");

  let entry = create("div", {
    class: "entry",
  });
  card.append(entry);

  if (info != null && info.fields_values != null) {
    entry.append(
      create("h3", { class: "title" }, i + 1 + ". " + info["question"]["title"])
    );
    info.fields_values.forEach((field) => {
      entry.append(
        create(
          "div",
          { class: "answer" },
          `${field["label"]}: ${field["pivot"]["value"] ?? ""}`
        )
      );
    });
  }
}

function activeResponse(responseId) {
  $("#preview-responses").html("<div class='loading'>Loading ...</div>");

  let resEl = $("#submission-" + responseId);
  $("#preview-date").text(resEl.data("date"));
  $("#preview-time").text(resEl.data("time"));
  $("#preview-name").text(resEl.data("name"));

  $.ajax({
    url: `${getResponseUrl}/${responseId}`,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      if (
        res != null &&
        res.entries_with_values != null &&
        res.entries_with_values.length > 0
      ) {
        $("#preview-responses").html("");
        res.entries_with_values.forEach((entry, i) => {
          if (entry.fields_values != null && entry.fields_values.length > 0) {
            createFormEnrty(i, entry);
          } else {
            createEnrty(i, entry);
          }
        });
      }
      if (res != null && res.result != null) {
        createResult(res.result);
        // if (result.title != null && result.title.length > 0) {
        // }
      }
    },
    error: (err) => console.log(err),
  });
}
