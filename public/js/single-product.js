function isInt(value) {
  return (
    !isNaN(value) &&
    (function (x) {
      return (x | 0) === x;
    })(parseFloat(value))
  );
}

function deleteAllCookies() {
  const cookies = document.cookie.split(";");
  for (let i = 0; i < cookies.length; i++) {
    const cookie = cookies[i];
    const eqPos = cookie.indexOf("=");
    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
  }
}

$(document).ready(function () {
  /* ======== Quantity Input ======== */
  $(quantity).on("input", function (e) {
    if (!isInt(e.originalEvent.data)) {
      $(this).val($(this).val().replace(e.originalEvent.data, ""));
    }
    if (
      !isInt($(quantity).val()) ||
      parseInt($(quantity).val()) < parseInt($(quantity).data("min"))
    ) {
      $(quantity).val(parseInt($(quantity).data("min")));
    } else {
      if (parseInt($(quantity).val()) > parseInt($(quantity).data("max"))) {
        $(quantity).val(parseInt($(quantity).data("max")));
      }
    }
  });
  $(".btn-minus").on("click", function () {
    if (parseInt($(quantity).val()) > parseInt($(quantity).data("min"))) {
      $(quantity).val(parseInt($(quantity).val()) - 1);
    }
  });
  $(".btn-plus").on("click", function () {
    if (parseInt($(quantity).val()) < parseInt($(quantity).data("max"))) {
      $(quantity).val(parseInt($(quantity).val()) + 1);
    }
  });

  /* ======== Product Image ======== */
  if ($(window).width() > 767) {
    $(".product-preview-image").hiZoom({
      width: $(".product-preview-image").width(),
      position: "right",
    });
  }

  /* ======== Product Images ======== */
  $(".product-images img").each(function () {
    $(this).on("click", function () {
      let link = $(this).attr("src");
      $(".product-preview-image")
        .find("img")
        .each(function () {
          $(this).attr("src", link);
        });
    });
  });
  $(".product-images").on("wheel", function (e) {
    e.preventDefault();
    let el = $(this).find(".track");
    if (e.originalEvent.wheelDelta > 0) {
      el.scrollLeft(el.scrollLeft() - 25);
    } else {
      el.scrollLeft(el.scrollLeft() + 25);
    }
  });

  /* ======== Sub Pages ======== */
  $(".sub-pages .nav-link").each(function () {
    $(this).on("click", function () {
      if (!$(this).parent().hasClass("active")) {
        $(this)
          .addClass("active")
          .parent()
          .addClass("active")
          .siblings()
          .removeClass("active")
          .find(".nav-link")
          .removeClass("active");
        $(".pages .page").slideUp();
        $("#" + $(this).data("page")).slideDown();
      }
    });
  });

  /* ======== Similar Products ======== */
  $(".similar-products-carousel").owlCarousel({
    loop: true,
    nav: true,
    lazyLoad: true,
    margin: 15,
    items: 4,
    navText: ["<i class='hot-deal-btnl'></i>", "<i class='hot-deal-btnr'></i>"],
    responsive: {
      0: { items: 2 },
      600: { items: 2 },
      960: { items: 4 },
      1200: { items: 4 },
    },
  });

  /* ======== Add To Card Button ======== */
  $("#add-to-cart").on("click", function () {
    let url = new URL(window.location.href);
    let data = {
      quantity: parseInt($("#quantity").val()),
      product: url.pathname.split("/").slice(-1)[0],
    };
    let cookies = document.cookie.split("; ");
    let cartIdCookie = cookies.filter((coki) => {
      return coki.includes("cartId=");
    });
    if (cartIdCookie.length > 0) {
      data["cart_id"] = cartIdCookie[0].replace("cartId=", "");
    }
    $.ajax(cardUrl, {
      method: "POST",
      data: data,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      success: function (cartId) {
        const now = new Date();
        const next30Days = now.getTime() + 30 * 24 * 60 * 60 * 1000;
        const next30DaysDate = new Date(next30Days);
        document.cookie = `cartId=${cartId};expires=${next30DaysDate}; path=/`;
        let cartItemsEl = $("#cartItemsCount");
        let newVal = parseInt(cartItemsEl.data("count")) + data.quantity;
        cartItemsEl.data("count", newVal);
        cartItemsEl.text(newVal);
      },
      error: function (err) {
        let id = Date.now();
        let el = `
        <div id="${id}" class="action-alert alert position-fixed alert-danger d-flex align-items-center mb-0 top-0 start-50" style="transition: all 0.3s ease-in-out;transform: translate(-50%, -100%);opacity:0" role="alert">
          <div>
            ${err.responseJSON.message}
          </div>
        </div>`;
        $(document.body).append(el);

        var myAlert = document.getElementById(id);
        let alert = new bootstrap.Alert(myAlert);
        setTimeout(() => {
          $("#" + id).attr(
            "style",
            "transition: all 0.3s ease-in-out;transform: translate(-50%, 100%); opacity:1"
          );
        }, 50);
        setTimeout(() => {
          $("#" + id).attr(
            "style",
            "transition: all 0.3s ease-in-out;transform: translate(-50%, -100%); opacity:0"
          );
        }, 2500);

        setTimeout(() => {
          alert.close();
        }, 3000);

        console.error(err);
      },
    });
  });
});
