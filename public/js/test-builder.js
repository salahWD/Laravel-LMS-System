const sidebarId = "modal_sidebar";
const previewId = "modal_preview";
const AlertsTimeout = 5000;
const AlertsTimeoutInBuilder = 3500;
const actionCoolDownTime = 1500;

$("#editNameSubmit").click(function () {
  editTestName($("#testTitle").val());
});

$("#copy-link").click(function () {
  if (!navigator.clipboard) {
    let input = $("<input>").attr("value", $(this).data("value"));
    $(document.body).append(input);
    input.select();
    document.execCommand("copy");
    input.remove();
  } else {
    navigator.clipboard.writeText($(this).data("value")).catch(function () {
      console.error("err"); // error
    });
  }

  $(this).find(".text").text(__["copied"]);
  setTimeout(() => {
    $(this).find(".text").text(__["copy"]);
  }, 1500);
});

$("#main_modal").on("hide.bs.modal", function (e) {
  resetModal();
});

$("#main_modal").on("show.bs.modal", function (e) {
  let modalParams = JSON.parse(e.relatedTarget ?? e);
  let type_id = modalParams.itemType;

  $(this).find(".modal-title").text(modalParams.itemName);
  if (modalParams.type == "question") {
    if (type_id == 1) {
      if (modalParams != null && modalParams.isEdit) {
        textQuestionModal(modalParams.data);
        $("#submit_modal").on("click", function () {
          updateTextQuestion(modalParams.data.id);
        });
      } else {
        textQuestionModal();
        $("#submit_modal").on("click", function () {
          createTextQuestion();
        });
      }
    } else if (type_id == 2) {
      if (modalParams != null && modalParams.isEdit) {
        imageQuestionModal(modalParams.data);
        $("#submit_modal").on("click", function () {
          updateImageQuestion(modalParams.data.id);
        });
      } else {
        imageQuestionModal();
        $("#submit_modal").on("click", function () {
          createImageQuestion(type_id, testType);
        });
      }
    } else if (type_id == 3) {
      if (modalParams != null && modalParams.isEdit) {
        formModal(modalParams.data);
        $("#submit_modal").on("click", function () {
          updateFormQuestion(modalParams.data.id);
        });
      } else {
        formModal();
        $("#submit_modal").on("click", function () {
          createFormQuestion(type_id, testType);
        });
      }
    } else if (type_id == 4) {
      if (modalParams != null && modalParams.isEdit) {
        mediaModal(modalParams.data);
        $("#submit_modal").on("click", function () {
          updateImageOrVideo(modalParams.data.id);
        });
      } else {
        mediaModal();
        $("#submit_modal").on("click", function () {
          createImageOrVideo(type_id, testType);
        });
      }
    } else if (type_id == 5) {
      if (modalParams != null && modalParams.isEdit) {
        equationQuestionModal(modalParams.data);
        $("#submit_modal").on("click", function () {
          updateEquationQuestion(modalParams.data.id);
        });
      } else {
        equationQuestionModal();
        $("#submit_modal").on("click", function () {
          createEquationQuestion(type_id, testType);
        });
      }
    } else {
      resetModal();
      console.error("wrong type");
    }
  } else if (modalParams.type == "certificate") {
    if (type_id == 1) {
      if (modalParams != null && modalParams.isEdit) {
        certificateModal(modalParams.data);
        $("#submit_modal").on("click", function () {
          updateCertificate(modalParams.data.id);
        });
      } else {
        certificateModal();
        $("#submit_modal").on("click", function () {
          createCertificate(modalParams.itemType, testType);
        });
      }
    }
  }
});

const fields_types = [
  { type: 1, icon: "fa-user" },
  { type: 2, icon: "fa-user" },
  { type: 3, icon: "fa-envelope" },
  { type: 4, icon: "fa-phone" },
  { type: 5, icon: "fa-bars" },
  { type: 6, icon: "fa-align-center" },
  { type: 7, icon: "fa-check-square-o" },
  { type: 8, icon: "fa-caret-square-o-down" },
];

function hasIntroHandler() {
  if ($("#intro-switch").prop("checked") == true) {
    $("#introTitle").fadeIn();
    $("#introDesc").fadeIn();
    $("#introButton").fadeIn();
    $("#intro-holder").slideDown();
  } else {
    $("#intro-holder").slideUp();
    $("#introTitle").fadeOut();
    $("#introDesc").fadeOut();
    $("#introButton").fadeOut();
  }
}

(function () {
  $("#intro-switch").on("input", function () {
    hasIntroHandler();
  });

  let imageHolder = document.getElementById("previewImageIntro");

  introImageRemove.addEventListener("click", function () {
    Swal.fire({
      title: "Are You Sure ?",
      customClass: {
        confirmButton: "btn btn-danger",
        cancelButton: "btn btn-primary me-4",
      },
      reverseButtons: true,
      buttonsStyling: false,
      showCancelButton: true,
      confirmButtonText: "Delete",
      preConfirm: async (violation) => {
        try {
          const response = await fetch(intro_image_delete, {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
          });
          if (!response.ok) {
            return Swal.showValidationMessage(`
              ${JSON.stringify(await response.json())}
            `);
          }
          return response.json();
        } catch (error) {
          Swal.showValidationMessage(`
            Request failed: ${error}
          `);
        }
      },
    }).then((result) => {
      if (result.isConfirmed) {
        introImage.value = "";
        introImageRemove.classList.add("d-none");
        imageHolder.classList.add("d-none");
      }
    });
  });

  introImage.addEventListener("input", function () {
    let [file] = this.files;

    if (file) {
      introImageRemove.classList.remove("d-none");
      imageHolder.classList.remove("d-none");
      imageHolder.querySelector("img").src = URL.createObjectURL(file);
    } else {
      introImage.value = "";
      introImageRemove.classList.add("d-none");
      imageHolder.classList.add("d-none");
    }
  });

  $("#introTitle, #introDesc, #introButton").each(function () {
    $(this).on("click", function () {
      let target = $("#" + $(this).data("target"));
      let parent = $(this);
      $(this).fadeOut(100, function () {
        target.fadeIn(100);
        target.focus();
        target.on("blur", function () {
          if (["introTitle", "introButton"].includes(parent.attr("id"))) {
            if (target.val().length > 0) {
              target.removeClass("border-danger");
              parent.text(target.val());
              $(this).fadeOut(100, function () {
                parent.fadeIn(100);
              });
            } else {
              target.addClass("border-danger");
            }
          } else {
            parent.text(target.val());
            $(this).fadeOut(100, function () {
              parent.fadeIn(100);
            });
          }
        });
      });
    });
  });

  $(".bind[data-target]").each(function () {
    function callback() {
      $("#" + $(this).data("target")).text($(this).val());
    }
    if ($(this).data("event")) {
      $(this).on($(this).data("event"), callback);
    } else {
      $(this).on("input", callback);
    }
  });

  $("#save-intro").on("click", function () {
    var formData = new FormData($("#intro-form")[0]);
    $.ajax({
      method: "POST",
      url: ajax_test_intro,
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (res) {
        let alert = makeSuccessAlert(__["Test Updated Successfully"]);
        setTimeout(() => {
          alert.close();
        }, AlertsTimeoutInBuilder);
      },
      error: (err) => console.error(err),
    });
  });
  $("#save-result").on("click", function () {
    var formData = new FormData($("#result-form")[0]);
    $.ajax({
      method: "POST",
      url: ajax_result,
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (res) {
        let alert = makeSuccessAlert(__["Test Updated Successfully"]);
        setTimeout(() => {
          alert.close();
        }, AlertsTimeoutInBuilder);
      },
      error: (err) => console.error(err),
    });
  });
})();

// config functions

function dragAndDropItemsHandler() {
  let quiestionsContainer = $("#contentItemsContainer");
  let draggableQuestions = $(".draggable-question");
  let resultsContainer = $("#resultItemsContainer");
  let draggableResults = $(".draggable-result");
  let resultElements = document.querySelectorAll(
    "#mapping-modal .results .result"
  );

  // quistions drage/drop logic
  quiestionsContainer.on("dragover", function (e) {
    e.preventDefault();
  });
  draggableQuestions.on("dragstart", function (e) {
    $(resultsContainer).addClass("opacity-50");
    let info = {
      type: "question",
      itemType: e.target.dataset.item,
      itemName: e.target.dataset.name,
    };
    e.originalEvent.dataTransfer.setData("text", JSON.stringify(info));
  });
  draggableQuestions.on("dragend", function (e) {
    $(resultsContainer).removeClass("opacity-50");
  });
  quiestionsContainer.on("drop", function (e) {
    e.preventDefault();
    var data = e.originalEvent.dataTransfer.getData("text"); // item => info object
    if (isJson(data)) {
      data = JSON.parse(data);
      if (data.type == "question") {
        if (data.itemType != null) {
          $("#main_modal").modal("show", JSON.stringify(data));
        }
      }
    }
  });

  // results drage/drop logic
  resultsContainer.on("dragover", function (e) {
    e.preventDefault();
  });
  draggableResults.on("dragstart", function (e) {
    $(quiestionsContainer).addClass("opacity-50");
    $("#resultInvisiblity").css("opacity", 0.25);
    let info = {
      type: "certificate",
      itemType: e.target.dataset.item,
      itemName: e.target.dataset.name,
    };
    e.originalEvent.dataTransfer.setData("text", JSON.stringify(info));
  });
  draggableResults.on("dragend", function (e) {
    $(quiestionsContainer).removeClass("opacity-50");
    $("#resultInvisiblity").css("opacity", 1);
  });
  resultsContainer.on("drop", function (e) {
    e.preventDefault();
    var data = e.originalEvent.dataTransfer.getData("text"); // item => info object
    if (isJson(data)) {
      data = JSON.parse(data);
      if (data.type == "certificate") {
        if (data.itemType != null) {
          $("#main_modal").modal("show", JSON.stringify(data));
        }
      }
    }
  });

  // maping modal logic
  resultElements.forEach((result) => {
    $(result).on("dragover", function (e) {
      e.preventDefault();
    });
    $(result).on("drop", function (e) {
      e.preventDefault();
      var data = JSON.parse(e.originalEvent.dataTransfer.getData("text"));

      let answer = $(`#answer-${data.result_id}`);
      if (
        !$(result)
          .find(".connected-answers")
          .find(`.answer[data-answer="${answer.data("id")}"]`).length > 0
      ) {
        if (answer.find(".letter").text() == "") {
          $(result)
            .find(".connected-answers")
            .append(
              $(`<div data-answer="${answer.data(
                "id"
              )}" class="answer rounded" style="background-image: ${answer
                .find(".letter")
                .css("background-image")
                .replace(/\"/g, "")}">
                <i class="icon fa fa-trash"></i>
              </div>`).click(function () {
                $(this).fadeOut(150, function () {
                  this.remove();
                });
              })
            );
        } else {
          $(result)
            .find(".connected-answers")
            .append(
              $(`<div data-answer="${answer.data(
                "id"
              )}" class="answer rounded" style="background-color: ${answer
                .find(".letter")
                .css("background-color")}">
                <span class="letter">${answer.find(".letter").text()}</span>
                <i class="icon fa fa-trash"></i>
              </div>`).click(function () {
                $(this).fadeOut(150, function () {
                  this.remove();
                });
              })
            );
        }
      }
    });
  });
}

function questionsReorderConfig() {
  var questionsReorderContainer = document.getElementById(
    "contentItemsContainer"
  );

  new Sortable(questionsReorderContainer, {
    animation: 150,
    handle: ".handler-questions",
    ghostClass: "reordering",
    filter: ".filtered",
  });

  var questions = questionsReorderContainer.querySelectorAll(".question");

  questions.forEach((el) => {
    el.addEventListener("dragstart", function () {
      questions.forEach((quest) => {
        quest.classList.add("filtered");
      });
    });
  });

  $(questionsReorderContainer).on("drop", function (e) {
    let val = e.originalEvent.dataTransfer.getData("text");
    if (!isJson(val)) {
      let orders = [];
      document
        .querySelectorAll("#contentItemsContainer .question")
        .forEach(function (order, i) {
          order.dataset.order = i + 1;
          order.querySelector(".order b").innerText = order.dataset.order;
          orders.push({ id: order.dataset.id, order: order.dataset.order });
        });
      reorderQuestions(orders, questions);
    }
  });
}

function reorderQuestions(orders, questions) {
  $.ajax({
    method: "POST",
    url: questions_reorder,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      questions: orders,
    },
    success: function (res) {
      if (res.status) {
        let alert = makeSuccessAlert(__["Questions Reordered Successfully"]);
        setTimeout(() => {
          alert.close();
        }, AlertsTimeoutInBuilder);
      }
    },
    complete: function () {
      questions.forEach((el) => {
        el.classList.remove("filtered");
      });
    },
    error: function (res) {
      console.error(res);
    },
  });
}

function editTestName(newName) {
  $.ajax({
    type: "POST",
    url: edit_test_name_route, // This is what I have updated
    data: { title: newName },
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
  }).done(function (msg) {
    $("#testTitleShow").text(newName);

    let alert = makeSuccessAlert(msg);
    setTimeout(() => {
      alert.close();
    }, AlertsTimeout);
  });
}

function resetModal() {
  $("#submit_modal").off("click");
  if ($("#sidebarOption").length > 0) {
    $("#sidebarOption").remove();
  }
  $("#submit_modal").click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
  });
  $("#modal_sidebar").html("");
  $("#modal_sidebar").show();
  $("#modal_preview").html("");
}

function resetMappingModal() {
  $("#submit_mapping_modal").off("click");
  $("#submit_mapping_modal").click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
  });
  $("#mapping-modal-answers").html("");
  $("#mapping-modal-results").html("");
}

function resetBranchingModal() {
  $("#add-new-condition").off("click");
  $("#submit_branching_modal").off("click");
  $("#submit_branching_modal").click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
  });
  $("#conditions-container").html("");
}

// ==== [AJAX] Create Questions/Results Functions ====

function createTextQuestion() {
  var formData = new FormData();
  $("#answersContainer .answer-val").each(function (el) {
    formData.append(`ar_answers[${el}][text]`, $(this).val());
    formData.append(
      `ar_answers[${el}][score]`,
      $("#" + $(this).attr("id") + "-score-input").val()
    );
    // formData.append(
    //   `ar_answers[${el}][en_text]`,
    //   $("#" + $(this).attr("id") + "-second").val()
    // );
    // if (
    //   (formData.get(`ar_answers[${el}][text]`) == null ||
    //     formData.get(`ar_answers[${el}][text]`) == "") &&
    //   formData.get(`ar_answers[${el}][en_text]`) != null &&
    //   formData.get(`ar_answers[${el}][en_text]`) != ""
    // ) {
    //   formData.append(
    //     `ar_answers[${el}][text]`,
    //     formData.get(`ar_answers[${el}][en_text]`)
    //   );
    // }
    formData.append(`ar_answers[${el}][order]`, $(this).parent().data("order"));
  });

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("type_id", 1);
  let imageValue = $("#imageInput").prop("files")[0];
  if (imageValue != undefined) {
    formData.append("image", imageValue);
  }
  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  // if (
  //   $("#EnQuestionTitleInput").val() &&
  //   $("#EnQuestionTitleInput").val().length > 0
  // ) {
  //   formData.append("en_question_title", $("#EnQuestionTitleInput").val());
  //   formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
  // }
  // if (
  //   (formData.get("ar_question_title") == null ||
  //     formData.get("ar_question_title") == "") &&
  //   formData.get("en_question_title") != null &&
  //   formData.get("en_question_title") != ""
  // ) {
  //   formData.set("ar_question_title", formData.get("en_question_title"));
  // }
  formData.append(
    "is_multi_select",
    document.getElementById("multiSelect").checked == true ? 1 : 0
  );
  formData.append("order", $("#contentItemsContainer .question").length + 1);

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: questions_ajax_route,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      let title =
        formData.get(LANG + "_question_title") ??
        formData.get("ar_question_title");
      createQuestion(
        $("#contentItemsContainer .question").length + 1,
        title,
        res,
        1
      );
      $("#contentRemovableInfo").addClass("d-none");
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Added Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      console.error(res);
      let errorsTable = {
        ar_question_title: "EnQuestionTitleInput",
        "ar_answers.0.text": "Answer-1",
        "ar_answers.1.text": "Answer-2",
        en_question_title: "ArQuestionTitleInput",
        "en_answers.0.text": "Answer-1-second",
        "en_answers.1.text": "Answer-2-second",
      };

      $("#modal_sidebar input").removeClass("border border-danger");
      Object.keys(res.responseJSON.errors).forEach((err) => {
        $("#" + errorsTable[err]).addClass("border border-danger");
      });
    },
  });
}

function createImageQuestion() {
  var formData = new FormData();
  $("#answersContainer .answer-val").each(function (el) {
    formData.append(`ar_answers[${el}][text]`, $(this).val());
    if (testType == 1) {
      formData.append(
        `ar_answers[${el}][score]`,
        $("#" + $(this).attr("id") + "-score-input").val()
      );
    }
    formData.append(
      `ar_answers[${el}][en_text]`,
      $("#" + $(this).attr("id") + "-second").val()
    );
    if (
      (formData.get(`ar_answers[${el}][text]`) == null ||
        formData.get(`ar_answers[${el}][text]`) == "") &&
      formData.get(`ar_answers[${el}][en_text]`) != null &&
      formData.get(`ar_answers[${el}][en_text]`) != ""
    ) {
      formData.append(
        `ar_answers[${el}][text]`,
        formData.get(`ar_answers[${el}][en_text]`)
      );
    }
    formData.append(`ar_answers[${el}][order]`, $(this).parent().data("order"));
    let imageValue = $(`#${$(this).attr("id")}-file`).prop("files")[0];
    if (imageValue != undefined && imageValue != "undefined") {
      formData.append(`ar_answers[${el}][image]`, imageValue);
    }
  });

  let imageValue = $("#imageInput").prop("files")[0];
  if (imageValue != undefined && imageValue != "undefined") {
    formData.append("image", imageValue);
  }

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("type_id", 2);
  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  // if ($("#ArQuestionTitleInput").val().length > 0) {
  //   formData.append("en_question_title", $("#ArQuestionTitleInput").val());
  //   formData.append("en_question_desc", $("#ArquestionDescInpiut").val());
  // }
  // if (
  //   (formData.get("ar_question_title") == null ||
  //     formData.get("ar_question_title") == "") &&
  //   formData.get("en_question_title") != null &&
  //   formData.get("en_question_title") != ""
  // ) {
  //   formData.set("ar_question_title", formData.get("en_question_title"));
  // }
  formData.append(
    "is_multi_select",
    document.getElementById("multiSelect").checked == true ? 1 : 0
  );
  formData.append("order", $("#contentItemsContainer .question").length + 1);

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: questions_ajax_route,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      let title =
        formData.get(LANG + "_question_title") ??
        formData.get("ar_question_title");
      createQuestion(
        $("#contentItemsContainer .question").length + 1,
        title,
        res,
        2
      );
      $("#contentRemovableInfo").addClass("d-none");
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Added Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      console.log(res);
      let errorsTable = {
        ar_question_title: "EnQuestionTitleInput",
        "ar_answers.0.text": "Answer-1",
        "ar_answers.1.text": "Answer-2",
        "ar_answers.2.text": "Answer-3",
        "ar_answers.3.text": "Answer-4",
        "ar_answers.4.text": "Answer-5",
        "ar_answers.5.text": "Answer-6",
        "ar_answers.6.text": "Answer-7",
        "ar_answers.7.text": "Answer-8",
        "ar_answers.8.text": "Answer-9",
        "ar_answers.9.text": "Answer-10",
        "ar_answers.0.image": "Answer-1-img-btn",
        "ar_answers.1.image": "Answer-2-img-btn",
        "ar_answers.2.image": "Answer-3-img-btn",
        "ar_answers.3.image": "Answer-4-img-btn",
        "ar_answers.4.image": "Answer-5-img-btn",
        "ar_answers.5.image": "Answer-6-img-btn",
        "ar_answers.6.image": "Answer-7-img-btn",
        "ar_answers.7.image": "Answer-8-img-btn",
        "ar_answers.8.image": "Answer-9-img-btn",
        "ar_answers.9.image": "Answer-10-img-btn",
        en_question_title: "ArQuestionTitleInput",
        "en_answers.0.text": "Answer-1-second",
        "en_answers.1.text": "Answer-2-second",
        "en_answers.2.text": "Answer-3-second",
        "en_answers.3.text": "Answer-4-second",
        "en_answers.4.text": "Answer-5-second",
        "en_answers.5.text": "Answer-6-second",
        "en_answers.6.text": "Answer-7-second",
        "en_answers.7.text": "Answer-8-second",
        "en_answers.8.text": "Answer-9-second",
        "en_answers.9.text": "Answer-10-second",
      };

      $("#modal_sidebar input").removeClass("border border-danger");
      Object.keys(res.responseJSON.errors).forEach((err) => {
        $("#" + errorsTable[err]).addClass("border border-danger");
      });
    },
  });
}

function createFormQuestion() {
  var formData = new FormData();

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("type_id", 3);

  let media_type = $("#modal_sidebar .nav-tabs .option-link.active").data(
    "option"
  );
  formData.append("media_type", media_type);

  if (media_type == "image") {
    let imageValue = $("#imageInput").prop("files")[0];
    if (imageValue != null) {
      formData.append("image", imageValue);
    }
  } else if (media_type == "video") {
    formData.append("video", $("#video_url_input").val());
  }

  if ($("#fields-holder-preview .field").length > 0) {
    $("#fields-holder-preview .field").each(function (i) {
      if ($(this).data("id") != null && $(this).data("id") != undefined) {
        formData.append(`fields[${i}][id]`, $(this).data("id"));
      }
      formData.append(`fields[${i}][label]`, $(this).data("title"));
      formData.append(`fields[${i}][type]`, $(this).data("type"));
      formData.append(`fields[${i}][order]`, $(this).data("order"));
      if ([1, 2, 3, 4, 5, 6, 8].includes($(this).data("type"))) {
        formData.append(
          `fields[${i}][placeholder]`,
          $(this).data("placeholder")
        );
      }
      formData.append(`fields[${i}][is_required]`, $(this).data("is_required"));
      if ($(this).data("type") == 3) {
        formData.append(
          `fields[${i}][is_lead_email]`,
          $(this).data("is_lead_email")
        );
      }
      if ([7, 8].includes($(this).data("type"))) {
        //is_multiple_chooseing
        if ($(this).data("type") == 7) {
          formData.append(
            `fields[${i}][is_multiple_chooseing]`,
            $(this)[0].dataset.is_multiple_chooseing
          );
        }
        $(this)
          .find(".option-input")
          .each(function (x) {
            formData.append(`fields[${i}][options][]`, $(this).val());
          });
      }
    });
  } else {
    $("#fields-holder-preview")
      .html("<div class='alert alert-danger'>" + __["No Fields"] + "</div>")
      .removeClass("d-none");
    return;
  }

  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  formData.append("ar_button_label", $("#ArQuestionButtonLabel").val());
  // if ($("#ArQuestionTitleInput").val().length > 0) {
  //   formData.append("en_question_title", $("#ArQuestionTitleInput").val());
  //   formData.append("en_question_desc", $("#ArquestionDescInpiut").val());
  //   formData.append("en_button_label", $("#ArQuestionButtonLabel").val());
  // }
  // if (
  //   (formData.get("ar_question_title") == null ||
  //     formData.get("ar_question_title") == "") &&
  //   formData.get("en_question_title") != null &&
  //   formData.get("en_question_title") != ""
  // ) {
  //   formData.set("ar_question_title", formData.get("en_question_title"));
  // }
  // if (
  //   (formData.get("ar_button_label") == null ||
  //     formData.get("ar_button_label") == "") &&
  //   formData.get("en_button_label") != null &&
  //   formData.get("en_button_label") != ""
  // ) {
  //   formData.set("ar_button_label", formData.get("en_button_label"));
  // }
  formData.append("is_skippable", $("#isSkippableCheckbox").prop("checked"));
  formData.append("order", $("#contentItemsContainer .question").length + 1);

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: questions_ajax_route,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      let title =
        formData.get(LANG + "_question_title") ??
        formData.get("ar_question_title");
      createQuestion(
        $("#contentItemsContainer .question").length + 1,
        title,
        res,
        3
      );
      $("#contentRemovableInfo").addClass("d-none");
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Added Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      console.error(res);
      let errorsTable = {
        ar_button_label: "EnQuestionButtonLabel",
        ar_question_title: "EnQuestionTitleInput",
      };
      let errorsTableAr = {
        ar_button_label: "ArQuestionTitleInput",
        ar_question_title: "ArQuestionButtonLabel",
      };

      $("#modal_sidebar input").removeClass("border border-danger");
      Object.keys(res.responseJSON.errors).forEach((err) => {
        $("#" + errorsTable[err]).addClass("border border-danger");
        $("#" + errorsTableAr[err]).addClass("border border-danger");
      });
    },
  });
}

function createImageOrVideo() {
  var formData = new FormData();

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("type_id", 4);

  let media_type = $("#modal_sidebar .nav-tabs .option-link.active").data(
    "option"
  );
  formData.append("media_type", media_type);

  if (media_type == "image") {
    let imageValue = $("#imageInput").prop("files")[0];
    if (imageValue != undefined) {
      formData.append("image", imageValue);
    } else {
      formData.append("media_type", "no");
    }
  } else if (media_type == "video") {
    if ($("#video_url_input").val() != "") {
      formData.append("video", $("#video_url_input").val());
    } else {
      formData.append("media_type", "no");
    }
  }

  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  formData.append("ar_button_label", $("#ArQuestionButtonLabel").val());
  // if ($("#EnQuestionTitleInput").val().length > 0) {
  //   formData.append("en_question_title", $("#EnQuestionTitleInput").val());
  //   formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
  //   formData.append("en_button_label", $("#EnQuestionButtonLabel").val());
  // }
  // if (
  //   (formData.get("ar_question_title") == null ||
  //     formData.get("ar_question_title") == "") &&
  //   formData.get("en_question_title") != null &&
  //   formData.get("en_question_title") != ""
  // ) {
  //   formData.set("ar_question_title", formData.get("en_question_title"));
  // }
  // if (
  //   (formData.get("ar_button_label") == null ||
  //     formData.get("ar_button_label") == "") &&
  //   formData.get("en_button_label") != null &&
  //   formData.get("en_button_label") != ""
  // ) {
  //   formData.set("ar_button_label", formData.get("en_button_label"));
  // }
  formData.append("order", $("#contentItemsContainer .question").length + 1);

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: questions_ajax_route,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      let title =
        formData.get(LANG + "_question_title") ??
        formData.get("ar_question_title");
      createQuestion(
        $("#contentItemsContainer .question").length + 1,
        title,
        res,
        4
      );
      $("#contentRemovableInfo").addClass("d-none");
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Added Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      console.log(res);
      let errorsTable = {
        ar_question_title: "EnQuestionTitleInput",
        ar_button_label: "EnQuestionButtonLabel",
        image: "image-option",
      };
      let errorsTableAr = {
        ar_question_title: "ArQuestionTitleInput",
        ar_button_label: "ArQuestionButtonLabel",
      };
      $("#modal_sidebar input").removeClass("border border-danger");
      Object.keys(res.responseJSON.errors).forEach((err) => {
        $("#" + errorsTable[err]).addClass("border border-danger");
        $("#" + errorsTableAr[err]).addClass("border border-danger");
      });
    },
  });
  // TODO => make an error visualaization before ajax is sent
}

function createEquationQuestion() {
  var formData = new FormData();

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("order", $("#contentItemsContainer .question").length + 1);
  formData.append("type_id", 5);

  let imageValue = $("#imageInput").prop("files")[0];
  if (imageValue != undefined) {
    formData.append("image", imageValue);
  }

  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  formData.append("score", $("#questionScoreInput").val());
  formData.append("is_skippable", $("#is_skippable").prop("checked"));
  formData.append(
    "answer_equation",
    Guppy("answer_equation").engine.get_content("xml")
  );
  formData.append(
    "answer_equation_formula",
    Guppy("answer_equation").engine.get_content("text")
  );
  formData.append("answer_decimals", $("#answer_decimals").val());

  $("#equation-variables-container .variable").each(function (i) {
    formData.append(`variables[${i}][decimal]`, $(this).find(".decimal").val());
    formData.append(`variables[${i}][min]`, $(this).find(".min").val());
    formData.append(`variables[${i}][max]`, $(this).find(".max").val());
    formData.append(`variables[${i}][name]`, $(this).data("var"));
  });

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: questions_ajax_route,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      createQuestion(
        $("#contentItemsContainer .question").length + 1,
        formData.get("ar_question_title"),
        res,
        5
      );
      $("#contentRemovableInfo").addClass("d-none");
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Added Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      console.log(res);
      let errorsTable = {
        question_title: "ArQuestionTitleInput",
        image: "image-option",
      };
      $("#modal_sidebar input").removeClass("border border-danger");
      Object.keys(res.responseJSON.errors).forEach((err) => {
        $("#" + errorsTable[err]).addClass("border border-danger");
      });
    },
  });
  // TODO => make an error visualaization before ajax is sent
}

function createCertificate() {
  var formData = new FormData();

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("certificate_id", $("#availableCertificates").val());
  formData.append("ar_title", $("#certificateTitleInput").val());
  formData.append("ar_desc", $("#certificateDescriptionInput").val());
  formData.append("theme", $("#certificateTheme").val());

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: ajax_create_certificate_route,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.status == true) {
        $("#certificateRemovableInfo").addClass("d-none");
        $(".questions-sidebar .item.draggable-result[data-item='1'")
          .css("opacity", "0.25")
          .attr("draggable", "false");
        $(`#main_modal`).modal("hide");
        createCertificateElement(res.title, res.id);
        let alert = makeSuccessAlert(__["Result has been duplicated"]);
        setTimeout(() => {
          alert.close();
        }, AlertsTimeout);
      }
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert(__["Somthing Went Wrong !!"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

// [AJAX] update items functions

function updateTextQuestion(questionId) {
  var formData = new FormData();
  $("#answersContainer .answer-val").each(function (el) {
    formData.append(`ar_answers[${el}][id]`, $(this).data("id"));
    formData.append(`ar_answers[${el}][text]`, $(this).val());
    formData.append(
      `ar_answers[${el}][score]`,
      $("#" + $(this).attr("id") + "-score-input").val()
    );
    formData.append(`ar_answers[${el}][order]`, $(this).parent().data("order"));
    if ($(this).data("action") != undefined) {
      formData.append(`ar_answers[${el}][action]`, $(this).data("action"));
    }
  });

  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  formData.append("type_id", 1);

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

  let imageValue = $("#imageInput").prop("files")[0];
  if (imageValue != undefined) {
    formData.append("image", imageValue);
  }

  // if (is_admin && $("#EnQuestionTitleInput").val().length > 0) {
  //   formData.append("en_question_title", $("#EnQuestionTitleInput").val());
  //   formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
  // }
  // if (is_admin) {
  //   if (
  //     (formData.get("ar_question_title") == null || formData.get("ar_question_title") == "") &&
  //     (formData.get("en_question_title") != null && formData.get("en_question_title") != "")
  //   ) {
  //     formData.set("ar_question_title", formData.get("en_question_title"));
  //   }
  // }

  formData.append(
    "is_multi_select",
    document.getElementById("multiSelect").checked == true ? 1 : 0
  );

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: `${question_ajax_route}/${questionId}`,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      let titel = [null, "", "undefined"].includes(
        formData.get(LANG + "_question_title")
      )
        ? formData.get("ar_question_title")
        : formData.get(LANG + "_question_title");
      $(`#question-${questionId}-title`).text(titel);
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Updated Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert("Can't Update This Question");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
      for (let error in res.responseJSON.errors) {
        console.error(error);
      }
    },
  });
}

function updateImageQuestion(questionId) {
  var formData = new FormData();
  $("#answersContainer .answer-val").each(function (el) {
    formData.append(`ar_answers[${el}][id]`, $(this).data("id"));
    formData.append(`ar_answers[${el}][text]`, $(this).val());

    /* if ($(this).val().length > 0) {
      formData.append(`ar_answers[${el}][en_text]`, $(this).siblings(".answer-second").first().val());
    }
    if (is_admin) {
      if (
        (formData.get(`ar_answers[${el}][text]`) == null || formData.get(`ar_answers[${el}][text]`) == "") &&
        (formData.get(`ar_answers[${el}][en_text]`) != null && formData.get(`ar_answers[${el}][en_text]`) != "")
      ) {
        formData.append(`ar_answers[${el}][text]`, formData.get(`ar_answers[${el}][en_text]`));
      }
    } */

    formData.append(
      `ar_answers[${el}][score]`,
      $("#" + $(this).attr("id") + "-score-input").val()
    );
    formData.append(`ar_answers[${el}][order]`, $(this).parent().data("order"));

    if ($(this).data("action") != undefined) {
      formData.append(`ar_answers[${el}][action]`, $(this).data("action"));
    }
    formData.append(
      `ar_answers[${el}][image]`,
      $(`#${$(this).attr("id")}-file`).prop("files")[0]
    ) ?? null;
  });

  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("type_id", 2);

  let imageValue = $("#imageInput").prop("files")[0];
  if (imageValue != undefined) {
    formData.append("image", imageValue);
  }

  /* if (is_admin && $("#EnQuestionTitleInput").val().length > 0) {
    formData.append("en_question_title", $("#EnQuestionTitleInput").val());
    formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
  }
  if (is_admin) {
    if (
      (formData.get("ar_question_title") == null || formData.get("ar_question_title") == "") &&
      (formData.get("en_question_title") != null && formData.get("en_question_title") != "")
    ) {
      formData.set("ar_question_title", formData.get("en_question_title"));
    }
  } */
  formData.append(
    "is_multi_select",
    document.getElementById("multiSelect").checked == true ? 1 : 0
  );

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: `${question_ajax_route}/${questionId}`,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      let titel = formData.get("ar_question_title");
      $(`#question-${questionId}-title`).text(titel);
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Updated Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      let alert = makeErrorAlert("Can't Update This Question");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
      for (let error in res.responseJSON.errors) {
        console.error(error);
      }
    },
  });
}

function updateFormQuestion(questionId) {
  var formData = new FormData();

  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  formData.append("ar_button_label", $("#ArQuestionButtonLabel").val());

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("type_id", 3);

  let media_type = $("#modal_sidebar .nav-tabs .option-link.active").data(
    "option"
  );
  formData.append("media_type", media_type);

  if (media_type == "image") {
    let imageValue = $("#imageInput").prop("files")[0];
    if (imageValue != undefined) {
      formData.append("image", imageValue);
    }
  } else if (media_type == "video") {
    formData.append("video", $("#video_url_input").val());
  }

  if ($("#fields-holder-preview .field").length > 0) {
    $("#fields-holder-preview .field").each(function (i) {
      if ($(this).data("id") != null && $(this).data("id") != undefined) {
        formData.append(`fields[${i}][id]`, $(this).data("id"));
      }
      formData.append(`fields[${i}][label]`, $(this).data("title"));
      formData.append(`fields[${i}][type]`, $(this).data("type"));
      formData.append(`fields[${i}][order]`, $(this).data("order"));

      if ([1, 2, 3, 4, 5, 6, 8].includes($(this).data("type"))) {
        formData.append(
          `fields[${i}][placeholder]`,
          $(this).data("placeholder")
        );
      }
      formData.append(`fields[${i}][is_required]`, $(this).data("is_required"));
      if ($(this).data("type") == 3) {
        formData.append(
          `fields[${i}][is_lead_email]`,
          $(this).data("is_lead_email")
        );
      }
      if ([7, 8].includes($(this).data("type"))) {
        //is_multiple_chooseing
        if ($(this).data("type") == 7) {
          formData.append(
            `fields[${i}][is_multiple_chooseing]`,
            $(this)[0].dataset.is_multiple_chooseing
          );
        }
        $(this)
          .find(".option-input")
          .each(function (x) {
            formData.append(`fields[${i}][options][]`, $(this).val());
          });
      }
    });
  } else {
    $("#fields-holder-preview")
      .html("<div class='alert alert-danger'>" + __["No Fields"] + "</div>")
      .removeClass("d-none");
    return;
  }

  /* if (is_admin && $("#EnQuestionTitleInput").val().length > 0) {
    formData.append("en_question_title", $("#EnQuestionTitleInput").val());
    formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
    formData.append("en_button_label", $("#EnQuestionButtonLabel").val());
  }
  if (is_admin) {
    if (
      (formData.get("ar_question_title") == null || formData.get("ar_question_title") == "") &&
      (formData.get("en_question_title") != null && formData.get("en_question_title") != "")
    ) {
      formData.set("ar_question_title", formData.get("en_question_title"));
    }
    if (
      (formData.get("ar_button_label") == null || formData.get("ar_button_label") == "") &&
      (formData.get("en_button_label") != null && formData.get("en_button_label") != "")
    ) {
      formData.append("ar_button_label", formData.get("en_button_label"));
    }
  } */

  formData.append("is_skippable", $("#isSkippableCheckbox").prop("checked"));

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: `${question_ajax_route}/${questionId}`,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      let titel = formData.get("ar_question_title");
      $(`#question-${questionId}-title`).text(titel);
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Updated Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      let alert = makeErrorAlert("Can't Update This Question");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);

      if (res.responseJSON != null && res.responseJSON.errors != null) {
        let errorsTable = {
          ar_button_label: "EnQuestionButtonLabel",
          ar_question_title: "EnQuestionTitleInput",
        };
        let errorsTableAr = {
          ar_button_label: "ArQuestionButtonLabel",
          ar_question_title: "ArQuestionTitleInput",
        };

        $("#modal_sidebar input").removeClass("border border-danger");
        Object.keys(res.responseJSON.errors).forEach((err) => {
          $("#" + errorsTable[err]).addClass("border border-danger");
          $("#" + errorsTableAr[err]).addClass("border border-danger");
        });
      }
    },
  });
}

function updateImageOrVideo(questionId) {
  var formData = new FormData();

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("type_id", 4);

  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  formData.append("ar_button_label", $("#ArQuestionButtonLabel").val());

  let media_type = $("#modal_sidebar .nav-tabs .option-link.active").data(
    "option"
  );
  formData.append("media_type", media_type);

  if (media_type == "image") {
    let imageValue = $("#imageInput").prop("files")[0];
    if (imageValue != undefined) {
      formData.append("image", imageValue);
    }
  } else if (media_type == "video") {
    formData.append("video", $("#video_url_input").val());
  }

  /* if (is_admin && $("#EnQuestionTitleInput").val().length > 0) {
    formData.append("en_question_title", $("#EnQuestionTitleInput").val());
    formData.append("en_question_desc", $("#EnquestionDescInpiut").val());
    formData.append("en_button_label", $("#EnQuestionButtonLabel").val());
  }
  if (is_admin) {
    if (
      (formData.get("ar_question_title") == null || formData.get("ar_question_title") == "") &&
      (formData.get("en_question_title") != null && formData.get("en_question_title") != "")
    ) {
      formData.set("ar_question_title", formData.get("en_question_title"));
    }
    if (
      (formData.get("ar_button_label") == null || formData.get("ar_button_label") == "") &&
      (formData.get("en_button_label") != null && formData.get("en_button_label") != "")
    ) {
      formData.append("ar_button_label", formData.get("en_button_label"));
    }
  } */

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: `${question_ajax_route}/${questionId}`,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      $(`#question-${questionId}-title`).text(
        formData.get("ar_question_title")
      );
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Updated Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      let alert = makeErrorAlert("Can't Update This Question");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
      for (let error in res.responseJSON.errors) {
        console.error(error);
      }
    },
  });
}

function updateEquationQuestion(questionId) {
  var formData = new FormData();

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("type_id", 5);

  let imageValue = $("#imageInput").prop("files")[0];
  if (imageValue != undefined) {
    formData.append("image", imageValue);
  }

  formData.append("ar_question_title", $("#ArQuestionTitleInput").val());
  formData.append("ar_question_desc", $("#ArquestionDescInpiut").val());
  formData.append("score", $("#questionScoreInput").val());
  formData.append("is_skippable", $("#is_skippable").prop("checked"));
  formData.append(
    "answer_equation",
    Guppy("answer_equation").engine.get_content("xml")
  );
  formData.append(
    "answer_equation_formula",
    Guppy("answer_equation").engine.get_content("text")
  );
  formData.append("answer_decimals", $("#answer_decimals").val());

  $("#equation-variables-container .variable").each(function (i) {
    formData.append(`variables[${i}][decimal]`, $(this).find(".decimal").val());
    formData.append(`variables[${i}][min]`, $(this).find(".min").val());
    formData.append(`variables[${i}][max]`, $(this).find(".max").val());
    formData.append(`variables[${i}][name]`, $(this).data("var"));
  });

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: `${question_ajax_route}/${questionId}`,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      $(`#question-${questionId}-title`).text(
        formData.get("ar_question_title")
      );
      $(`#main_modal`).modal("hide");
      let alert = makeSuccessAlert(__["Question Updated Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
    },
    error: function (res) {
      let alert = makeErrorAlert("Can't Update This Question");
      setTimeout(() => {
        alert.close();
      }, AlertsTimeoutInBuilder);
      for (let error in res.responseJSON.errors) {
        console.error(error);
      }
    },
  });
}

function updateCertificate(certificateId) {
  var formData = new FormData();

  formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
  formData.append("ar_title", $("#certificateTitleInput").val());
  formData.append("ar_desc", $("#certificateDescriptionInput").val());
  formData.append("theme", $("#certificateTheme").val());

  $.ajax({
    type: "POST", // For jQuery < 1.9
    method: "POST",
    url: `${certificate_ajax_route}/${certificateId}`,
    data: formData,
    cache: false,
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.status == true) {
        $("#result-" + certificateId + "-title").text(
          $("#certificateTitleInput").val()
        );
        $(`#main_modal`).modal("hide");
        let alert = makeSuccessAlert(__["Certificate Updated Successfully"]);
        setTimeout(() => {
          alert.close();
        }, AlertsTimeout);
      }
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert(__["Somthing Went Wrong !!"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

// [AJAX] delete items functions

function deleteQuestion(id) {
  $.ajax({
    method: "POST",
    url: `${question_ajax_route}/${id}/delete`, // route("delete_question")
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      question: id,
    },
    success: function (res) {
      $(`#question-${id}`).fadeOut(150, function () {
        $(this).remove();
        if ($("#contentItemsContainer .question").length == 0) {
          $("#contentRemovableInfo").removeClass("d-none");
        }
      });
      let alert = makeSuccessAlert(__["Question Deleted Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert(__["Somthing Went Wrong !!"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function deleteCertificate(id) {
  $.ajax({
    method: "POST",
    url: ajax_delete_certificate_route, // route("certificate_delete")
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      $(`#result-${id}`).fadeOut(150, function () {
        $(this).remove();
        if ($("#certificatesContainer .result").length == 0) {
          $("#certificateRemovableInfo").removeClass("d-none");
        }
      });
      $(".questions-sidebar .item.draggable-result[data-item='1'")
        .css("opacity", "1")
        .attr("draggable", "true");
      let alert = makeSuccessAlert(__["Certificate Deleted Successfully"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert(__["Somthing Went Wrong !!"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function copyQuestion(id) {
  $.ajax({
    method: "POST",
    url: `${question_ajax_route}/${id}/copy`, // route("copy_question")
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    data: {
      question: id,
    },
    success: function (res) {
      let count = $("#contentItemsContainer .question").length + 1;
      let name = $(`#question-${id}-title`).text();
      let type = $("#question-" + id).data("type");
      createQuestion(count, name, res, type, "contentItemsContainer", res);
      let alert = makeSuccessAlert(__["Question has been duplicated"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert(__["Somthing Went Wrong !!"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

// ==== [AJAX] Retrive Questions/Result Functions ====

function editQuestion(questionId) {
  $.ajax({
    method: "GET",
    url: `${question_ajax_route}/${questionId}`,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      let modalTitle = `Edit (${res.title})`;
      if (LANG == "ar") {
        modalTitle = `تعديل (${res.title})`;
      }
      let modalInfo = JSON.stringify({
        itemType: res.type,
        type: "question",
        itemName: modalTitle,
        isEdit: true,
        data: res,
      });

      $("#main_modal").modal("show", modalInfo);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert(__["you can't edit this Question ):"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

function editCertificate(resultId) {
  $.ajax({
    method: "GET",
    url: `${certificate_ajax_route}/${resultId}`,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      let modalTitle = `تعديل (${res.title})`;
      if (LANG == "en") {
        modalTitle = `Edit (${res.title})`;
      }
      let info = {
        itemName: modalTitle,
        isEdit: true,
        type: "certificate",
        itemType: 1,
        data: res,
      };
      let modalInfo = JSON.stringify(info);

      $(`#main_modal`).modal("show", modalInfo);
    },
    error: function (res) {
      console.error(res);
      let alert = makeErrorAlert(__["you can't edit this Result ):"]);
      setTimeout(() => {
        alert.close();
      }, AlertsTimeout);
    },
  });
}

// ==== [HTML Elements] Create Questions/Result Functions ====

function createAnswer(count, value = null) {
  let answersContainer = document.getElementById("answersContainer");
  let answer = create("div", {
    class: "input-group answer mb-2",
    id: `answer-${count}`,
    draggable: "true",
    "data-order": count,
  });
  answersContainer.append(answer);

  let grabingBtn = create(
    "span",
    { class: "input-group-text grabable answer-order-handler" },
    '<i class="fa fa-list-ul"></i></span>'
  );
  answer.append(grabingBtn);

  var answerInput = create("input", {
    type: "text",
    id: `Answer-${count}`,
    class: "form-control answer-val",
    placeholder: "Answer",
    "data-target": `Answer-${count}-preview`,
    value: value != null && value.text != null ? value.text : "",
    "data-id": value != null && value.id != null ? value.id : "",
  });
  answer.append(answerInput);

  let answerScoreInput = create("input", {
    type: "number",
    id: `Answer-${count}-score-input`,
    class: "form-control",
    placeholder: "score",
    value: value != null && value.score != null ? value.score : "",
  });
  answer.append(answerScoreInput);

  let answerDeleteBtn = create(
    "button",
    {
      id: `Answer-${count}-delete-btn`,
      class: "input-group-text btn bg-white text-danger border",
      tabindex: "-1",
    },
    '<i class="fa fa-trash"></i>'
  );
  answer.append(answerDeleteBtn);

  let answerPreviewClass =
    value != null && value.text != null ? "answer" : "answer d-none";
  let answerPreview = create("div", {
    class: answerPreviewClass,
  });

  let highlight = create("div", {
    class: "highlight",
  });
  answerPreview.append(highlight);

  let letter = create("div", { class: "answer-letter" }, "A");
  answerPreview.append(letter);

  var text = create(
    "p",
    {
      class: "text m-0",
      id: `Answer-${count}-preview`,
    },
    value != null && value.text != null ? value.text : ""
  );
  answerPreview.append(text);

  document.getElementById("answersContainerPreview").append(answerPreview);

  $(answerDeleteBtn).click(function () {
    if ($(answersContainer).find(".answer").length > 2) {
      // more then two answers are available
      if (answerInput.dataset.id != "") {
        $(answer).fadeOut(150, function () {
          answerInput.dataset.action = "remove";
        });
      } else {
        $(answer).fadeOut(150, function () {
          $(this).remove();
        });
      }
      $(`#${answerInput.dataset.target}`)
        .parent()
        .fadeOut(150, function () {
          $(this).remove();
        });
    }
  });

  bindAnswer(answerInput);
  if (typeof answerInputSecond != "undefined") {
    bindAnswer(answerInputSecond);
  }
}

function createEquationVariable(name, value = null) {
  let answersContainer = document.getElementById(
    "equation-variables-container"
  );

  let answer = create("div", {
    class: "input-group variable mb-2",
    "data-var": name,
    id: `variable-${name}`,
  });
  answersContainer.append(answer);

  let variableName = create("div", {
    class: "input-group-prepend",
  });
  answer.append(variableName);
  variableName.append(
    create(
      "span",
      {
        class: "input-group-text bg-secondary rounded-0",
      },
      name.replaceAll(/[\[\]]/gi, "").toUpperCase()
    )
  );

  answer.append(
    create("input", {
      type: "number",
      class: "form-control decimal",
      placeholder: "decimals",
      value: value != null && value.decimal != null ? value.decimal : "",
    })
  );

  answer.append(
    create("input", {
      type: "number",
      class: "form-control min",
      placeholder: "min",
      value: value != null && value.min_range != null ? value.min_range : "",
    })
  );

  answer.append(
    create("input", {
      type: "number",
      class: "form-control max",
      placeholder: "max",
      value: value != null && value.max_range != null ? value.max_range : "",
    })
  );
}

function createImageAnswer(count, value = null) {
  const defaultImageName = "default.svg";
  let answersContainer = document.getElementById("answersContainer");

  let answerHolder = create("div", { class: "answer-holder" });
  answersContainer.append(answerHolder);

  let answer = create("div", {
    class: "input-group answer mb-2",
    id: `answer-${count}`,
    draggable: "true",
    "data-order": count,
  });
  answerHolder.append(answer);

  let grabingBtn = create(
    "span",
    { class: "input-group-text grabable answer-order-handler" },
    '<i class="fa fa-list-ul"></i></span>'
  );
  answer.append(grabingBtn);

  if (false) {
    let ENTrans = null;
    if (value != null && value.translations != null) {
      ENTrans = value.translations.find((x) => x.locale == "en");
    }

    var answerInput = create("input", {
      type: "text",
      id: `Answer-${count}`,
      class: "form-control answer-val answer-primary",
      placeholder: "Answer",
      "data-target": `Answer-${count}-preview`,
      value: ENTrans != null && ENTrans.text != undefined ? ENTrans.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInput);
    let ARTrans = null;
    if (value != null && value.translations != null) {
      ARTrans = value.translations.find((x) => x.locale == "ar");
    }
    var answerInputAR = create("input", {
      type: "text",
      id: `Answer-${count}-second`,
      style: `display: none`,
      class: "form-control answer-val answer-second",
      placeholder: "الجواب",
      "data-target": `Answer-${count}-preview`,
      value: ARTrans != null && ARTrans.text != undefined ? ARTrans.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInputAR);
  } else {
    var answerInput = create("input", {
      type: "text",
      id: `Answer-${count}`,
      class: "form-control answer-val answer-primary",
      placeholder: "Answer",
      "data-target": `Answer-${count}-preview`,
      value: value != null && value.text != null ? value.text : "",
      "data-id": value != null && value.id != null ? value.id : "",
    });
    answer.append(answerInput);
  }

  let answerScoreInput = create("input", {
    type: "number",
    id: `Answer-${count}-score-input`,
    class: "form-control flex-shrink-2",
    placeholder: "score",
    value: value != null && value.score != null ? value.score : "",
  });
  answer.append(answerScoreInput);

  let answerImageInput = create("input", {
    type: "file",
    accept: ".png, .jpg, .jpeg",
    id: `Answer-${count}-file`,
    class: "d-none",
  });
  answer.append(answerImageInput);

  let answerImageBtn = create(
    "button",
    {
      class: "btn btn-white border",
      type: "file",
      id: `Answer-${count}-img-btn`,
      onclick: "document.getElementById('Answer-" + count + "-file').click()",
    },
    "<i class='fa fa-picture-o'></i>"
  );
  answer.append(answerImageBtn);

  imgSize = {
    x: 200,
    y: 200,
  };

  let answerDeleteBtn = create(
    "button",
    {
      id: `Answer-${count}-delete-btn`,
      class: "input-group-text btn bg-white text-danger border",
      tabindex: "-1",
    },
    '<i class="fa fa-trash"></i>'
  );
  answer.append(answerDeleteBtn);

  let answerPreviewClass =
    value != null && value.text != null
      ? "answer img-answer card border-0"
      : "answer img-answer card border-0 d-none";
  let answerPreview = create("div", {
    class: answerPreviewClass,
    style: `min-width: ${imgSize.x}px`,
  });

  let highlight = create("div", {
    class: "highlight rounded",
  });
  answerPreview.append(highlight);

  let image = create("img", {
    class: "img card-img-top",
    src: `${public_route}/images/questions/${
      value == null ? defaultImageName : value.image
    }`,
    width: imgSize.x,
    height: imgSize.y,
  });
  answerPreview.append(image);

  let text = create(
    "p",
    {
      class: "text m-0",
      id: `Answer-${count}-preview`,
    },
    value != null && value.text != null ? value.text : ""
  );
  answerPreview.append(text);

  document.getElementById("answersContainerPreview").append(answerPreview);

  $(answerDeleteBtn).click(function () {
    if ($(answersContainer).find(".answer").length > 2) {
      // more then two answers are available
      if (answerInput.dataset.id != "") {
        $(answer).fadeOut(150, function () {
          answerInput.dataset.action = "remove";
        });
      } else {
        $(answer).fadeOut(150, function () {
          $(this).remove();
        });
      }
      $(`#${answerInput.dataset.target}`)
        .parent()
        .fadeOut(150, function () {
          $(this).remove();
        });
    }
  });

  bindAnswer(answerInput);
  // if (typeof answerInputAR != "undefined") {
  //   bindAnswer(answerInputAR);
  //   if ($("#ar-answer-link") && $("#ar-answer-link").hasClass("active")) {
  //     $(answerInputAR).show();
  //     $(answerInput).hide();
  //   } else {
  //     $(answerInputAR).hide();
  //     $(answerInput).show();
  //   }
  // }

  answerImageInput.addEventListener("change", function () {
    let [file] = this.files;
    if (file) {
      image.src = URL.createObjectURL(file);
    }
  });
}

function createQuestion(count, text, id, type = 1) {
  type = parseInt(type);
  let container = document.getElementById("contentItemsContainer");

  let question = create("div", {
    class: "question",
    id: `question-${id}`,
    "data-id": `${id}`,
    "data-type": `${type}`,
    "data-order": `${count}`,
  });
  container.append(question);

  let order = create("div", { class: "order" }, `<b>${count}</b>`);
  question.append(order);

  let body = create("div", { class: "body border" });
  question.append(body);

  let title = create("div", { class: "title" });
  body.append(title);

  let titleTextContainer = create("b", {});
  title.append(titleTextContainer);

  let titleIcon = create("i", {
    class: `icon fa ${questions_types[type - 1]["icon"]}`,
  });

  titleTextContainer.append(titleIcon);

  let titleText = create("span", { id: `${question.id}-title` }, text);
  titleTextContainer.append(titleText);

  let actions = create("ul", { class: "actions" });
  body.append(actions);

  let li1 = create("li", {});
  actions.append(li1);

  let btn1 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-edit`,
  });
  li1.append(btn1);
  $(btn1).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    editQuestion($(question).data("id"));
  });

  let icon1 = create("i", {
    class: "fa fa-pencil",
    title: "Edit Element",
    id: `Q-${count}-edit`,
  });
  btn1.append(icon1);

  let li2 = create("li", {});
  actions.append(li2);

  let btn2 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-copy`,
  });
  li2.append(btn2);
  $(btn2).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    copyQuestion($(question).data("id"));
  });

  let icon2 = create("i", { class: "fa fa-copy", title: "Copy" });
  btn2.append(icon2);

  let li3 = create("li", {});
  actions.append(li3);

  let btn3 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-delete`,
  });
  li3.append(btn3);
  $(btn3).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    deleteQuestion($(question).data("id"));
  });

  let icon3 = create("i", { class: "fa fa-trash-o", title: "Delete Element" });
  btn3.append(icon3);

  let li4 = create("li", { class: "handler-questions" });
  actions.append(li4);

  let btn4 = create("button", {
    class: "btn action-btn px-1",
  });
  li4.append(btn4);

  let icon4 = create("i", {
    class: "fa fa-arrows handler-questions",
    title: "Reorder Element",
    id: `Q-${count}-reorder`,
  });
  btn4.append(icon4);
}

function createCertificateElement(text, id, type = 1) {
  let container = document.getElementById("certificatesContainer");

  let result = create("div", {
    class: "result",
    id: `result-${id}`,
    "data-id": id,
  });
  container.append(result);

  let body = create("div", { class: "body border" });
  result.append(body);

  let title = create("div", { class: "title" });
  body.append(title);

  let titleTextContainer = create("b", {});
  title.append(titleTextContainer);

  let titleIcon = create("i", {
    class: `icon fa ${results_types[type - 1]["icon"]}`,
  });

  titleTextContainer.append(titleIcon);

  let titleText = create("span", { id: `${result.id}-title` }, text);
  titleTextContainer.append(titleText);

  let actions = create("ul", { class: "actions py-2" });
  body.append(actions);

  let li3 = create("li", {});
  actions.append(li3);

  let btn3 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${id}-edit`,
  });
  li3.append(btn3);
  $(btn3).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    editCertificate($(result).data("id"));
  });

  let icon3 = create("i", {
    class: "fa fa-pencil",
    title: "Edit Element",
    id: `Q-${id}-edit`,
  });
  btn3.append(icon3);

  let li5 = create("li", {});
  actions.append(li5);

  let btn5 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${id}-delete`,
  });
  li5.append(btn5);
  $(btn5).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    deleteCertificate($(result).data("id"));
  });

  let icon5 = create("i", { class: "fa fa-trash-o", title: "Delete Element" });
  btn5.append(icon5);
}

function createField(values) {
  let {
    count,
    type,
    label: text,
    placeholder = "",
    is_required = 0,
    id,
    options = [],
    is_lead_email = "",
    is_multiple_chooseing = "",
    hidden_value = "",
  } = values;
  type = parseInt(type);
  let container = document.getElementById("fields-holder-preview");
  let fieldInfo = {
    class: "field",
    id: `field-${count}`,
    "data-type": type,
    "data-order": count,
    "data-title": text,
  };
  fieldInfo["data-is_required"] = is_required;
  if ([1, 2, 3, 4, 5, 6, 8].includes(type)) {
    fieldInfo["data-placeholder"] = placeholder;
  }
  if (type == 7 && values != null && values.is_multiple_chooseing != null) {
    fieldInfo["data-is_multiple_chooseing"] = is_multiple_chooseing;
  } else {
    fieldInfo["data-is_multiple_chooseing"] = 0;
  }
  if (type == 3) {
    // email type (index) not (type id)
    let emailFields = $(container).find('.field[data-type="3"]');
    if (emailFields.length > 0 && is_lead_email == 1) {
      emailFields.each(function (i) {
        $(this)[0].dataset.is_lead_email = 0;
      });
    }
    fieldInfo["data-is_lead_email"] = is_lead_email ?? 0;
  }
  if (id != null) {
    fieldInfo["data-id"] = id;
  }
  let field = create("div", fieldInfo);
  container.append(field);

  let order = create("div", { class: "order" }, `<b>${count}</b>`);
  field.append(order);

  let body = create("div", { class: "body border" });
  field.append(body);

  if ([7, 8].includes(type) && options != null && options.length > 0) {
    options.forEach((option) => {
      field.append(
        create("input", {
          class: "option-input",
          type: "hidden",
          value: option,
        })
      );
    });
  }

  let title = create("div", { class: "title" });
  body.append(title);

  let titleTextContainer = create("b", {});
  title.append(titleTextContainer);

  let titleIcon = create("i", {
    class: `icon fa ${fields_types[type - 1]["icon"]}`,
  });

  titleTextContainer.append(titleIcon);

  let titleText = create("span", { id: `${field.id}-title` }, text);
  titleTextContainer.append(titleText);

  let actions = create("ul", { class: "actions" });
  body.append(actions);

  let li1 = create("li", {});
  actions.append(li1);

  let btn1 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-edit`,
  });

  li1.append(btn1);
  $(btn1).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    let editInfo = {
      label: field.dataset.title,
      placeholder: field.dataset.placeholder,
      is_required: field.dataset.is_required,
      id: field.id,
    };
    if (type == 3) {
      // email
      editInfo["is_lead_email"] = field.dataset.is_lead_email;
    }
    if (type == 7) {
      // checkbox
      editInfo["is_multiple_chooseing"] = field.dataset.is_multiple_chooseing;
    }
    if ([7, 8].includes(type)) {
      let options = $(field).find(".option-input");
      editInfo["options"] = [];
      if (options != null && options.length > 0) {
        options.each(function (i) {
          editInfo["options"].push($(this).val());
        });
      }
    }
    openField(type, editInfo); // use type id not index
  });

  let icon1 = create("i", {
    class: "fa fa-pencil",
    title: "Edit Element",
    id: `Q-${count}-edit`,
  });
  btn1.append(icon1);

  let li2 = create("li", {});
  actions.append(li2);

  let btn2 = create("button", {
    class: "btn action-btn px-1",
    id: `Q-${count}-delete`,
  });
  li2.append(btn2);
  $(btn2).click(function () {
    var btn = $(this);
    btn.prop("disabled", true);
    setTimeout(function () {
      btn.prop("disabled", false);
    }, actionCoolDownTime);
    btn.parents(".field").remove();
  });

  let icon2 = create("i", { class: "fa fa-trash-o", title: "Delete Element" });
  btn2.append(icon2);

  let li3 = create("li", { class: "handler-field" });
  actions.append(li3);

  let btn3 = create("button", {
    class: "btn action-btn px-1",
  });
  li3.append(btn3);

  let icon3 = create("i", {
    class: "fa fa-arrows",
    title: "Reorder Element",
    id: `Q-${count}-reorder`,
  });
  btn3.append(icon3);
  return fieldInfo.id;
}

// ==== Modals Functions ====

function textQuestionModal(values = null) {
  var answersCount = 0;
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);
  attrs = {
    type: "file",
    id: "imageInput",
    class: "d-none",
    "data-target": "img-preview",
    accept: ".jpg,.png,.jpeg",
  };
  let imgInput = create("input", attrs);
  py.append(imgInput);
  let imageTitle = create("p", {}, __["Add Image:"]);
  py.append(imageTitle);
  let uploadImageBtn = create(
    "button",
    {
      type: "button",
      class: "btn btn-success",
      onclick: "imageInput.click()",
    },
    __["Upload Image"]
  );
  py.append(uploadImageBtn);
  py.append(
    create(
      "button",
      {
        type: "button",
        id: "removeImage",
        class:
          values != null && values.image != null
            ? "btn btn-danger mx-2"
            : "btn btn-danger mx-2 d-none",
      },
      '<i class="fa fa-trash"></i>'
    )
  );

  // ==========================

  if (false) {
    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center option-link" +
          (LANG == "en" ? " active" : ""),
        "data-option": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center option-link" +
          (LANG == "ar" ? " active" : ""),
        "data-option": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let inputContainer1 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer1);
    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "question title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "400",
      "data-target": "questionTitlePreview",
      value: ENTrans != null ? ENTrans.title : "",
    });
    inputContainer1.append(input1);
    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "question description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null ? ENTrans.description : ""
    );
    inputContainer2.append(input2);

    ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let ARinputContainer1 = create("div", { class: "mb-3" });
    translationPage2.append(ARinputContainer1);
    let ARinputLabel1 = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "عنوان السؤال *"
    );
    ARinputContainer1.append(ARinputLabel1);
    var ARinput1 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "أدخل عنواناً",
      maxlength: "400",
      "data-target": "questionTitlePreview",
      value: ARTrans != null && ARTrans.title != null ? ARTrans.title : "",
    });
    ARinputContainer1.append(ARinput1);
    let ARinputContainer2 = create("div", { class: "mb-3" });
    translationPage2.append(ARinputContainer2);
    let ARinputLabel2 = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "وصف السؤال"
    );
    ARinputContainer2.append(ARinputLabel2);
    var ARinput2 = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "أدخل وصفاً",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ARTrans != null &&
        ARTrans != "" &&
        ARTrans.description != null &&
        ARTrans.description != ""
        ? ARTrans.description
        : ""
    );
    ARinputContainer2.append(ARinput2);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);
    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "question title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "400",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);
    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "question description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);
  }

  // ==========================

  let inputContainer3 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer3);
  let inputLabel3 = create("label", { class: "mb-1" }, __["Answers:"]);
  inputContainer3.append(inputLabel3);

  if (false) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3 mb-2",
    });
    inputContainer3.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center answer-link" +
          (LANG == "en" ? " active" : ""),
        id: "en-answer-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center answer-link" +
          (LANG == "ar" ? " active" : ""),
        id: "ar-answer-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);
  }

  let answersContainer = create("div", {
    class: "answers-order-container list-group",
    id: "answersContainer",
  });
  inputContainer3.append(answersContainer);

  let addAnswerBtn = create(
    "button",
    {
      type: "button",
      id: "addAnswerBtn",
      class: "btn btn-secondary",
      placeholder: "Answer",
    },
    `${__["Add Answer"]} <i style="margin-left: 8px" class="fa fa-plus"></i>`
  );
  inputContainer3.append(addAnswerBtn);
  let inputContainer4 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer4);
  let sectoinTitle1 = create("h5", { class: "title" }, __["SETTING"]);
  inputContainer4.append(sectoinTitle1);
  let checkBoxContainer = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer4.append(checkBoxContainer);
  let inputLabel4 = create(
    "label",
    { class: "form-check-label", for: "multiSelect" },
    __["Allow multiple choices:"]
  );
  checkBoxContainer.append(inputLabel4);
  let checkBoxInfo = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "multiSelect",
    "data-target": "nextQuestionBtn",
  };
  if (
    values != null &&
    values.is_multi_select != null &&
    values.is_multi_select == 1
  ) {
    checkBoxInfo["checked"] = values.is_multi_select;
  }
  let input3 = create("input", checkBoxInfo);
  checkBoxContainer.append(input3);

  let modalPreview = document.getElementById(previewId);

  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Question Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc mb-2"
      : "questionDesc mb-2 d-none";

  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "no description"
  );
  centerHolder.append(questionDesc);

  let questionMedia = create("div", {
    class: "ratio ratio-16x9 media-container d-none",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && values.image != null) {
    $(questionMedia).removeClass("d-none");
    let media = create("img", {
      class: "media-item",
      src: `${public_route}/images/questions/${values.image}`,
    });
    $(questionMedia).append(media);
  }

  let answersContainerPreview = create("div", {
    class: "answers my-4",
    id: "answersContainerPreview",
  });
  centerHolder.append(answersContainerPreview);
  let nextQuestionBtnClass =
    values != null &&
    values.is_multi_select != null &&
    values.is_multi_select == 1
      ? "btn btn-primary d-inline-block mt-3 ml-auto"
      : "btn btn-primary d-inline-block mt-3 ml-auto d-none";
  let nextQuestionBtn = create(
    "button",
    {
      class: nextQuestionBtnClass,
      type: "button",
      id: "nextQuestionBtn",
    },
    __["Submit"]
  );
  centerHolder.append(nextQuestionBtn);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindCheck(input3);
  // if (typeof ARinput1 != "undefined") {
  //   bindInput(ARinput1, "input", "d-none", "is-invalid");
  // }
  // if (typeof ARinput2 != "undefined") {
  //   bindInput(ARinput2, "input", "d-none");
  // }

  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }

  if (values != null && values.answers != null) {
    values.answers.forEach((answer) => {
      createAnswer(++answersCount, answer);
    });
  } else {
    createAnswer(++answersCount);
    createAnswer(++answersCount);
  }

  addAnswerBtn.addEventListener("click", function () {
    createAnswer(++answersCount);
  });

  var answersSortable = document.getElementById("answersContainer");

  new Sortable(answersSortable, {
    animation: 150,
    handle: ".answer-order-handler",
    ghostClass: "border-primary",
  });

  $(answersContainer).on("drop", function (e) {
    document
      .querySelectorAll("#answersContainer .answer")
      .forEach(function (answer, i) {
        answer.dataset.order = i + 1;
      });
  });
}

function imageQuestionModal(values = null) {
  var answersCount = 0;
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);
  attrs = {
    type: "file",
    id: "imageInput",
    class: "d-none",
    "data-target": "img-preview",
    accept: ".jpg,.png,.jpeg",
  };

  let imgInput = create("input", attrs);
  py.append(imgInput);
  let imageTitle = create("p", {}, __["Add Image:"]);
  py.append(imageTitle);
  let uploadImageBtn = create(
    "button",
    {
      type: "button",
      class: "btn btn-success",
      onclick: "imageInput.click()",
    },
    __["Upload Image"]
  );
  py.append(uploadImageBtn);
  let imageRemoveBtn = create(
    "button",
    {
      type: "button",
      id: "removeImage",
      class:
        values != null && values.image != null
          ? "btn btn-danger mx-2"
          : "btn btn-danger mx-2 d-none",
    },
    '<i class="fa fa-trash"></i>'
  );
  py.append(imageRemoveBtn);

  if (false) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center option-link" +
          (LANG == "en" ? " active" : ""),
        "data-option": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center option-link" +
          (LANG == "ar" ? " active" : ""),
        "data-option": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    // ====== EN ======

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);
    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "question title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ENTrans != null ? ENTrans.title : "",
    });
    inputContainer1.append(input1);
    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "question description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null ? ENTrans.description : ""
    );
    inputContainer2.append(input2);

    // ====== AR ======

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let ARinputContainer1 = create("div", { class: "my-3" });
    translationPage2.append(ARinputContainer1);
    let ARinputLabel1 = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "عنوان السؤال *"
    );
    ARinputContainer1.append(ARinputLabel1);
    var ARinput1 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "ادخل السؤال",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    ARinputContainer1.append(ARinput1);
    let ARinputContainer2 = create("div", { class: "mb-3" });
    translationPage2.append(ARinputContainer2);
    let ARinputLabel2 = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "وصف السؤال"
    );
    ARinputContainer2.append(ARinputLabel2);
    var ARinput2 = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "ادخل وصف السؤال",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    ARinputContainer2.append(ARinput2);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);
    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "question title *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);
    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "question description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);
  }

  let inputContainer3 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer3);

  if (false) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3 mb-2",
    });
    inputContainer3.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center answer-link" +
          (LANG == "en" ? " active" : ""),
        id: "en-answer-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center answer-link" +
          (LANG == "ar" ? " active" : ""),
        id: "ar-answer-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);
  }

  let inputLabel3 = create("label", { class: "mb-2" }, __["Answers:"]);
  inputContainer3.append(inputLabel3);
  let LabelP3 = create(
    "p",
    { class: "mb-2 small" },
    __["largest image size is: 2mb"]
  );
  inputContainer3.append(LabelP3);

  let answersContainer = create("div", {
    class: "answers-order-container list-group",
    id: "answersContainer",
  });
  inputContainer3.append(answersContainer);
  let addAnswerBtn = create(
    "button",
    {
      type: "button",
      id: "addAnswerBtn",
      class: "btn btn-secondary",
      placeholder: "Answer",
    },
    `${__["Add Answer"]} <i style="margin-left: 8px" class="fa fa-plus"></i>`
  );
  inputContainer3.append(addAnswerBtn);
  let inputContainer4 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer4);
  let sectoinTitle1 = create("h5", { class: "title" }, __["SETTING"]);
  inputContainer4.append(sectoinTitle1);
  let checkBoxContainer = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer4.append(checkBoxContainer);
  let inputLabel4 = create(
    "label",
    { class: "form-check-label", for: "multiSelect" },
    __["Allow multiple choices:"]
  );
  checkBoxContainer.append(inputLabel4);
  let checkBoxInfo = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "multiSelect",
    "data-target": "nextQuestionBtn",
  };
  if (
    values != null &&
    values.is_multi_select != null &&
    values.is_multi_select == 1
  ) {
    checkBoxInfo["checked"] = values.is_multi_select;
  }
  let input3 = create("input", checkBoxInfo);
  checkBoxContainer.append(input3);
  let modalPreview = document.getElementById(previewId);
  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Question Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc"
      : "questionDesc d-none";
  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "no description"
  );
  centerHolder.append(questionDesc);

  let questionMedia = create("div", {
    class: "ratio ratio-16x9 media-container d-none",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && (values.video != null || values.image != null)) {
    let mediaType;
    let mediaSrc;
    if (values != null && values.video != null) {
      mediaType = "iframe";
      mediaSrc = values.video;
    } else if (values != null && values.image != null) {
      mediaType = "img";
      mediaSrc = `${public_route}/images/questions/${values.image}`;
    }
    let media = create(mediaType, { class: "media-item", src: mediaSrc });
    questionMedia.append(media);
    $(questionMedia).removeClass("d-none");
  }

  let answersContainerPreview = create("div", {
    class: "answers img-answers my-4",
    id: "answersContainerPreview",
  });
  centerHolder.append(answersContainerPreview);
  let nextQuestionBtnClass =
    values != null &&
    values.is_multi_select != null &&
    values.is_multi_select == 1
      ? "btn mt-3 d-inline-block ml-auto"
      : "btn mt-3 d-inline-block ml-auto d-none";
  let nextQuestionBtn = create(
    "button",
    {
      class: nextQuestionBtnClass,
      type: "button",
      id: "nextQuestionBtn",
    },
    __["Submit"]
  );
  centerHolder.append(nextQuestionBtn);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  // if (typeof ARinput1 != "undefined") {
  //   bindInput(ARinput1, "input", "d-none", "is-invalid");
  // }
  // if (typeof ARinput2 != "undefined") {
  //   bindInput(ARinput2, "input", "d-none");
  // }
  bindCheck(input3);
  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }

  if (false) {
    $(modalSidebar)
      .find(".option-link")
      .each(function () {
        $(this).click(function () {
          $(this)
            .addClass("active")
            .siblings(".option-link")
            .removeClass("active");
          $("#" + $(this).data("option"))
            .fadeIn(100)
            .siblings(".option-page")
            .fadeOut(100);
        });
        $(this)
          .addClass("active")
          .siblings(".option-link")
          .removeClass("active");
        $("#" + $(this).data("option"))
          .fadeIn()
          .siblings(".option-page")
          .fadeOut();
      });

    $("#en-answer-link").click(function () {
      $(this).addClass("active").siblings(".answer-link").removeClass("active");
      $("#answersContainer .answer .answer-second").hide();
      $("#answersContainer .answer .answer-primary").show();
    });
    $("#ar-answer-link").click(function () {
      $(this).addClass("active").siblings(".answer-link").removeClass("active");
      $("#answersContainer .answer .answer-primary").hide();
      $("#answersContainer .answer .answer-second").show();
    });
  }

  if (values != null && values.answers != null) {
    values.answers.forEach((answer) => {
      createImageAnswer(++answersCount, answer);
    });
  } else {
    createImageAnswer(++answersCount);
    createImageAnswer(++answersCount);
  }
  // if ($("#ar-answer-link").hasClass("active")) {
  //   $("#answersContainer .answer .answer-primary").hide();
  //   $("#answersContainer .answer .answer-second").show();
  // } else {
  //   $("#answersContainer .answer .answer-second").hide();
  //   $("#answersContainer .answer .answer-primary").show();
  // }

  addAnswerBtn.addEventListener("click", function () {
    createImageAnswer(++answersCount);
  });

  var answersSortable = document.getElementById("answersContainer");

  new Sortable(answersSortable, {
    animation: 150,
    handle: ".answer-order-handler",
    ghostClass: "border-primary",
  });

  $(answersContainer).on("drop", function (e) {
    document
      .querySelectorAll("#answersContainer .answer")
      .forEach(function (answer, i) {
        answer.dataset.order = i + 1;
      });
  });
}

function formModal(values = null) {
  let modalSidebar = document.getElementById(sidebarId);

  $("#" + sidebarId).after(
    `<div id="sidebarOption" style="height: calc(100vh - 192px);overflow-y: auto;display: none">
      <button class="btn close-btn"><i class="fa fa-close"></i></button>
      <div class="my-3">
        <div id="filed-inputs-box"></div>
        <div class="px-3">
          <button id="save-field-btn" class="btn btn-primary save-btn w-100">${__["save"]} <i class="fa fa-save"></i></button>
        </div>
      </div>
    </div>`
  );

  let navTaps = create("ul", { class: "nav nav-tabs w-100" });
  modalSidebar.append(navTaps);

  let tap1 = create(
    "li",
    {
      class: "nav-link w-50 text-center option-link",
      "data-option": "image",
      id: "image-link",
    },
    __["Image"]
  );
  navTaps.append(tap1);

  let option1 = create("div", {
    class: "option-container py-3 px-2 bg-white border border-top-0",
    id: "image-option",
  });
  modalSidebar.append(option1);

  let imgInput = create("input", {
    class: "d-none",
    type: "file",
    id: "imageInput",
    accept: ".jpg,.png,.jpeg",
    "data-target": "img-preview",
  });
  option1.append(imgInput);
  let uploadImageBtn = create(
    "button",
    {
      type: "button",
      class: "btn btn-success",
      onclick: "imageInput.click()",
    },
    __["Upload Image"]
  );
  option1.append(uploadImageBtn);
  option1.append(
    create(
      "button",
      {
        type: "button",
        id: "removeImage",
        class:
          values != null && values.image != null
            ? "btn btn-danger mx-2"
            : "btn btn-danger mx-2 d-none",
      },
      '<i class="fa fa-trash"></i>'
    )
  );

  let tap2 = create(
    "li",
    {
      class: "nav-link w-50 text-center option-link",
      "data-option": "video",
      id: "video-link",
    },
    __["Video"]
  );
  navTaps.append(tap2);

  let option2 = create("div", {
    class: "option-container py-3 px-2 bg-white border border-top-0",
    id: "video-option",
  });
  modalSidebar.append(option2);

  let videoLabel = create(
    "label",
    {
      class: "form-label",
      for: "video_url_input",
    },
    __["Video URL:"]
  );
  option2.append(videoLabel);

  let flexholder = create("div", { class: "d-flex gap-2" });
  option2.append(flexholder);

  let videoUrlInput = create("input", {
    class: "form-control",
    type: "url",
    "data-target": "img-preview",
    placeholder: __["Enter A Youtube Video URL"],
    id: "video_url_input",
  });
  flexholder.append(videoUrlInput);

  let deleteBtn = create(
    "button",
    {
      class: "btn btn-danger delete-value-url p-2",
      onclick: "video_url_input.value = '';",
    },
    "<i class='fa fa-trash fa-lg fa-fw'></i>"
  );
  flexholder.append(deleteBtn);

  if (false) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center option-link" +
          (LANG == "en" ? " active" : ""),
        "data-target": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center option-link" +
          (LANG == "ar" ? " active" : ""),
        "data-target": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2 form-label", for: "EnQuestionTitleInput" },
      "Title:"
    );
    inputContainer1.append(inputLabel1);

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ENTrans != null && ENTrans.title != undefined ? ENTrans.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2 form-label", for: "EnquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null && ENTrans.description != undefined
        ? ENTrans.description
        : ""
    );
    inputContainer2.append(input2);

    let buttonLabelContainer = create("div", { class: "mb-3" });
    translationPage1.append(buttonLabelContainer);
    buttonLabelContainer.append(
      create(
        "label",
        { class: "form-label", for: "EnQuestionButtonLabel" },
        __["Button Label:"]
      )
    );

    var formButton = create("input", {
      class: "form-control",
      placeholder: __["submit"],
      type: "text",
      id: "EnQuestionButtonLabel",
      "data-target": "nextQuestionBtn",
      value:
        ENTrans != null &&
        ENTrans.button_label != undefined &&
        ENTrans.button_label.length > 0
          ? ENTrans.button_label
          : "",
    });
    buttonLabelContainer.append(formButton);

    let inputContainer1Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer1Ar);

    let inputLabel1Ar = create(
      "label",
      { class: "mb-2 form-label", for: "ArQuestionTitleInput" },
      "العنوان:"
    );
    inputContainer1Ar.append(inputLabel1Ar);

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    var input1Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "ادخل عنواناً",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    inputContainer1Ar.append(input1Ar);

    let inputContainer2Ar = create("div", { class: "mb-3" });
    translationPage2.append(inputContainer2Ar);
    let inputLabel2Ar = create(
      "label",
      { class: "mb-2 form-label", for: "ArquestionDescInpiut" },
      "الوصف"
    );
    inputContainer2Ar.append(inputLabel2Ar);
    var input2Ar = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "ادخل وصفاً",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    inputContainer2Ar.append(input2Ar);

    let buttonLabelContainerAr = create("div", { class: "mb-3" });
    translationPage2.append(buttonLabelContainerAr);
    buttonLabelContainerAr.append(
      create(
        "label",
        { class: "form-label", for: "ArQuestionButtonLabel" },
        "عنوان الزر:"
      )
    );

    var formButtonAr = create("input", {
      class: "form-control",
      placeholder: "إرسال",
      type: "text",
      id: "ArQuestionButtonLabel",
      "data-target": "nextQuestionBtn",
      value:
        ARTrans != null &&
        ARTrans.button_label != undefined &&
        ARTrans.button_label.length > 0
          ? ARTrans.button_label
          : "",
    });
    buttonLabelContainerAr.append(formButtonAr);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2 form-label", for: "ArQuestionTitleInput" },
      "Title:"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2 form-label", for: "ArquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);

    let buttonLabelContainer = create("div", { class: "mb-3" });
    modalSidebar.append(buttonLabelContainer);
    buttonLabelContainer.append(
      create(
        "label",
        { class: "form-label", for: "ArQuestionButtonLabel" },
        "Button Label:"
      )
    );

    var formButton = create("input", {
      class: "form-control",
      placeholder: __["submit"],
      type: "text",
      id: "ArQuestionButtonLabel",
      "data-target": "nextQuestionBtn",
      value:
        values != null &&
        values.button_label != null &&
        values.button_label.length > 0
          ? values.button_label
          : "",
    });
    buttonLabelContainer.append(formButton);
  }

  let fieldsContainers = create("div", { class: "mb-3" });
  modalSidebar.append(fieldsContainers);

  let fieldsHolder = create("div", {
    class: "content-items row items mt-3 mb-4",
  });
  fieldsContainers.append(fieldsHolder);

  let Fields = [
    {
      id: 1,
      type: "text",
      name: "first_name",
      title: __["First Name"],
      icon: "fa fa-user",
    },
    {
      id: 2,
      type: "text",
      name: "last_name",
      title: __["Last Name"],
      icon: "fa fa-user",
    },
    {
      id: 3,
      type: "email",
      name: "email",
      title: __["Email"],
      icon: "fa fa-envelope",
    },
    {
      id: 4,
      type: "number",
      name: "phone_number",
      title: __["Phone Number"],
      icon: "fa fa-phone",
    },
    {
      id: 5,
      type: "textarea",
      name: "long_answer",
      title: __["Long Answer"],
      icon: "fa fa-bars",
    },
    {
      id: 6,
      type: "text",
      name: "short_answer",
      title: __["Short Answer"],
      icon: "fa fa-align-center",
    },
    {
      id: 7,
      type: "checkbox",
      name: "checkbox",
      title: __["Checkbox"],
      icon: "fa fa-check-square-o",
    },
    {
      id: 8,
      type: "select",
      name: "dropdown",
      title: __["Dropdown"],
      icon: "fa fa-caret-square-o-down",
    },
  ];

  Fields.forEach((fieldData) => {
    $(fieldsHolder).append(`
      <div class="col-6 px-1 mb-2">
        <div class="item card flex-row draggable-field py-1" draggable="true" data-type="${fieldData.id}" data-name="${fieldData.title}">
          <div class="icon-bg py-1 px-3 d-flex align-items-center justify-content-center">
            <i class="${fieldData.icon}"></i>
          </div>
          <div class="card-body py-2 px-2">
            <h6 class="card-title m-0">${fieldData.title}</h6>
          </div>
        </div>
      </div>
    `);
  });

  let inputContainer3 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer3);
  let sectoinTitle1 = create("h5", { class: "title" }, __["Settings:"]);
  inputContainer3.append(sectoinTitle1);

  let checkBoxContainer = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer3.append(checkBoxContainer);
  let inputLabel3 = create(
    "label",
    { class: "form-check-label", for: "isSkippableCheckbox" },
    __["Enable skip form option:"]
  );
  checkBoxContainer.append(inputLabel3);
  let checkBoxInfo = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "isSkippableCheckbox",
    "data-target": "isSkippable",
  };
  if (
    values != null &&
    values.is_skippable != null &&
    values.is_skippable == 1
  ) {
    checkBoxInfo["checked"] = true;
  }
  let input3 = create("input", checkBoxInfo);
  checkBoxContainer.append(input3);

  let checkBoxContainer2 = create("div", {
    class: "form-check form-switch p-0 my-4 d-flex gap-3",
  });
  inputContainer3.append(checkBoxContainer2);

  /////////////////////////////////

  let modalPreview = document.getElementById(previewId);
  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Text Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc"
      : "questionDesc d-none";
  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "Text Description"
  );
  centerHolder.append(questionDesc);

  let questionMedia = create("div", {
    class: "ratio ratio-16x9 media-container d-none",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && (values.video != null || values.image != null)) {
    let mediaType;
    let mediaSrc;
    if (values != null && values.video != null) {
      mediaType = "iframe";
      mediaSrc = values.video;
    } else if (values != null && values.image != null) {
      mediaType = "img";
      mediaSrc = `${public_route}/images/questions/${values.image}`;
    }
    let media = create(mediaType, { class: "media-item", src: mediaSrc });
    questionMedia.append(media);
    $(questionMedia).removeClass("d-none");
  }

  let fieldsContainersPreview = create("div", {
    class: "alert alert-primary d-flex flex-column gap-2 fields d-none",
    style: "border-style: dashed",
    id: "fields-holder-preview",
  });
  centerHolder.append(fieldsContainersPreview);

  if (values != null && values.fields != null && values.fields.length > 0) {
    values.fields.forEach((field, i) => {
      let info = {
        count: i + 1,
        type: parseInt(field.type),
        label: field.label,
        id: field.id,
        placeholder: field.placeholder ?? "",
        is_required: field.is_required ?? "",
        en_label: field.en_label ?? "",
        en_placeholder: field.en_placeholder ?? "",
        is_multiple_chooseing: field.is_multiple_chooseing ?? "",
        hiddar_value: field.value ?? "",
      };
      field.type = parseInt(field.type);

      if (field.type == 3) {
        info["is_lead_email"] = field.is_lead_email ?? 0;
      }
      if ([7, 8].includes(field.type)) {
        info["options"] = [];
        if (field.options != null && field.options.length > 0) {
          field.options.forEach((el) => {
            info["options"].push(el.value);
          });
        }
      }

      createField(info);
    });
  }

  if ($(fieldsContainersPreview).find(".field").length > 0) {
    $(fieldsContainersPreview).removeClass("d-none");
  } else {
    $(fieldsContainersPreview).addClass("d-none");
  }

  let buttonsContainerInfo = {
    class: "ml-auto mt-2",
  };
  if (LANG == "ar") {
    buttonsContainerInfo["class"] = "ml-auto mt-2 d-flex flex-row-reverse";
  }
  let buttonsContainer = create("div", buttonsContainerInfo);

  let skippableInputInfo = {
    class: "btn ml-auto",
    id: "isSkippable",
  };

  if (!$(input3).prop("checked")) {
    skippableInputInfo["class"] += " d-none ";
  }

  let isSkippable = create("button", skippableInputInfo, __["skip"]);
  buttonsContainer.append(isSkippable);

  let nextQuestionBtn = create(
    "button",
    {
      class: "btn btn-primary d-inline-block mt-3 ml-auto",
      type: "button",
      id: "nextQuestionBtn",
    },
    values != null && values.button_label != null
      ? values.button_label
      : __["submit"]
  );
  buttonsContainer.append(nextQuestionBtn);

  centerHolder.append(buttonsContainer);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindInput(formButton, "input", "d-none");

  if (typeof input1Ar != "undefined") {
    bindInput(input1Ar, "input", "d-none", "is-invalid");
  }
  if (typeof input2Ar != "undefined") {
    bindInput(input2Ar, "input", "d-none");
  }
  if (typeof formButtonAr != "undefined") {
    bindInput(formButtonAr, "input", "d-none");
  }

  bindCheck(input3);

  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }
  bindVideo(videoUrlInput);

  $("#modal_sidebar .nav-tabs .option-link").each(function (i) {
    $(this).click(function () {
      $(this).addClass("active").siblings().removeClass("active");
      $(this)
        .addClass("active")
        .siblings()
        .each(function (i) {
          $(`#${$(this).data("option")}-option`).addClass("d-none");
        });
      $(`#${$(this).data("option")}-option`).removeClass("d-none");
    });
    $(`#${$(this).data("option")}-option`)
      .removeClass("active")
      .addClass("d-none");
  });

  if (values != null && values.image != null) {
    $(`#image-option`).removeClass("d-none");
    $(`#image-link`).addClass("active");
  } else if (values != null && values.video != null) {
    $(`#video-option`).removeClass("d-none");
    $(`#video-link`).addClass("active");
    $(`#video_url_input`).val(values.video);
  } else {
    $(`#image-option`).removeClass("d-none");
    $(`#image-link`).addClass("active");
  }

  if (false) {
    $("#modal_sidebar .option-link").click(function () {
      $(this).addClass("active").siblings(".option-link").removeClass("active");
      $("#" + $(this).data("target"))
        .show()
        .siblings(".option-page")
        .hide();
    });
  }

  $(".content-items .draggable-field").each(function () {
    $(this).on("dragstart", function (e) {
      let field = $(this);
      let info = {
        field_id: field.data("type"),
      };
      e.originalEvent.dataTransfer.setData("text", JSON.stringify(info));
    });
  });

  $(".test-preview").on("dragover", function (e) {
    e.preventDefault();
  });

  $(".test-preview").on("drop", function (e) {
    e.preventDefault();
    var data = e.originalEvent.dataTransfer.getData("text"); // item => info object
    if (isJson(data)) {
      data = JSON.parse(data);
      if (data.field_id != null) {
        $("#fields-holder-preview").find(".alert").remove();
        openField(data.field_id);
      }
    }
  });

  var fieldsContainer = document.getElementById("fields-holder-preview");

  new Sortable(fieldsContainer, {
    animation: 150,
    handle: ".handler-field",
    ghostClass: "active",
  });

  $("#fields-holder-preview").on("drop", function (e) {
    let val = e.originalEvent.dataTransfer.getData("text");
    if (!isJson(val)) {
      let orders = [];
      document
        .querySelectorAll("#fields-holder-preview .field")
        .forEach(function (order, i) {
          order.dataset.order = i + 1;
          order.querySelector(".order b").innerText = order.dataset.order;
          orders.push({ id: order.dataset.id, order: order.dataset.order });
        }); //come
    }
  });

  $("#sidebarOption .close-btn").on("click", function (e) {
    e.preventDefault();
    $("#save-field-btn").off("click");
    $("#modal_sidebar").show();
    $("#sidebarOption").hide();
    // if ($("#sidebarOption").length > 0) {
    //   $("#sidebarOption").remove();
    // }
  });
  $("#modal_sidebar .option-link.active").each(function () {
    $("#" + $(this).data("target"))
      .show()
      .siblings(".option-page")
      .hide();
  });
}

function mediaModal(values = null) {
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);

  let navTaps = create("ul", { class: "nav nav-tabs w-100" });
  modalSidebar.append(navTaps);

  let tap1 = create(
    "li",
    {
      class: "nav-link w-50 text-center option-link",
      "data-option": "image",
      id: "image-link",
    },
    __["Image"]
  );
  navTaps.append(tap1);

  let option1 = create("div", {
    class: "option-container py-3 px-2 bg-white border border-top-0",
    id: "image-option",
  });
  modalSidebar.append(option1);

  let imgInput = create("input", {
    class: "d-none",
    type: "file",
    id: "imageInput",
    accept: ".jpg,.png,.jpeg",
    "data-target": "img-preview",
  });
  option1.append(imgInput);

  let uploadImageBtn = create(
    "button",
    {
      type: "button",
      class: "btn btn-success",
      onclick: "imageInput.click()",
    },
    __["Upload Image"]
  );
  option1.append(uploadImageBtn);
  option1.append(
    create(
      "button",
      {
        type: "button",
        id: "removeImage",
        class:
          values != null && values.image != null
            ? "btn btn-danger mx-2"
            : "btn btn-danger mx-2 d-none",
      },
      '<i class="fa fa-trash"></i>'
    )
  );

  let tap2 = create(
    "li",
    {
      class: "nav-link w-50 text-center option-link",
      "data-option": "video",
      id: "video-link",
    },
    __["Video"]
  );
  navTaps.append(tap2);

  let option2 = create("div", {
    class: "option-container py-3 px-2 bg-white border border-top-0",
    id: "video-option",
  });
  modalSidebar.append(option2);

  let videoLabel = create(
    "label",
    {
      class: "form-label",
      for: "video_url_input",
    },
    __["Video URL:"]
  );
  option2.append(videoLabel);

  let videoUrlInput = create("input", {
    class: "form-control",
    type: "url",
    "data-target": "img-preview",
    placeholder: __["Enter A Youtube Video URL"],
    id: "video_url_input",
  });
  option2.append(videoUrlInput);

  if (false) {
    let translationPagesBtnsHolder = create("ul", {
      class: "nav nav-tabs w-100 mt-3",
    });
    modalSidebar.append(translationPagesBtnsHolder);

    let translationPagesBtn1 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center option-link" +
          (LANG == "en" ? " active" : ""),
        "data-target": "en-question-translation",
        id: "en-intro-link",
      },
      "English"
    );
    translationPagesBtnsHolder.append(translationPagesBtn1);

    let translationPagesBtn2 = create(
      "li",
      {
        class:
          "nav-link w-50 text-center option-link" +
          (LANG == "ar" ? " active" : ""),
        "data-target": "ar-question-translation",
        id: "ar-intro-link",
      },
      "العربية"
    );
    translationPagesBtnsHolder.append(translationPagesBtn2);

    let translationPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      id: "en-question-translation",
    });
    modalSidebar.append(translationPage1);

    let translationPage2 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 option-page",
      style: "display: none",
      id: "ar-question-translation",
    });
    modalSidebar.append(translationPage2);

    let inputContainer1 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "EnQuestionTitleInput" },
      "Title: *"
    );
    inputContainer1.append(inputLabel1);

    let ENTrans = null;
    if (values != null && values.translations != null) {
      ENTrans = values.translations.find((x) => x.locale == "en");
    }

    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ENTrans != null && ENTrans.title != undefined ? ENTrans.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    translationPage1.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "EnquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "EnquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ENTrans != null && ENTrans.description != undefined
        ? ENTrans.description
        : ""
    );
    inputContainer2.append(input2);

    let inputContainer3 = create("div", { class: "my-3" });
    translationPage1.append(inputContainer3);
    let inputLabel3 = create(
      "label",
      { class: "mb-2", for: "EnQuestionButtonLabel" },
      __["Button: *"]
    );
    inputContainer3.append(inputLabel3);
    var input3 = create("input", {
      type: "text",
      class: "form-control",
      id: "EnQuestionButtonLabel",
      maxlength: "40",
      placeholder: "Button Label",
      "data-target": "nextQuestionBtn",
      value:
        ENTrans != null && ENTrans.button_label != undefined
          ? ENTrans.button_label
          : "",
    });
    inputContainer3.append(input3);

    let inputContainer1Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer1Ar);

    let ARTrans = null;
    if (values != null && values.translations != null) {
      ARTrans = values.translations.find((x) => x.locale == "ar");
    }

    let inputLabel1Ar = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "العنوان: *"
    );
    inputContainer1Ar.append(inputLabel1Ar);
    var input1Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "ادخل عنواناً",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: ARTrans != null && ARTrans.title != undefined ? ARTrans.title : "",
    });
    inputContainer1Ar.append(input1Ar);

    let inputContainer2Ar = create("div", { class: "mb-3" });
    translationPage2.append(inputContainer2Ar);
    let inputLabel2Ar = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "الوصف"
    );
    inputContainer2Ar.append(inputLabel2Ar);
    var input2Ar = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "ادخل وصفاً",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      ARTrans != null && ARTrans.description != undefined
        ? ARTrans.description
        : ""
    );
    inputContainer2Ar.append(input2Ar);

    let inputContainer3Ar = create("div", { class: "my-3" });
    translationPage2.append(inputContainer3Ar);
    let inputLabel3Ar = create(
      "label",
      { class: "mb-2", for: "ArQuestionButtonLabel" },
      "الزر: *"
    );
    inputContainer3Ar.append(inputLabel3Ar);
    var input3Ar = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionButtonLabel",
      "data-target": "nextQuestionBtn",
      maxlength: "40",
      placeholder: "عنوان الزر",
      value:
        ARTrans != null && ARTrans.button_label != undefined
          ? ARTrans.button_label
          : "",
    });
    inputContainer3Ar.append(input3Ar);
  } else {
    let inputContainer1 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer1);

    let inputLabel1 = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "Title: *"
    );
    inputContainer1.append(inputLabel1);
    var input1 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      "data-target": "questionTitlePreview",
      value: values != null ? values.title : "",
    });
    inputContainer1.append(input1);

    let inputContainer2 = create("div", { class: "mb-3" });
    modalSidebar.append(inputContainer2);
    let inputLabel2 = create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "Text Description"
    );
    inputContainer2.append(inputLabel2);
    var input2 = create(
      "textarea",
      {
        class: "form-control",
        id: "ArquestionDescInpiut",
        placeholder: "Enter a Description",
        maxlength: "400",
        "data-target": "questionDescPreview",
      },
      values != null ? values.description : ""
    );
    inputContainer2.append(input2);

    let inputContainer3 = create("div", { class: "my-3" });
    modalSidebar.append(inputContainer3);
    let inputLabel3 = create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      __["Button: *"]
    );
    inputContainer3.append(inputLabel3);
    var input3 = create("input", {
      type: "text",
      class: "form-control",
      id: "ArQuestionButtonLabel",
      maxlength: "40",
      placeholder: "Button Label",
      "data-target": "nextQuestionBtn",
      value:
        values != null && values.button_label != null
          ? values.button_label
          : "",
    });
    inputContainer3.append(input3);
  }

  /////////////////////////////////

  let modalPreview = document.getElementById(previewId);
  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  let questionTitleClass =
    values != null && values.title != null ? "question" : "question d-none";
  let questionTitle = create(
    "span",
    { class: questionTitleClass, id: "questionTitlePreview" },
    values != null && values.title != null ? values.title : "Text Title"
  );
  centerHolder.append(questionTitle);
  let questionDescClass =
    values != null && values.description != null
      ? "questionDesc"
      : "questionDesc d-none";
  let questionDesc = create(
    "span",
    { class: questionDescClass, id: "questionDescPreview" },
    values != null && values.description != null
      ? values.description
      : "Text Description"
  );
  centerHolder.append(questionDesc);

  let questionMedia = create("div", {
    class: "ratio ratio-16x9 media-container",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && (values.video != null || values.image != null)) {
    let mediaType;
    let mediaSrc;
    if (values != null && values.video != null) {
      mediaType = "iframe";
      mediaSrc = values.video;
    } else if (values != null && values.image != null) {
      mediaType = "img";
      mediaSrc = `${public_route}/images/questions/${values.image}`;
    }
    let media = create(mediaType, { class: "media-item", src: mediaSrc });
    questionMedia.append(media);
    $(questionMedia).removeClass("d-none");
  }

  let nextQuestionBtnClass =
    values != null && values.button_label != null
      ? "btn btn-primary d-inline-block mt-3 ml-auto"
      : "btn btn-primary d-inline-block mt-3 ml-auto d-none";
  let nextQuestionBtn = create(
    "button",
    {
      class: nextQuestionBtnClass,
      type: "button",
      id: "nextQuestionBtn",
    },
    values != null && values.button_label != null ? values.button_label : ""
  );
  centerHolder.append(nextQuestionBtn);

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindInput(input3, "input", "d-none", "is-invalid");

  if (typeof input1Ar != "undefined") {
    bindInput(input1Ar, "input", "d-none", "is-invalid");
  }
  if (typeof input2Ar != "undefined") {
    bindInput(input2Ar, "input", "d-none");
  }
  if (typeof input3Ar != "undefined") {
    bindInput(input3Ar, "input", "d-none", "is-invalid");
  }
  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }
  bindVideo(videoUrlInput);

  $("#modal_sidebar .nav-tabs .option-link").each(function (i) {
    $(this).click(function () {
      $(this).addClass("active").siblings().removeClass("active");
      $(this)
        .addClass("active")
        .siblings()
        .each(function (i) {
          $(`#${$(this).data("option")}-option`).addClass("d-none");
        });
      $(`#${$(this).data("option")}-option`).removeClass("d-none");
    });
    $(`#${$(this).data("option")}-option`)
      .removeClass("active")
      .addClass("d-none");
  });
  if (values != null && values.image != null) {
    $(`#image-option`).removeClass("d-none");
    $(`#image-link`).addClass("active");
    $(`#imageName`).parent().removeClass("d-none");
    $(`#imageName`).text(values.image);
  } else if (values != null && values.video != null) {
    $(`#video-option`).removeClass("d-none");
    $(`#video-link`).addClass("active");
    $(`#video_url_input`).val(values.video);
  } else {
    $(`#image-option`).removeClass("d-none");
    $(`#image-link`).addClass("active");
  }

  $("#modal_sidebar .option-link").click(function () {
    $(this).addClass("active").siblings(".option-link").removeClass("active");
    $("#" + $(this).data("target"))
      .show()
      .siblings(".option-page")
      .hide();
  });
  $("#modal_sidebar .option-link.active").each(function () {
    $("#" + $(this).data("target"))
      .show()
      .siblings(".option-page")
      .hide();
  });
}

function equationQuestionModal(values = null) {
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);
  attrs = {
    type: "file",
    id: "imageInput",
    class: "d-none",
    "data-target": "img-preview",
    accept: ".jpg,.png,.jpeg",
  };
  let imgInput = create("input", attrs);
  py.append(imgInput);
  py.append(create("p", {}, __["Add Image:"]));
  py.append(
    create(
      "button",
      {
        type: "button",
        class: "btn btn-success",
        onclick: "imageInput.click()",
      },
      __["Upload Image"]
    )
  );
  py.append(
    create(
      "button",
      {
        type: "button",
        id: "removeImage",
        class:
          values != null && values.image != null
            ? "btn btn-danger mx-2"
            : "btn btn-danger mx-2 d-none",
      },
      '<i class="fa fa-trash"></i>'
    )
  );

  // ==========================

  let inputContainer1 = create("div", { class: "my-3" });
  modalSidebar.append(inputContainer1);
  inputContainer1.append(
    create(
      "label",
      { class: "mb-2", for: "ArQuestionTitleInput" },
      "question title *"
    )
  );
  var input1 = create("input", {
    type: "text",
    class: "form-control",
    id: "ArQuestionTitleInput",
    placeholder: "Enter A Title",
    maxlength: "400",
    "data-target": "questionTitlePreview",
    value: values != null ? values.title : __["question title"],
  });
  inputContainer1.append(input1);

  let inputContainer2 = create("div", { class: "mb-3" });
  modalSidebar.append(inputContainer2);
  inputContainer2.append(
    create(
      "label",
      { class: "mb-2", for: "ArquestionDescInpiut" },
      "question description"
    )
  );
  var input2 = create(
    "textarea",
    {
      class: "form-control",
      id: "ArquestionDescInpiut",
      placeholder: "Enter a Description",
      maxlength: "400",
      "data-target": "questionDescPreview",
    },
    values != null ? values.description : __["question description"]
  );
  inputContainer2.append(input2);

  let inputContainer3 = create("div", {
    class: "mb-3",
  });
  modalSidebar.append(inputContainer3);
  inputContainer3.append(
    create(
      "label",
      { for: "answer_equation" },
      __["correct answer equation"] + ":"
    )
  );
  let correctAnswer = create("div", {
    id: "answer_equation",
    style: "direction: ltr",
  });
  inputContainer3.append(correctAnswer);
  let mathEditor = new Guppy("answer_equation");
  if (values != null && values.answers != null && values.answers[0] != null) {
    mathEditor.engine.set_content(values.answers[0].text);
    mathEditor.render(true);
  }

  let inputContainer4 = create("div", {
    class: "mb-3",
  });
  modalSidebar.append(inputContainer4);
  inputContainer4.append(
    create("label", { for: "answer_decimals" }, __["answer decimals"] + ":")
  );
  inputContainer4.append(
    create("input", {
      type: "number",
      class: "form-control",
      id: "answer_decimals",
      placeholder: "required decimals",
      value:
        values != null && values.answers != null && values.answers[0] != null
          ? values.answers[0].decimals
          : "",
    })
  );

  modalSidebar.append(create("hr", {}));

  let inputContainer5 = create("div", {
    class: "mb-3",
    id: "equation-variables-container",
  });
  modalSidebar.append(inputContainer5);

  if (values != null && values.equation_variables != null) {
    values.equation_variables.forEach((variable) => {
      createEquationVariable(variable.title, variable);
    });
  }

  let inputContainer6 = create("div", {
    class: "mb-3",
  });
  modalSidebar.append(inputContainer6);
  inputContainer6.append(
    create("label", { for: "questionScoreInput" }, __["score"] + ":")
  );
  inputContainer6.append(
    create("input", {
      class: "form-control",
      id: "questionScoreInput",
      type: "number",
      value:
        values != null &&
        values.answers != null &&
        values.answers[0] != null &&
        values.answers[0].score != null
          ? values.answers[0].score
          : "",
    })
  );

  let inputContainer7 = create("div", {
    class: "mb-3 form-check form-switch p-0 my-4 d-flex gap-3",
  });
  modalSidebar.append(inputContainer7);
  inputContainer7.append(
    create(
      "label",
      { class: "form-check-label", for: "is_skippable" },
      __["Enable Skip Question"] + ":"
    )
  );
  let checkBoxInfo = {
    class: "form-check-input mx-0 float-none",
    type: "checkbox",
    role: "switch",
    id: "is_skippable",
    "data-target": "isSkippableBtn",
  };
  if (
    values != null &&
    values.is_skippable != null &&
    values.is_skippable == 1
  ) {
    checkBoxInfo["checked"] = values.is_skippable;
  }
  let input3 = create("input", checkBoxInfo);
  inputContainer7.append(input3);

  // ==================
  // ==================
  // ==================

  let modalPreview = document.getElementById(previewId);

  let previewContainer = create("div", { class: "container-floued h-100" });
  modalPreview.append(previewContainer);
  let centerHolder = create("div", {
    class: "d-flex h-100 flex-column justify-content-center py-4 px-5 m-auto",
  });
  previewContainer.append(centerHolder);
  centerHolder.append(
    create(
      "span",
      {
        class: "question",
        id: "questionTitlePreview",
      },
      values != null && values.title != null
        ? values.title
        : __["question title"]
    )
  );
  centerHolder.append(
    create(
      "span",
      {
        class: "questionDesc mb-2",
        id: "questionDescPreview",
      },
      values != null && values.description != null
        ? values.description
        : __["question description"]
    )
  );

  let questionMedia = create("div", {
    class: "ratio ratio-16x9 media-container d-none",
    id: "img-preview",
  });
  centerHolder.append(questionMedia);

  if (values != null && values.image != null) {
    $(questionMedia).removeClass("d-none");
    let media = create("img", {
      class: "media-item",
      src: `${public_route}/images/questions/${values.image}`,
    });
    $(questionMedia).append(media);
  }

  centerHolder.append(
    create("textarea", {
      placeholder: __["enter you answer here"],
      class: "form-control mt-2",
      style: "max-width: 500px;",
    })
  );

  centerHolder.append(
    create(
      "button",
      {
        type: "button",
        id: "isSkippableBtn",
        class:
          values != null &&
          values.is_skippable != null &&
          values.is_skippable == 1
            ? "btn w-fit mt-3 ml-auto"
            : "btn w-fit mt-3 ml-auto d-none",
      },
      __["skip"]
    )
  );

  bindInput(input1, "input", "d-none", "is-invalid");
  bindInput(input2, "input", "d-none");
  bindCheck(input3);

  $(input2).on("input", function () {
    let matches = new Set(
      $(this)
        .val()
        .match(/\[[^\s]+\]/g)
    );
    if (matches != null && matches.size > 0) {
      matches.forEach((match) => {
        if (!document.getElementById("variable-" + match)) {
          createEquationVariable(match);
        }
      });
    }
    $("#equation-variables-container .variable").each(function () {
      if (!matches.has($(this).data("var"))) {
        $(this).fadeOut(300, function () {
          $(this).remove();
        });
      }
    });
  });

  if (values != null && values.id != null) {
    bindImage(imgInput, values.id);
  } else {
    bindImage(imgInput);
  }
}

function certificateModal(values = null) {
  let modalSidebar = document.getElementById(sidebarId);

  let py = create("div", {});
  modalSidebar.append(py);

  let optionsHolder = create("ul", {
    class: "nav nav-tabs w-100 mt-3",
  });
  modalSidebar.append(optionsHolder);

  if (values == null) {
    let optionBtn1 = create(
      "li",
      {
        class: "nav-link score-link w-50 text-center active",
        "data-target": "select-certificate",
      },
      __["select"]
    );
    optionsHolder.append(optionBtn1);

    var optionPage1 = create("div", {
      class: "border border-top-0 my-0 mb-3 pt-2 px-2 score-page",
      id: "select-certificate",
    });
    modalSidebar.append(optionPage1);

    let availableCertificatesOptions = create("select", {
      class: "form-control mb-2",
      id: "availableCertificates",
    });

    optionPage1.append(availableCertificatesOptions);
    if (availableCertificates.length > 0) {
      availableCertificates.forEach((certificate) => {
        availableCertificatesOptions.append(
          create(
            "option",
            {
              value: certificate.id,
            },
            certificate.title
          )
        );
      });
      $(availableCertificatesOptions).on("input", function () {
        availableCertificatePreview($(this).val());
      });
    } else {
      availableCertificatesOptions.append(
        create("option", { value: "" }, __["-- no options --"])
      );
    }
  }

  let attrs = {
    class: "w-50",
    text: __["create"],
  };
  if (values != null) {
    attrs = {
      class: "w-100 active",
      text: __["edit"],
    };
  }
  let optionBtn2 = create(
    "li",
    {
      class: "nav-link score-link text-center px-1 " + attrs.class,
      "data-target": "create-certificate",
    },
    attrs.text
  );
  optionsHolder.append(optionBtn2);

  let optionPage2 = create("div", {
    class: "border border-top-0 my-0 mb-3 pt-2 px-2 score-page",
    style: "display: none",
    id: "create-certificate",
    dir: "rtl",
  });
  if (values != null) {
    optionPage2.style = "";
  }
  modalSidebar.append(optionPage2);

  let titleContainer = create("div", { class: "my-3" });
  optionPage2.append(titleContainer);

  titleContainer.append(
    create(
      "label",
      { class: "mb-2", for: "certificateTitleInput" },
      "certificate title *"
    )
  );
  titleContainer.append(
    create("input", {
      type: "text",
      class: "form-control",
      id: "certificateTitleInput",
      placeholder: "Enter A Title",
      maxlength: "200",
      value: values != null ? values.title : "",
    })
  );

  let descriptionContainer = create("div", { class: "my-3" });
  optionPage2.append(descriptionContainer);

  descriptionContainer.append(
    create(
      "label",
      { class: "mb-2", for: "certificateDescriptionInput" },
      __["certificate description"] + " :"
    )
  );
  descriptionContainer.append(
    create("textarea", {
      class: "form-control",
      id: "certificateDescriptionInput",
      placeholder: "Enter A Description",
      value: values != null ? values.description : "",
    })
  );

  optionPage2.append(
    create("label", { class: "mb-1", for: "certificateTheme" }, "Theme *")
  );

  let certificateTheme = create("select", {
    class: "form-control mb-2",
    id: "certificateTheme",
  });
  certificateTheme.append(create("option", { value: "" }, __["select"]));
  certificatesTypes.forEach((type) => {
    let attrs = {
      value: type,
    };
    if (values != null && values.template != null && values.template == type) {
      attrs.selected = true;
    }
    certificateTheme.append(create("option", attrs, type));
  });
  optionPage2.append(certificateTheme);

  $(certificateTheme).on("input", function () {
    changeCertificateThemePreview($(certificateTheme).val());
  });

  ///////////////////////////

  $("#modal_sidebar .score-link").click(function () {
    $(this).addClass("active").siblings(".score-link").removeClass("active");
    $("#" + $(this).data("target"))
      .show()
      .siblings(".score-page")
      .hide();
  });

  if (availableCertificates.length > 0) {
    if (values != null) {
      availableCertificatePreview(values.id);
    } else {
      availableCertificatePreview($("#availableCertificates").val());
    }
  } else {
    changeCertificateThemePreview("default");
  }
}

function createNewOption(container, optionVal = null) {
  let inputHolder = create("div", { class: "input-holder d-flex gap-2" });

  let val = {
    type: "text",
    class: "form-control",
    placeholder: __["Please add an option"],
  };
  if (optionVal != null) {
    val["value"] = optionVal;
  }
  $(inputHolder).append(create("input", val, ""));

  let createOptionBtn = create(
    "button",
    {
      type: "button",
      class: "btn bg-white border create-option-btn",
    },
    "<i class='fa fa-plus'></i>"
  );

  $(inputHolder).append(createOptionBtn);

  $(createOptionBtn).click(function () {
    if ($(".options-holder .input-holder").length > 0) {
      createNewOption(container);
    }
  });

  let deleteOptionBtn = create(
    "button",
    {
      type: "button",
      class: "btn bg-danger text-white border delete-option-btn",
    },
    "<i class='fa fa-trash'></i>"
  );

  $(inputHolder).append(deleteOptionBtn);

  $(deleteOptionBtn).click(function () {
    if ($(".options-holder .input-holder").length > 1) {
      inputHolder.remove();
    }
  });

  $(container).append(inputHolder);
}

function openField(type, values = null) {
  type = parseInt(type);
  // reset the sidebar
  $("#filed-inputs-box").html("");
  $("#save-field-btn").off("click");
  // start making elements
  let holder = create("div", { class: "px-3 mb-3" });

  /* -------- Label -------- */
  $(holder).append(
    create("label", { class: "form-label", for: "label-input" }, __["Label:"])
  );
  $(holder).append(
    create(
      "input",
      {
        type: "text",
        class: "form-control mb-2",
        id: "label-input",
        value:
          values != null && values.label != null
            ? values.label
            : type == 1
            ? __["First Name"]
            : type == 2
            ? __["Last Name"]
            : type == 3
            ? __["Email address"]
            : type == 4
            ? __["Phone number"]
            : type == 5
            ? __["Long answer"]
            : type == 6
            ? __["Short answer"]
            : type == 7
            ? __["Checkbox"]
            : __["Dropdown"],
        placeholder: __["enter a label"],
      },
      ""
    )
  );
  /* -------- End Label -------- */

  /* -------- Placeholder -------- */
  if ([1, 2, 3, 4, 5, 6, 8].includes(type)) {
    $(holder).append(
      create(
        "label",
        { class: "form-label", for: "input-placeholder" },
        __["Placeholder"]
      )
    );
    $(holder).append(
      create("input", {
        type: "text",
        class: "form-control mb-2",
        id: "input-placeholder",
        value:
          values != null && values.placeholder != null
            ? values.placeholder
            : "",
        placeholder: __["Enter a Placeholder"],
      })
    );
  }
  if ([7, 8].includes(type)) {
    if (type == 7) {
      $(holder).append(
        create(
          "label",
          { class: "form-label", for: "input-placeholder" },
          __["Checkbox options:"]
        )
      );
    }
    if (type == 8) {
      $(holder).append(
        create(
          "label",
          { class: "form-label", for: "input-placeholder" },
          __["Dropdown options:"]
        )
      );
    }
    var optionsHolder = create("div", {
      class: "d-flex flex-column gap-2 options-holder",
    });
    $(holder).append(optionsHolder);
    if (values != null && values.options != null && values.options.length > 0) {
      values.options.forEach((option) => {
        createNewOption(optionsHolder, option);
      });
    } else {
      createNewOption(optionsHolder);
    }
  }
  /* -------- End Placeholder -------- */

  /* -------- Checkbox -------- */
  if (type == 7) {
    let block = create("div", { class: "my-2" });
    $(block).append(
      create(
        "label",
        { class: "form-label", for: "input-is-multiple" },
        __["Allow multiple choices:"]
      )
    );
    let info = {
      type: "checkbox",
      class: "form-check-input m-2",
      id: "input-is-multiple",
    };
    if (
      values != null &&
      values.is_multiple_chooseing != null &&
      values.is_multiple_chooseing == 1
    ) {
      info["checked"] = true;
    }
    $(block).append(create("input", info));

    $(holder).append(block);
  }
  if (type == 3) {
    let is_lead_email =
      $('.fields .field[data-type="3"]').length == 0 ||
      (values != null &&
        values.is_lead_email != null &&
        values.is_lead_email == 1);
    $(holder).append(
      `<div class="d-block my-2">
        <label for="is_lead_email" class="form-label">${
          __["Make this field lead email field"]
        }:</label>
        <input type="checkbox" id="is_lead_email" class="form-check-input m-2" ${
          is_lead_email ? "checked disabled" : ""
        }>
      </div>`
    );
  }
  if ([1, 2, 3, 4, 5, 6, 7, 8].includes(type)) {
    $(holder).append(
      create(
        "label",
        { class: "form-label", for: "input-is_required" },
        __["Field Is Required"]
      )
    );
    let info = {
      type: "checkbox",
      class: "form-check-input m-2",
      id: "input-is_required",
      placeholder: __["Enter a Placeholder"],
    };
    if (
      values != null &&
      values.is_required != null &&
      values.is_required == 1
    ) {
      info.checked = true;
    }
    if (
      type == 3 &&
      ($('.fields .field[data-type="3"]').length == 0 ||
        (values != null &&
          values.is_lead_email != null &&
          values.is_lead_email == 1))
    ) {
      info.checked = true;
      info.disabled = true;
    }
    $(holder).append(create("input", info));
  }
  /* -------- End Checkbox -------- */

  $("#filed-inputs-box").append(holder);

  if (type == 3) {
    $("#is_lead_email").on("change", function () {
      if ($(this).prop("checked")) {
        $("#input-is_required").prop("checked", true);
        $("#input-is_required").prop("disabled", true);
      } else {
        $("#input-is_required").prop("disabled", false);
      }
    });
  }

  $("#modal_sidebar").hide();
  $("#sidebarOption").show();

  if (values != null && values.id != null) {
    $("#save-field-btn").click(function () {
      let editEl = document.querySelector(`#${values.id}`);
      // if didn't found the field sold close field panel

      editEl.dataset.title = $("#label-input").val();
      // info["en_label"] = ""; // $("#label-input-ar").val();
      // info["en_placeholder"] = "";

      if ([1, 2, 3, 4, 5, 6, 8].includes(type)) {
        editEl.dataset.placeholder = $("#input-placeholder").val();
      }
      if (type == 3) {
        editEl.dataset.is_lead_email =
          $("#is_lead_email").prop("checked") == true ? 1 : 0;
      }
      if ([7, 8].includes(type)) {
        if (type == 7) {
          editEl.dataset.is_multiple_chooseing =
            $("#input-is-multiple").prop("checked") == true ? 1 : 0;
        }
        $(editEl).find(".option-input").remove();
        if ($(".options-holder .input-holder").length > 1) {
          if (
            $(".options-holder .input-holder input").first().val().length > 0
          ) {
            $(".options-holder .input-holder").each(function () {
              $(editEl).append(
                create("input", {
                  class: "option-input",
                  type: "hidden",
                  value: $(this).find("input").first().val(),
                })
              );
            });
          } else {
            if ($(".options-holder .error").length == 0) {
              $(".options-holder").append(
                create(
                  "div",
                  { class: "alert alert-danger error" },
                  "no options"
                )
              );
            }
            return null;
          }
        } else {
          if ($(".options-holder .error").length == 0) {
            $(".options-holder").append(
              create(
                "div",
                { class: "alert alert-danger error" },
                __["no options"]
              )
            );
          }
          return null;
        }
      }

      // edit the title
      $(editEl).find(`#${values.id}-title`).text($("#label-input").val());

      // reset
      $(this).off("click");
      $("#filed-inputs-box").html("");
      $("#modal_sidebar").show();
      $("#sidebarOption").hide();

      // show fields container or not
      if ($("#fields-holder-preview").find(".field").length > 0) {
        $("#fields-holder-preview").removeClass("d-none");
      } else {
        $("#fields-holder-preview").addClass("d-none");
      }
    });
  } else {
    $("#save-field-btn").click(function () {
      if ($("#label-input").val().length > 0) {
        let info = {
          count: $("#fields-holder-preview .field").length + 1,
          type: type,
          label: $("#label-input").val(),
        };

        if ([1, 2, 3, 4, 5, 6, 8].includes(type)) {
          info["placeholder"] = $("#input-placeholder").val();
          // info["en_label"] = ""; // $("#label-input-ar").val();
          // info["en_placeholder"] = "";
        }
        if (type == 3) {
          info["is_lead_email"] =
            $("#is_lead_email").prop("checked") == true ? 1 : 0;
        }
        if (type == 7) {
          info["is_multiple_chooseing"] =
            $("#input-is-multiple").prop("checked") == true ? 1 : 0;
        }
        if ([7, 8].includes(type)) {
          info["options"] = [];

          // check options count
          if ($(".options-holder .input-holder").length > 1) {
            if (
              $(".options-holder .input-holder input").first().val().length > 0
            ) {
              $(".options-holder .input-holder").each(function () {
                info["options"].push($(this).find("input").first().val());
              });
            } else {
              if ($(".options-holder .error").length == 0) {
                $(".options-holder").append(
                  create(
                    "div",
                    { class: "alert alert-danger error" },
                    "no options"
                  )
                );
              }
              return null;
            }
          } else {
            if ($(".options-holder .error").length == 0) {
              $(".options-holder").append(
                create(
                  "div",
                  { class: "alert alert-danger error" },
                  __["no options"]
                )
              );
            }
            return null;
          }
        }

        createField(info);

        // reset
        $("#filed-inputs-box").html("");
        $("#modal_sidebar").show();
        $("#sidebarOption").hide();
        $(this).off("click");

        // show fields container or not
        if ($("#fields-holder-preview").find(".field").length > 0) {
          $("#fields-holder-preview").removeClass("d-none");
        } else {
          $("#fields-holder-preview").addClass("d-none");
        }
      } else {
        $("#label-input").addClass("is-invalid");
      }
    });
  }
}

// ==== Helper Functions ====

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

function bindInput(
  input,
  event = "input",
  targetClass = false,
  inputClass = ""
) {
  let target = document.getElementById(input.dataset.target);
  input.addEventListener(event, function () {
    target.innerText = this.value;
    // if (this.value.length > 0) {
    //   target.classList.remove("d-none");
    // } else {
    //   target.classList.add("d-none");
    // }
    if (targetClass != false) {
      if (this.value.length > 0) {
        this.className = this.className.replace(inputClass, "");
        target.className = target.className.replace(targetClass, "");
      } else {
        this.className = this.className + " " + inputClass;
        target.className = target.className + " " + targetClass;
      }
    }
  });
}

function bindCheck(check) {
  let target = document.getElementById(check.dataset.target);
  check.addEventListener("input", function () {
    if (check.checked) {
      target.classList.remove("d-none");
    } else {
      target.classList.add("d-none");
    }
  });
}

function bindImage(imgInput, questionId = false) {
  let target = $("#" + imgInput.dataset.target);
  let removeBtn = $("#removeImage");
  removeBtn.click(function () {
    imgInput.value = "";
    target.addClass("d-none").html("");
    removeBtn.addClass("d-none");
    if (questionId) {
      $.ajax({
        type: "POST", // For jQuery < 1.9
        method: "POST",
        url: `${question_ajax_route}/${questionId}/image_actions`,
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: {
          action: "remove",
        },
        success: function (res) {
          console.log("Image Uploaded (:");
        },
        error: function (res) {
          console.error("something went wrong :(");
          if (
            res.responseJSON.errors["question_title"] != null &&
            res.responseJSON.errors["question_title"][0] != null
          ) {
            $("#ArQuestionTitleInput").addClass("is-invalid");
            $(
              "<div class='invalid-feedback'>" +
                res.responseJSON.errors["question_title"][0] +
                "</div>"
            ).insertAfter("#ArQuestionTitleInput");
          }
        },
      });
    }
  });
  imgInput.addEventListener("input", function () {
    const [file] = this.files;
    if (file) {
      removeBtn.removeClass("d-none");
      target
        .removeClass("d-none")
        .html(
          $("<img class='media-item' src='" + URL.createObjectURL(file) + "'>")
        );
    }
  });
}

function bindVideo(urlInput) {
  let target = $("#" + urlInput.dataset.target);
  urlInput.addEventListener("input", function () {
    let url = new URL($(urlInput).val());
    let urlsearch = new URLSearchParams(url.search);
    let videoId = urlsearch.get("v");
    let videoUrl = $(urlInput).val();
    if (videoId) {
      videoUrl = `https://www.youtube.com/embed/${videoId}`;
    }

    if ($(urlInput).val().length > 0) {
      if (
        [
          "www.youtube.com",
          "youtube.com",
          "youtube-nocookie.com",
          "youtu.be",
          "www.youtube-nocookie.com",
        ].includes(url.hostname)
      ) {
        target.removeClass("d-none");
        target.html(`
          <iframe
              width="800"
              height="400"
              src="${videoUrl}"
              title="YouTube video player"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowfullscreen></iframe>
        `);
      } else {
        target.addClass("d-none");
      }
    } else {
      target.addClass("d-none");
    }
  });
}

function bindAnswer(input) {
  let target = document.getElementById(input.dataset.target);
  input.addEventListener("input", function () {
    target.innerText = input.value;
    if (input.value.length > 0) {
      target.parentElement.classList.remove("d-none");
    } else {
      target.parentElement.classList.add("d-none");
    }
  });
}

function changeCertificateThemePreview(theme) {
  let preview = $("#modal_preview");

  let container = create("div", {
    class: "certificate-preview-box container-fluid",
  });
  preview.html(container);

  $.ajax({
    method: "GET",
    url: certificate_theme_route + "/" + theme,
    success: function (res) {
      $(container).html(res);
      $("#certificateTitleInput").off("input");
      $("#certificateTitleInput").on("input", function () {
        $("#certificate-title").text($(this).val());
      });
      $("#certificateDescriptionInput").off("input");
      $("#certificateDescriptionInput").on("input", function () {
        $("#certificate-desc").text($(this).val());
      });
    },
    error: function (err) {
      console.error(err);
    },
  });
}

function availableCertificatePreview(certificate_id) {
  let preview = $("#modal_preview");

  let container = create("div", {
    class: "certificate-preview-box container-fluid",
  });
  preview.html(container);

  $.ajax({
    method: "GET",
    url: certificate_show_route + "/" + certificate_id,
    success: function (res) {
      $(container).html(res);
    },
    error: function (err) {
      console.error(err);
    },
  });
}

// logic functions

function isJson(item) {
  let value = typeof item !== "string" ? JSON.stringify(item) : item;
  try {
    value = JSON.parse(value);
    return true;
  } catch (e) {
    return false;
  }
}

function makeSuccessAlert(msg) {
  let id = Date.now();
  let el = `<div id="${id}" class="action-alert alert position-absolute alert-success d-flex align-items-center mb-0" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
    <div>
      ${msg}
    </div>
  </div>`;
  $("#alerts").append(el);
  var myAlert = document.getElementById(id);
  return new bootstrap.Alert(myAlert);
}

function makeErrorAlert(msg) {
  let id = Date.now();
  let el = `<div id="${id}" class="action-alert alert position-absolute alert-danger d-flex align-items-center" role="alert">
      <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
    <div>
      ${msg}
    </div>
  </div>`;
  $("#alerts").append(el);
  var myAlert = document.getElementById(id);
  return new bootstrap.Alert(myAlert);
}
