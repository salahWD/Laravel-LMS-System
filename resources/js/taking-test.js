const answerCoolDown = 2000;
let forms = document.querySelectorAll(".disabled-form");
forms.forEach((e) => {
  e.addEventListener("submit", function (e) {
    e.preventDefault();
  });
});

let globalActiveQuestion = 0;

function updateStepBtnsUi() {
  let nextTarget = $(
    `#questions-holder > .question[data-order="${globalActiveQuestion + 1}"]`
  );
  if (nextTarget.length == 0 || nextTarget.hasClass("no-answer-yet")) {
    $("#forward-ques-btn").addClass("opacity-50");
  } else {
    $("#forward-ques-btn").removeClass("opacity-50");
  }
  if (globalActiveQuestion <= 0) {
    $("#back-ques-btn").addClass("opacity-50");
  } else {
    $("#back-ques-btn").removeClass("opacity-50");
  }
}

$("#forward-ques-btn").click(function () {
  let target = $(
    `#questions-holder > .question[data-order="${globalActiveQuestion + 1}"]`
  );
  if (!target.hasClass("no-answer-yet")) {
    activeQuestion(target.attr("id"));
  }
});
$("#back-ques-btn").click(function () {
  if (globalActiveQuestion - 1 == 0) {
    globalActiveQuestion = 0;
    activeQuestion("test-intro");
  } else {
    activeQuestion(
      $(
        `#questions-holder > .question[data-order="${
          globalActiveQuestion - 1
        }"]`
      ).attr("id")
    );
  }
});

function activeQuestion(selectorId) {
  let target = $(`#${selectorId}`);
  target.removeClass("no-answer-yet");
  let target_order = parseInt(target.data("order"));
  if (!isNaN(target_order)) {
    globalActiveQuestion = target_order > 0 ? target_order : 0;
  } else if (target.hasClass("result") || target.attr("id") == "no-result") {
    // remove back/forward btns on result or (Not Result Found)
    $("#steps-btns").fadeOut();
  }
  updateStepBtnsUi();
  target
    .fadeIn(300)
    .css("display", "flex")
    .addClass("active")
    .siblings()
    .each(function () {
      $(this).removeClass("active");
      $(this).fadeOut(300);
    });
  if (
    $("#" + selectorId).hasClass("question") &&
    $("#" + selectorId).data("id")
  ) {
    $(document).unbind("keypress");
    if (parseInt($("#" + selectorId).data("type")) == 3) {
      $(document).on("keypress", function (e) {
        if (e.which == 13) {
          $("#" + selectorId)
            .find(".nex-btn")
            .click();
        }
      });
    }
  }
}

function notEmpty(param) {
  return typeof param != "undefined" && param != null && param != "";
}

$("#intro-btn").one("click", function () {
  if ($(this).data("target") != "end") {
    let target = $(this).data("target");
    create_submittion(function () {
      activeQuestion(target);
    });
  } else {
    console.log("::::::: INTRO END :::::::");
  }
});

/*===============
    App Flow Start
        ===============*/

// scoring
var TotalScore = 0;
var TotalCorrectQuestions = 0;

function questionAnswered(questionId, selectedAnswers = null, defaultTarget) {
  let url = AnswersRoute;
  let data = {
    id: questionId,
  };

  // form fields handling
  if (
    $("#question-" + questionId).data("type") == 3 &&
    selectedAnswers != null
  ) {
    data["form_data"] = selectedAnswers;
    url = FormRoute;
  } else if (selectedAnswers != null) {
    // answers handling
    data["answers"] = selectedAnswers;
  }

  if (window.submission != null) {
    data["submission_code"] = window.submission;
  } else {
    if (
      $(".questions .intro").length == 0 &&
      $(".questions .question.no-answer-yet.active").length > 0 &&
      $(".questions .question.no-answer-yet.active").data("order") == 1
    ) {
      return create_submittion(function () {
        questionAnswered(questionId, selectedAnswers, defaultTarget);
      });
    }
  }

  // get variables of next question (if next question is an equation question)
  let next_question = $(`#question-${questionId} + .question`);
  if (next_question.data("type") == 5) {
    data["equation_question"] = next_question.data("id");
  }

  $.ajax({
    url: url,
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: data,
    success: function (res) {
      if (res != null) {
        if (res != null && res.submission_code != null) {
          window.submission = res.submission_code;
        }

        if (res != null && res.equation_variables != null) {
          handleVariablesValue(next_question, res.equation_variables);
        }

        if (
          res != null &&
          res.equation_result != null &&
          res.equation_result == true
        ) {
          let score = $("#question-" + questionId).data("score");
          TotalScore += parseInt(score);
          if (score) {
            TotalCorrectQuestions++;
          }
        }

        if (res != null && res.certificate != null && res.certificate == true) {
          $("#certificate-download").show();
          $("#certificate-noti").show();
        }

        // return => next
        if (defaultTarget == "end") {
          calculateResult(TotalScore);
        } else {
          activeQuestion(defaultTarget);
        }
      } else {
        console.log("no response");
      }
    },
    error: (err) => {
      console.error(err);
      activeQuestion(defaultTarget);
    },
  });
}

function calculateResult(score) {
  let result = $(".questions .result").first();
  let errors = [];

  if (result != null && result != undefined) {
    $(`#${result.attr("id")}-score`).text(score);

    if (result.data("min-score") != undefined) {
      if (score < result.data("min-score")) {
        errors.push(
          __["your score is less then"] +
            ` (${result.data("min-score")}), ` +
            __["try again later"]
        );
      }
    }
    if (result.data("min-percent") != undefined) {
      if (
        Math.round((score / getPossibleScore()) * 100) <
        Math.trunc(result.data("min-percent"))
      ) {
        errors.push(
          __["your overall percent is less then"] +
            ` ${Math.trunc(result.data("min-percent"))}%, ` +
            __["try again later"]
        );
      }
    }
    console.log(TotalCorrectQuestions);
    console.log(result.data("min-correct"));
    if (result.data("min-correct") != undefined) {
      if (result.data("min-correct") > TotalCorrectQuestions) {
        errors.push(
          __["your overall correct answered questions are less then"] +
            ` ${result.data("min-correct")}, ` +
            __["try again later"]
        );
      }
    }
    if (errors.length > 0) {
      let resultDesc = result.find(".desc").first();
      result.find(".failed-title").show();
      result.find(".success-title").hide();
      result.find(".btn-danger").show();
      result.find(".btn-primary").hide();
      resultDesc.html("");
      errors.forEach((err) => {
        resultDesc.append(`
          <p class="lead text-error">${err}</p>
        `);
      });
    }
    activeQuestion(result.attr("id"));
  } else {
    activeQuestion("no-result");
  }
}

// text, media
$(".questions .question.media .nex-btn").each(function () {
  $(this).click(function () {
    let btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, 2000);

    questionAnswered(
      $(this).parents(".question").data("id"),
      null, // no answers to send
      $(this).data("target")
    );
  });
});

// form
$(".questions .question.form").each(function () {
  let question = $(this);
  let form = $(this).find(".form");

  $(this)
    .find(".nex-btn")
    .click(function () {
      let btn = $(this);
      btn.prop("disabled", true);
      setTimeout(function () {
        btn.prop("disabled", false);
      }, 2000);

      let requiredErrors = [];
      if (form.find("input").length > 0) {
        form.find("input").each(function () {
          if ($(this).prop("required") == true) {
            if (["text", "textarea"].includes($(this).attr("type"))) {
              if ($(this).val().length < 1) {
                requiredErrors.push($(this).attr("id"));
              }
            } else if ($(this).attr("type") == "email") {
              if (
                !$(this)
                  .val()
                  .toLowerCase()
                  .match(
                    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                  )
              ) {
                requiredErrors.push($(this).attr("id"));
              }
            } else if ($(this).attr("type") == "number") {
              if (isNaN($(this).val()) || $(this).val() <= 0) {
                requiredErrors.push($(this).attr("id"));
              }
            }
          }
        });
      }

      if (requiredErrors.length == 0) {
        let form_data = {};
        form
          .find(
            "input[type='radio']:checked,[type='checkbox']:checked,input:not([type='radio']):not([type='checkbox']),select,textarea"
          )
          .each(function (i) {
            form_data[$(this).data("id")] = {
              id: $(this).data("id"),
              type: $(this).data("type"),
              value: $(this).val(),
            };
          });

        questionAnswered(
          $(this).parents(".question").data("id"),
          form_data,
          $(this).data("target")
        );
      } else {
        requiredErrors.forEach((el) => {
          document.getElementById(el).classList.add("border-danger");
        });
        question
          .find(".btns-row .error-box")
          .html(
            "<div class='alert alert-danger'>" +
              __["required fields are not filled"] +
              "</div>"
          );
      }
    });

  $(this)
    .find(".btn.skip")
    .first()
    .click(function () {
      let btn = $(this);
      btn.prop("disabled", true);
      setTimeout(function () {
        btn.prop("disabled", false);
      }, 2000);

      questionAnswered(
        $(this).parents(".question").data("id"),
        {},
        $(this).data("target")
      );
    });
});

// equation
$(".questions .question.equation").each(function () {
  let question = $(this);

  $(this)
    .find(".nex-btn")
    .click(function () {
      let btn = $(this);
      btn.prop("disabled", true);
      setTimeout(function () {
        btn.prop("disabled", false);
      }, 2000);

      let requiredErrors = [];
      if (!question.find(".answer_val").val().length > 0) {
        requiredErrors.push(question.attr("id") + "-answer");
      }

      if (requiredErrors.length == 0) {
        let val = question.find("#" + question.attr("id") + "-answer");
        questionAnswered(
          $(this).parents(".question").data("id"),
          [val.val()],
          $(this).data("target")
        );
      } else {
        requiredErrors.forEach((el) => {
          document.getElementById(el).classList.add("border-danger");
        });
        question
          .find(".btns-row .error-box")
          .html(
            "<div class='alert alert-danger'>" +
              __["required fields are not filled"] +
              "</div>"
          );
      }
    });

  $(this)
    .find(".btn.skip")
    .first()
    .click(function () {
      let btn = $(this);
      btn.prop("disabled", true);
      setTimeout(function () {
        btn.prop("disabled", false);
      }, 2000);

      questionAnswered(
        $(this).parents(".question").data("id"),
        {},
        $(this).data("target")
      );
    });
});

$(".answers").each(function () {
  let selectedAnswers = [];

  if ($(this).data("multi") == 1) {
    let questionScores = [];
    $(this)
      .find(".answer")
      .each(function () {
        $(this).click(function () {
          if (selectedAnswers.includes($(this).data("id"))) {
            const idIndex = selectedAnswers.indexOf($(this).data("id"));
            const scoreIndex = questionScores.indexOf($(this).data("score"));
            if (idIndex > -1) {
              selectedAnswers.splice(idIndex, 1);
              questionScores.splice(scoreIndex, 1);
              $(this).removeClass("active");
            }
          } else {
            selectedAnswers.push($(this).data("id"));
            questionScores.push($(this).data("score"));
            $(this).addClass("active");
          }
        });
      });
    $(this)
      .siblings(".nex-btn")
      .click(function () {
        let btn = $(this);
        btn.prop("disabled", true);
        setTimeout(function () {
          btn.prop("disabled", false);
        }, 2000);

        let score = questionScores.reduce(
          (totalAnswers, a) => totalAnswers + a,
          0
        );
        TotalScore += parseInt(score);
        if (score > 0) {
          TotalCorrectQuestions++;
        }

        if (selectedAnswers.length > 0) {
          questionAnswered(
            $(this).parents(".question").data("id"),
            selectedAnswers,
            $(this).data("target")
          );
        }
      });
  } else {
    $(this)
      .find(".answer")
      .each(function () {
        $(this).on("click", function () {
          answerClickHandler($(this), selectedAnswers);
        });
      });
  }
});

function answerClickHandler(answerItem, answers) {
  TotalScore += parseInt(answerItem.data("score"));
  if (answerItem.data("score") > 0) {
    TotalCorrectQuestions++;
  }

  if (answerItem.data("id") != undefined) {
    questionAnswered(
      answerItem.parents(".question").data("id"),
      [answerItem.data("id")],
      answerItem.data("target")
    );
  }

  answerItem.off("click");
  setTimeout(function () {
    answerItem.on("click", function () {
      answerClickHandler(answerItem, answers);
    });
    console.log("after timeout");
  }, answerCoolDown);
}

function getPossibleScore() {
  let totalPossibleScore = 0;
  $(".questions .answers").each(function () {
    if ($(this).data("multi") == 1) {
      $(this)
        .find(".answer")
        .each(function () {
          if ($(this).data("score") > 0) {
            totalPossibleScore += $(this).data("score");
          }
        });
    } else {
      let largestScore = 0;
      $(this)
        .find(".answer")
        .each(function () {
          if ($(this).data("score") > largestScore) {
            largestScore = $(this).data("score");
          }
        });
      totalPossibleScore += largestScore;
    }
  });
  return totalPossibleScore;
}

function create_submittion(callback = null) {
  let data = {
    test_id: testId,
  };
  let next_question = $(`.intro + .question`);
  if (next_question.data("type") == 5) {
    data.equation_question = next_question.data("id");
  }
  $.ajax({
    method: "POST",
    url: submissionRoute,
    data: data,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      if (res.submission != undefined) {
        window.submission = res.submission;
      }
      if (res != null && res.equation_variables != null) {
        handleVariablesValue(next_question, res.equation_variables);
      }
      if (callback != null) {
        callback();
      }
    },
    error: (err) => console.error(err),
  });
}

function handleVariablesValue(question, data) {
  let desc = question.find(".question-desc");
  let txt = desc.text();
  let matches = new Set(txt.match(/\[[^\s]+\]/g));
  if (matches != null && matches.size > 0) {
    matches.forEach((match) => {
      if (data[match] != undefined && data[match] != "undefined") {
        txt = txt.replaceAll(match, data[match]);
      }
    });
    desc.text(txt);
  }
}
