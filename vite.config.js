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
        "resources/css/dashboard/notyf.min.css",

        "resources/csslibs/style.css",
        "resources/csslibs/slick.css",
        "resources/csslibs/simple-line-icons.css",
        "resources/csslibs/bootstrap.min.css",
        "resources/csslibs/all.min.css",

        "resources/js/taking-test.js",
        "resources/js/slick.min.js",
        "resources/js/popper.min.js",
        "resources/js/jquery.sticky-sidebar.min.js",
        "resources/js/jquery.min.js",
        "resources/js/custom.js",
        "resources/js/bootstrap.min.js",
        "resources/js/video-player.js",
        "resources/js/yt-player.js",

        "resources/js/dashboard/bootstrap.js",

        "resources/js/popper.min.js",
        "resources/js/bootstrap.min.js",
        "resources/js/slick.min.js",
        "resources/js/jquery.sticky-sidebar.min.js",
        "resources/js/app.js",
        "resources/js/custom.js",
      ],
      refresh: true,
    }),
  ],
});
