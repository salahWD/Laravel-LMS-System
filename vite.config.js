import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { readdirSync } from "fs";
import { resolve } from "path";

// Helper function to dynamically include all CSS and JS files
const getFilesFromDirectory = (dir, fileTypes) => {
  const ret = readdirSync(dir)
    .filter((file) => fileTypes.includes(file.split(".").pop()))
    .map((file) => `${dir}/${file}`);

  return ret;
};

export default defineConfig({
  plugins: [
    laravel({
      input: [
        ...getFilesFromDirectory("resources/css", ["css"]),
        ...getFilesFromDirectory("resources/csslibs", ["css"]),
        ...getFilesFromDirectory("resources/css/dashboard", ["css"]),
        ...getFilesFromDirectory("public/js", ["js"]),
      ],
      refresh: true,
    }),
  ],
});
