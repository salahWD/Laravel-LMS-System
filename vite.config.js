import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/css/style.css",
        "resources/css/video-player.css",
        "resources/css/taking-test.css",
        "resources/css/slick.css",
        "resources/css/simple-line-icons.css",
        "resources/css/sections.css",
        "resources/css/login.css",
        "resources/css/home.css",
        "resources/css/editor.css",
        "resources/css/custom.css",
        "resources/css/courses.css",
        "resources/css/contact.css",

        "resources/css/dashboard/sweetalert2.min.css",
        "resources/css/dashboard/notyf.min.css",
        "resources/css/dashboard/volt.css",
        "resources/css/dashboard/volt.css",
        "resources/css/dashboard/user-edit.css",
        "resources/css/dashboard/test-report.css",
        "resources/css/dashboard/test-builder.css",
        "resources/css/dashboard/sweetalert2.min.css",
        "resources/css/dashboard/style.css",
        "resources/css/dashboard/meetings.css",
        "resources/css/dashboard/notyf.min.css",

        "resources/csslibs/style.css",
        "resources/csslibs/slick.css",
        "resources/csslibs/simple-line-icons.css",
        "resources/csslibs/bootstrap.min.css",
        "resources/csslibs/all.min.css",

        "resources/css/shop.css",
        "resources/css/product.css",
        "resources/css/single-product.css",
        "resources/css/checkout.css",

        "public/js/taking-test.js",
        "public/js/slick.min.js",
        "public/js/popper.min.js",
        "public/js/jquery.sticky-sidebar.min.js",
        "public/js/jquery.min.js",
        "public/js/custom.js",
        "public/js/bootstrap.min.js",
        "public/js/video-player.js",
        "public/js/yt-player.js",
        "public/js/meeting.js",

        "public/js/dashboard/bootstrap.js",

        "public/js/popper.min.js",
        "public/js/bootstrap.min.js",
        "public/js/slick.min.js",
        "public/js/jquery.sticky-sidebar.min.js",
        "public/js/app.js",
        // "resources/js/app.js",
        // "public/js/custom.js",
      ],
      refresh: true,
    }),
  ],
});
