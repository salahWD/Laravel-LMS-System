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

imagesPreview = document.getElementById("product-images-preview");
var selectedImage;

document.getElementById("product-form").addEventListener("submit", function () {
  if (productImagePreview.src.match(/(https?:\/\/.*\.(?:png|jpg|jpeg))/i)) {
    // if it's from a link and not uploaded from pc
    if (!productImagePreview.src.includes(window.location.hostname)) {
      selectedImage = "new_images"; // new image uploaded from a link
    } else {
      selectedImage = productImagePreview.src; // the link of the image is from our site so it is not new
    }
  } else {
    // it is uploaded from pc
    selectedImage = "new_images";
  }
  let selectedImageInput = create("input", {
    type: "hidden",
    class: "d-none",
    name: "selected_image",
    value: selectedImage,
  });
  this.append(selectedImageInput);
});

imagesPreview.querySelectorAll(".product-img").forEach((img) => {
  img.querySelector(".btn-success").addEventListener("click", function () {
    productImagePreview.src = img.querySelector("img").src;
    imagesPreview.querySelectorAll(".product-img").forEach((el, index) => {
      let i = index + 1;
      el.querySelector("input").setAttribute("name", "old_images[" + i + "]");
    });
    img.querySelector("input").setAttribute("name", "old_images[0]");
  });
  img.querySelector(".btn-danger").addEventListener("click", function () {
    this.parentElement.parentElement.remove();
  });
});

productImage.addEventListener("input", function (event) {
  var fileList = event.target.files;

  if (this.files.length > 0) {
    imagesPreview.querySelectorAll(".product-img.new").forEach((el) => {
      el.remove();
    });
    let trig = true;
    [...this.files].map((file, index) => {
      if (trig) {
        trig = false;
        productImagePreview.src = URL.createObjectURL(file);
      }
      addImage(file, event);
      // let imgBox = create("div", {
      //   class: "product-img new rounded",
      // });

      // const image = new Image();
      // image.title = file.name;
      // image.src = URL.createObjectURL(file);

      // let btns = create("div", {
      //   class: "btns",
      // });

      // let btn1 = create(
      //   "button",
      //   {
      //     class: "btn btn-sm btn-success",
      //     type: "button",
      //     tabindex: "-1",
      //   },
      //   "<i class='fa fa-image'></i>"
      // );
      // btn1.addEventListener("click", function () {
      //   productImagePreview.src = image.src;
      //   var newFileList = Array.from(fileList);
      //   let selected = newFileList.splice(index, 1);
      //   newFileList.unshift(selected[0]);
      //   // Create a new DataTransfer object and add the files
      //   var dataTransfer = new DataTransfer();
      //   newFileList.forEach(function (file) {
      //     dataTransfer.items.add(file);
      //   });
      //   // Set the new FileList using the DataTransfer object
      //   event.target.files = dataTransfer.files;
      // });

      // let btn2 = create(
      //   "button",
      //   {
      //     class: "btn btn-sm btn-danger",
      //     type: "button",
      //     tabindex: "-1",
      //   },
      //   "<i class='fa fa-trash'></i>"
      // );
      // btn2.addEventListener("click", function () {
      //   this.parentElement.parentElement.remove();
      //   // Create a new FileList without the file to be removed
      //   var newFileList = Array.from(fileList);
      //   newFileList.splice(index, 1);
      //   // Create a new DataTransfer object and add the files
      //   var dataTransfer = new DataTransfer();
      //   newFileList.forEach(function (file) {
      //     dataTransfer.items.add(file);
      //   });
      //   // Set the new FileList using the DataTransfer object
      //   event.target.files = dataTransfer.files;
      //   // Re-trigger the change event to update the UI
      //   event.target.dispatchEvent(new Event("input"));
      // });

      // imgBox.appendChild(image);
      // btns.appendChild(btn1);
      // btns.appendChild(btn2);
      // imgBox.appendChild(btns);
      // imagesPreview.appendChild(imgBox);
    });
  }
});

$("#product_link").on("input", async function () {
  let link = $(this).val();
  getProductDetails(link);
});

function isAliExpressUrl(url) {
  // Define a regular expression pattern to match AliExpress product URLs
  var pattern = /^(https?:\/\/)?(www\.)?aliexpress\.com\/item\/\d+\.html/i;

  // Test if the URL matches the pattern
  return pattern.test(url);
}

function getProductDetails(productUrl) {
  // Check if the provided URL is a valid AliExpress link
  if (!isAliExpressUrl(productUrl)) {
    console.error("Invalid AliExpress URL");
    return;
  }

  // Make a GET request to your Laravel endpoint with the product URL
  $.ajax({
    url: proxyUrl, // Change to your Laravel endpoint URL
    method: "GET",
    data: { url: productUrl },
    success: function (res) {
      let trig = true;
      res.images.map((file) => {
        if (trig) {
          trig = false;
          productImagePreview.src = file;
        }
        addImage(file);
      });

      $(productId).val(res.product_id);
      $(title).val(res.title);

      // if ($("#dropshipping").prop("checked") == true) {
      $(price).val(res.price);
      $(stock).val(res.quantity);
      // } else {
      //   $(price).attr("name", "");
      //   $(stock).attr("name", "");
      // }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}

function addImage(file, event = null) {
  let imgBox = create("div", {
    class: "product-img new rounded",
  });

  let hiddenUrl;
  const image = new Image();
  if (isValidHttpUrl(file)) {
    image.src = file;
    hiddenUrl = create("input", {
      value: file,
      name: "url_images[]",
      type: "hidden",
      class: "d-none",
    });
    imgBox.append(hiddenUrl);
    imgBox.classList.add("url-image");
  } else {
    image.src = URL.createObjectURL(file);
  }

  let btns = create("div", {
    class: "btns",
  });

  let btn1 = create(
    "button",
    {
      class: "btn btn-sm btn-success",
      type: "button",
      tabindex: "-1",
    },
    "<i class='fa fa-image'></i>"
  );
  btn1.addEventListener("click", function () {
    productImagePreview.src = image.src;
    if (event != null) {
      var newFileList = Array.from(fileList);
      let selected = newFileList.splice(index, 1);
      newFileList.unshift(selected[0]);
      // Create a new DataTransfer object and add the files
      var dataTransfer = new DataTransfer();
      newFileList.forEach(function (file) {
        dataTransfer.items.add(file);
      });
      // Set the new FileList using the DataTransfer object
      event.target.files = dataTransfer.files;
    } else {
      imagesPreview
        .querySelectorAll(".product-img.url-image input")
        .forEach((el, index) => {
          let i = index + 1;
          el.setAttribute("name", "url_images[" + i + "]");
        });
      console.log(image);
      hiddenUrl.setAttribute("name", "url_images[0]");
    }
  });

  let btn2 = create(
    "button",
    {
      class: "btn btn-sm btn-danger",
      type: "button",
      tabindex: "-1",
    },
    "<i class='fa fa-trash'></i>"
  );
  btn2.addEventListener("click", function () {
    this.parentElement.parentElement.remove();
    if (event != null) {
      // Create a new FileList without the file to be removed
      var newFileList = Array.from(fileList);
      newFileList.splice(index, 1);
      // Create a new DataTransfer object and add the files
      var dataTransfer = new DataTransfer();
      newFileList.forEach(function (file) {
        dataTransfer.items.add(file);
      });
      // Set the new FileList using the DataTransfer object
      event.target.files = dataTransfer.files;
      // Re-trigger the change event to update the UI
      event.target.dispatchEvent(new Event("input"));
    }
  });

  imgBox.appendChild(image);
  btns.appendChild(btn1);
  btns.appendChild(btn2);
  imgBox.appendChild(btns);
  imagesPreview.appendChild(imgBox);
}

function isValidHttpUrl(string) {
  let url;

  try {
    url = new URL(string);
  } catch (_) {
    return false;
  }

  return url.protocol === "http:" || url.protocol === "https:";
}
